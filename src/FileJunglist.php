<?php
namespace Etki\Composer\Installers\Opencart;

use Symfony\Component\Filesystem\Filesystem;

/**
 * This class is responsible for all file jungling.
 *
 * @version Release: 0.1.0
 * @since   0.1.1
 * @package Etki\Composer\Installers\Opencart
 * @author  Fike Etki <etki@etki.name>
 */
class FileJunglist
{
    /**
     * Filesystem nodes that have to be chmodded, according to Opencart
     * installation notes
     *
     * @type string[]
     * @since 0.1.1
     */
    protected $chmodNodes = array(
        'download',
        'system/cache',
        'system/logs',
        'system/download',
        'image',
        'image/cache',
        'image/catalog',
        'config.php',
        'admin/config.php',
        'config-dist.php',
        'admin/config-dist.php',
    );
    /**
     * List of configuration files
     *
     * @type string[]
     * @since 0.1.1
     */
    protected $modifiableFiles = array(
        'config.php',
        '.htaccess',
        'php.ini',
        'robots.txt',
        'admin/config.php',
        'image',
        'system/logs',
        'system/download',
    );
    protected $ignoredFiles = [
	    'build.xml',
    	'CHANGELOG.md',
	    'CHANGELOG_AUTO.md',
	    'composer.json',
	    'composer.lock',
	    'install.txt',
	    'license.txt',
	    'README.md',
	    'upgrade.txt',
	    '.idea',
	    '.git'
    ];
    protected $usefullFiles = [
    	'.gitignore'
    ];

    /**
     * Saves config files during install.
     *
     * @param string $installPath Opencart install path.
     *
     * @return void
     * @since 0.1.0
     */
    public function saveModifiedFiles($installPath)
    {
        DebugPrinter::log('Saving modified items');
        $tmpRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        $installPath = trim($installPath, '\\/') . DIRECTORY_SEPARATOR;
        $fsm = new Filesystem;
        DebugPrinter::log(
            'Items to search: %s',
            implode(', ', $this->modifiableFiles)
        );
        foreach ($this->modifiableFiles as $file) {
            $source = $installPath . $file;
            $target = $tmpRoot . md5($source);
            if (!$fsm->exists($source)) {
                DebugPrinter::log('Item `%s` is missing, skipping it', $source);
                continue;
            }
            $args = array($source, $target,);
            DebugPrinter::log('Saving `%s` to `%s`', $args);
            if (is_dir($source)) {
                $fsm->mirror($source, $target);
            } else {
                $fsm->copy($source, $target);
            }
        }
        DebugPrinter::log('Finished saving modified items');
    }

    /**
     * Restores previously saved config files.
     *
     * @param string $installPath Opencart installation path.
     *
     * @return void
     * @since 0.1.1
     */
    public function restoreModifiedFiles($installPath)
    {
        DebugPrinter::log('Restoring modified items');
        $tmpRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR;
        $installPath = trim($installPath, '\\/') . DIRECTORY_SEPARATOR;
        $fsm = new Filesystem;
        DebugPrinter::log(
            'Items to search: %s',
            implode(', ', $this->modifiableFiles)
        );
        foreach ($this->modifiableFiles as $file) {
            $target = $installPath . $file;
            $source = $tmpRoot . md5($target);
            if (!$fsm->exists($source)) {
                DebugPrinter::log('Item `%s` is missing, skipping it', $source);
                continue;
            }
            $args = array($source, $target,);
            DebugPrinter::log('Restoring `%s` to `%s`', $args);
            if ($fsm->exists($target)) {
                $fsm->remove($target);
            }

            $fsm->rename($source, $target, true);
        }
        DebugPrinter::log('Finished restoring modified items');
    }

