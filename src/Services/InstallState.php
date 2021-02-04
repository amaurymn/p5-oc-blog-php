<?php

namespace App\Services;

use App\Exception\ConfigException;
use Symfony\Component\Yaml\Yaml;

class InstallState
{
    /**
     * Write the config file when the first admin is created
     * @param bool $state
     * @throws ConfigException
     */
    public function writeInstallStatus(bool $state): void
    {
        try {
            $configFile              = CONF_DIR . '/config.yml';
            $config                  = Yaml::parse(file_get_contents($configFile));
            $config['install_state'] = $state;
            $saveConfig              = Yaml::dump($config, 2, 2);
            file_put_contents($configFile, $saveConfig);
        } catch (\Exception $e) {
            throw new ConfigException("Le fichier de configuration du site est manquant.");
        }
    }
}
