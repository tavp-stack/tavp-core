<?php

declare(strict_types=1);

namespace Tavp\Core\Environment\Adapters;

/**
 * Laragon environment adapter — generates setup scripts for Windows Laragon.
 */
class LaragonAdapter
{
    public function getName(): string
    {
        return 'laragon';
    }

    public function generate(array $config): array
    {
        $files = [];

        $files['scripts/setup-laragon.bat'] = $this->setupScript($config);

        return $files;
    }

    private function setupScript(array $config): string
    {
        $phpVersion = $config['php_version'] ?? '8.3';

        return <<<BAT
@echo off
echo Setting up TAVP for Laragon...
echo.

REM Detect PHP version
set PHP_DIR=C:\\laragon\\bin\\php\\php-{$phpVersion}
if not exist "%PHP_DIR%" (
    echo PHP {$phpVersion} not found in Laragon.
    echo Please install PHP {$phpVersion} via Laragon > Menu > PHP > Version.
    pause
    exit /b 1
)

REM Download Phalcon DLL
echo Downloading Phalcon extension...
set DLL_URL=https://packages.tavp.dev/php/phalcon-{$phpVersion}-windows.zip
set DLL_DIR=%PHP_DIR%\\ext

REM Copy php.ini
echo Configuring php.ini...
if not exist "%PHP_DIR%\\php.ini" (
    copy "%PHP_DIR%\\php.ini-development" "%PHP_DIR%\\php.ini"
)

REM Add extension to php.ini
findstr /C:"extension=phalcon" "%PHP_DIR%\\php.ini" >nul 2>&1
if errorlevel 1 (
    echo extension=phalcon >> "%PHP_DIR%\\php.ini"
    echo Added phalcon extension to php.ini
)

REM Add TAVP to PATH
setx PATH "%PATH%;%PHP_DIR%"

echo.
echo Setup complete! Restart Laragon to apply changes.
echo Run: tavp serve to start the development server.
pause
BAT;
    }
}
