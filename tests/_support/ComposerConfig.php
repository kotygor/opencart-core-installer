<?php
namespace Etki\Composer\Installers\Opencart\Tests\Support;

/**
 * Simple `composer.json` generator.
 *
 * @version Release: 0.1.0
 * @since   0.1.0
 * @package Codeception\Module
 * @author  Fike Etki <etki@etki.name>
 */
class ComposerConfig
{
    /**
     * Template body,
     *
     * @type string
     * @since 0.1.0
     */
    protected $template;

    /**
     * Typical constructor.
     *
     * @param string $type        Template type (extra/no-extra).
     * @param string $templateDir Templates directory,
     *
     * @throws \RuntimeException Thrown if template file doesn't exist.
     *
     * @since 0.1.0
     */
    public function __construct($type, $templateDir)
    {
        $templatePath = sprintf($templateDir . '/composer.%s.json', $type);
        if (!file_exists($templatePath)) {
            $message = sprintf(
                '`composer.json` template `%s` doesn\'t exist',
                $templatePath
            );
            throw new \RuntimeException($message);
        }
        $this->template = file_get_contents($templatePath);
    }

    /**
     * Generates `composer.json` file.
     *
     * @param string $dir             Directory `composer.json` should be
     *                                created in
     * @param string $opencartVersion Opencart version.
     * @param string $installDir      Opencart installation directory.
     * @param string $pluginPath      Path to plugin repository.
     *
     * @return void
     * @since 0.1.0
     */
    public function write($dir, $opencartVersion, $installDir, $pluginPath)
    {
        $content = str_replace(
            array('%opencart-version%', '%install-dir%', '%repo-path%',),
            array($opencartVersion, $installDir, $pluginPath,),
            $this->template
        );
        $configPath = $dir . DIRECTORY_SEPARATOR . 'composer.json';
        file_put_contents($configPath, $content);
    }
}
