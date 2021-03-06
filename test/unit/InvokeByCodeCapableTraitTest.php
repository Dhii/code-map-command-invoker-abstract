<?php

namespace Dhii\Invocation\UnitTest;

use ArrayIterator;
use Traversable;
use Xpmock\TestCase;
use Dhii\Invocation\InvokeByCodeCapableTrait as TestSubject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class InvokeByCodeCapableTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Invocation\InvokeByCodeCapableTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return object
     */
    public function createInstance($map = [])
    {
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                ->getMockForTrait();
        $mock->method('_getCallableByCode')
                ->will($this->returnCallback(function ($code) use ($map) {
                    return isset($map[$code]) ? $map[$code] : null;
                }));
        $mock->method('_invokeCallable')
                ->will($this->returnCallback(function ($callable, $args) {
                    $args = $args instanceof Traversable
                        ? iterator_to_array($args)
                        : $args;
                    return call_user_func_array($callable, $args);
                }));

        return $mock;
    }

    /**
     * Creates a traversable list.
     *
     * @since [*next-version*]
     *
     * @param array $array The array with elements for the traversable.
     *
     * @return Traversable The new Traversable.
     */
    public function createTraversable(array $array)
    {
        return new ArrayIterator($array);
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInternalType(
            'object',
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests whether invoking a callable works as expected.
     *
     * @since [*next-version*]
     */
    public function testInvokeByCode()
    {
        $code = uniqid('code-');
        $args = ['Apple', 'Banana'];
        $callable = function () {
            return func_get_args();
        };

        $subject = $this->createInstance([$code => $callable]);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_getCallableByCode')
            ->with($this->equalTo($code));
        $subject->expects($this->exactly(1))
            ->method('_invokeCallable')
            ->with(
                $this->equalTo($callable),
                $this->equalTo($args)
            );

        $result = $_subject->_invokeByCode($code, $args);

        $this->assertEquals($args, $result, 'Invocation (array args) produced a wrong result');
    }

    /**
     * Tests whether invoking a callable works as expected.
     *
     * @since [*next-version*]
     */
    public function testInvokeByCodeTraversable()
    {
        $code = uniqid('code-');
        $_args = ['Apple', 'Banana'];
        $args = $this->createTraversable($_args);
        $callable = function () {
            return func_get_args();
        };

        $subject = $this->createInstance([$code => $callable]);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_getCallableByCode')
            ->with($this->equalTo($code));
        $subject->expects($this->exactly(1))
            ->method('_invokeCallable')
            ->with(
                $this->equalTo($callable),
                $this->equalTo($args)
            );

        $result = $_subject->_invokeByCode($code, $args);

        $this->assertEquals($_args, $result, 'Invocation (traversable args) produced a wrong result');
    }
}
