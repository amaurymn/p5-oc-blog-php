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
        $this->twig = new Twig();
    }

    public function execute()
    {
        $method = 'execute' . ucfirst($this->action);
        $this->$method();
    }

    /**
     * @param $template
     * @param array $array
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render($template, $array = [])
    {
       echo $this->twig->twigRender($template, $array);
    }

    /**
     * @param $submitName
     * @return bool
     */
    protected function isFormSubmit($submitName)
    {
        return (!empty($_POST) && isset($_POST[$submitName]));
    }

    /**
     * @param $url
     */
    public function redirectUrl($url)
    {
        if (!empty($url)) {
            header('Location: ' . $url);
        } else {
            header('Location: /');
        }
        exit();
    }
}
