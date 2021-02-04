<?php

namespace App\Core;

use App\Exception\TwigException;

abstract class Controller
{
    protected $action;
    protected $params;
    protected $twig;

    public function __construct($action, $params)
    {
        $this->action = $action;
        $this->params = $params;
        $this->twig   = new Twig();
    }

    public function execute(): void
    {
        $method = 'execute' . ucfirst($this->action);
        $this->$method();
    }

    /**
     * render twig template
     * @param $template
     * @param array $array
     * @throws TwigException
     */
    public function render($template, $array = []): void
    {
        echo $this->twig->twigRender($template, $array);
    }

    /**
     * check if the form is submitted
     * @param $submitName
     * @return bool
     */
    protected function isFormSubmit($submitName): bool
    {
        return (!empty($_POST) && isset($_POST[$submitName]));
    }

    /**
     * @param string $url
     */
    public function redirectUrl(string $url = '/'): void
    {
        if (!empty($url)) {
            header('Location: ' . $url);
            exit();
        }
    }
}
