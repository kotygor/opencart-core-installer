<?php
namespace Codeception\Module;

use Codeception\Module;
use Etki\Composer\Installers\Opencart\Tests\Support\ComposerConfig;

/**
 * Helps generated composer.json.
 *
 * @version 0.1.0
 * @since   0.1.1
 * @package Codeception\Module
 * @author  Etki <etki@etki.name>
 */
class ComposerConfigHelper extends Module
{
    /**
     * Generates composer.json from template.
     *
     * @param string   $dummyProjectPath Opencart installation path.
     * @param string   $templateName     Name of the template.
     * @param string[] $args             Values to format the template.
     *
     * @return void
     * @since 0.1.0
     */
    public function generateComposerJson(
        $dummyProjectPath,
        $templateName,
        array $args
    ) {
        $pathTemplate = '%s/_data/composer-templates/composer.%s.json';
        $templatePath = sprintf($pathTemplate, dirname(__DIR__), $templateName);
        $template = file_get_contents($templatePath);
        $config = new ComposerConfig($template);
        $config->write($dummyProjectPath, $args);
    }
}
