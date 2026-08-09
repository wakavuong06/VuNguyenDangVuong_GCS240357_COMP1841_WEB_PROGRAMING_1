<?php

session_start();

/* Absolute path to the project root - used everywhere for file access. */
define('ROOT_PATH', dirname(__DIR__));

/* While developing / marking it is useful to see every PHP problem.   */
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/app/helpers.php';
require_once ROOT_PATH . '/vendor/autoload.php';

/* Route the request: reads $_GET['url'] and runs controller@method.   */
new App\Core\App();