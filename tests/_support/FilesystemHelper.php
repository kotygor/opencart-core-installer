<?php
namespace Codeception\Module;

use Codeception\Module;
use Symfony\Component\Filesystem\Filesystem;

/**
 * A little helper to issue and tear down temporary directories.
 *
 * @version 0.1.0
 * @since   0.1.1
 * @package Codeception\Module
 * @author  Etki <etki@etki.name>
 */
class FilesystemHelper extends Module
{
    /**
     * Symfony's Filesystem instance.
     *
     * @type Filesystem
     * @since 0.1.0
     */
    protected static $filesystem;
    /**
     * List of issued directories that require cleanup.
     *
     * @type string[]
     * @since 0.1.0
     */
    protected static $dirs = array();

    /**
     * Creates temporary directory.
     *
     * @return string Path to new directory.
     * @since 0.1.0
     */
    public function issueTemporaryDirectory()
    {
        $root = sys_get_temp_dir();
        // Oh hi there. If you got all the way up here - i just have the habit
        // to place ramdisk not as /tmp itself, but inside /tmp (i'm just little
        // bit paranoid of overrunning it's space and running on swap further).
        $candidates = array('ram', 'ramdisk');
        foreach ($candidates as $subDir) {
            $candidate = $root . DIRECTORY_SEPARATOR . $subDir;
            if (is_dir($candidate) && is_writable($candidate)) {
                $root = $candidate;
                break;
            }
        }
        $fullPath = $root . DIRECTORY_SEPARATOR . uniqid('oi-test-');
        mkdir($fullPath);
        self::$dirs[] = $fullPath;
        return $fullPath;
    }

    /**
     * Destroys temporary directory.
     *
     * @param string $path Path to temporary directory.
     *
     * @return void
     * @since 0.1.0
     */
    public function tearDownTemporaryDirectory($path)
    {
        if (!isset(self::$dirs[$path])) {
            return;
        }
        if (!isset(self::$filesystem)) {
            self::$filesystem = new Filesystem;
        }
        self::$filesystem->remove($path);
    }

    /**
     * Cleans all issued temporary directories.
     *
     * @param string[] $excludedDirs List of dirs to exclude from cleanup.
     *
     * @return void
     * @since 0.1.0
     */
    public function cleanTemporaryDirectories(array $excludedDirs)
    {
        if (!isset(self::$filesystem)) {
            self::$filesystem = new Filesystem;
        }
        foreach (self::$dirs as $dir) {
            if (!in_array($dir, $excludedDirs)
                && self::$filesystem->exists($dir)
            ) {
                self::$filesystem->remove($dir);
            }
        }
    }

    /**
     * Returns path of the package root.
     *
     * @return string
     * @since 0.1.0
     */
    public function getPackageRoot()
    {
        return dirname(dirname(__DIR__));
    }

    /**
     * Returns path to composer executable.
     *
     * @return string
     * @since 0.1.0
     */
    public function getComposerExecutable()
    {
        return $this->getPackageRoot() . '/vendor/bin/composer';
    }

    /**
     * Fetches `composer.json` template.
     *
     * @param string $name Template name.
     *
     * @return string
     * @since 0.1.0
     */
    public function getComposerJsonTemplate($name)
    {
        $path = sprintf(
            '%s/tests/_data/composer-templates/composer.%s.json',
            $this->getPackageRoot(),
            $name
        );
        return file_get_contents($path);
    }
}