    /**
     * Sets Opencart folder permissions as required in manual.
     *
     * @param string $installPath Opencart install path.
     * @param int    $basePerms   Base permissions. Please note that all
     *                            directories permissions will be masked by 111
     *                            (this will set all three ugo executive bits).
     *
     * @return void
     * @since 0.1.1
     */
    protected function setPermissions($installPath, $basePerms = 0644)
    {
        $fsm = new Filesystem;
        foreach ($this->chmodNodes as $fsNode) {
            $path = $installPath . DIRECTORY_SEPARATOR . $fsNode;
            $isFile = is_file($path);
            $isDir = is_dir($path);
            if (!$isFile && !$isDir) {
                DebugPrinter::log('Filesystem node %s not found', $path);
                continue;
            }
            $perms = $basePerms;
            if ($isDir) {
                $perms |= 0111;
            }
            $args = array($path, decoct($perms),);
            DebugPrinter::log('Chmodding `%s` to `%s`', $args);
            $fsm->chmod($path, $perms);
        }
    }

    /**
     * Moves installed files out of `upload` dir.
     *
     * @param string $packageFolder {vendorDir}/{packagePrettyName}
     * @param string $webRootFolder Opencart public folder name.
     *
     * @return void
     * @since 0.1.0
     */
    public function rotateInstalledFiles($packageFolder, $webRootFolder, $storageFolder)
    {
        $fsm = new Filesystem;

	    $packageFolder = str_replace('\\', '/', $packageFolder);
	    $projectFolder = dirname($webRootFolder);
	    DebugPrinter::log("Project folder path: `%s`", $projectFolder);
	    DebugPrinter::log('Package location: `%s`', $packageFolder);

	    $files = glob($packageFolder . '/*');
	    DebugPrinter::log("1. projectRootFiles: %s", print_r($files, 1));

	    foreach ($files as $i => $file) {

		    $filename = basename($file);

		    if (!in_array($filename, $this->ignoredFiles)) {
		    	if ($filename == 'upload') { // Remove files from UploadDir to webRoot
				    $webRootFiles = glob($file . DIRECTORY_SEPARATOR . '*');

				    DebugPrinter::log('Moving public files to webRootFolder');
				    foreach ($webRootFiles as $webRootFile) {
				    	$webRootFileName = basename($webRootFile);
				    	DebugPrinter::log('$webRootFileName = `%s`', $webRootFileName);
				    	DebugPrinter::log('$webRootFile = `%s`', $webRootFile);

					    if(!in_array($webRootFileName, $this->ignoredFiles)) {
					    	if ($webRootFileName == 'system') { // Move storageDir to projectRoot (outside from web-access)
					    		if(!$fsm->exists($storageFolder)) {
					    			$fsm->mirror($webRootFile . '/storage', $storageFolder);
								    $fsm->remove($webRootFile . '/storage');
							    }
					    		else {
					    			$fsm->mirror($webRootFile . '/storage', $storageFolder);
//					    			$fsm->remove($webRootFile . '/storage');
							    }

						    }
//					    	$fsm->rename($webRootFile, $webRootFolder . '/' . $webRootFileName);
					    	$fsm->mirror($webRootFile, $webRootFolder . '/' . $webRootFileName);

					    }
				    }
				    if (!$fsm->exists($webRootFolder . '/.htaccess')) {
					    $fsm->copy($file . '/.htaccess.txt', $webRootFolder . '/.htaccess.txt');
				    }
			    }
		    	else {
				    DebugPrinter::log("Filename: -`%s`-", $filename);
				    $filename = $projectFolder . '/' . $filename;
				    DebugPrinter::log("File: `%s`", $file);
				    $fsm->mirror($file, $filename);
			    }
		    }
	    }
	    if (!$fsm->exists('.gitignore')) {
		    $fsm->copy($packageFolder . '/.gitignore', '.gitignore');
	    }
//	    else {
//	    	$fsm->remove($packageFolder . '/.gitignore');
//	    }
    }

    /**
     * Copies configuration files from their dists as specified by installation
     * notes.
     *
     * @param string $installPath Opencart installation path.
     *
     * @return void
     * @since 0.1.0
     */
    public function copyConfigFiles($installPath)
    {
        $filesystem = new Filesystem;
        $configFiles = array('/config', '/admin/config',);
        foreach ($configFiles as $configFile) {
            $source = $installPath . $configFile . '-dist.php';
            $target = $installPath . $configFile . '.php';
            if ($filesystem->exists($source)) {
                $filesystem->copy($source, $target);
            } else {
                DebugPrinter::log(
                    'File `%s` doesn\'t exist, though i am sure it should',
                    $source
                );
            }
        }
    }
}
