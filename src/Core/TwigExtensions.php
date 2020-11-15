<?php

namespace App\Core;

use App\Services\FlashBag;
use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TwigExtensions extends AbstractExtension
{
    /** @var mixed */
    private $config;
    private $flash;

    public function __construct()
    {
        $this->config = Yaml::parseFile(CONF_DIR . '/config.yml');
        $this->flash  = new FlashBag();
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
            new TwigFunction('flashBag', [$this, 'getFlashBag'])
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

    /**
     * @param string|null $type
     * @return array|mixed|null
     */
    public function getFlashBag(?string $type = null)
    {
        if ($type !== null) {
            return $this->flash->get($type);
        }

        return $this->flash->getAll();
    }
}
