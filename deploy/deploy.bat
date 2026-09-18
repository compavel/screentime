@echo off
echo ========================================
echo   ScreenTime Deployment Script
echo ========================================
echo.

echo [1/4] Dumping database schema...
php artisan schema:dump --no-interaction
if %errorlevel% neq 0 (
    echo ERROR: Failed to dump schema
    pause
    exit /b 1
)

echo.
echo [2/4] Generating deployment package...
echo - Pastikan vendor/ sudah lengkap
echo - Pastikan build/ sudah ada (npm run build)
echo.

echo [3/4] File yang perlu di-upload:
echo   - SELURUH folder project ke htdocs/
echo   - KECUALI: node_modules/, .git/
echo   - PASTIKAN: vendor/ ter-upload lengkap!
echo.

echo [4/4] Setelah upload:
echo   1. Buat .htaccess di htdocs/ (lihat deploy/.htaccess.root)
echo   2. Edit .env (lihat deploy/.env.infinityfree)
echo   3. Import database via phpMyAdmin
echo   4. Set permissions storage/ ke 775
echo   5. Buka website!
echo.
echo ========================================
echo   Deploy instructions: DEPLOY_INFINITYFREE.md
echo ========================================
pause
