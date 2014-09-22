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
        $this->writeDebugMessage(
            'Checking support for package type `%s` (%s)',
            array($packageType, $packageType === $this->packageType ? 'y' : 'n')
        );
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
        $this->writeDebugMessage(
            'Getting install path for `%s` package',
            array($prettyName,)
        );
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
        $installDir = $installDir ? $installDir : $this->defaultInstallDir;
        $this->writeDebugMessage(
            'Computed install dir for package `%s`: `%s`',
            array($prettyName, $installDir,)
        );
        return $installDir;
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
        $this->writeDebugMessage('Rotating files in `%s`', array($parentDir));
        $tempDir = $parentDir . DIRECTORY_SEPARATOR . 'tmp-oi-' . md5(time());
        $uploadDir = $tempDir . DIRECTORY_SEPARATOR . 'upload';
        $this->writeDebugMessage(
            'Moving files from `%s` to `%s`',
            array($installPath, $tempDir,)
        );
        $this->filesystem->rename($installPath, $tempDir);
        $this->writeDebugMessage(
            'Moving files from `%s` to `%s`',
            array($uploadDir, $installPath,)
        );
        $this->filesystem->rename($uploadDir, $installPath);
        $this->writeDebugMessage('Removing `%s`', array($tempDir,));
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
        $installPath = $this->getInstallPath($package);
        $this->writeDebugMessage(
            'Installing package `%s` to %s',
            array($package->getPrettyName(), $installPath,)
        );
        parent::install($repo, $package);
        $this->writeDebugMessage('Post-install file rotating');
        $this->rotateFiles($installPath);
        $this->writeDebugMessage('Finished installation');
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
        $this->writeDebugMessage('Updating package');
        parent::update($repo, $initial, $target);
        $this->writeDebugMessage('Post-update file rotate');
        $this->rotateFiles($this->getInstallPath($target));
        $this->writeDebugMessage('Finished updating');
    }

    /**
     * Writes debug message to stdout.
     *
     * @param string            $message Message to be shown.
     * @param array|string|null $args    Additional arguments for message
     *                                   formatting.
     *
     * @return void
     * @since 0.1.0
     */
    protected function writeDebugMessage($message, $args = null)
    {
        if (getenv('DEBUG') || getenv('OPENCART_INSTALLER_DEBUG')) {
            if ($args) {
                if (!is_array($args)) {
                    $args = array($args);
                }
                $message = vsprintf($message, $args);
            }
            $this->io->write('OpencartInstaller: ' . $message);
        }
    }
}
