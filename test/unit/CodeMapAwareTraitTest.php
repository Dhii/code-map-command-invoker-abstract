<?php

namespace Dhii\Invocation\UnitTest;

use Xpmock\TestCase;
use Dhii\Invocation\CodeMapAwareTrait as TestSubject;
use InvalidArgumentException;
use OutOfRangeException;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class CodeMapAwareTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Invocation\CodeMapAwareTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return object
     */
    public function createInstance()
    {
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                ->getMockForTrait();
        $mock->method('__')
                ->will($this->returnArgument(0));
        $mock->method('_normalizeString')
                ->will($this->returnCallback(function ($string) {
                    return (string) $string;
                }));
        $mock->method('_createInvalidArgumentException')
                ->will($this->returnCallback(function ($message) {
                    return new InvalidArgumentException($message);
                }));
        $mock->method('_createOutOfRangeException')
                ->will($this->returnCallback(function ($message) {
                    return new OutOfRangeException($message);
                }));

        $this->reflect($mock)->callableCodeMap = [];

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
    public function testMapCallableToCode()
    {
        $code = uniqid('code-');
        $callable = function ($arg) {
            return $arg;
        };

        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->assertArrayNotHasKey($code, $_subject->callableCodeMap, 'Initial state of test subject is wrong');

        $_subject->_mapCallableToCode($code, $callable);

        $this->assertArraySubset([$code => $callable], $_subject->callableCodeMap, 'Modified state of test subject is wrong');
    }

    /**
     * Tests whether retrieving a callable by code works as expected.
     *
     * @since [*next-version*]
     */
    public function testGetCallableByCode()
    {
        $code = uniqid('code-');
        $callable = function ($arg) {
            return $arg;
        };

        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->callableCodeMap = [$code => $callable];

        $result = $_subject->_getCallableByCode($code);

        $this->assertSame($callable, $result, 'Correct callable was not returned');
    }

    /**
     * Tests whether retrieving a non-existing callable by code fails as expected.
     *
     * @since [*next-version*]
     */
    public function testGetCallableByCodeFailureNoSuchCallable()
    {
        $code = uniqid('non-existing-code-');
        $callable = function ($arg) {
            return $arg;
        };

        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->callableCodeMap = [uniqid('code-') => $callable];

        $this->setExpectedException('OutOfRangeException');
        $_subject->_getCallableByCode($code);
    }

    /**
     * Tests whether unmapping a callable from a code works as expected.
     *
     * @since [*next-version*]
     */
    public function testUnmapCallableFromCode()
    {
        $code = uniqid('code-');
        $callable = function ($arg) {
            return $arg;
        };

        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->callableCodeMap = [$code => $callable];

        $_subject->_unmapCallableFromCode($code);

        $this->assertCount(0, $_subject->callableCodeMap, 'Altered state of codemap is wrong');
    }

    /**
     * Tests whether unmapping a non-existing callable from a code fails as expected.
     *
     * @since [*next-version*]
     */
    public function testUnmapCallableFromCodeFailureNoSuchMapping()
    {
        $code = uniqid('non-existing-code-');
        $callable = function ($arg) {
            return $arg;
        };

        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $_subject->callableCodeMap = [uniqid('code-') => $callable];

        $this->setExpectedException('OutOfRangeException');
        $_subject->_unmapCallableFromCode($code);
    }

    /**
     * Tests whether checking for a callable by code works as expected.
     *
     * @since [*next-version*]
     */
    public function testHasMappingWithCode()
    {
        $code = uniqid('code-');
        $callable = function ($arg) {
            return $arg;
        };

        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result1 = $_subject->_hasMappingWithCode($code);
        $this->assertFalse($result1, 'Initial state of codemap is wrong');

        $_subject->callableCodeMap = [$code => $callable];
        $result2 = $_subject->_hasMappingWithCode($code);

        $this->assertTrue($result2, 'Altered state of codemap is wrong');
    }
}
