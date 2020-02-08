<?php

namespace Rosem\Component\Authentication\Middleware;

use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface
};
use Rosem\Contract\Authentication\{
    UserFactoryInterface,
    UserInterface
};

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
    protected string $realm;

    /**
     * Define de users.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param UserFactoryInterface     $userFactory
     * @param callable                 $userPasswordResolver
     * @param string                   $realm
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UserFactoryInterface $userFactory,
        callable $userPasswordResolver,
        string $realm
    ) {
        parent::__construct($responseFactory, $userFactory, $userPasswordResolver);

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
            $match
        )
        ) {
            return null;
        }

        [$identity, $enteredPassword] = explode(':', base64_decode($match['credentials']), 2);
        $password = call_user_func($this->userPasswordResolver, $identity, $request);

        if (!$password || $password !== $enteredPassword) {
            return null;
        }

        return $this->userFactory->createUser($identity);
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
