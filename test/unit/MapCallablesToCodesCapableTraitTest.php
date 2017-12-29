<?php

namespace Dhii\Invocation\UnitTest;

use OutOfRangeException;
use Xpmock\TestCase;
use Dhii\Invocation\MapCallablesToCodesCapableTrait as TestSubject;
use ArrayIterator;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class MapCallablesToCodesCapableTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Invocation\MapCallablesToCodesCapableTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return object
     */
    public function createInstance($methods = [])
    {
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                ->getMockForTrait();

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
     * Retrieves a mapping as a 2-element array.
     *
     * If you have a mapping like this:
     * ```
     * [
     *      'name'  => 'John',
     *      'age'   => 66
     * ]
     * ```
     *
     * Invoking this function with the above map and the key `0` and `1` would return
     * `['name', 'John']` and `['age', 66]` respectively.
     *
     * @since [*next-version*]
     *
     * @param $array The array, for which to get the mapping.
     * @param $index int The index, for which to get the mapping.
     *
     * @return array An array with two elements, where the first element
     *               is the key at the specified index, and the second element is the value of that index.
     */
    public function getMappingAsArray(array $array, $index)
    {
        if (!is_numeric($index)) {
            throw new \InvalidArgumentException('Index must be numeric');
        }

        $index = intval($index);
        $keys = array_keys($array);

        if (!isset($keys[$index])) {
            throw new OutOfRangeException(sprintf('No key exists at index #%1$s', $index));
        }

        $key = $keys[$index];
        $value = $array[$key];

        return [$key, $value];
    }

    /**
     * Tests whether the `_mapCallablesToCodes()` method works as expected.
     *
     * @since [*next-version*]
     */
    public function testMapCallablesToCodes()
    {
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);
        $map = [
            uniqid('code-1') => function () {},
            uniqid('code-2') => function () {},
        ];
        $traversable = new ArrayIterator($map);
        $args = function ($index) use ($map) {
            return $this->getMappingAsArray($map, $index);
        };

        $subject->expects($this->exactly(count($map)))
                ->method('_mapCallableToCode')
                ->withConsecutive($args(0), $args(1));

        $_subject->_mapCallablesToCodes($traversable);
    }
}
