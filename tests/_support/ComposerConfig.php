<?php
namespace Etki\Composer\Installers\Opencart\Tests\Support;

/**
 * Simple `composer.json` generator.
 *
 * @version 0.1.1
 * @since   0.1.0
 * @package Codeception\Module
 * @author  Etki <etki@etki.name>
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
     * List of arguments required by config.
     *
     * @type string[]
     * @since 0.1.1
     */
    protected $requiredArgs = array();
    /**
     * List of hard-binded args that persist between different config
     * compilations.
     *
     * @type string[]
     * @since 0.1.1
     */
    protected $bindedArgs = array();

    /**
     * Typical constructor.
     *
     * @param string   $template   Template body,
     * @param string[] $bindedArgs List of hard-binded args.
     *
     * @since 0.1.0
     */
    public function __construct($template, array $bindedArgs)
    {
        $this->template = $template;
        preg_match_all('~%([\w-_]+)%~', $template, $args);
        if (!$args[1]) {
            return;
        }
        $this->requiredArgs = array_unique($args[1]);
        $this->bindedArgs = $bindedArgs;
    }

    /**
     * Retrieves binded argument.
     *
     * @param string $name Name of the binded argument.
     *
     * @throws \BadMethodCallException Thrown in case binded argument doesn\'t
     * exist.
     *
     * @return string
     * @since 0.1.0
     */
    public function getBindedArg($name)
    {
        if (!isset($this->bindedArgs[$name])) {
            $message = sprintf('Arg `%s` doesn\'t exist', $name);
            throw new \BadMethodCallException($message);
        }
        return $this->bindedArgs[$name];
    }

    /**
     * Generates `composer.json` file.
     *
     * @param string[] $args Args to format array.
     *
     * @return string `composer.json` content.
     * @since 0.1.0
     */
    public function compile(array $args)
    {
        $args = array_merge($this->bindedArgs, $args);
        if (!$this->validateArgs($args)) {
            $diff = array_diff($this->requiredArgs, array_keys($args));
            $message = 'Following arguments are required for composer.json ' .
                'build: ' . implode(', ', $diff);
            throw new \RuntimeException($message);
        }
        $args = $this->prepareArgs($args);
        return str_replace(array_keys($args), $args, $this->template);
    }

    /**
     * Validates args.
     *
     * @param string[] $args Argument list.
     *
     * @return bool True on successful validation, false otherwise
     * @since 0.1.0
     */
    protected function validateArgs(array $args)
    {
        foreach ($this->requiredArgs as $arg) {
            if (!isset($args[$arg])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Wraps argument keys with percent symbols.
     *
     * @param string[] $args List of arguments.
     *
     * @return string[] Modified list.
     * @since 0.1.0
     */
    protected function prepareArgs($args)
    {
        foreach ($args as $key => $value) {
            unset($args[$key]);
            $args['%' . $key . '%'] = $value;
        }
        return $args;
    }
}
