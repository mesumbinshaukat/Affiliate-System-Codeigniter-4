<?php
/**
 * Migration Runner - List Protection (Password & Security Question)
 * Visit: http://localhost:8080/run_migration_list_protection.php
 *
 * Adds protection_type, protection_password, protection_question and protection_answer columns to the lists table
 *
 * SECURITY: Delete this file after running!
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
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
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B'\"");
        $env[$key] = $value;
    }
}

// Database config
$db_host = $env['database.default.hostname'] ?? 'localhost';
$db_name = $env['database.default.database'] ?? 'lijstje_db';
$db_user = $env['database.default.username'] ?? 'root';
$db_pass = $env['database.default.password'] ?? '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Migration Runner - List Protection</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 980px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #E31E24;
            border-bottom: 3px solid #E31E24;
            padding-bottom: 10px;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            border-left: 4px solid #E31E24;
            background: #f8f9fa;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Migration Runner - List Protection Fields</h1>
        <p><strong>Mode:</strong> Standalone (No CodeIgniter required)</p>
        <?php
        echo '<div class="step">';
        echo '<h3>Step 1: Testing Database Connection</h3>';

        try {
            $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
            if ($mysqli->connect_error) {
                throw new Exception('Connection failed: ' . $mysqli->connect_error);
            }
            $mysqli->query('SELECT 1');
            echo '<div class="success">✓ Database connection successful!</div>';
            echo '<pre>';
            echo 'Host: ' . $db_host . "\n";
            echo 'Database: ' . $db_name . "\n";
            echo 'Username: ' . $db_user . "\n";
            echo '</pre>';
        } catch (Exception $e) {
            echo '<div class="error">✗ Database connection failed!</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<div class="warning">Check your .env file for correct database credentials.</div>';
            die('</div></div></body></html>');
        }

        echo '</div>';

        $columns = [
            'protection_type' => "ALTER TABLE lists ADD COLUMN protection_type ENUM('none','password','question') NOT NULL DEFAULT 'none' COMMENT 'Type of protection required for list sharing' AFTER is_crossable",
            'protection_password' => "ALTER TABLE lists ADD COLUMN protection_password TEXT NULL COMMENT 'Encrypted password for protected lists' AFTER protection_type",
            'protection_question' => "ALTER TABLE lists ADD COLUMN protection_question VARCHAR(255) NULL COMMENT 'Security question for guests' AFTER protection_password",
            'protection_answer' => "ALTER TABLE lists ADD COLUMN protection_answer TEXT NULL COMMENT 'Encrypted answer for question-protected lists' AFTER protection_question",
        ];

        foreach ($columns as $column => $sql) {
            echo '<div class="step">';
            echo '<h3>Adding column: ' . $column . '</h3>';
            try {
                $result = $mysqli->query("SHOW COLUMNS FROM lists LIKE '" . $mysqli->real_escape_string($column) . "'");
                if ($result && $result->num_rows > 0) {
                    echo '<div class="info">ℹ️ Column ' . $column . ' already exists.</div>';
                } else {
                    if ($mysqli->query($sql)) {
                        echo '<div class="success">✓ Column ' . $column . ' added successfully!</div>';
                    } else {
                        throw new Exception($mysqli->error);
                    }
                }
            } catch (Exception $e) {
                echo '<div class="error">✗ Error adding column ' . $column . '</div>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            }
            echo '</div>';
        }

        echo '<div class="step">';
        echo '<h3>Verifying lists Table Structure</h3>';
        try {
            $result = $mysqli->query('SHOW COLUMNS FROM lists');
            $existing = [];
            while ($row = $result->fetch_assoc()) {
                $existing[$row['Field']] = $row['Type'];
            }

            $expected = [
                'protection_type' => "enum('none','password','question')",
                'protection_password' => 'text',
                'protection_question' => 'varchar(255)',
                'protection_answer' => 'text',
            ];

            echo '<table>';
            echo '<tr><th>Column</th><th>Type</th><th>Status</th></tr>';
            foreach ($expected as $field => $type) {
                $status = isset($existing[$field]) ? '✓ Present' : '✗ Missing';
                $actualType = $existing[$field] ?? 'N/A';
                echo '<tr><td><strong>' . $field . '</strong></td><td>' . $actualType . '</td><td>' . $status . '</td></tr>';
            }
            echo '</table>';

            if (count(array_intersect_key($expected, $existing)) === count($expected)) {
                echo '<div class="success">✓ All list protection columns are configured!</div>';
            } else {
                echo '<div class="warning">⚠️ Some columns are missing. Review the errors above and rerun this script.</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">Error verifying lists table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        echo '</div>';

        $mysqli->close();
        ?>

        <div class="info">
            <h3>✅ Migration Complete! Next Steps:</h3>
            <ol>
                <li>Delete this file: <code>public/run_migration_list_protection.php</code></li>
                <li>Clear any application caches if you use them</li>
                <li>Test by creating a list with a password or security question</li>
                <li>Share the list link and confirm the protection gate works</li>
            </ol>
        </div>

        <div class="warning">
            <h3>⚠️ Security Reminder</h3>
            <p>This file exposes a direct database migration interface. <strong>Remove it immediately after running</strong> to avoid abuse.</p>
        </div>

        <a href="/" class="info" style="display:inline-block;padding:10px 18px;border-radius:6px;text-decoration:none;color:#0c5460;border:1px solid #bee5eb;background:#d1ecf1;">Ga naar home</a>
        <a href="/dashboard" class="success" style="display:inline-block;padding:10px 18px;border-radius:6px;text-decoration:none;color:#155724;border:1px solid #c3e6cb;background:#d4edda;margin-left:10px;">Ga naar dashboard</a>
    </div>
</body>
</html>
