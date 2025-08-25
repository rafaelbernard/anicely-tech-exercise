<?php

use Symfony\Component\Dotenv\Dotenv;

include_once __DIR__ . '/../../vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/../.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

error_reporting(E_ALL);
ini_set('display_startup_errors', 'On');
ini_set('display_errors', 'On');
