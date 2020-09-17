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
            new TwigFunction('asset', [$this, 'getAssetPath']),
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

    /**
     * @param string $asset
     * @return string
     */
    public function getAssetPath(string $asset): string
    {
        return sprintf('/%s', ltrim($asset, '/'));
    }
}