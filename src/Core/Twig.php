<?php

namespace App\Core;

use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class Twig
{
    /** @var Environment */
    private $twig;
    /** @var mixed */
    private $config;

    /**
     * Twig constructor.
     * @throws \Twig\Error\LoaderError
     */
    public function __construct()
    {
        $this->config = Yaml::parseFile(CONF_DIR . '/config.yml');

        $loader = new FilesystemLoader(TEMPLATE_DIR);

        $loader->addPath(TEMPLATE_DIR . '/public', 'public');
        $loader->addPath(TEMPLATE_DIR . '/admin', 'admin');

        $twig = new Environment($loader, [
            'debug' => $this->config['envProd'] ? false : true,
            'cache' => $this->config['envProd'] ? ROOT_DIR . '/var/cache' : false
        ]);

        $twig->addExtension(new TwigExtensions());
        $twig->addExtension(new DebugExtension());

        $this->twig = $twig;
    }

    /**
     * @param $template
     * @param $array
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function twigRender($template, $array)
    {
        return $this->twig->render($template, $array);
    }
}