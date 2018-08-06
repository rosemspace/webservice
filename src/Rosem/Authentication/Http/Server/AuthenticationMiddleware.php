<?php

namespace Rosem\Authentication\Http\Server;

use Fig\Http\Message\{
    RequestMethodInterface, StatusCodeInterface
};
use Psr\Http\Message\{
    ResponseFactoryInterface, ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Rosem\Psr\Authentication\IdentityInterface;
use Rosem\Psr\Authentication\UserFactoryInterface;
use Rosem\Psr\Authentication\UserInterface;
use function call_user_func;

class AuthenticationMiddleware extends AbstractAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Bearer';

    protected $loginUri;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UserFactoryInterface $userFactory,
        callable $userPasswordResolver,
        ?callable $userRolesResolver = null,
        ?callable $userDetailsResolver = null,
        string $loginUri = '/login'
    ) {
        parent::__construct($responseFactory, $userFactory, $userPasswordResolver, $userRolesResolver,
            $userDetailsResolver);

        $this->loginUri = $loginUri;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        $user = $this->authenticate($request);

        if ($user) {
            return $requestHandler->handle($request->withAttribute(UserInterface::class, $user));
        }

        if ($request->getMethod() === RequestMethodInterface::METHOD_GET
            && $request->getUri()->getPath() === $this->loginUri
        ) {
            return $requestHandler->handle($request);
        }

        return $this->createUnauthorizedResponse();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return UserInterface|null
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $identity = $session->get(IdentityInterface::class);

        if (!$identity) {
            if ($request->getMethod() !== RequestMethodInterface::METHOD_POST) {
                return null;
            }

            $body = $request->getParsedBody();

            if (empty($body['username']) || empty($body['password'])) {
                return null;
            }

            $identity = $body['username'];
            $password = call_user_func($this->userPasswordResolver, $identity);

            if (!$password || $password !== $body['password']) {
                return null;
            }

            $session->set(IdentityInterface::class, $identity);
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
        return $this->responseFactory->createResponse(StatusCodeInterface::STATUS_FOUND)
            ->withHeader('Location', $this->loginUri);
    }
}
