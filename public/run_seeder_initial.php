<?php
/**
 * Initial Seeder Runner
 * Visit: https://your-domain/run_seeder_initial.php
 *
 * SECURITY: Remove this file immediately after seeding!
 */

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
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B'\"");
        $env[$key] = $value;
        putenv($key . '=' . $value);
        $_ENV[$key]    = $value;
        $_SERVER[$key] = $value;
    }
}

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

require_once SYSTEMPATH . 'Common.php';

$steps = [];
$errors = [];

function log_step(string $title, string $body, string $type = 'info'): void
{
    global $steps;
    $steps[] = compact('title', 'body', 'type');
}

try {
    require_once $rootPath . '/vendor/autoload.php';
    if (file_exists(APPPATH . 'Config/Constants.php')) {
        require_once APPPATH . 'Config/Constants.php';
    }

    $autoloader = Config\Services::autoloader();
    $autoloader->initialize(new Config\Autoload(), new Config\Modules());

    $db = Config\Database::connect();
    $db->query('SELECT 1');
    log_step('Database Connection', 'Database connection successful.', 'success');

    $seeder = Config\Database::seeder();
    $seeder->call('InitialSeeder');
    log_step('Seeding', 'InitialSeeder executed successfully.', 'success');
} catch (Throwable $e) {
    $errors[] = $e->getMessage();
    log_step('Error', '<pre>' . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . '</pre>', 'error');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Initial Seeder Runner</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; max-width: 900px; margin: 40px auto; padding: 24px; background: #f6f8fc; }
        .panel { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 20px 60px rgba(15,23,42,0.12); }
        h1 { margin-top: 0; }
        .step { border-left: 5px solid #2563eb; padding: 16px; margin-bottom: 16px; background: #f8fafc; border-radius: 0 12px 12px 0; }
        .success { border-left-color: #16a34a; }
        .error { border-left-color: #dc2626; }
        pre { background: #0f172a; color: #fff; padding: 12px; border-radius: 8px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        .security { background: #fff7ed; border: 1px solid #fdba74; padding: 16px; border-radius: 10px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="panel">
        <h1>🌱 Initial Seeder Runner</h1>
        <p><strong>Environment:</strong> <?= htmlspecialchars(ENVIRONMENT ?? 'production'); ?></p>
        <p><strong>Database:</strong> <?= htmlspecialchars($env['database.default.database'] ?? ''); ?></p>

        <?php foreach ($steps as $step): ?>
            <div class="step <?= htmlspecialchars($step['type']); ?>">
                <h3><?= htmlspecialchars($step['title']); ?></h3>
                <?= $step['body']; ?>
            </div>
        <?php endforeach; ?>

        <div class="security">
            <h3>⚠️ Security Reminder</h3>
            <p>Delete <code>public/run_seeder_initial.php</code> immediately after running to prevent unauthorized access.</p>
        </div>
    </div>
</body>
</html>
