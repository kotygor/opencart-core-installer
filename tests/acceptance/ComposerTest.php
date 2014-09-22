<?php

use Etki\Composer\Installers\Opencart\Tests\Support\ComposerConfig;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Tests composer installation using the most recent master-branch commit.
 *
 * @version Release: 0.1.0
 * @since   0.1.0
 * @author  Fike Etki <etki@etki.name>
 */
class ComposerTest extends \Codeception\TestCase\Test
{
    /**
     * List of created dir paths.
     *
     * @type string[]
     * @since 0.1.0
     */
    protected static $dirs = array();

    /**
     * Creates temporary directory and returns it's path.
     *
     * @return string
     * @since 0.1.0
     */
    protected static function issueTempDirectory()
    {
        $dir = sys_get_temp_dir() . '/' . uniqid('oi-test-', true);
        static::$dirs[] = $dir;
        return $dir;
    }

    /**
     * Cleanup.
     *
     * @return void
     * @since 0.1.0
     */
    public static function tearDownAfterClass()
    {
        $fsm = new Filesystem();
        foreach (static::$dirs as $dir) {
            $fsm->remove($dir);
        }
    }

    /**
     * Returns path of the package root.
     *
     * @return string
     * @since 0.1.0
     */
    protected function getPackageRoot()
    {
        return dirname(dirname(__DIR__));
    }

    /**
     * Returns path to composer executable.
     *
     * @return string
     * @since 0.1.0
     */
    protected function getComposerExecutable()
    {
        return $this->getPackageRoot() . '/vendor/bin/composer';
    }

    /**
     * Returns path to `composer.json` templates directory.
     *
     * @return string
     * @since 0.1.0
     */
    protected function getTemplateDir()
    {
        return $this->getPackageRoot() . '/tests/_data/composer-templates';
    }

    /**
     * Executes composer action.
     *
     * @param string $action Composer action to be executed.
     * @param string $dir    Directory in which composer should be ran.
     *
     * @return int Process exit code.
     * @since 0.1.0
     */
    protected function execute($action, $dir)
    {
        $command = sprintf(
            '%s %s -d %s',
            $this->getComposerExecutable(),
            $action,
            $dir
        );
        exec($command, $out, $exitCode);
        return $exitCode;
    }

    // tests

    /**
     * Tests installation.
     *
     * @return void
     * @since 0.1.0
     */
    public function testInstallation()
    {
        $fsm = new \Symfony\Component\Filesystem\Filesystem();
        $defs = array(
            'extra' => 'opencart-install-dir',
            'no-extra' => 'opencart',
        );
        $scenario = array(
            array('version' => '1.5.6.3', 'action' => 'install'),
            array('version' => '1.5.6.4', 'action' => 'update'),
            array('version' => '1.5.6.3', 'action' => 'update'),
        );
        $pluginPath = $this->getPackageRoot();
        foreach ($defs as $type => $installDir) {
            $tempDir = $this->issueTempDirectory();
            $fsm->mkdir($tempDir);
            $config = new ComposerConfig($type, $this->getTemplateDir());
            foreach ($scenario as $step) {
                $version = $step['version'];
                $action = $step['action'];
                $config->write($tempDir, $version, $installDir, $pluginPath);
                $this->assertSame(0, $this->execute($action, $tempDir));
                $this->checkIndex($tempDir . '/' . $installDir, $version);
            }
            $fsm->remove($tempDir);
        }
    }

    /**
     * Checks index.php for expected version.
     *
     * @param string $root    Path to Opencart root.
     * @param string $version Expected Opencart version.
     *
     * @return void
     * @since 0.1.0
     */
    protected function checkIndex($root, $version)
    {
        $path = $root . DIRECTORY_SEPARATOR . 'index.php';
        $message = sprintf('File `%s` doesn\'t exist', $path);
        $this->assertTrue(file_exists($path), $message);
        $index = file_get_contents($path);
        $pattern = "~.*define\('VERSION',\s*'$version'\).*~ius";
        $message = sprintf(
            "Pattern %s didn't match. Index content: \n\n %s",
            $pattern,
            $index
        );
        $this->assertTrue(preg_match($pattern, $index) > 0, $message);
    }
}