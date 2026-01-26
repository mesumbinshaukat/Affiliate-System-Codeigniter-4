<?php

/**
 * Migration Runner - Drawing Invite Tokens
 * Visit: http://localhost:8080/run_migration_drawing_invite_tokens.php
 *
 * Adds the invite_token column to drawings and backfills secure tokens
 *
 * SECURITY: Delete this file after running!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$envFile = __DIR__ . '/../.env';
$env = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim($value, " \t\n\r\0\x0B'\"");
    }
}

$dbHost = $env['database.default.hostname'] ?? 'localhost';
$dbName = $env['database.default.database'] ?? 'lijstje_db';
$dbUser = $env['database.default.username'] ?? 'root';
$dbPass = $env['database.default.password'] ?? '';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Migration Runner - Drawing Invite Tokens</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 960px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
        .container { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 12px 25px rgba(0,0,0,0.08); }
        h1 { color: #E31E24; border-bottom: 3px solid #E31E24; padding-bottom: 12px; }
        .step { margin: 24px 0; padding: 20px; border-left: 5px solid #E31E24; background: #fafafa; border-radius: 8px; }
        .success { background: #d1f2d7; border: 1px solid #9dd7aa; color: #1d5a2b; padding: 14px; border-radius: 6px; margin-top: 12px; }
        .error { background: #ffe0e0; border: 1px solid #ffb3b3; color: #7c1111; padding: 14px; border-radius: 6px; margin-top: 12px; }
        .warning { background: #fff5d9; border: 1px solid #ffe199; color: #664b00; padding: 14px; border-radius: 6px; margin-top: 12px; }
        .info { background: #e4f0ff; border: 1px solid #c3ddff; color: #0b4c97; padding: 14px; border-radius: 6px; margin-top: 12px; }
        pre { background: #1e1e1e; color: #f5f5f5; padding: 14px; border-radius: 6px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 10px 12px; text-align: left; }
        th { background: #f2f2f2; }
        .btn { display: inline-block; margin: 16px 12px 0 0; padding: 12px 24px; border-radius: 6px; text-decoration: none; color: #fff; background: #E31E24; font-weight: 600; }
        .btn-secondary { background: #555; }
        code { background: #f0f0f0; padding: 2px 4px; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔐 Migration Runner - Drawing Invite Tokens</h1>
    <p><strong>Mode:</strong> Standalone (no CodeIgniter bootstrap required)</p>

    <?php
    $mysqli = null;

    echo '<div class="step">';
    echo '<h3>Step 1: Testing Database Connection</h3>';
    try {
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        if ($mysqli->connect_error) {
            throw new Exception('Connection failed: ' . $mysqli->connect_error);
        }
        $mysqli->query('SELECT 1');

        echo '<div class="success">✓ Connected to database successfully.</div>';
        echo '<pre>';
        echo "Host: {$dbHost}\n";
        echo "Database: {$dbName}\n";
        echo "User: {$dbUser}\n";
        echo '</pre>';
    } catch (Throwable $e) {
        echo '<div class="error">✗ Unable to connect to database.</div>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<div class="warning">Check the credentials in <code>.env</code> and rerun.</div>';
        echo '</div></div></body></html>';
        exit;
    }
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>Step 2: Ensuring <code>invite_token</code> column exists</h3>';
    try {
        $columnResult = $mysqli->query("SHOW COLUMNS FROM drawings LIKE 'invite_token'");
        if ($columnResult && $columnResult->num_rows > 0) {
            echo '<div class="info">ℹ️ Column already present. Skipping ALTER TABLE.</div>';
        } else {
            $alterSql = "ALTER TABLE drawings ADD COLUMN invite_token VARCHAR(64) NULL AFTER status";
            if (!$mysqli->query($alterSql)) {
                throw new Exception($mysqli->error);
            }
            echo '<div class="success">✓ invite_token column added to drawings table.</div>';
        }
    } catch (Throwable $e) {
        echo '<div class="error">✗ Failed to add invite_token column.</div>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>Step 3: Backfilling existing drawings with secure tokens</h3>';
    try {
        $result = $mysqli->query("SELECT id FROM drawings WHERE invite_token IS NULL OR invite_token = ''");
        $updated = 0;
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $token = bin2hex(random_bytes(16));
                $stmt = $mysqli->prepare('UPDATE drawings SET invite_token = ? WHERE id = ?');
                $stmt->bind_param('si', $token, $row['id']);
                if ($stmt->execute()) {
                    $updated++;
                }
                $stmt->close();
            }
        }
        echo '<div class="success">✓ Backfill complete.</div>';
        echo '<div class="info">' . $updated . ' drawing(s) received new invite tokens.</div>';
    } catch (Throwable $e) {
        echo '<div class="error">✗ Failed to backfill invite tokens.</div>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }
    echo '</div>';

    echo '<div class="step">';
    echo '<h3>Step 4: Verification</h3>';
    try {
        $columns = $mysqli->query("SHOW COLUMNS FROM drawings");
        echo '<table><tr><th>Column</th><th>Type</th><th>Status</th></tr>';
        while ($col = $columns->fetch_assoc()) {
            $status = $col['Field'] === 'invite_token' ? '✓ includes invite_token' : '';
            echo '<tr><td>' . htmlspecialchars($col['Field']) . '</td><td>' . htmlspecialchars($col['Type']) . '</td><td>' . $status . '</td></tr>';
        }
        echo '</table>';
    } catch (Throwable $e) {
        echo '<div class="error">✗ Unable to list drawings columns.</div>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }
    echo '</div>';

    echo '<div class="warning">';
    echo '<strong>SECURITY REMINDER:</strong> Delete <code>/public/run_migration_drawing_invite_tokens.php</code> after running this script to avoid exposing migration utilities publicly.';
    echo '</div>';

    echo '<div class="info">';
    echo '<h3>Next Steps</h3>';
    echo '<ol>';
    echo '<li>Remove this file from the server once the migration succeeded.</li>';
    echo '<li>Test the updated invitation links (they now use secure tokens).</li>';
    echo '<li>Share only the /drawings/invite/{token} URL with users.</li>';
    echo '</ol>';
    echo '</div>';

    echo '<a href="/" class="btn">Go to Homepage</a>';
    echo '<a href="/drawings" class="btn btn-secondary">Open Drawings Dashboard</a>';

    if ($mysqli) {
        $mysqli->close();
    }
    ?>
</div>
</body>
</html>
