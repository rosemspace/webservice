<?php

namespace Rosem\Authentication;

use Psr\Http\Message\ServerRequestInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;

abstract class AbstractAuthentication
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var callable The function to get a password by a username.
     */
    protected $getPassword;

    /**
     * @var string
     */
    protected $realm = 'Login';

    /**
     * @var string|null
     */
    protected $attribute = 'userIdentity';

    /**
     * Define de users.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param callable                 $getPassword function (string $username) {...}
     */
    public function __construct(ResponseFactoryInterface $responseFactory, callable $getPassword)
    {
        $this->responseFactory = $responseFactory;
        $this->getPassword = $getPassword;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    abstract public function authenticate(ServerRequestInterface $request): ?string;

    /**
     * Set the realm value.
     *
     * @param string $realm
     */
    public function setRealm(string $realm): void
    {
        $this->realm = $realm;
    }

    /**
     * Set the attribute name to store the user name.
     *
     * @param string $attribute
     */
    public function setAttribute(string $attribute): void
    {
        $this->attribute = $attribute;
    }
}
