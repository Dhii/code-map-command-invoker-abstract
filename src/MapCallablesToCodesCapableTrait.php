<?php

namespace Dhii\Invocation;

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
}
