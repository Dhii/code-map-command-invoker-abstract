<?php

namespace Dhii\Invocation;

use Dhii\Invocation\Exception\InvocationFailureExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use OutOfRangeException;
use Traversable;

/**
 * Abstract functionality for invokers that map command codes to invocables.
 *
 * @since [*next-version*]
 */
trait InvokeCapableByCodeTrait
{
    /**
     * Invoke a command.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $command The command to invoke.
     *                                   Its string value will be used to find the mapped callable.
     * @param array|Traversable $args    The arguments to invoke the command with.
     *
     * @throws InvocationFailureExceptionInterface If the command could not be invoked.
     *
     * @return mixed The result of the invocation.
     */
    protected function _invoke($command, $args)
    {
        $args = $this->_normalizeArray($args);

        try {
            return $this->_invokeByCode($command, $args);
        } catch (OutOfRangeException $e) {
            throw $this->_createInvocationFailureException($this->__('Could not invoke callable'), null, $e, $command, $args);
        }
    }

    /**
     * Invokes functionality by code.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $code The code of the functionality to invoke.
     * @param array             $args The args to invoke with.
     *
     * @throws OutOfRangeException If no callable corresponds to the given code.
     *
     * @return mixed The result of the invocation.
     */
    abstract protected function _invokeByCode($code, array $args);

    /**
     * Creates a new Invocation Failure exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param string|Stringable      $command  The command that failed invocation, if any.
     * @param array|null             $args     The invocation arguments, if any.
     *
     * @return InvocationFailureExceptionInterface The new exception.
     */
    abstract protected function _createInvocationFailureException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $command = null,
        $args = null
    );

    /**
     * Normalizes a value into an array.
     *
     * @since [*next-version*]
     *
     * @param array|Traversable $value The value to normalize.
     *
     * @throws InvalidArgumentException If value cannot be normalized.
     *
     * @return array The normalized value.
     */
    abstract protected function _normalizeArray($value);

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
