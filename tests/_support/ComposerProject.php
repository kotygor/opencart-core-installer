<?php
namespace Etki\Composer\Installers\Opencart\Tests\Support;

/**
 * Enframes some console Composer commands over particular composer project.
 *
 * @version 0.1.0
 * @since   0.1.1
 * @package Codeception\Module
 * @author  Etki <etki@etki.name>
 */
class ComposerProject
{
    /**
     * Path to project.
     *
     * @type string
     * @since 0.1.0
     */
    protected $path;
    /**
     * Path to Composer executable.
     *
     * @type string
     * @since 0.1.0
     */
    protected $executable;
    /**
     * Config instance.
     *
     * @type ComposerConfig
     * @since 0.1.0
     */
    protected $config;

    /**
     * Simple one-time properties setter.
     *
     * @param string   $path       Path to Composer-based project.
     * @param string   $executable Path to Composer executable.
     * @param string   $template   `composer.json` template name.
     * @param string[] $configArgs Constant configuration args.
     *
     * @return self
     * @since 0.1.0
     */
    public function __construct(
        $path,
        $executable,
        $template,
        array $configArgs
    ) {
        $this->path = $path;
        $this->executable = $executable;
        $this->template = $template;
        $this->config = new ComposerConfig($template, $configArgs);
    }

    /**
     * Returns full path to Opencart installation dir.
     *
     * @return string
     * @since 0.1.0
     */
    public function getOpencartPath()
    {
        return $this->path . DIRECTORY_SEPARATOR .
            $this->config->getBindedArg('install-dir');
    }

    /**
     * Updates project `composer.json` file.
     *
     * @param array $args Arguments to compile config (opencart-version,
     *                    repo-path, install-dir)
     *
     * @return void
     * @since 0.1.0
     */
    public function updateConfig(array $args)
    {
        $config = $this->config->compile($args);
        $path = $this->path . DIRECTORY_SEPARATOR . 'composer.json';
        file_put_contents($path, $config);
    }

    /**
     * Executes raw command over Composer executable.
     *
     * @param string $command Command to be ran.
     *
     * @return ConsoleCommandRun
     * @since 0.1.0
     */
    public function executeRawCommand($command)
    {
        if (is_array($command)) {
            $command = implode(' ', $command);
        }
        $command = $this->executable . ' ' . $command . ' -d ' . $this->path;
        $run = new ConsoleCommandRun($command);
        $run->run();
        return $run;
    }

    /**
     * Runs install action over project.
     *
     * @param bool $dev        Whether to fetch dev dependencies or not.
     * @param bool $preferDist Whether to fetch packages as dist archives or
     *                         from source.
     *
     * @return ConsoleCommandRun Action result.
     * @since 0.1.0
     */
    public function install($dev = true, $preferDist = true)
    {
        $args = array(
            'update',
            $dev ? '--dev' : '--no-dev',
            $preferDist ? '--prefer-dist' : '--prefer-source',
            '--no-progress',
        );
        return $this->executeRawCommand($args);
    }

    /**
     * Updates project.
     *
     * @param bool $dev        Whether to fetch dev dependencies or not.
     * @param bool $preferDist Whether to fetch packages as dist archives or
     *                         from source.
     *
     * @return ConsoleCommandRun Action result.
     * @since 0.1.0
     */
    public function update($dev = true, $preferDist = true)
    {
        $args = array(
            'update',
            $dev ? '--dev' : '--no-dev',
            $preferDist ? '--prefer-dist' : '--prefer-source',
            '--no-progress',
        );
        return $this->executeRawCommand($args);
    }

    /**
     * Returns path to Composer project.
     *
     * @return string
     * @since 0.1.0
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns path to Composer executable.
     *
     * @return string
     * @since 0.1.0
     */
    public function getExecutable()
    {
        return $this->executable;
    }

    /**
     * Returns composer.json template name.
     *
     * @return string
     * @since 0.1.0
     */
    public function getTemplateName()
    {
        return $this->template;
    }
}
