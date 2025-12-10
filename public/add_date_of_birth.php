<?php

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
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B'\"");
        $env[$key] = $value;
    }
}

// Get database config from .env
$db_host = $env['database.default.hostname'] ?? 'localhost';
$db_name = $env['database.default.database'] ?? 'lijst';
$db_user = $env['database.default.username'] ?? 'wmcdev';
$db_pass = $env['database.default.password'] ?? 'LSTZlUb2bziQXgB';

// Connect to database
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Add date_of_birth Column</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
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
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
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
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Add date_of_birth Column to Users Table</h1>
        
        <?php
        
        // Check if column already exists
        $result = $mysqli->query("SHOW COLUMNS FROM users LIKE 'date_of_birth'");
        
        if ($result && $result->num_rows > 0) {
            echo '<div class="info">';
            echo '<strong>ℹ️ Column Already Exists</strong><br>';
            echo 'The date_of_birth column is already present in the users table.';
            echo '</div>';
        } else {
            // Add the column
            $sql = "ALTER TABLE users ADD COLUMN date_of_birth DATE NULL AFTER last_name";
            
            if ($mysqli->query($sql)) {
                echo '<div class="success">';
                echo '<strong>✅ Success!</strong><br>';
                echo 'The date_of_birth column has been added to the users table.<br>';
                echo '<pre>ALTER TABLE users ADD COLUMN date_of_birth DATE NULL AFTER last_name</pre>';
                echo '</div>';
            } else {
                echo '<div class="error">';
                echo '<strong>❌ Error</strong><br>';
                echo 'Failed to add column: ' . $mysqli->error;
                echo '</div>';
            }
        }
        
        // Show table structure
        echo '<div class="info">';
        echo '<strong>📋 Users Table Structure</strong><br>';
        $result = $mysqli->query("DESCRIBE users");
        echo '<pre>';
        echo "Field\t\t\tType\t\t\tNull\tKey\tDefault\n";
        echo str_repeat("-", 80) . "\n";
        while ($row = $result->fetch_assoc()) {
            printf("%-20s %-25s %-5s %-5s %s\n", 
                $row['Field'], 
                $row['Type'], 
                $row['Null'], 
                $row['Key'] ?? '', 
                $row['Default'] ?? ''
            );
        }
        echo '</pre>';
        echo '</div>';
        
        $mysqli->close();
        
        ?>
    </div>
</body>
</html>
