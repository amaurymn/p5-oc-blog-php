<?php

use App\Controllers\PublicController\HomeController;
use App\Core\Router;
use App\Exception\NotFoundException;

define('ROOT_DIR', realpath(dirname(__DIR__)));
define('CONF_DIR', realpath(dirname(__DIR__)) . '/config');
define('TEMPLATE_DIR', realpath(dirname(__DIR__)) . '/templates');
define('PUBLIC_DIR', realpath(dirname(__DIR__)) . '/public');

require_once (ROOT_DIR . '/vendor/autoload.php');

try {
    $router = new Router();
    $controller = $router->getRoutes();
    $controller->execute();
} catch (Throwable $e) {

    if ($e instanceof NotFoundException) {
        $action = "showError404";
    } else {
        $action = "showError";
    }

    $msg = iconv(mb_detect_encoding($e->getMessage(), mb_detect_order(), true), "UTF-8", $e->getMessage());

    return (new HomeController($action, ['exceptionMsg' => $msg]))->execute();
}
