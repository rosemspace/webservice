<?php

namespace Rosem\Http\Authentication;

use ArrayAccess;
use InvalidArgumentException;
use Psr\Http\Message\{
    ServerRequestInterface, ResponseInterface
};
use Psr\Http\Server\RequestHandlerInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;

use function is_array;

abstract class AbstractHttpAuthentication
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var array|ArrayAccess The available users
     */
    protected $users;

    /**
     * @var string
     */
    protected $realm = 'Login';

    /**
     * @var string|null
     */
    protected $attribute = 'User';

    /**
     * Define de users.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param array|ArrayAccess        $users [username => password]
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(ResponseFactoryInterface $responseFactory, $users)
    {
        $this->responseFactory = $responseFactory;

        if (!is_array($users) && !($users instanceof ArrayAccess)) {
            throw new InvalidArgumentException(
                'The users argument must be an array or implement the ArrayAccess interface'
            );
        }
        $this->users = $users;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return mixed
     */
    abstract public function authenticate(ServerRequestInterface $request);

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

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $requestHandler
     * @param string                  $authHeader
     *
     * @return ResponseInterface
     */
    protected function createResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $requestHandler,
        string $authHeader
    ): ResponseInterface {
        $user = $this->authenticate($request);

        if (null === $user) {
            return $this->responseFactory->createResponse(401)
                ->withHeader('WWW-Authenticate', $authHeader);
        }

        if (null !== $this->attribute) {
            $request = $request->withAttribute($this->attribute, $user);
        }

        return $requestHandler->handle($request);
    }
}
