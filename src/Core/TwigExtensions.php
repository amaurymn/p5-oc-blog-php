<?php

namespace App\Core;

use App\Services\Session;
use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TwigExtensions extends AbstractExtension
{
    /** @var mixed */
    private $config;
    /** @var Session */
    private Session $session;

    public function __construct()
    {
        $this->config  = Yaml::parseFile(CONF_DIR . '/config.yml');
        $this->session = new Session();
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
            new TwigFunction('uploadPath', [$this, 'getUploadPath']),
            new TwigFunction('flashBag', [$this, 'getFlashBag']),
            new TwigFunction('isAuth', [$this, 'isUserAuth']),
            new TwigFunction('isAdmin', [$this, 'isUserAdmin']),
            new TwigFunction('getSessionParam', [$this, 'getSessionParam']),
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
     * @param string $asset
     * @return string
     */
    public function getUploadPath(string $asset): string
    {
        return sprintf('/img/' . $this->config['imgUploadPath'] . '/%s', ltrim($asset, '/'));
    }

    /**
     * @return array|mixed
     */
    public function getFlashBag()
    {
        $messages = [];

        if (isset($_SESSION['flash'])) {
            $messages = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        return $messages;
    }

    /**
     * @return bool
     */
    public function isUserAuth(): bool
    {
        return $this->session->isAuth();
    }

    /**
     * @return bool
     */
    public function isUserAdmin(): bool
    {
        return $this->session->isAdmin();
    }

    /**
     * @param string $param
     * @return mixed|null
     */
    public function getSessionParam(string $param)
    {
        return $this->session->get($param);
    }
}
