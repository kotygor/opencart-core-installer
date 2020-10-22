<?php
namespace Etki\Composer\Installers\Opencart;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;
use Composer\Repository\InstalledRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Just an opencart installer, nothing to see here, move along.
 *
 * @todo implement rich caching
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
     * Cache for quick lookup for package install dirs.
     *
     * @type string[]
     * @since 0.1.0
     */
    protected $installDirCache = array();

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
        DebugPrinter::log(
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
     * @todo refactor
     *
     * @return string
     * @since 0.1.0
     */
    public function getInstallPath(PackageInterface $package)
    {
        $prettyName = $package->getPrettyName();
        DebugPrinter::log(
            'Getting install path for `%s` package',
            array($prettyName,)
        );
        if (isset($this->installDirCache[$prettyName])) {
            return $this->installDirCache[$prettyName];
        }
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
                $this->installDirCache[$prettyName] = $installDir[$prettyName];
                return $installDir[$prettyName];
            }
            $this->installDirCache[$prettyName] = $this->defaultInstallDir;
            return $this->defaultInstallDir;
        }
        $installDir = $installDir ? $installDir : $this->defaultInstallDir;
        DebugPrinter::log(
            'Computed install dir for package `%s`: `%s`',
            array($prettyName, $installDir,)
        );
        $this->installDirCache[$prettyName] = $installDir;
        return $installDir;
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
    	putenv("DEBUG=true");
    	putenv("OPENCART_INSTALLER_DEBUG=true");
        $installPath = $this->getInstallPath($package);
        $junglist = new FileJunglist;
        DebugPrinter::log(
            'Installing package `%s` to %s',
            array($package->getPrettyName(), $installPath,)
        );
        parent::install($repo, $package);
        die();
        DebugPrinter::log('Post-install file rotating');
        $junglist->rotateInstalledFiles($installPath);
        $junglist->copyConfigFiles($installPath);
        DebugPrinter::log('Finished installation');
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
        $installPath = $this->getInstallPath($target);
        $junglist = new FileJunglist;
        $junglist->saveModifiedFiles($installPath);
        DebugPrinter::log('Updating package');
        parent::update($repo, $initial, $target);
        DebugPrinter::log('Post-update file rotate');
        $junglist->rotateInstalledFiles($installPath);
        $junglist->restoreModifiedFiles($installPath);
        DebugPrinter::log('Finished updating');
    }
}
