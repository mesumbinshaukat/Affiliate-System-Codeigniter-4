@echo off
echo ========================================
echo Running Authentication Tests
echo ========================================
echo.

echo [1/2] Running PHPUnit Tests...
echo.
vendor\bin\phpunit --testdox
echo.

echo [2/2] Running Database Verification...
echo.
php tests/db_check.php
echo.

echo ========================================
echo All Tests Complete!
echo ========================================
pause
