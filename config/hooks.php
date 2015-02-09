<?php
use SlaxWeb\Hooks\Hooks as H;

/**
 * Example hook
 *
 * When hook "default.example.hook" will be fired, the Hooks class will load the
 * "\Hooks\ExampleHook" class in your directory, and execute its "init" method.
 *
 * Feel free to remove this hook. It is not needed. It's here only to show you
 * how you add hooks.
 */
H::set(["name" => "default.example.hook", "class" => "\\Hooks\\ExampleHook"]);
