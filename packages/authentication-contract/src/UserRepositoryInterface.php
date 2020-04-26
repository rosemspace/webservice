<?php

namespace Rosem\Contract\Authentication;

interface UserRepositoryInterface
{
    public function identifyUser(string $identity): ?UserInterface;
}
