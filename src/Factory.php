<?php
/**
 * Hooks Factory
 *
 * The Factory provides convenient way to instantiate the Hooks container, and
 * retrieve a fresh, empty Hook object for injection into the container.
 *
 * @package   SlaxWeb\Hooks
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.5
 */
namespace SlaxWeb\Hooks;

use SlaxWeb\Logger\Factory as Logger;
use SlaxWeb\Config\Container as Config;

class Factory
{
    /**
     * Initiate Hooks Container
     *
     * Init the logger through its own factory and create an object of the Hooks
     * container, and return it to the caller. For this the configuration
     * component is required, so the logger can be instantiated.
     *
     * @param \SlaxWeb\Config\Container $config Configuration component
     * @return \SlaxWeb\Hooks\Container
     */
    public static function init(Config $config): Container
    {
        $logger = Logger::init($config);
        return new Container($logger);
    }

    /**
     * Create Hook object
     *
     * Creates an empty Hook object where the hook definition can be stored.
     *
     * @return \SlaxWeb\Hooks\Hook
     */
    public static function newHook(): Hook
    {
        return new Hook;
    }
}
