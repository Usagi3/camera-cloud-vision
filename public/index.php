<?php
ini_set("display_errors", 1);
ini_set("error_reporting",E_ALL);
ini_set("error_log","../logs/error.log");

require_once __DIR__."/../vendor/autoload.php";

define('BASE_PATH', __DIR__.'/../');
define('APP_PATH', BASE_PATH.'App/');
(new App\DotEnv(BASE_PATH))->load();

use App\Service;
use App\Input;

$requestMethod = Input::requestMethod();

$controller = new Service();
$controller->{$requestMethod}();
