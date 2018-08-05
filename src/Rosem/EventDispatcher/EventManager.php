<?php

namespace Rosem\EventDispatcher;

use Rosem\Psr\EventDispatcher\{
    EventInterface, EventListenerInterface, EventManagerInterface
};

class EventManager implements EventManagerInterface
{
    /**
     * @var EventListenerCollection[]
     */
    protected $listeners = [];

    /**
     * Attaches a listener to an event
     *
     * @param string $event the event to attach too
     * @param callable $callback a callable function
     * @param int $priority the priority at which the $callback executed
     *
     * @return EventListenerInterface
     */
    public function attach(string $event, callable $callback, int $priority = 0): EventListenerInterface
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = new EventListenerCollection;
        }

        return $this->listeners[$event][] = new EventListener($callback, $priority);
    }

    /**
     * Detaches a listener from an event
     *
     * @param string $event the event to attach too
     * @param callable $callback a callable function
     *
     * @return bool true on success false on failure
     */
    public function detach(string $event, callable $callback): bool
    {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event]->getList() as $index => $listener) {
                if ($listener->getCallable() === $callback) {
                    unset($this->listeners[$event][$index]);

                    if (\count($this->listeners[$event]) === 0) {
                        unset($this->listeners[$event]);
                    }

                    return true;
                }
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
    public function clearListeners(?string $event = null): void
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
     * @param  object|string $target
     * @param  array|object $argv
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function trigger($event, $target = null, $argv = [])
    {
        $result = true;

        if (\is_string($event)) {
            $name = $event;
            $event = new Event;
            $event->setName($name);
            $event->setTarget($target);
            $event->setParams($argv);
        } elseif (!$event instanceof EventInterface) {
            throw new \InvalidArgumentException('Event should be a string or should implement ' .
                EventInterface::class);
        }

        if (isset($this->listeners[$event->getName()])) {
            foreach ($this->listeners[$event->getName()]->getList() as $listener) {
                $result = $listener->process($event);

                if ($event->isPropagationStopped()) {
                    break;
                }
            }
        }

        return $result;
    }
}
