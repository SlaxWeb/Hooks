<?php
/**
 * Hooks Service Provider
 *
 * Service Provider for the Pimple\Container for convenient creation of the
 * Hooks container service, and creation of new, empty Hook objects for
 * injection into the container.
 *
 * @package   SlaxWeb\Hooks
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.5
 */
namespace SlaxWeb\Hooks\Service;

class Provider implements \Pimple\ServiceProviderInterface
{
    /**
     * Register provider
     *
     * Register the Hooks Service Provider to the DIC.
     *
     * @param \Pimple\Container $container DIC
     * @return void
     */
    public function register(\Pimple\Container $container)
    {
        $container["hooks.service"] = function (\Pimple\Container $cont) {
            return new \SlaxWeb\Hooks\Container($cont["logger.service"]("System"));
        };

        $container["newHook.factory"] = $container->factory(
            function (\Pimple\Container $cont) {
                return new \SlaxWeb\Hooks\Hook;
            }
        );
    }
}
