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
            if (isset($hook["method"])) {
                self::$_hooks[$hook["name"]][$hook["method"]] = $hook["class"];
            } else {
                self::$_hooks[$hook["name"]][] = $hook["class"];
            }
        } else {
            if (isset($hook["method"])) {
                self::$_hooks[$hook["name"]] = [$hook["method"] => $hook["class"]];
            } else {
                self::$_hooks[$hook["name"]] = [$hook["class"]];
            }
        }

        return true;
    }

    public static function call()
    {
        $params = func_get_args();

        $name = array_shift($params);
        if (isset(self::$_hooks[$name])) {
            foreach (self::$_hooks[$name] as $method => $h) {
                $obj = new $h;
                if (is_int($method)) {
                    $ret = $obj->init(...$params);
                } else {
                    $ret = $obj->{$method}(...$params);
                }
                if ($ret !== null) {
                    return $ret;
                }
            }
        }
    }
}
