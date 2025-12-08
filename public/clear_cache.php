<?php
/**
 * Clear CodeIgniter Cache
 * Visit: https://lijst.wmcdev.nl/clear_cache.php
 * DELETE AFTER USE!
 */

echo "<h1>Clearing Cache...</h1>";

$cacheDir = __DIR__ . '/../writable/cache';

function deleteFiles($dir) {
    $count = 0;
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            } elseif (is_dir($file)) {
                $count += deleteFiles($file);
            }
        }
    }
    return $count;
}

$deleted = deleteFiles($cacheDir);

echo "<p style='color: green;'>✓ Deleted $deleted cache files</p>";
echo "<p><strong>Cache cleared!</strong></p>";
echo "<p>Now visit: <a href='index.php'>https://lijst.wmcdev.nl/index.php</a></p>";
echo "<p style='color: red;'><strong>DELETE THIS FILE NOW!</strong></p>";
