<?php

namespace Dhii\Invocation;

use Dhii\Util\String\StringableInterface as Stringable;
use OutOfRangeException;
use InvalidArgumentException;
use Exception as RootException;

/**
 * Functionality for mapping callables to codes.
 *
 * @since [*next-version*]
 */
trait CodeMapAwareTrait
{
    /**
     * A map of codes to callables.
     *
     * @since [*next-version*]
     *
     * @var array|null
     */
    protected $callableCodeMap;

    /**
     * Retrieves a callable by its code.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $code The code of the callable to retrieve.
     *
     * @throws OutOfRangeException If no callable corresponds to the given code.
     *
     * @return callable The callable that corresponds to the given code
     */
    protected function _getCallableByCode($code)
    {
        $code = $this->_normalizeString($code);

        if (!isset($this->callableCodeMap[$code])) {
            throw $this->_createOutOfRangeException($this->__('No such callable'), null, null, $code);
        }

        return $this->callableCodeMap[$code];
    }

    /**
     * Maps a callable to a code.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $code     The code to map the callable to.
     * @param callable          $callable The callable to map.
     *
     * @throws InvalidArgumentException If the callable is not valid.
     */
    protected function _mapCallableToCode($code, callable $callable)
    {
        $code = $this->_normalizeString($code);

        $this->callableCodeMap[$code] = $callable;
    }

    /**
     * Removes a mapping of a callable from the code.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $code The code to unmap from.
     *
     * @throws OutOfRangeException If no mapping exists for the code.
     */
    protected function _unmapCallableFromCode($code)
    {
        $code = $this->_normalizeString($code);

        if (!isset($this->callableCodeMap[$code])) {
            throw $this->_createOutOfRangeException($this->__('No such mapping'), null, null, $code);
        }

        unset($this->callableCodeMap[$code]);
    }

    /**
     * Determines if a mapping exists for a code.
     *
     * @since [*next-version*]
     *
     * @param string $code The code to check for.
     *
     * @return bool True if the mapping exists; false otherwise.
     */
    protected function _hasMappingWithCode($code)
    {
        return isset($this->callableCodeMap[$code]);
    }

    /**
     * Creates a new Out of Range exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return OutOfRangeException The new exception.
     */
    abstract protected function _createOutOfRangeException(
            $message = null,
            $code = null,
            RootException $previous = null,
            $argument = null
    );

    /**
     * Creates a new invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
            $message = null,
            $code = null,
            RootException $previous = null,
            $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see Stringable).
     *
     * @since [*next-version*]
     *
     * @param Stringable|string|int|float|bool $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);
}
