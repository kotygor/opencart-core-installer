<?php
namespace Etki\Composer\Installers\Opencart;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;

/**
 * Just an opencart installer, nothing to see here, move along.
 *
 * @todo implement caching for calculated install dirs
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
        $prettyName = $package->getPrettyName();
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
            if (isset($installDir[$prettyName])) {
                return $installDir[$prettyName];
            }
            return $this->defaultInstallDir;
        }
        return $installDir ? $installDir : $this->defaultInstallDir;
    }

    /**
     * Moves opencart from it's basic `upload` dir one level higher.
     *
     * @param string $installPath Path to files.
     *
     * @return void
     * @since 0.1.0
     */
    protected function rotateFiles($installPath)
    {
        $parentDir = dirname($installPath);
        $tempDir = $parentDir . DIRECTORY_SEPARATOR . 'tmp-oi-' . md5(time());
        $uploadDir = $tempDir . DIRECTORY_SEPARATOR . 'upload';
        $this->filesystem->rename($installPath, $tempDir);
        $this->filesystem->rename($uploadDir, $installPath);
        $this->filesystem->remove($tempDir);
    }

    /**
     * {@inheritdoc}
     *
     * @param InstalledRepositoryInterface $repo    Repository,
     * @param PackageInterface             $package Package.
     *
     * @return void
     * @since 0.1.0
     */
    public function install(
        InstalledRepositoryInterface $repo,
        PackageInterface $package
    ) {
        parent::install($repo, $package);
        $this->rotateFiles($this->getInstallPath($package));
    }

    /**
     * {@inheritdoc}
     *
     * @param InstalledRepositoryInterface $repo    Repository,
     * @param PackageInterface             $initial Current package.
     * @param PackageInterface             $target  Newly installed package.
     *
     * @return void
     * @since 0.1.0
     */
    public function update(
        InstalledRepositoryInterface $repo,
        PackageInterface $initial,
        PackageInterface $target
    ) {
        parent::update($repo, $initial, $target);
        $this->rotateFiles($this->getInstallPath($target));
    }
}
