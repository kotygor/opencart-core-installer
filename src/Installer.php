<?php
namespace Etki\Composer\Installers\Opencart;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

/**
 * 
 *
 * @version 0.1.0
 * @since   0.1.0
 * @package Etki\Composer\Installers\Opencart
 * @author  Fike Etki <etki@etki.name>
 */
class Installer extends LibraryInstaller
{
    public $packageType = 'opencart-base';

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
    public function getPackageBasePath(PackageInterface $package)
    {
        var_dump($package->getExtra());
        die;
    }
    /*public function isInstalled(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {

    }
    public function install(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {

    }
    public function update(
        InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target
    ) {

    }
    public function uninstall(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {

    }
    public function getInstallPath(PackageInterface $package)
    {

    }*/
}
 