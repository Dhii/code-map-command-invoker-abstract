<?php

namespace Dhii\Invocation;

/**
 * Abstract functionality for invokers that map command codes to invocables.
 *
 * @since [*next-version*]
 */
abstract class AbstractCodeMapInvoker
{
    /*
     * Adds internal code to callable mapping capabilities.
     *
     * @since [*next-version*]
     */
    use CodeMapAwareTrait;

    /*
     * Adds functionality for invoking an arbitrary callable.
     *
     * @since [*next-version*]
     */
    use InvokeCallableCapableTrait;

    /*
     * Adds functionality for invoking a mapped callable by code.
     *
     * @since [*next-version*]
     */
    use InvokeByCodeCapableTrait;

    /*
     * Adds invocation ability which works with a functionality code.
     *
     * @since [*next-version*]
     */
    use InvokeCapableByCodeTrait;

    /**
     * Parameter-less constructor.
     *
     * Invoke this in actual constructor.
     *
     * @since [*next-version*]
     */
    protected function _construct()
    {
        $this->callableCodeMap = [];
    }
}
