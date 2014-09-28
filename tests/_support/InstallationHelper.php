<?php
namespace Codeception\Module;

use Codeception\Module;
use Etki\Composer\Installers\Opencart\Tests\Support\ComposerProject;

/**
 * 
 *
 * @version 0.1.0
 * @since   
 * @package Codeception\Module
 * @author  Etki <etki@etki.name>
 */
class InstallationHelper extends Module
{
    /**
     * Prepares Composer project.
     *
     * @param string $templateName `composer.json` template name.
     * @param string $installDir   Opencart installation directory (relative to
     *                             project root).
     *
     * @return ComposerProject
     * @since 0.1.0
     */
    public function prepareProject($templateName, $installDir)
    {
        /** @type FilesystemHelper $fsHelper */
        $fsHelper = $this->getModule('FilesystemHelper');
        $tempDirectory = $fsHelper->issueTemporaryDirectory();
        $executable = $fsHelper->getComposerExecutable();
        $template = $fsHelper->getComposerJsonTemplate($templateName);
        $args = array(
            'install-dir' => $installDir,
            'repo-path' => $fsHelper->getPackageRoot()
        );
        $project = new ComposerProject(
            $tempDirectory,
            $executable,
            $template,
            $args
        );
        return $project;
    }
}
