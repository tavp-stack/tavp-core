<?php

declare(strict_types=1);

namespace Tavp\Core\Environment\Adapters;

/**
 * WAMP environment adapter — generates setup scripts for Windows WAMP.
 */
class WampAdapter
{
    public function getName(): string
    {
        return 'wamp';
    }

    public function generate(array $config): array
    {
        $files = [];

        $files['scripts/setup-wamp.bat'] = $this->setupScript($config);

        return $files;
    }

    private function setupScript(array $config): string
    {
        $phpVersion = $config['php_version'] ?? '8.3';

        return <<<BAT
@echo off
echo Setting up TAVP for WAMP...
echo.

REM Detect WAMP PHP directory
set PHP_DIR=C:\\wamp64\\bin\\php\\php{$phpVersion}
if not exist "%PHP_DIR%" (
    echo WAMP not found at C:\wamp64.
    echo Please install WAMP from https://www.wampserver.com
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
echo Setup complete! Restart all WAMP services.
echo Run: tavp serve to start the development server.
pause
BAT;
    }
}
