<?php

namespace Dhii\Invocation;

use OutOfRangeException;
use InvalidArgumentException;
use Dhii\Util\String\StringableInterface as Stringable;
use Traversable;

/**
 * Functionality for mapping callables to codes.
 *
 * @since [*next-version*]
 */
trait InvokeByCodeCapableTrait
{
    /**
     * Invokes functionality by code.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $code The code of the functionality to invoke.
     * @param array|Traversable $args The args to invoke with.
     *
     * @throws OutOfRangeException If no callable corresponds to the given code.
     * @throws OutOfRangeException If the callable is not callable.
     *
     * @return mixed The result of the invocation.
     */
    protected function _invokeByCode($code, $args)
    {
        $callable = $this->_getCallableByCode($code);

        return $this->_invokeCallable($callable, $args);
    }

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
    abstract protected function _getCallableByCode($code);

    /**
     * Invokes a callable.
     *
     * @since [*next-version*]
     *
     * @param callable          $callable The callable to invoke.
     * @param array|Traversable $args     The arguments to invoke the callable with.
     *
     * @throws InvalidArgumentException If the callable is not callable.
     *
     * @return mixed The result of the invocation.
     */
    abstract protected function _invokeCallable($callable, $args);
}
