<?php
namespace Etki\Composer\Installers\Opencart;

/**
 * Simple debug messages printer.
 *
 * @version Release: 0.1.0
 * @since   0.1.0
 * @package Etki\Composer\Installers\Opencart
 * @author  Fike Etki <etki@etki.name>
 */
class DebugPrinter
{
    /**
     * Writes debug message to stdout.
     *
     * @param string            $message Message to be shown.
     * @param array|string|null $args    Additional arguments for message
     *                                   formatting.
     *
     * @return void
     * @since 0.1.0
     */
    public static function log($message, $args = null)
    {
        if (getenv('DEBUG') || getenv('OPENCART_INSTALLER_DEBUG')) {
            if ($args) {
                if (!is_array($args)) {
                    $args = array($args);
                }
                $message = vsprintf($message, $args);
            }
            echo 'OpencartInstaller: ' . $message . PHP_EOL;
        }
    }
}
