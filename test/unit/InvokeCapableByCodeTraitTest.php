<?php

namespace Dhii\Invocation\UnitTest;

use Xpmock\TestCase;
use Dhii\Invocation\InvokeCapableByCodeTrait as TestSubject;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Traversable;
use Dhii\Invocation\Exception\InvocationFailureExceptionInterface;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class InvokeCapableByCodeTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Invocation\InvokeCapableByCodeTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return MockObject
     */
    public function createInstance($methods = [])
    {
        $me = $this;
        // Adding always mocked methods
        $methods = $this->mergeList($methods, [
            '__',
            '_normalizeArray',
        ]);

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                ->setMethods($methods)
                ->getMockForTrait();
        $mock->method('__')
                ->will($this->returnCallback(function ($message) {
                    return $message;
                }));
        $mock->method('_normalizeArray')
                ->will($this->returnCallback(function ($array) {
                    return ($array instanceof Traversable)
                            ? iterator_to_array($array)
                            : (array) $array;
                }));

        return $mock;
    }

    public function mergeList($destination, $source)
    {
        return array_keys(
                array_merge(
                    array_flip($destination),
                    array_flip($source)
                ));
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string $className      Name of the class for the mock to extend.
     * @param string $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return object The object that extends and implements the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [], $methods = [], $constructorArgs = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockBuilder($paddingClassName)
                ->setMethods($methods)
                ->setConstructorArgs($constructorArgs)
                ->getMock();
    }

    /**
     * Creates an Invocation Failure exception.
     *
     * @param string $message The error message.
     *
     * @return InvocationFailureExceptionInterface The new exception.
     */
    public function createInvocationFailureException($message = '', $code = 0, $previous = null, $command = null, $args = null)
    {
        $mock = $this->mockClassAndInterfaces(
                'Exception',
                ['Dhii\Invocation\Exception\InvocationFailureExceptionInterface'],
                ['getCommand', 'getArgs', 'getCommandInvoker'],
                [$message, $code, $previous]);
        $mock->method('getCommand')
                ->will($this->returnValue($command));
        $mock->method('getArgs')
                ->will($this->returnValue($args));
        $mock->method('getCommandInvoker')
                ->will($this->returnValue(null));

        return $mock;
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
     * Tests whether registering and invoking callables works as expected.
     *
     * @since [*next-version*]
     */
    public function testInvokeSuccess()
    {
        $code = uniqid('code-');
        $args = [
            uniqid('arg1-'),
            uniqid('arg2-'),
        ];
        $return = uniqid('invocation-return-');

        $subject = $this->createInstance(['_invokeByCode']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
                ->method('_invokeByCode')
                ->with(
                    $this->equalTo($code),
                    $this->equalTo($args)
                )
                ->will($this->returnValue($return));

        $result = $_subject->_invoke($code, $args);
        $this->assertSame($return, $result, 'Invocation did not return the correct value');
    }

    /**
     * Tests whether registering and invoking callables fails as expected.
     *
     * @since [*next-version*]
     */
    public function testInvokeFailure()
    {
        $code = uniqid('code-');

        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_invokeByCode')
            ->will($this->throwException(new \OutOfRangeException()));
        $subject->expects($this->exactly(1))
            ->method('_createInvocationFailureException')
            ->will($this->returnCallback(function ($message) {
                return $this->createInvocationFailureException($message);
            }));

        $this->setExpectedException('Dhii\Invocation\Exception\InvocationFailureExceptionInterface');
        $_subject->_invoke($code, []);
    }
}
