<?php

namespace TrueStd\EventManager;

interface EventListenerInterface
{
    public function setLimit(int $limit = INF) : void;

    public function process(EventInterface $event);
}
