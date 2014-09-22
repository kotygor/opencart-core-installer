<?php
namespace Etki\Composer\Installers\Opencart;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

/**
 * Just an opencart installer, nothing to see here, move along.
 *
 * @version 0.1.0
 * @since   0.1.0
 * @package Etki\Composer\Installers\Opencart
 * @author  Fike Etki <etki@etki.name>
 */
class Installer extends LibraryInstaller
{
    /**
     * Supported package type.
     *
     * @type string
     * @since 0.1.0
     */
    public $packageType = 'opencart-core';

    /**
     * Default installation directory.
     *
     * @type string
     * @since 0.1.0
     */
    public $defaultInstallDir = 'opencart';

    /**
     * Tells composer if this installer supports provided package type.
     *
     * @param string $packageType Package type name.
     *
     * @return bool
     * @since 0.1.0
     */
    public function supports($packageType)
    {
        return $packageType === $this->packageType;
    }

    /**
     * Provides installation path for package. Kudos go to
     * https://github.com/johnpbloch/wordpress-core-installer
     *
     * @param PackageInterface $package Installed package.
     *
     * @return string
     * @since 0.1.0
     */
    public function getInstallPath(PackageInterface $package)
    {
        $installDir = null;
        if ($this->composer->getPackage()) {
            $rootExtra = $this->composer->getPackage()->getExtra();
            if (!empty($rootExtra['opencart-install-dir'])) {
                $installDir = $rootExtra['opencart-install-dir'];
            }
        }
        if (!$installDir) {
            $extra = $package->getExtra();
            if (!empty($extra['opencart-install-dir'])) {
                $installDir = $extra['opencart-install-dir'];
            }
        }
        if (is_array($installDir)) {
            $prettyName = $package->getPrettyName();
            if (isset($installDir[$prettyName])) {
                return $installDir[$prettyName];
            }
            return $this->defaultInstallDir;
        }
        return $installDir ? $installDir : $this->defaultInstallDir;
    }
}
