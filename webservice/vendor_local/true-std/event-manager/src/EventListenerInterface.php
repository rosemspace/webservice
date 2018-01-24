<?php

namespace TrueStd\EventManager;

interface EventListenerInterface
{
    public function setLimit(int $limit) : void;

    public function process(EventInterface $event);
}
