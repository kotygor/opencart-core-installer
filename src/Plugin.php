<?php
namespace Etki\Composer\Installers\Opencart;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Installer\InstallationManager;

/**
 * Plugin that hooks up installer.
 *
 * @version 0.1.0
 * @since   0.1.0
 * @package Etki\Composer\Installers\Opencart
 * @author  Fike Etki <etki@etki.name>
 */
class Plugin implements PluginInterface
{
    /**
     * Activates plugin and registers installer.
     *
     * @param Composer    $composer Composer instance.
     * @param IOInterface $ioc      I/O controller
     *
     * @return void
     * @since 0.1.0
     */
    public function activate(Composer $composer, IOInterface $ioc)
    {
        $installer = new Installer($ioc, $composer);
        /** @type InstallationManager $manager */
        $manager = $composer->getInstallationManager();
        $manager->addInstaller($installer);
    }
}
