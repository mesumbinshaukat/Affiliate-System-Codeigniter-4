<?php
/**
 * Mega Migration Runner
 * Visit: http://your-domain/run_migration_full.php
 *
 * - Ensures the configured database exists
 * - Runs all CodeIgniter migrations (covering every model/table)
 * - Verifies that tables used by models now exist
 *
 * SECURITY: Delete this file immediately after running!
 */

// Strict error visibility while running manually
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootPath = realpath(__DIR__ . '/..');
$envFile  = $rootPath . '/.env';
$env      = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key           = trim($key);
        $value         = trim($value, " \t\n\r\0\x0B'\"");
        $env[$key]     = $value;
        // Make sure the variables are available through getenv()/$_ENV for CI Services
        putenv($key . '=' . $value);
        $_ENV[$key]    = $value;
        $_SERVER[$key] = $value;
    }
}

$dbHost = $env['database.default.hostname'] ?? '127.0.0.1';
$dbName = $env['database.default.database'] ?? 'wmcmedia_maakjeLijst';
$dbUser = $env['database.default.username'] ?? 'root';
$dbPass = $env['database.default.password'] ?? '';
$dbPort = (int)($env['database.default.port'] ?? 3306);
$dbChar = $env['database.default.charset'] ?? 'utf8mb4';
$dbColl = $env['database.default.DBDriver'] === 'Postgre' ? 'UTF8' : 'utf8mb4_unicode_ci';

$steps  = [];
$errors = [];
$debugEnv = [
    'CI_ENVIRONMENT' => $env['CI_ENVIRONMENT'] ?? getenv('CI_ENVIRONMENT') ?? '(not set)',
    'database.default.hostname' => $env['database.default.hostname'] ?? getenv('database.default.hostname') ?? '(not set)',
    'database.default.database' => $env['database.default.database'] ?? getenv('database.default.database') ?? '(not set)',
    'database.default.username' => $env['database.default.username'] ?? getenv('database.default.username') ?? '(not set)',
    'database.default.password' => $env['database.default.password'] ?? getenv('database.default.password') ?? '(not set)',
];

// Load CodeIgniter's path configuration early so we can define constants before bootstrapping
require_once $rootPath . '/app/Config/Paths.php';
$paths = new Config\Paths();

defined('ENVIRONMENT') || define('ENVIRONMENT', $env['CI_ENVIRONMENT'] ?? 'production');
defined('ROOTPATH')    || define('ROOTPATH', $rootPath . DIRECTORY_SEPARATOR);
defined('APPPATH')     || define('APPPATH', realpath($paths->appDirectory) . DIRECTORY_SEPARATOR);
defined('SYSTEMPATH')  || define('SYSTEMPATH', realpath($paths->systemDirectory) . DIRECTORY_SEPARATOR);
defined('FCPATH')      || define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
defined('WRITEPATH')   || define('WRITEPATH', realpath($paths->writableDirectory) . DIRECTORY_SEPARATOR);
defined('TESTPATH')    || define('TESTPATH', realpath($paths->testsDirectory) . DIRECTORY_SEPARATOR);
defined('CI_DEBUG')    || define('CI_DEBUG', false);

// Load common functions (config(), log_message(), etc.) before bootstrapping services
require_once SYSTEMPATH . 'Common.php';

function log_step(string $title, string $body, string $type = 'info'): void
{
    global $steps;
    $steps[] = compact('title', 'body', 'type');
}

function format_exception(Throwable $e): string
{
    return htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString());
}

// -----------------------------------------------------------------------------
// STEP 1: Ensure database exists
// -----------------------------------------------------------------------------
try {
    $serverConnection = @new mysqli($dbHost, $dbUser, $dbPass, '', $dbPort);
    if ($serverConnection->connect_errno) {
        throw new RuntimeException('Failed to connect to database server: ' . $serverConnection->connect_error);
    }

    $dbNameEscaped = $serverConnection->real_escape_string($dbName);
    $charset       = $serverConnection->real_escape_string($dbChar);
    $collation     = $serverConnection->real_escape_string($dbColl);

    if ($serverConnection->query("CREATE DATABASE IF NOT EXISTS `{$dbNameEscaped}` CHARACTER SET {$charset} COLLATE {$collation}")) {
        log_step(
            'Database Check',
            "Database <strong>{$dbName}</strong> is ready (created if it did not exist).",
            'success'
        );
    } else {
        throw new RuntimeException('Unable to create database: ' . $serverConnection->error);
    }

    $serverConnection->close();
} catch (Throwable $e) {
    $errors[] = 'Database bootstrap failed.';
    log_step('Database Check', format_exception($e), 'error');
}

// -----------------------------------------------------------------------------
// STEP 2: Bootstrap CodeIgniter + run migrations
// -----------------------------------------------------------------------------
$dbTables   = [];
$modelTableMap = [];

