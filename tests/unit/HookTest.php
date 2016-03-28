<?php
/**
 * Hook Tests
 *
 * Test all processes and execution paths of the Hook class.
 *
 * @package   SlaxWeb\Hooks
 * @author    Tomaz Lovrec <tomaz.lovrec@gmail.com>
 * @copyright 2016 (c) Tomaz Lovrec
 * @license   MIT <https://opensource.org/licenses/MIT>
 * @link      https://github.com/slaxweb/
 * @version   0.4
 */
namespace SlaxWeb\Hooks\Tests\Unit;

class HookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Hook mock
     *
     * @var mocked \SlaxWeb\Hooks\Hook
     */
    protected $_hook = null;

    /**
     * Set up the test
     *
     * Called before each test method is invoked to set up some common stuff.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->_hook = $this->getMockBuilder("\\SlaxWeb\\Hooks\\Hook")
            ->setMethods(null)
            ->getMock();
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
     * Test hook creation type hinting
     *
     * Make sure that the input parameters are properly type hinted.
     *
     * @return void
     */
    public function testCreateTypeHint()
    {
        $exception = [false, false, false];
        try {
            $this->_hook->create(new \stdClass, "callable");
        } catch (\TypeError $e) {
            $exception[0] = true;
        }
        try {
            $this->_hook->create("name", "callable");
        } catch (\TypeError $e) {
            $exception[1] = true;
        }
        try {
            $this->_hook->create("name", function () {
                return null;
            });
        } catch (\TypeError $e) {
            $exception[2] = true;
        }

        if ($exception !== [true, true, false]) {
            throw new \Exception(
                "Expected TypeError throwable two times, occured less or more "
                . "times. Result expected: [true, true, false], actual result: "
                . json_encode($exception)
            );
        }
    }

    /**
     * Test hook properties set
     *
     * Test that the hook properties were properly set, and they have public
     * visibility.
     *
     * @return void
     */
    public function testHookProperties()
    {
        $this->_hook->create("test", function () {
            return true;
        });

        $this->assertEquals("test", $this->_hook->name);
        $this->assertTrue(call_user_func($this->_hook->definition));
    }

    /**
     * Test property access
     *
     * Ensure that accessing non-existing protected properties through magic
     * set/get methods throw appropriate errors. Also ensure that 'name' and
     * 'definition' properties can not be altered, through simple property
     * access.
     *
     * @return void
     */
    public function testPropertyAccess()
    {
        $exception = [false, false];
        try {
            $this->_hook->name = "changed";
        } catch (\SlaxWeb\Hooks\Exception\HookPropertyChangeException $e) {
            $exception[0] = true;
        }
        try {
            $this->_hook->definition = function () {
                return false;
            };
        } catch (\SlaxWeb\Hooks\Exception\HookPropertyChangeException $e) {
            $exception[1] = true;
        }
        if ($exception !== [true, true]) {
            throw new \Exception(
                "Expected HookPropertyChangeException twice, but occured once, "
                . "or not even once. Expected: [true, true], actual: "
                . json_encode($exception)
            );
        }

        $exception = [false, false];
        try {
            $this->_hook->missing;
        } catch (\SlaxWeb\Exception\UnknownPropertyException $e) {
            $exception[0] = true;
        }
        try {
            $this->_hook->missing = "set";
        } catch (\SlaxWeb\Exception\UnknownPropertyException $e) {
            $exception[1] = true;
        }
        if ($exception !== [true, true]) {
            throw new \Exception(
                "Expected UnknownPropertyException twice, but occured once, or "
                . "not even once. Expected: [true, true], actual: "
                . json_encode($exception)
            );
        }
    }
}
