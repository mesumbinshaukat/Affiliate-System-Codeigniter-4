<?php
/**
 * Server Configuration Test
 * Visit: http://lijst.wmcdev.nl/test_server.php
 * 
 * This will help diagnose 404 errors
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Server Configuration Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
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
        h1 { color: #2563eb; }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 10px 0; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 10px 0; }
        .info { background: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #dee2e6; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Server Configuration Test</h1>
        <p><strong>Test URL:</strong> <?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?></p>
        
        <h2>1. PHP Configuration</h2>
        <?php
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th></tr>';
        echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
        echo '<tr><td>Server Software</td><td>' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '</td></tr>';
        echo '<tr><td>Document Root</td><td>' . $_SERVER['DOCUMENT_ROOT'] . '</td></tr>';
        echo '<tr><td>Script Filename</td><td>' . $_SERVER['SCRIPT_FILENAME'] . '</td></tr>';
        echo '<tr><td>Current Directory</td><td>' . __DIR__ . '</td></tr>';
        echo '</table>';
        ?>
        
        <h2>2. Required PHP Extensions</h2>
        <?php
        $required = ['mysqli', 'intl', 'json', 'mbstring', 'curl'];
        $allLoaded = true;
        
        echo '<table>';
        echo '<tr><th>Extension</th><th>Status</th></tr>';
        foreach ($required as $ext) {
            $loaded = extension_loaded($ext);
            $status = $loaded ? '<span style="color: green;">✓ Loaded</span>' : '<span style="color: red;">✗ Missing</span>';
            echo '<tr><td>' . $ext . '</td><td>' . $status . '</td></tr>';
            if (!$loaded) $allLoaded = false;
        }
        echo '</table>';
        
        if (!$allLoaded) {
            echo '<div class="error">⚠️ Some required PHP extensions are missing!</div>';
        } else {
            echo '<div class="success">✓ All required PHP extensions are loaded</div>';
        }
        ?>
        
        <h2>3. File Structure Check</h2>
        <?php
        $files = [
            'index.php' => 'Main entry point',
            '.htaccess' => 'URL rewrite rules',
            '../app/Config/App.php' => 'App configuration',
            '../app/Config/Database.php' => 'Database configuration',
            '../.env' => 'Environment variables',
            '../vendor/autoload.php' => 'Composer autoloader'
        ];
        
        echo '<table>';
        echo '<tr><th>File</th><th>Description</th><th>Status</th></tr>';
        foreach ($files as $file => $desc) {
            $path = __DIR__ . '/' . $file;
            $exists = file_exists($path);
            $status = $exists ? '<span style="color: green;">✓ Exists</span>' : '<span style="color: red;">✗ Missing</span>';
            echo '<tr><td>' . $file . '</td><td>' . $desc . '</td><td>' . $status . '</td></tr>';
        }
        echo '</table>';
        ?>
        
        <h2>4. .htaccess Test</h2>
        <?php
        $htaccessPath = __DIR__ . '/.htaccess';
        if (file_exists($htaccessPath)) {
            echo '<div class="success">✓ .htaccess file exists</div>';
            
            // Check if mod_rewrite is working
            if (function_exists('apache_get_modules')) {
                $modules = apache_get_modules();
                if (in_array('mod_rewrite', $modules)) {
                    echo '<div class="success">✓ mod_rewrite is enabled</div>';
                } else {
                    echo '<div class="error">✗ mod_rewrite is NOT enabled</div>';
                    echo '<div class="warning">Contact your hosting provider to enable mod_rewrite</div>';
                }
            } else {
                echo '<div class="info">Cannot detect mod_rewrite status (function not available)</div>';
            }
            
            echo '<h3>.htaccess Contents:</h3>';
            echo '<pre>' . htmlspecialchars(file_get_contents($htaccessPath)) . '</pre>';
        } else {
            echo '<div class="error">✗ .htaccess file is missing!</div>';
            echo '<div class="warning">Upload the .htaccess file to the /public directory</div>';
        }
        ?>
        
        <h2>5. Writable Directories</h2>
        <?php
        $writableDirs = [
            '../writable/cache',
            '../writable/logs',
            '../writable/session',
            '../writable/uploads'
        ];
        
        echo '<table>';
        echo '<tr><th>Directory</th><th>Status</th><th>Permissions</th></tr>';
        foreach ($writableDirs as $dir) {
            $path = __DIR__ . '/' . $dir;
            $exists = is_dir($path);
            $writable = is_writable($path);
            $perms = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
            
            if ($exists && $writable) {
                $status = '<span style="color: green;">✓ Writable</span>';
            } elseif ($exists && !$writable) {
                $status = '<span style="color: red;">✗ Not Writable</span>';
            } else {
                $status = '<span style="color: red;">✗ Missing</span>';
            }
            
            echo '<tr><td>' . $dir . '</td><td>' . $status . '</td><td>' . $perms . '</td></tr>';
        }
        echo '</table>';
        ?>
        
        <h2>6. Environment File</h2>
        <?php
        $envPath = __DIR__ . '/../.env';
        if (file_exists($envPath)) {
            echo '<div class="success">✓ .env file exists</div>';
            
            // Parse .env
            $env = file_get_contents($envPath);
            $lines = explode("\n", $env);
            $config = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) continue;
                if (strpos($line, '=') === false) continue;
                
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Hide sensitive values
                if (strpos($key, 'password') !== false || strpos($key, 'secret') !== false || strpos($key, 'key') !== false) {
                    $value = '***HIDDEN***';
                }
                
                $config[$key] = $value;
            }
            
            echo '<table>';
            echo '<tr><th>Setting</th><th>Value</th></tr>';
            foreach ($config as $key => $value) {
                echo '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="error">✗ .env file is missing!</div>';
            echo '<div class="warning">Upload the .env file to the root directory (not /public)</div>';
        }
        ?>
        
        <h2>7. Database Connection Test</h2>
        <?php
        if (file_exists($envPath)) {
            // Try to connect
            $env = parse_ini_file($envPath);
            
            $host = $env['database.default.hostname'] ?? 'localhost';
            $database = $env['database.default.database'] ?? '';
            $username = $env['database.default.username'] ?? '';
            $password = $env['database.default.password'] ?? '';
            
            echo '<p><strong>Database:</strong> ' . htmlspecialchars($database) . '</p>';
            echo '<p><strong>Username:</strong> ' . htmlspecialchars($username) . '</p>';
            
            try {
                $conn = new mysqli($host, $username, $password, $database);
                
                if ($conn->connect_error) {
                    echo '<div class="error">✗ Connection failed: ' . htmlspecialchars($conn->connect_error) . '</div>';
                } else {
                    echo '<div class="success">✓ Database connection successful!</div>';
                    
                    // List tables
                    $result = $conn->query("SHOW TABLES");
                    if ($result) {
                        $tables = [];
                        while ($row = $result->fetch_array()) {
                            $tables[] = $row[0];
                        }
                        
                        if (empty($tables)) {
                            echo '<div class="warning">⚠️ Database is empty. Run migrations!</div>';
                        } else {
                            echo '<div class="info">Found ' . count($tables) . ' table(s): ' . implode(', ', $tables) . '</div>';
                        }
                    }
                    
                    $conn->close();
                }
            } catch (Exception $e) {
                echo '<div class="error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        ?>
        
        <h2>8. Common Issues & Solutions</h2>
        <div class="info">
            <h3>404 Error - Possible Causes:</h3>
            <ol>
                <li><strong>Document root not pointing to /public</strong>
                    <ul>
                        <li>Solution: Configure your domain to point to the /public folder</li>
                        <li>Contact hosting support if needed</li>
                    </ul>
                </li>
                <li><strong>.htaccess not working</strong>
                    <ul>
                        <li>Solution: Ensure mod_rewrite is enabled</li>
                        <li>Check if .htaccess file was uploaded</li>
                    </ul>
                </li>
                <li><strong>File permissions</strong>
                    <ul>
                        <li>Solution: Set writable folders to 777</li>
                        <li>Set other folders to 755</li>
                    </ul>
                </li>
            </ol>
        </div>
        
        <div class="warning">
            <h3>⚠️ SECURITY NOTICE</h3>
            <p><strong>Delete this file after testing!</strong></p>
            <p>File to delete: <code>/public/test_server.php</code></p>
        </div>
        
        <h2>Next Steps</h2>
        <ol>
            <li>Fix any issues shown above</li>
            <li>Run migrations: <a href="run_migrations.php?token=<?= md5('lijst_wmcdev_nl_2024') ?>">run_migrations.php</a></li>
            <li>Visit homepage: <a href="/">Go to Homepage</a></li>
            <li>Delete this test file</li>
        </ol>
    </div>
</body>
</html>
