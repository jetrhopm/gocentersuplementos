<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = realpath(__DIR__);
$serverBasePath = dirname(dirname(__DIR__)).'/gocentersuplementos';
$configuredBasePath = getenv('LARAVEL_BASE_PATH') ?: null;

if ($configuredBasePath && file_exists($configuredBasePath.'/vendor/autoload.php')) {
    $basePath = rtrim($configuredBasePath, '/\\');
} elseif (! file_exists($basePath.'/vendor/autoload.php') && file_exists($serverBasePath.'/vendor/autoload.php')) {
    $basePath = $serverBasePath;
}

if (! $basePath || ! file_exists($basePath.'/vendor/autoload.php')) {
    http_response_code(500);
    exit('Laravel base path not found.');
}

if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
