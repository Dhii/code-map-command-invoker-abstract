<?php

namespace Dhii\Invocation\UnitTest;

use Xpmock\TestCase;
use Dhii\Invocation\AbstractCodeMapInvoker as TestSubject;

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
    public function createInstance($methods = [])
    {
        $me = $this;
        // Adding always mocked methods
        $methods = $this->mergeList($methods, [
            '__',
            '_normalizeString',
            '_createInvalidArgumentException',
            '_createOutOfRangeException',
            '_createInvocationFailureException',
            '_normalizeArray',
        ]);

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                ->setMethods($methods)
                ->getMockForAbstractClass();

        $this->reflect($mock)->_construct();

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

    public function testConstruct()
    {
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->callableCodeMap = null;
        $_subject->_construct();
        $this->assertEquals([], $_subject->callableCodeMap, 'The protected constructor did not bring the subject to initial state');
    }
}
