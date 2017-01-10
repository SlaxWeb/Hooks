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
 * @version   0.5
 */
namespace SlaxWeb\Hooks;

class Container
{
    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger = null;

    /**
     * Hook definition container
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * Hook Parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * Prevent further execution
     *
     * @var bool
     */
    protected $stop = false;

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
        $this->logger = $logger;

        $this->logger->info("Hooks component initialized");
    }

    /**
     * Add hook definition to container
     *
     * @param \SlaxWeb\Hooks\Hook $hook Hook definition
     * @return void
     */
    public function addHook(Hook $hook)
    {
        if (isset($this->hooks[$hook->name]) === false) {
            $this->logger->debug(
                "Adding definition for hook '{$hook->name}' for the first time."
            );
            $this->hooks[$hook->name] = [];
        }

        $this->hooks[$hook->name][] = $hook->definition;
    }

    /**
     * Execute hook definition
     *
     * Execute all definitions for the retrieved hook names in the order that
     * they have been inserted, and store their return values in an array, if it
     * is not null. If only one definition was called, then return that
     * executions return value directly, if there were more calls, return all the
     * return values in an array.
     *
     * @param string Hook name
     * @return mixed
     */
    public function exec(string $name)
    {
        if (isset($this->hooks[$name]) === false) {
            $this->logger->debug(
                "No hook definitions found for '{$name}'. Available hook "
                . "definitions",
                [array_keys($this->hooks)]
            );
            return null;
        }

        $return = [];
        $params = array_merge($this->params, array_slice(func_get_args(), 1));
        foreach ($this->hooks[$name] as $definition) {
            if ($this->stop === true) {
                $this->stop = false;
                $this->logger->info(
                    "Hook execution was interrupted for hook '{$name}'"
                );
                break;
            }

            $this->logger->info("Calling definition for hook '{$name}'");
            $this->logger->debug("Hook definition parameters", $params);
            $retVal = $definition(...$params);
            if ($retVal !== null) {
                $return[] = $retVal;
            }
        }

        return count($return) === 1 ? $return[0] : $return;
    }

    /**
     * Set parameters
     *
     * Set parameters to be used in hook executions.
     *
     * @param array $params Array of parameters
     * @return \SlaxWeb\Hooks\Container
     */
    public function setParams(array $params): Container
    {
        $this->params = $params;
        return $this;
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
        $this->stop = true;
    }
}
