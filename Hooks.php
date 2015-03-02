<?php
namespace SlaxWeb\Hooks;

class Hooks
{
    protected static $_hooks = [];

    public static function set(array $hook)
    {
        if ((isset($hook["name"]) && isset($hook["class"])) === false) {
            return false;
        }

        if (isset(self::$_hooks[$hook["name"]])) {
            self::$_hooks[$hook["name"]][] = $hook["class"];
        } else {
            self::$_hooks[$hook["name"]] = [$hook["class"]];
        }

        return true;
    }

    public static function call()
    {
        $params = func_get_args();

        $name = array_shift($params);
        if (isset(self::$_hooks[$name])) {
            foreach (self::$_hooks[$name] as $h) {
                $obj = new $h;
                $ret = $obj->init(...$params);
                if ($ret !== null) {
                    return $ret;
                }
            }
        }
    }
}
