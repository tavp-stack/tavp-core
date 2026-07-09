<?php

declare(strict_types=1);

namespace Tavp\Environment\Adapters;

/**
 * XAMPP environment adapter — generates setup scripts for Windows XAMPP.
 */
class XamppAdapter
{
    public function getName(): string
    {
        return 'xampp';
    }

    public function generate(array $config): array
    {
        $files = [];

        $files['scripts/setup-xampp.bat'] = $this->setupScript($config);

        return $files;
    }

    private function setupScript(array $config): string
    {
        $phpVersion = $config['php_version'] ?? '8.3';

        return <<<BAT
@echo off
echo Setting up TAVP for XAMPP...
echo.

REM Detect XAMPP PHP directory
set PHP_DIR=C:\\xampp\\php
if not exist "%PHP_DIR%" (
    echo XAMPP not found at C:\xampp.
    echo Please install XAMPP from https://www.apachefriends.org
    pause
    exit /b 1
)

REM Download Phalcon DLL
echo Downloading Phalcon extension...
set DLL_URL=https://packages.tavp.dev/php/phalcon-windows.zip
set DLL_DIR=%PHP_DIR%\\ext

REM Configure php.ini
echo Configuring php.ini...
findstr /C:"extension=phalcon" "%PHP_DIR%\\php.ini" >nul 2>&1
if errorlevel 1 (
    echo extension=phalcon >> "%PHP_DIR%\\php.ini"
    echo Added phalcon extension to php.ini
)

echo.
echo Setup complete! Restart Apache via XAMPP Control Panel.
echo Run: tavp serve to start the development server.
pause
BAT;
    }
}