if (empty($errors)) {
    try {
        require_once $rootPath . '/vendor/autoload.php';
        if (file_exists(APPPATH . 'Config/Constants.php')) {
            require_once APPPATH . 'Config/Constants.php';
        }

        // Ensure the autoloader knows about framework namespaces before discovering services
        $autoloader = Config\Services::autoloader();
        $autoloader->initialize(new Config\Autoload(), new Config\Modules());

        $db = Config\Database::connect();
        $db->query('SELECT 1');
        log_step('Database Connection', 'Connected to database with CodeIgniter configuration.', 'success');

        $migrate = Config\Services::migrations();
        $historyBefore = $migrate->getHistory();
        $previousVersion = !empty($historyBefore) ? end($historyBefore)->version : null;

        if ($migrate->latest()) {
            $historyAfter = $migrate->getHistory();
            $newVersion = !empty($historyAfter) ? end($historyAfter)->version : null;
            $msg = ($previousVersion === $newVersion)
                ? 'No new migrations were pending. Schema is already up to date.'
                : "Migrations executed successfully. Latest version is now <strong>" . ($newVersion ?? 'unknown') . '</strong>.';
            log_step('Run Migrations', $msg, 'success');
        } else {
            log_step('Run Migrations', 'No migrations were executed (possibly already up to date).', 'info');
        }

        $dbTables = $db->listTables();

        // Gather model => table mapping dynamically
        $modelFiles = glob($rootPath . '/app/Models/*.php') ?: [];
        foreach ($modelFiles as $modelFile) {
            $contents = file_get_contents($modelFile);
            if ($contents && preg_match('/protected\s+\$table\s*=\s*\'([^\']+)\'/', $contents, $matches)) {
                $tableName = $matches[1];
                $modelName = basename($modelFile, '.php');
                $modelTableMap[$tableName][] = $modelName;
            }
        }
        ksort($modelTableMap);

        // Build verification table
        ob_start();
        echo '<table><thead><tr><th>Table</th><th>Models</th><th>Status</th></tr></thead><tbody>';
        foreach ($modelTableMap as $tableName => $models) {
            $exists = in_array($tableName, $dbTables, true);
            printf(
                '<tr><td>%s</td><td>%s</td><td style="color:%s;">%s</td></tr>',
                htmlspecialchars($tableName),
                htmlspecialchars(implode(', ', $models)),
                $exists ? '#198754' : '#dc3545',
                $exists ? '✓ present' : '✗ missing'
            );
        }
        echo '</tbody></table>';
        $verificationTable = ob_get_clean();

        log_step('Model Table Verification', $verificationTable, 'info');

        // Quick stats per table
        ob_start();
        echo '<table><thead><tr><th>Table</th><th style="text-align:right;">Records</th></tr></thead><tbody>';
        foreach ($dbTables as $dbTable) {
            try {
                $count = $db->table($dbTable)->countAllResults();
            } catch (Throwable $e) {
                $count = 'n/a';
            }
            printf('<tr><td>%s</td><td style="text-align:right;">%s</td></tr>', htmlspecialchars($dbTable), htmlspecialchars((string)$count));
        }
        echo '</tbody></table>';
        $statsTable = ob_get_clean();

        log_step('Database Table Counts', $statsTable, 'info');
    } catch (Throwable $e) {
        $errors[] = 'Migration runner crashed.';
        log_step('Migration Runner', format_exception($e), 'error');
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mega Migration Runner</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 1100px; margin: 40px auto; padding: 0 20px; background: #f4f6fb; }
        .container { background: #fff; border-radius: 14px; padding: 32px; box-shadow: 0 20px 60px rgba(15,23,42,0.15); }
        h1 { margin-top: 0; color: #0f172a; }
        .step { border-left: 5px solid #2563eb; padding: 16px 20px; margin: 20px 0; background: #f8fafc; border-radius: 0 12px 12px 0; }
        .success { border-left-color: #16a34a; }
        .error { border-left-color: #dc2626; }
        .warning { border-left-color: #d97706; }
        .info { border-left-color: #2563eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; }
        th { background: #f1f5f9; }
        code { background: #e2e8f0; padding: 2px 6px; border-radius: 4px; }
        .security { background: #fef3c7; border: 1px solid #fcd34d; padding: 16px; border-radius: 10px; margin-top: 30px; }
        a.btn { display: inline-block; padding: 10px 18px; margin-top: 15px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; }
        pre { background: #0f172a; color: #f8fafc; padding: 15px; border-radius: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Mega Migration Runner</h1>
        <p><strong>Environment:</strong> <?= htmlspecialchars(ENVIRONMENT ?? 'production'); ?></p>
        <p><strong>Database Host:</strong> <?= htmlspecialchars($dbHost); ?> &middot; <strong>Database Name:</strong> <?= htmlspecialchars($dbName); ?></p>

        <div class="step info">
            <h3>Loaded Environment Variables</h3>
            <table>
                <thead>
                    <tr><th>Key</th><th>Value</th></tr>
                </thead>
                <tbody>
                <?php foreach ($debugEnv as $key => $value): ?>
                    <tr>
                        <td><?= htmlspecialchars($key); ?></td>
                        <td><?= htmlspecialchars(is_string($value) ? $value : json_encode($value)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php foreach ($steps as $step): ?>
            <div class="step <?= htmlspecialchars($step['type']); ?>">
                <h3><?= htmlspecialchars($step['title']); ?></h3>
                <?= $step['body']; ?>
            </div>
        <?php endforeach; ?>

        <div class="security">
            <h3>⚠️ IMPORTANT SECURITY NOTICE</h3>
            <p>This file provides direct database access. <strong>Delete <code>public/run_migration_full.php</code> immediately after use.</strong></p>
        </div>

        <a href="/" class="btn">Back to Site</a>
    </div>
</body>
</html>
