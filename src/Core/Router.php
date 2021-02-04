<?php

namespace App\Core;

use App\Exception\ConfigException;
use App\Exception\NotFoundException;
use Symfony\Component\Yaml\Yaml;

class Router
{
    private $controller;

    /**
     * Router constructor.
     * @throws ConfigException
     * @throws NotFoundException
     */
    public function __construct()
    {
        $this->setRoutes();
    }

    /**
     * get parameters from routes.yml
     * @return mixed
     * @throws ConfigException
     * @throws NotFoundException
     */
    public function setRoutes()
    {
        try {
            $routes = Yaml::parseFile(CONF_DIR . '/routes.yml');
        } catch (\Exception $e) {
            throw new ConfigException($e->getMessage());
        }

        foreach ($routes as $route) {

            if (preg_match('#^' . $route['uri'] . '$#', $_SERVER['REQUEST_URI'], $matches)) {
                $controller = '\\App\\Controllers\\' . $route['area'] . 'Controller\\' . $route['controller'];
                $params     = array_combine($route['parameters'], array_slice($matches, 1));

                return $this->controller = new $controller($route['action'], $params);
            }
        }

        throw new NotFoundException($_SERVER['REQUEST_URI']);
    }

    public function getRoutes()
    {
        return $this->controller;
    }
}
