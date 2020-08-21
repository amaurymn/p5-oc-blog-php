<?php

namespace App\Core;

use App\Controllers\HomeController;
use Symfony\Component\Yaml\Yaml;

class Router
{
    private $controller;

    public function __construct(){
        $this->setRoutes();
    }

    public function setRoutes()
    {
        $routes = Yaml::parseFile(CONF_DIR . '/routes.yml');

        foreach ($routes as $route) {
            if (preg_match('#^'. $route['uri'] .'$#', $_SERVER['REQUEST_URI'], $matches)) {
                $controller = '\\App\\Controllers\\' . $route['controller'];
                $params = array_combine($route['parameters'], array_slice($matches,1));

                return $this->controller = new $controller($route['action'], $params);
            }
        }

        return $this->controller = new HomeController('error404', []);
    }

    public function getRoutes()
    {
        return $this->controller;
    }
}