<?php

namespace TrueCode\EventManager;

use TrueStd\EventManager\EventInterface;
use TrueStd\EventManager\EventListenerInterface;

class EventListener implements EventListenerInterface
{
    /**
     * The listener.
     *
     * @var callable
     */
    protected $listener;

    /**
     * Limit of processes.
     *
     * @var int
     */
    protected $limit;

    public function __construct(callable $listener)
    {
        $this->listener = $listener;
    }

    protected function getRemainingLimit() : int
    {
        return $this->limit;
    }

    public function setLimit(int $limit = INF) : void
    {
        $this->limit = $limit;
    }

    public function process(EventInterface $event)
    {
        return ${$this->listener}($event);
    }
}
