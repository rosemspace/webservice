<?php

namespace TrueCode\EventManager;

use Psrnext\EventManager\EventInterface;
use Psrnext\EventManager\EventListenerInterface;

class EventListener implements EventListenerInterface
{
    /**
     * The listener.
     *
     * @var callable
     */
    protected $listener;

    /**
     * @var int
     */
    protected $priority;

    /**
     * Limit of processes.
     *
     * @var int
     */
    protected $limit = INF;

    public function __construct(callable $listener, int $priority = 0)
    {
        $this->listener = $listener;
        $this->priority = $priority;
    }

    public function getPriority() : int
    {
        return $this->priority;
    }

    public function getCallable() : callable
    {
        return $this->listener;
    }

    public function setLimit(int $limit) : void
    {
        $this->limit = $limit;
    }

    public function process(EventInterface $event)
    {
        if ($this->limit) {
            --$this->limit;

            return call_user_func($this->listener, $event);
        }

        return null;
    }
}
