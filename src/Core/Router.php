<?php

namespace App\Core;

use Symfony\Component\Yaml\Yaml;
use function Siler\Route\route;

class Router
{
    private $controller;


    public function setRoutes()
    {
        $routes = Yaml::parseFile(CONF_DIR . '/routes.yml');

        foreach ($routes as $route) {
            if (preg_match('#^'. $route['uri'] .'$#', $_SERVER['REQUEST_URI'], $matches)) {
                $controller = '\\App\\Controllers\\' . $route['controller'];
                $params = array_combine($route['parameters'], array_slice($matches,1));

                dump('route trouvée');
                dump($controller);
                dump($params);
                dump($matches);
                dump($route);
                dump(array_slice($matches,1));
                return $this->controller = new Controller($route['action'], $params);
            }
        }

        return print('Error 404');
    }


    public function getRoutes()
    {
        return $this->controller;
    }
}