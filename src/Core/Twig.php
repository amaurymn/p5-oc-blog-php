<?php

namespace App\Core;

use App\Exception\ConfigException;
use App\Exception\TwigException;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Error\LoaderError;
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
     * @throws ConfigException
     * @throws LoaderError
     */
    public function __construct()
    {
        try {
            $this->config = Yaml::parseFile(CONF_DIR . '/config.yml');
        } catch (\Exception $e) {
            throw new ConfigException($e->getMessage());
        }

        $loader = new FilesystemLoader(TEMPLATE_DIR);

        $loader->addPath(TEMPLATE_DIR . '/public', 'public');
        $loader->addPath(TEMPLATE_DIR . '/admin', 'admin');

        $twig = new Environment($loader, [
            'debug' => $this->config['env'] === 'dev',
            'cache' => $this->config['env'] === 'dev' ? false : ROOT_DIR . '/var/cache'
        ]);

        $twig->addExtension(new TwigExtensions());
        $twig->addExtension(new DebugExtension());

        $this->twig = $twig;
    }

    /**
     * @param $template
     * @param $array
     * @return string
     * @throws TwigException
     */
    public function twigRender($template, $array): ?string
    {
        try {
            return $this->twig->render($template, $array);
        } catch (\Exception $e) {
            throw new TwigException($e->getMessage());
        }
    }
}
