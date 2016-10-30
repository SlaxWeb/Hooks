<?php
/**
 * Hooks Container Tests
 *
 * Test all processes and execution paths of the Container class.
 *
 * @package   SlaxWeb\Hooks
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.4
 */
namespace SlaxWeb\Hooks\Tests\Unit;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Logger Mock
     *
     * @var object
     */
    protected $logger = null;

    /**
     * Container Mock Builder
     *
     * @var object
     */
    protected $container = null;

    /**
     * Hook name
     *
     * Used for mocking the Hook class properties
     *
     * @var string
     */
    protected $hookName = "";

    /**
     * Hook definition
     *
     * Used for mocking the Hook class properties
     *
     * @var callable
     */
    protected $hookDefinition = null;

    /**
     * Set up the test
     *
     * Called before each test method is invoked to set up some common stuff.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->logger = $this->getMockForAbstractClass(
            "\\Psr\\Log\\LoggerInterface"
        );

        $this->container = $this->getMockBuilder("\\SlaxWeb\\Hooks\\Container")
            ->disableOriginalConstructor();
    }

    /**
     * Tear down test
     *
     * Called after each test method was invoked, to tear down set stuff.
     *
     * @return void
     */
    protected function tearDown()
    {
    }

    /**
     * Test constructor
     *
     * Test that the Container constructor is in fact logging in 'info' log
     * level. The actual text message is not checked.
     *
     * @return void
     */
    public function testConstructorLogs()
    {
        $container = $this->container->setMethods(null)
            ->getMock();
        $this->logger->expects($this->once())->method("info");

        $container->__construct($this->logger);
    }

    /**
     * Test 'addHook' type hint
     *
     * Test that the type hint is in place, and that it will throw a TypeError
     * when not a correct type is supplied as first parameter.
     *
     * @return void
     */
    public function testAddHookTypeHint()
    {
        $container = $this->container->setMethods(null)
            ->getMock();
        $this->logger->expects($this->once())->method("info");
        $container->__construct($this->logger);

        // Make sure it typehints
        $error = false;
        try {
            $container->addHook(new \stdClass);
        } catch (\TypeError $e) {
            $error = true;
        }
        if ($error === false) {
            throw new \Exception("Expected TypeError did not occur.");
        }

        // Make sure it accepts the Hook object
        $hook = $this->getMockBuilder("\\SlaxWeb\\Hooks\\Hook")
            ->setMethods([])
            ->getMock();
        $hook->name = "test";
        $hook->definition = function () {
            return true;
        };
        $container->addHook($hook);
    }

    /**
     * Test 'addHook'
     *
     * Test that the 'addHook' method does in fact create a new item in its
     * internal container when a hook with a new name is received, and it does
     * not do so again when a hook with a same name is received.
     *
     * @todo extend test to check that said hooks are actually stored, but can
     *       only be done, once other methods in the Container class exist.
     *
     * @return void
     */
    public function testAddHook()
    {
        $container = $this->container->setMethods(null)->getMock();

        $this->logger->expects($this->exactly(3))->method("info");
        $this->logger->expects($this->exactly(3))->method("debug");

        $hook = $this->createMock("\\SlaxWeb\\Hooks\\Hook");
        $this->hookName = "test";
        $this->hookDefinition = function () {
            return true;
        };
        $hook->expects($this->any())
            ->method("__get")
            ->will($this->returnCallback([$this, "hookReturn"]));

        $container->__construct($this->logger);
        $container->addHook($hook);
        $container->addHook($hook);
        $this->assertEquals([true, true], $container->exec("test"));
    }

    /**
     * Test Hook Not Found On 'exec'
     *
     * Test that correct errors are thrown, when no hook name is provided for
     * the 'exec' method or the name is not in string format, or the name is not
     * found in the container.
     *
     * @return \Mock_Container Mocked Hooks Container
     */
    public function testExecHookNameErrors()
    {
        $container = $this->container->setMethods(null)->getMock();

        $this->logger->expects($this->once())->method("info");
        $this->logger->expects($this->once())->method("debug");

        $container->__construct($this->logger);

        $exception = [false, false];
        try {
            $container->exec();
        } catch (\TypeError $e) {
            $exception[0] = true;
        }
        try {
            $container->exec(new \stdClass);
        } catch (\TypeError $e) {
            $exception[1] = true;
        }
        if ($exception !== [true, true]) {
            throw new \Exception("Expected TypeError to be thrown two times");
        }

        $this->assertNull($container->exec("missing"));
    }

    /**
     * Test Hook Order of Execution
     *
     * Test that added hook definitions get executed in the correct order, and
     * return values are returned in an array on multiple definitions.
     *
     * @return void
     */
    public function testHookOrderOfExec()
    {
        $container = $this->container->setMethods(null)->getMock();
        $this->logger->expects($this->exactly(5))->method("info");
        $this->logger->expects($this->exactly(6))->method("debug");
        $container->__construct($this->logger);

        $hook = $this->createMock("\\SlaxWeb\\Hooks\\Hook");

        for ($hooks = 1; $hooks <= 3; $hooks++) {
            $hook = clone $hook;
            $this->hookName = "test";
            $this->hookDefinition = function () use ($hooks) {
                if ($hooks === 3) {
                    return null;
                }
                return $hooks;
            };
            $hook->expects($this->any())
                ->method("__get")
                ->will($this->returnCallback([$this, "hookReturn"]));

            $container->addHook($hook);
        }

        $this->assertEquals($container->exec("test"), [1, 2]);

        $hook = clone $hook;
        $this->hookName = "test2";
        $this->hookDefinition = function () use ($hooks) {
            return true;
        };
        $hook->expects($this->any())
            ->method("__get")
            ->will($this->returnCallback([$this, "hookReturn"]));

        $container->addHook($hook);
        $this->assertTrue($container->exec("test2"));
    }

    /**
     * Test Hook Execution Parameters
     *
     * Test that an instance of the Hooks container is passed as the first
     * parameter to the hook definition, and all further parameters passed to
     * 'exec' method get passed to the hook definition.
     *
     * @return void
     */
    public function testHookExecParams()
    {
        $container = $this->container->setMethods(null)->getMock();

        $this->logger->expects($this->exactly(2))->method("info");
        $this->logger->expects($this->exactly(2))->method("debug");

        $container->__construct($this->logger);
        $container->setParams([$container]);

        $hook = $this->createMock("\\SlaxWeb\\Hooks\\Hook");
        $this->hookName = "test";
        $this->hookDefinition = function () {
            return func_get_args();
        };
        $hook->expects($this->any())
            ->method("__get")
            ->will($this->returnCallback([$this, "hookReturn"]));

        $container->addHook($hook);

        $this->assertEquals(
            [$container, true, false, "param"],
            $container->exec("test", true, false, "param")
        );
    }

    /**
     * Test Hook Execution Interuption
     *
     * Test that a hook definition can stop further execution of definitions.
     *
     * @return void
     */
    public function testHookInteruption()
    {
        $container = $this->container->setMethods(null)->getMock();
        $this->logger->expects($this->exactly(3))->method("info");
        $container->__construct($this->logger);
        $container->setParams([$container]);

        $hook = $this->createMock("\\SlaxWeb\\Hooks\\Hook");

        $this->hookName = "interrupt";
        $this->hookDefinition = function ($container) {
            $container->stopExec();
            return true;
        };
        $hook->expects($this->any())
            ->method("__get")
            ->will($this->returnCallback([$this, "hookReturn"]));
        $container->addHook($hook);

        $hook = clone $hook;
        $this->hookName = "interrupt";
        $this->hookDefinition = function ($container) {
            return false;
        };
        $hook->expects($this->any())
            ->method("__get")
            ->will($this->returnCallback([$this, "hookReturn"]));
        $container->addHook($hook);

        $this->assertTrue($container->exec("interrupt"));
    }

    /**
     * Hook class callback
     *
     * Return the defined values for the name and definition properties of the
     * mocked Hook object.
     *
     * @param string $param Name of the property that was accessed
     * @return mixed
     */
    public function hookReturn($param)
    {
        if ($param === "name") {
            return $this->hookName;
        } elseif ($param === "definition") {
            return $this->hookDefinition;
        }
    }
}
