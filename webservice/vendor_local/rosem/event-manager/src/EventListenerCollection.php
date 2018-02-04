<?php

namespace Rosem\EventManager;

use ArrayAccess;
use Countable;

class EventListenerCollection implements ArrayAccess, Countable
{
    /**
     * @var EventListener[]
     */
    protected $listeners = [];

    /**
     * @var bool
     */
    protected $ordered = true;

    /**
     * @return \Generator|EventListener[]
     */
    public function getList()
    {
        if (! $this->ordered) {
            usort(
                $this->listeners,
                function (
                    EventListener $currentListener,
                    EventListener $nextListener
                ) {
                    return $nextListener->getPriority() <=> $currentListener->getPriority();
                }
            );
            $this->ordered = true;
        }

        foreach ($this->listeners as $listener) {
            yield $listener;
        }
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset An offset to check for.
     *
     * @return boolean true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->listeners[$offset]);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->listeners[$offset];
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($value->getPriority()) {
            $this->ordered = false;
        }

        null === $offset ? $this->listeners[] = $value : $this->listeners[$offset] = $value;
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->listeners[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->listeners);
    }
}
