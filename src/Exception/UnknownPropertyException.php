<?php
/**
 * Unknown Property Exception
 *
 * Thrown when an access is made to a set/get magic method to an
 * unknown(missing) property.
 *
 * @package   SlaxWeb\Hooks
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.5
 */
namespace SlaxWeb\Hooks\Exception;

class UnknownPropertyException extends \Exception
{
}
