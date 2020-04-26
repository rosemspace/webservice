<?php

namespace Rosem\Contract\Authentication;

interface UserFactoryInterface
{
    public function createUser(string $identity): UserInterface;
}
