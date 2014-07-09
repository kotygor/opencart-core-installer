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
        $extra = $this->composer->getPackage()->getExtra();
        if (!isset($extra['opencart-install-path'])) {
            throw new \InvalidArgumentException(
                'Extra section didn\'t provide `opencart-install-path` option'
            );
        }
        return $extra['opencart-install-path'];
    }
    /*public function isInstalled(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {

    }*/
    public function install(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {
        parent::install($repo, $package);
        $path = $this->getPackageBasePath($package);
        $tmpDir = dirname($path).'/opencart-tmp';
        $extra = $package->getExtra();

        // Come on, what the heck is this? Use filesystem ffs. It's not 123 A.D.
        rename($path, $tmpDir);
        rename($tmpDir.'/upload', $path);
        foreach (scandir($tmpDir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            unlink($tmpDir.'/'.$file);
        }
        rmdir($tmpDir);
    }
    /*
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
 