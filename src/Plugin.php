<?php
namespace Etki\Composer\Installers\Opencart;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * 
 *
 * @version 0.1.0
 * @since   0.1.0
 * @package Etki\Composer\Installers\Opencart
 * @author  Fike Etki <etki@etki.name>
 */
class Plugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new Installer($composer, $io);
        $composer->getInstallationManager()->addInstaller($installer);
    }
}
 