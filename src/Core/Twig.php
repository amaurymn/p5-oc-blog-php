<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Twig
{
    /** @var Environment */
    private $twig;

    /**
     * Twig constructor.
     * @throws \Twig\Error\LoaderError
     */
    public function __construct()
    {
        $loader = new FilesystemLoader(TEMPLATE_DIR);

        $loader->addPath(TEMPLATE_DIR . '/public', 'public');
        $loader->addPath(TEMPLATE_DIR . '/admin', 'admin');

        $twig = new Environment($loader, [
            'debug' => true,
            'cache' => false
        ]);

        $this->twig = $twig;
    }

    /**
     * @param $template
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function twigRender($template)
    {
        return $this->twig->render($template);
    }
}