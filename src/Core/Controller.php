<?php

namespace App\Core;

abstract class Controller
{
    protected $action;
    protected $params;
    protected $twig;

    public function __construct($action, $params)
    {
        $this->action = $action;
        $this->params = $params;
    }

    public function execute()
    {
        $method = 'execute' . ucfirst($this->action);
        $this->$method();
    }
}
