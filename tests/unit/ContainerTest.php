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
    protected $_logger = null;

    /**
     * Container Mock Builder
     *
     * @var object
     */
    protected $_container = null;

    /**
     * Set up the test
     *
     * Called before each test method is invoked to set up some common stuff.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->_logger = $this->getMockForAbstractClass(
            "\\Psr\\Log\\LoggerInterface"
        );

        $this->_logger->expects($this->once())->method("info");

        $this->_container = $this->getMockBuilder("\\SlaxWeb\\Hooks\\Container")
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
        $container = $this->_container->setMethods(null)
            ->getMock();

        $container->__construct($this->_logger);
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
        $container = $this->_container->setMethods(null)
            ->getMock();
        $container->__construct($this->_logger);

        // Make sure it typehints
        $error = false;
        try {
            $container->addHook(new \stdClass);
        } catch (\TypeError $e) {
            $error = true;
        }
        if ($error === false) {
            throw new Exception("Expected TypeError did not occur.");
        }

        // Make sure it accepts the Hook object
        $hook = $this->getMockBuilder("\\SlaxWeb\\Hooks\\Hook")
            ->setMethods(null)
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
        $container = $this->_container->setMethods(null)
            ->getMock();

        $this->_logger->expects($this->once())->method("debug");

        $hook = $this->getMockBuilder("\\SlaxWeb\\Hooks\\Hook")
            ->setMethods(null)
            ->getMock();
        $hook->name = "test";
        $hook->definition = function () {
            return true;
        };

        $container->__construct($this->_logger);
        $container->addHook($hook);
        $container->addHook($hook);
    }
}
