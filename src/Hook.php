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

class Hook
{
    /**
     * Name of the hook
     *
     * @var string
     */
    protected $_name = "";

    /**
     * Hook definition
     *
     * @var callable
     */
    protected $_definition = null;

    /**
     * Get magic method
     *
     * Used to retrieved protected class properties.
     *
     * @param string $param Name of the protected parameter, without the
     *                      underscore.
     * @return mixed
     */
    public function __get(string $param)
    {
        $property = "_{$param}";
        if (isset($this->{$property}) === false) {
            throw new Exception\UnknownPropertyException(
                "Property '{$param}' does not exist in " . __CLASS__ . ", "
                . "unable to get value."
            );
        }

        return $this->{$property};
    }

    /**
     * Set magic method
     *
     * Used to set protected class properties, and ensures that 'name' and
     * 'definition' properties can not be overwritten.
     *
     * @param string $param Name of the property
     * @param mixed $value Value of the property
     * @return void
     */
    public function __set(string $param, $value)
    {
        if (in_array($param, ["name", "definition"]) === true) {
            throw new Exception\HookPropertyChangeException(
                "Properties 'name' and 'definition' can not be overwritten."
            );
        }

        $property = "_{$param}";
        if (isset($this->{$property}) === false) {
            throw new Exception\UnknownPropertyException(
                "Property '{$param}' does not exist in " . __CLASS__ . ", "
                . "unable to get value."
            );
        }
    }

    /**
     * Create the Hook
     *
     * Create the hook by adding its name and definition to the protected
     * properties.
     *
     * @param string $name Name of the hook
     * @param callable $definition Definition of the hook
     * @return void
     */
    public function create(string $name, callable $definition)
    {
        $this->_name = $name;
        $this->_definition = $definition;
    }
}
