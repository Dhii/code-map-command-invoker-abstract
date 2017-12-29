<?php

namespace Dhii\Invocation;

use InvalidArgumentException;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use Traversable;

trait MapCallablesToCodesCapableTrait
{
    /**
     * Maps a list of callables to their codes.
     *
     * @since [*next-version*]
     *
     * @param array|Traversable $map The map, where keys are codes, and values are callables.
     */
    protected function _mapCallablesToCodes($map)
    {
        if (!is_array($map) && !($map instanceof Traversable)) {
            throw $this->_createInvalidArgumentException($this->__('Not a valid map'));
        }

        foreach ($map as $_code => $_callable) {
            $this->_mapCallableToCode($_code, $_callable);
        }
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
    abstract protected function _mapCallableToCode($code, callable $callable);

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
}
