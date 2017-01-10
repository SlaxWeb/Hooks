<?php
/**
 * Hook Class
 *
 * The Hook class is used to define that hooks actions, and is injected into the
 * container.
 *
 * @package   SlaxWeb\Hooks
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.5
 */
namespace SlaxWeb\Hooks;

class Hook
{
    use \SlaxWeb\GetSet\MagicGet;
    use \SlaxWeb\GetSet\MagicSet {
        __set as magicSet;
    }

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

        $this->magicSet($param, $value);
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
