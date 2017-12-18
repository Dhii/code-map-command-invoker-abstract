<?php

namespace Dhii\Invocation\FuncTest;

use Xpmock\TestCase;
use Dhii\Invocation\AbstractCodeMapInvoker as TestSubject;
use InvalidArgumentException;
use OutOfRangeException;
use Dhii\Invocation\Exception\InvocationFailureExceptionInterface;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class AbstractCodeMapInvokerTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Invocation\AbstractCodeMapInvoker';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return TestSubject
     */
    public function createInstance()
    {
        $me = $this;
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
                ->__($this->returnArgument(0))
                ->_normalizeString(function ($string) {
                    return (string) $string;
                })
                ->_createInvalidArgumentException(function ($message) {
                    return new InvalidArgumentException($message);
                })
                ->_createOutOfRangeException(function ($message) {
                    return new OutOfRangeException($message);
                })
                ->_createInvocationFailureException(function ($message) use ($me) {
                    return $me->createInvocationFailureException($message);
                })
                ->_normalizeArray(function ($array) {
                    return ($array instanceof Traversable)
                            ? iterator_to_array($array)
                            : (array) $array;
                })
                ->new();

        $this->reflect($mock)->_construct();

        return $mock;
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
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockForAbstractClass($paddingClassName);
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
                ['getMessage', 'getPrevious', 'getCommand', 'getArgs', 'getCommandInvoker']);
        $mock->method('getMessage')
                ->will($this->returnValue($message));
        $mock->method('getPrevious')
                ->will($this->returnValue($previous));
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

        $this->assertInstanceOf(
            static::TEST_SUBJECT_CLASSNAME,
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
        $data1 = uniqid('mystring-');
        $data2 = uniqid('otherstring-');
        $code = uniqid('code-');
        $callable = function ($arg) use ($data1) {
            return $data1.$arg;
        };

        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->_mapCallableToCode($code, $callable);

        $result = $_subject->_invoke($code, [$data2]);

        $this->assertContains($data1, $result, 'Check value was not found in result');
        $this->assertContains($data2, $result, 'Argument value was not found in result');
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

        $this->setExpectedException('Dhii\Invocation\Exception\InvocationFailureExceptionInterface');
        $_subject->_invoke($code, []);
    }
}
