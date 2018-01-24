<?php

namespace TrueStandards\GraphQL;

interface GraphInterface
{
    public function addType(string $class, string $name, string $description) : void;

    public function getType(string $type);

    public function addQuery(string $class, string $name, string $description, string $schema = 'default') : void;

    public function addMutation(string $class, string $name, string $description, string $schema = 'default') : void;

    public function addSubscription(string $class, string $name, string $description, string $schema = 'default') : void;
}
