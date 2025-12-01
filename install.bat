@echo off
echo ========================================
echo Lijstje.nl Installation Script
echo ========================================
echo.

echo Step 1: Installing Composer Dependencies...
call composer install
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)
echo Done!
echo.

echo Step 2: Running Database Migrations...
php spark migrate
if %errorlevel% neq 0 (
    echo ERROR: Migration failed! Make sure database is created and credentials are correct in .env
    pause
    exit /b 1
)
echo Done!
echo.

echo Step 3: Seeding Initial Data...
php spark db:seed InitialSeeder
if %errorlevel% neq 0 (
    echo ERROR: Seeding failed!
    pause
    exit /b 1
)
echo Done!
echo.

echo Step 4: Generating Encryption Key...
php spark key:generate
echo Done!
echo.

echo ========================================
echo Installation Complete!
echo ========================================
echo.
echo Default Admin Credentials:
echo Email: admin@lijstje.nl
echo Password: Admin@123
echo.
echo IMPORTANT: Change these credentials after first login!
echo.
echo To start the development server, run:
echo php spark serve
echo.
echo Then visit: http://localhost:8080
echo.
pause
