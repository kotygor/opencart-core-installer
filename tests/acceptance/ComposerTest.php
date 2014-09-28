<?php

namespace Etki\Composer\Installers\Opencart\Tests\Acceptance;

use Etki\Composer\Installers\Opencart\Tests\Support\ComposerProject;
use Symfony\Component\Filesystem\Filesystem;
use Codeception\Module\FilesystemHelper;

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
     * Actor.
     *
     * @type \AcceptanceTester
     * @since 0.1.0
     */
    protected $tester;
    /**
     * List of directories to after test has been run.
     *
     * @type string[]
     * @since 0.1.0
     */
    protected static $keptDirs = array();

    // helpers

    /**
     * Cleanup.
     *
     * @return void
     * @since 0.1.0
     */
    public static function tearDownAfterClass()
    {
        $helper = new FilesystemHelper();
        $helper->cleanTemporaryDirectories(static::$keptDirs);
    }

    // data providers

    /**
     * Provides installation test with config template key and Opencart
     * installation directory.
     *
     * @return array List of template key and installation directory sets.
     * @since 0.1.0
     */
    public function installationDataProvider()
    {
        return array(
            array('extra', 'opencart-install-dir',), // installDir specified
            array('no-extra', 'opencart',), // installDir not specified
        );
    }

    /**
     * Data provider that spits out file permissions.
     *
     * @return array List of array consisting of single octal perms definition.
     * @since 0.1.0
     */
    public function permissionsProvider()
    {
        return array(
            array(0666,),
            array(0777,),
            array(0750,),
        );
    }

    // tests

    /**
     * Empty test just to make sure tearDownAfterClass will be working.
     * See https://github.com/sebastianbergmann/phpunit/issues/1295
     *
     * @return void
     * @since 0.1.0
     */
    public function testNothing()
    {

    }
    /**
     * Tests installation.
     *
     * @param string $key        `composer.json` template key.
     * @param string $installDir Opencart installation directory (relative).
     *
     * @dataProvider installationDataProvider
     *
     * @return void
     * @since 0.1.0
     */
    public function testInstallation($key, $installDir)
    {
        $scenario = array(
            array('version' => '1.5.6.3', 'action' => 'install'),
            array('version' => '1.5.6.4', 'action' => 'update'),
            array('version' => '1.5.6.3', 'action' => 'update'),
            array('version' => '2.0.0.0b2', 'action' => 'update'),
        );
        /** @type ComposerProject $project */
        $project = $this->tester->prepareProject($key, $installDir);
        $opencartPath = $project->getPath() . DIRECTORY_SEPARATOR . $installDir;
        if (getenv('DEBUG') || getenv('OPENCART_INSTALLER_DEBUG')) {
            self::$keptDirs[] = $project->getPath();
        }
        $lastVersion = null;
        foreach ($scenario as $step) {
            $action = $step['action'];
            $version = $step['version'];
            $this->runInstallationScenarioStep($project, $version, $action);
            if ($action === 'update') {
                $this->tester->checkModifiableFilesAfterUpdate(
                    $opencartPath,
                    $lastVersion
                );
            }
            $lastVersion = $version;
        }
        $this->tester->tearDownTemporaryDirectory($project->getPath());
    }

    /**
     * Runs single installation step against particular Opencart version.
     *
     * @param ComposerProject $project Project.
     * @param string          $version Opencart version.
     * @param string          $action  Composer action to be run.
     *
     * @return void
     * @since 0.1.0
     */
    protected function runInstallationScenarioStep(
        ComposerProject $project,
        $version,
        $action
    ) {
        $project->updateConfig(array('opencart-version' => $version));
        $result = $project->executeRawCommand($action);
        $this->assertSame(
            0,
            $result->getExitCode(),
            'Composer error: ' . PHP_EOL . PHP_EOL . $result->getOutput()
        );
        $this->checkIndex($project->getOpencartPath(), $version);
        if ($action === 'install') {
            $this->tester->createModifiableFiles($project->getOpencartPath());
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
        // opencart specifies different versions in tags and index
        preg_match('~\d+\.\d+\.\d+\.\d+~', $version, $strippedVersion);
        $strippedVersion = $strippedVersion[0];
        $pattern = "~.*define\('VERSION',\s*'$strippedVersion\w*'\).*~ius";
        $message = sprintf(
            "Pattern %s didn't match. Index content: \n\n %s",
            $pattern,
            $index
        );
        $this->assertTrue(preg_match($pattern, $index) > 0, $message);
    }
}