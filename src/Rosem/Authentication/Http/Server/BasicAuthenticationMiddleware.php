<?php

namespace Rosem\Authentication\Http\Server;

use Psr\Http\Message\{
    ResponseFactoryInterface, ResponseInterface, ServerRequestInterface
};
use Rosem\Psr\Authentication\UserFactoryInterface;
use Rosem\Psr\Authentication\UserInterface;
use function call_user_func;

class BasicAuthenticationMiddleware extends AbstractAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Basic';

    /**
     * @var string
     */
    protected $realm;

    /**
     * Define de users.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param UserFactoryInterface     $userFactory
     * @param callable                 $userPasswordResolver
     * @param callable|null            $userRolesResolver
     * @param callable|null            $userDetailsResolver
     * @param string                   $realm
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UserFactoryInterface $userFactory,
        callable $userPasswordResolver,
        ?callable $userRolesResolver = null,
        ?callable $userDetailsResolver = null,
        string $realm = 'Login'
    ) {
        parent::__construct($responseFactory, $userFactory, $userPasswordResolver, $userRolesResolver,
            $userDetailsResolver);

        $this->realm = $realm;
    }

    /**
     * Check the user credentials and return the username or false.
     *
     * @param ServerRequestInterface $request
     *
     * @return UserInterface|null
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        $authHeader = $request->getHeader('Authorization');

        if (empty($authHeader)) {
            return null;
        }

        if (!preg_match(
            '/' . self::AUTHORIZATION_HEADER_PREFIX . ' (?<credentials>[a-zA-Z0-9\+\/\=]+)/',
            reset($authHeader),
            $match)
        ) {
            return null;
        }

        [$identity, $enteredPassword] = explode(':', base64_decode($match['credentials']), 2);
        $password = call_user_func($this->userPasswordResolver, $identity, $request);

        if (!$password || $password !== $enteredPassword) {
            return null;
        }

        return $this->userFactory->createUser(
            $identity,
            call_user_func($this->userRolesResolver, $identity),
            call_user_func($this->userDetailsResolver, $identity)
        );
    }

    /**
     * Create unauthorized response.
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function createUnauthorizedResponse(): ResponseInterface
    {
        return $this->responseFactory->createResponse(401)
            ->withHeader(
                'WWW-Authenticate',
                self::AUTHORIZATION_HEADER_PREFIX . ' realm="' . $this->realm . '"'
            );
    }
}
