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
     * Class constructor
     *
     * Instantiates the Container, and sets the Logger to the protected proprty
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

        $this->_hooks[$hook->name] = $hook;
    }
}
