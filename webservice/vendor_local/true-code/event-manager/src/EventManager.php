<?php

namespace TrueCode\EventManager;

use TrueStd\EventManager\{
    EventInterface, EventManagerInterface
};

class EventManager implements EventManagerInterface
{
    /**
     * @var array
     */
    protected $listeners = [];

    protected function normalizePriority($event, $priority, int $count = 1) : string
    {
        if (isset($event[$priority])) {
            [$priority] = explode('-', $priority, 2);

            return $this->normalizePriority($event, "$priority-$count", ++$count);
        }

        return $priority;
    }

    /**
     * Attaches a listener to an event
     *
     * @param string   $event    the event to attach too
     * @param callable $callback a callable function
     * @param int      $priority the priority at which the $callback executed
     *
     * @return bool true on success false on failure
     */
    public function attach(string $event, callable $callback, int $priority = 0)
    {
        if (! isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        } else {
            $priority = $this->normalizePriority($this->listeners[$event], $priority);
        }

        $this->listeners[$event][$priority] = $callback;

        return true;
    }

    /**
     * Detaches a listener from an event
     *
     * @param string   $event    the event to attach too
     * @param callable $callback a callable function
     *
     * @return bool true on success false on failure
     */
    public function detach(string $event, callable $callback)
    {
        if (isset($this->listeners[$event])) {
            $index = \array_search($callback, $this->listeners[$event], true);

            if (false !== $index) {
                unset($this->listeners[$event][$index]);

                if (\count($this->listeners[$event]) === 0) {
                    unset($this->listeners[$event]);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Clear all listeners for a given event
     *
     * @param  string $event
     *
     * @return void
     */
    public function clearListeners(?string $event = null) : void
    {
        if (null === $event) {
            $this->listeners = [];
        } else {
            unset($this->listeners[$event]);
        }
    }

    /**
     * Trigger an event
     * Can accept an EventInterface or will create one if not passed
     *
     * @param  string|EventInterface $event
     * @param  object|string         $target
     * @param  array|object          $argv
     *
     * @return mixed
     * @throws \Exception
     */
    public function trigger($event, $target = null, $argv = [])
    {
        $result = true;

        if (is_string($event)) {
            $eventInstance = new Event;
            $eventInstance->setTarget($target);
            $eventInstance->setParams($argv);
        } elseif ($event instanceof EventInterface) {
            $eventInstance = $event;
        } else {
            throw new \Exception('Event should be a string or should implement ' . EventInterface::class);
        }

        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                $result = $listener($eventInstance);

                if ($eventInstance->isPropagationStopped()) {
                    break;
                }
            }
        }

        return $result;
    }
}
