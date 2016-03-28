# Hooks

[![Build Status](https://travis-ci.org/SlaxWeb/Hooks.svg?branch=0.4.0)](https://travis-ci.org/SlaxWeb/Hooks)

Hooks component for the SlaxWeb/Framework, to execute arbitrary code in regular
application execution. Even when the component is meant for the
SlaxWeb/Framework it can be used outside of it as well. It the SlaxWeb/Logger as
an additional component.

For ease of use the component provides a Factory and a Service Provider for the
Pimple/Container.

## Installation

Easiest form of installation is through composer, edit your *composer.json* file
to contain:
```json
{
    "require": {
        "slaxweb/hooks": "~0.5.*@dev"
    },
    "minimum-stability": "dev"
}
```

## Usage

This section will cover instantiation of the Hooks container, and adding simple
hook definitions to the container, as well as execution of this definition, and
advanced execution of hook definitions.

### Instantiation

You do not need to instantiate the Logger component it self, but you do have to
instantiate the Config component, and make sure that the logger configuration is
loaded, refer to [Logger documentation](https://github.com/SlaxWeb/Logger/) on
how to get this done.

When you got the dependencies instantiated the usage is pretty straight forward:
```php
<?php
use SlaxWeb\Hooks\Container as Hooks;

require_once "vendor/autoload.php";

// instantiate Config object..

// initiate the Hooks container
$hooks = \SlaxWeb\Hooks\Factory::init($config);

// create a hook
$hook = \SlaxWeb\Hooks\Factory::newHook();
$hook->create("hook.name", function (Hooks $container) {
    // stuff..
    return "I ran!";
});

// add the hook to the container
$hook->addHook($hook);

// some app stuff

// execute the hook
if ($hooks->execute("hook.name") === "I ran!") {
    // stuff..
}
```

You can achieve the same thing using the Pimple Dependency Injection Container,
just make sure that you have registered the Service Providers of all 3
components:
* SlaxWeb/Config
* SlaxWeb/Logger
* SlaxWeb/Hooks

When you have all those three providers registered, you can safely use the hooks
container:
```php
<?php
use SlaxWeb\Hooks\Container as Hooks;

require_once "vendor/autoload.php":

$container = new Pimple\Container;

// register the providers
$container->register(new \SlaxWeb\Config\Service\Provider);
$container->register(new \SlaxWeb\Logger\Service\Provider);
$container->register(new \SlaxWeb\Hooks\Service\Provider);

// load the config for the logger etc.

$hook = $container["newHook.factory"];
$hook->create("hook.name", function (Hooks $container) {
    // stuff ..
    return "I ran!";
});

// add the hook to the container
$container["hooks.service"]->addHook($hook);

// some app stuff..

// execute the hook
if ($container["hook.service"]->exec("hook.name") === "I ran!") {
    // stuff..
}
```

### Multiple Hook Definitions

As it is already visible from above examples, hook execution method will return
the value of the hook definition. But a single hook point may hold more than one
definition. In this case the **exec** method will return all return values in an
array, except if the return value of definition was **null**. If no definition
returned a valid return value, the **exec** method will return an empty array.

For a better representation of the above examples, please refer to the code
example bellow:
```php
// instantiation of everything required etc.

// create hooks
$hook = $container["newHook.factory"];
$hook->create("hook.name", function (Hooks $container) {
    return 1;
});
$container["hooks.service"]->addHook($hook);

$hook = $container["newHook.factory"];
$hook->create("hook.name", function (Hooks $container) {
    return null;
});
$container["hooks.service"]->addHook($hook);

$hook = $container["newHook.factory"];
$hook->create("hook.name", function (Hooks $container) {
    return 3;
});
$container["hooks.service"]->addHook($hook);

// execute the hook
$container["hooks.service"]->exec("hook.name"); // will return: [1, 2]

// create 'no return' hook
$hook = $container["newHook.factory"];
$hook->create("noreturn.hook", function (Hooks $container) {
    // stuff
});
$container["hooks.service"]->addHook($hook);

// execute the hook
$container["hooks.service"]->exec("noreturn.hook"); // will return: []
```

### Definition Execution Interruption

Additionally to multiple definitions per hook, a hook can also prevent other
definitions from executing. For this case an instance of the container is passed
into the hook definition as the first parameter. Each hook may call **stopExec**
method on that passed in container object, and it will stop execution of other
hook definitions.
```php
// create hooks
$hook = $container["newHook.factory"];
$hook->create("hook.name", function (Hooks $container) {
    $container->stopExec();
    return 1;
});
$container["hooks.service"]->addHook($hook);

$hook = $container["newHook.factory"];
$hook->create("hook.name", function (Hooks $container) {
    return 2;
});
$container["hooks.service"]->addHook($hook);

$container["hooks.service"]->exec("hook.name"); // will return: 1
```

### Hook definition parameters

When calling the **exec** method you can pass additional parameters to it, and
all those parameters will be passed on to your hook definition.
```php
// create hook
$hook = $container["newHook.factory"];
$hook->create("hook.name", function (Hooks $container, string $myParam) {
    return $myParam;
});
$container["hooks.service"]->addHook($hook);

$container["hooks.service"]->exec("hook.name", "foo"); // will return: "foo"
```
