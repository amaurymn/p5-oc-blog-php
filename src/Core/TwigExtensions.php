<?php

namespace App\Core;

use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TwigExtensions extends AbstractExtension
{
    /** @var mixed */
    private $config;

    public function __construct()
    {
        $this->config = Yaml::parseFile(CONF_DIR . '/config.yml');
    }

    /**
     * @return array|TwigFilter[]
     */
    public function getFilters() {
        return [];
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('configParam', [$this, 'getConfigParameter']),
        ];
    }

    /**
     * Custom twig functions
     * @param string $setting
     * @return string
     */
    public function getConfigParameter(string $setting): string
    {
        return $this->config[$setting];
    }
}