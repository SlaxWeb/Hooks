<?php
/**
 * Hooks Container Class
 *
 * The Container class holds all user definitions for hook points in the form of
 * Hook class objects. It also provides methods for adding new definitions, and
 * execution of those user definitions.
 *
 * @package   SlaxWeb\Hooks
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.4
 */
namespace SlaxWeb\Hooks;

class Container
{
    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger = null;

    /**
     * Hook definition container
     *
     * @var array
     */
    protected $_hooks = [];

    /**
     * Prevent further execution
     *
     * @var bool
     */
    protected $_stop = false;

    /**
     * Class constructor
     *
     * Instantiates the Container, and sets the Logger to the protected property
     * and writes the successful init message to the logger.
     *
     * @param \Psr\Log\LoggerInterface $logger Logger that implements the PSR
     *                                         interface
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->_logger = $logger;

        $this->_logger->info("Hooks component initialized");
    }

    /**
     * Add hook definition to container
     *
     * @param \SlaxWeb\Hooks\Hook $hook Hook definition
     * @return void
     */
    public function addHook(Hook $hook)
    {
        if (isset($this->_hooks[$hook->name]) === false) {
            $this->_logger->debug(
                "Adding definition for hook '{$hook->name}' for the first time."
            );
            $this->_hooks[$hook->name] = [];
        }

        $this->_hooks[$hook->name][] = $hook->definition;
    }

    /**
     * Execute hook definition
     *
     * Execute all definitions for the retrieved hook names in the order that
     * they have been inserted, and store their return values in an array, if it
     * is not null. If only one definition was called, then return that
     * executions return value directly, if there were more calls, return the
     * 'return array'.
     *
     * @param string Hook name
     * @return mixed
     */
    public function exec(string $name)
    {
        if (isset($this->_hooks[$name]) === false) {
            $this->_logger->notice("No hook definitions found for '{$name}'");
            $this->_logger->debug(
                "Available hook definitions",
                [array_keys($this->_hooks)]
            );
            return null;
        }

        $return = [];
        $params = [$this, array_slice(func_get_args(), 1)];
        foreach ($this->_hooks[$name] as $definition) {
            if ($this->_stop === true) {
                $this->_stop = false;
                $this->_logger->info(
                    "Hook execution was interrupted for hook '{$name}'"
                );
                break;
            }

            $this->_logger->info("Calling definition for hook '{$name}'");
            $this->_logger->debug("Hook definition parameters", $params);
            $retVal = $definition(...$params);
            if ($retVal !== null) {
                $return[] = $retVal;
            }
        }

        return count($return) === 1 ? $return[0] : $return;
    }

    /**
     * Prevent further execution
     *
     * Stops execution of all further defined hook definitions.
     *
     * @return void
     */
    public function stopExec()
    {
        $this->_stop = true;
    }
}
