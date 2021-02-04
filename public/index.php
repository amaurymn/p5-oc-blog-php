<?php

use App\Controllers\PublicController\AccountController;
use App\Controllers\PublicController\HomeController;
use App\Core\Router;
use App\Exception\ConfigException;
use App\Exception\NotFoundException;
use App\Services\InstallState;
use App\Services\UserAuth;
use Symfony\Component\Yaml\Yaml;

define('ROOT_DIR', realpath(dirname(__DIR__)));
define('CONF_DIR', realpath(dirname(__DIR__)) . '/config');
define('TEMPLATE_DIR', realpath(dirname(__DIR__)) . '/templates');
define('PUBLIC_DIR', realpath(dirname(__DIR__)) . '/public');

require_once(ROOT_DIR . '/vendor/autoload.php');

/*
 * load the main config file
 */
try {
    $config = Yaml::parseFile(CONF_DIR . '/config.yml');
} catch (\Exception $e) {
    throw new ConfigException("Le fichier de configuration du site est manquant.");
}

/**
 * check if the admin exist otherwise redirect to register form page
 * if the admin exist, set install status to true in the config file
 */
$adminAlreadyExist = (new UserAuth())->isAdminAlreadyExist();
if (isset($config['install_state']) && $config['install_state'] === false && $adminAlreadyExist) {
    (new InstallState())->writeInstallStatus(true);
}

if (!isset($config['install_state']) || $config['install_state'] !== true) {
    return (new AccountController('showRegister', []))->execute();
}

/**
 * initialise router and execute controller class
 */
try {
    $router     = new Router();
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
