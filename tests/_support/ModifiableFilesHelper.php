<?php
namespace Codeception\Module;

use Codeception\Module;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 
 *
 * @version Release: 0.1.0
 * @since   0.1.0
 * @package Codeception\Module
 * @author  Etki <etki@etki.name>
 */
class ModifiableFilesHelper extends Module
{
    /**
     * List of directories that may be modified and should save their contents.
     *
     * @type string[] List in [dir => majorVersion] format.
     * @since 0.1.0
     */
    protected $modifiableDirs = array(
        'image' => '*',
        'download' => 1,
        'system/logs' => 2,
        'system/download' => 2,
        'image/catalog' => 2,
    );

    /**
     * Creates dummy files in modifiable directories.
     *
     * @param string $opencartPath Full path to Opencart installation.
     *
     * @return void
     * @since 0.1.0
     */
    public function createModifiableFiles($opencartPath)
    {
        $fsm = new Filesystem;
        foreach (array_keys($this->modifiableDirs) as $dir) {
            $path = $opencartPath . DIRECTORY_SEPARATOR . $dir;
            if ($fsm->exists($path)) {
                $filePath = $path . DIRECTORY_SEPARATOR . 'dummy';
                file_put_contents($filePath, 'yarr!');
            }
        }
    }

    /**
     * Deletes all modifiable files from installation.
     *
     * @param string $opencartPath Full Opencart installation path.
     *
     * @return void
     * @since 0.1.0
     */
    public function deleteModifiableFiles($opencartPath)
    {
        $fsm = new Filesystem;
        foreach (array_keys($this->modifiableDirs) as $dir) {
            $path = $opencartPath . DIRECTORY_SEPARATOR . $dir . 'dummy';
            if ($fsm->exists($path)) {
                $fsm->remove($path);
            }
        }
    }

    /**
     * Patches config files with dummy data so
     *
     * @param string $opencartPath Full Opencart installation path.
     *
     * @return void
     * @since 0.1.0
     */
    public function patchConfigFiles($opencartPath)
    {
        $configFiles = array('config.php', 'admin/config.php',);
        foreach ($configFiles as $file) {
            $path = $opencartPath . DIRECTORY_SEPARATOR . $file;
            $handle = fopen($path, 'a');
            fwrite($handle, PHP_EOL . '# patched!' . PHP_EOL);
            fclose($handle);
        }
    }

    /**
     * Verifies that config files were patched successfully.
     *
     * @param string $opencartPath Full path to opencart installation.
     *
     * @return void
     * @since 0.1.0
     */
    public function verifyPatchedConfigFiles($opencartPath)
    {
        $configFiles = array('config.php', 'admin/config.php',);
        foreach ($configFiles as $file) {
            $path = $opencartPath . DIRECTORY_SEPARATOR . $file;
            $contents = file_get_contents($path);
            $this->assertTrue(strpos($contents, '# patched!') !== false);
        }
    }

    /**
     * Checks that modifiable files are returned to their places.
     *
     * @param string $opencartPath    Path to Opencart installation.
     * @param string $previousVersion Previously used Opencart version (as of
     *                                before update
     *
     * @return void
     * @since
     */
    public function checkModifiableFilesAfterUpdate(
        $opencartPath,
        $previousVersion
    ) {
        $major = (int) $previousVersion[0];
        $dirSep = DIRECTORY_SEPARATOR;
        $fsm = new Filesystem;
        foreach ($this->modifiableDirs as $dir => $constraint) {
            if ($constraint !== $major || $constraint !== '*') {
                continue;
            }
            $path = $opencartPath . $dirSep . $dir . $dirSep . 'dummy';
            $this->assertTrue($fsm->exists($path));
        }
    }
}
