<?php

namespace Etki\Composer\Installers\Opencart\Tests\Support;

/**
 * Saves results of a single command execution (exit code, running time and
 * output).
 *
 * @version 0.1.0
 * @since   0.1.1
 * @package Codeception\Module
 * @author  Etki <etki@etki.name>
 */
class ConsoleCommandRun
{
    /**
     * Command itself.
     *
     * @type string
     * @since 0.1.0
     */
    protected $command;
    /**
     * Returned exit code.
     *
     * @type int
     * @since 0.1.0
     */
    protected $exitCode;
    /**
     * Console command output.
     *
     * @type string
     * @since 0.1.0
     */
    protected $output;
    /**
     * Time of run start.
     *
     * @type float
     * @since 0.1,0
     */
    protected $startTime;
    /**
     * TIme of run end.
     *
     * @type float
     * @since 0.1.0
     */
    protected $endTime;
    /**
     * Internal flag that shows if command run has been finished.
     *
     * @type bool
     * @since 0.1.0
     */
    protected $finished = false;

    /**
     * Simple property initializer.
     *
     * @param string $command Command to be ran.
     *
     * @return self
     * @since 0.1.0
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * Runs command.
     *
     * @return void
     * @since 0.1.0
     */
    public function run()
    {
        $this->assertReady();
        $this->startTime = microtime(true);
        exec($this->command, $output, $exitCode);
        $this->finished = true;
        $this->endTime = microtime(true);
        $this->exitCode = $exitCode;
        $this->output = implode(PHP_EOL, $output);
    }

    /**
     * Returns time taken by command run.
     *
     * @return float
     * @since 0.1.0
     */
    public function getRunTime()
    {
        $this->assertHasFinished();
        return $this->endTime - $this->startTime;
    }

    /**
     * Returns exit code.
     *
     * @return int
     * @since 0.1.0
     */
    public function getExitCode()
    {
        $this->assertHasFinished();
        return $this->exitCode;
    }

    /**
     * Returns run output.
     *
     * @return string
     * @since 0.1.0
     */
    public function getOutput()
    {
        $this->assertHasFinished();
        return $this->output;
    }

    /**
     * Tells if run has been finished.
     *
     * @return bool
     * @since 0.1.0
     */
    public function isFinished()
    {
        return $this->finished;
    }

    /**
     * Asserts that run has been finished.
     *
     * @throws \RuntimeException Thrown if run has not been finished.
     * @inline
     *
     * @return void
     * @since 0.1.0
     */
    protected function assertHasFinished()
    {
        if (!$this->finished) {
            throw new \RuntimeException('Execution hasb\'t been run yet');
        }
    }
    /**
     * Throws an exception if run has already been ended.
     *
     * @throws \RuntimeException
     * @inline
     *
     * @return void
     * @since 0.1.0
     */
    protected function assertReady()
    {
        if (isset($this->startTime)) {
            throw new \RuntimeException('Execution has already been run');
        }
    }
}
