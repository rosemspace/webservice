<?php

namespace Rosem\Component\Authentication\Http\Server;

use Fig\Http\Message\{
    RequestMethodInterface, StatusCodeInterface
};
use Psr\Http\Message\{
    ResponseFactoryInterface, ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Rosem\Contract\Authentication\{
    UserFactoryInterface, UserInterface
};
use function call_user_func;

class AuthenticationMiddleware extends AbstractAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Bearer';

    protected $identityParameter;

    protected $passwordParameter;

    protected $loginUri;

    protected $loggedInUri;

    /**
     * AuthenticationMiddleware constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param UserFactoryInterface     $userFactory
     * @param callable                 $userPasswordResolver
     * @param string                   $identityParameter
     * @param string                   $passwordParameter
     * @param string                   $loginUri
     * @param null|string              $loggedInUri
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UserFactoryInterface $userFactory,
        callable $userPasswordResolver,
        string $identityParameter = 'username',
        string $passwordParameter = 'password',
        ?string $loginUri = '/login',
        ?string $loggedInUri = '/'
    ) {
        parent::__construct($responseFactory, $userFactory, $userPasswordResolver);

        $this->identityParameter = $identityParameter;
        $this->passwordParameter = $passwordParameter;
        $this->loginUri = $loginUri;
        $this->loggedInUri = $loggedInUri;
    }

    private function setLoginUri(string $uri): void
    {
        $this->loginUri = $uri;
    }

    /**
     * @param string $uri
     *
     * @return AuthenticationMiddleware
     */
    public function withLoginUri(string $uri): self
    {
        $new = clone $this;
        $new->setLoginUri($uri);

        return $new;
    }

    private function setLoggedInUri(string $uri): void
    {
        $this->loggedInUri = $uri;
    }

    /**
     * @param string $uri
     *
     * @return AuthenticationMiddleware
     */
    public function withLoggedInUri(string $uri): self
    {
        $new = clone $this;
        $new->setLoggedInUri($uri);

        return $new;
    }

    private function setIdentityParameter(string $identityParameter): void
    {
        $this->identityParameter = $identityParameter;
    }

    public function withIdentityParameter(string $identityParameter): self
    {
        $new = clone $this;
        $new->setIdentityParameter($identityParameter);

        return $new;
    }

    private function setPasswordParameter(string $passwordParameter): void
    {
        $this->passwordParameter = $passwordParameter;
    }

    public function withPasswordParameter(string $passwordParameter): self
    {
        $new = clone $this;
        $new->setPasswordParameter($passwordParameter);

        return $new;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        $user = $this->authenticate($request);

        if ($user) {
            $response = $requestHandler->handle($request->withAttribute(UserInterface::class, $user));

            if ($this->loggedInUri && $request->getUri()->getPath() !== $this->loggedInUri) {
                return $response->withStatus(StatusCodeInterface::STATUS_FOUND)
                    ->withHeader('Location', $this->loggedInUri);
            }

            return $response;
        }

        if ($this->loginUri && $request->getMethod() === RequestMethodInterface::METHOD_GET
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
        $identity = $session->get('identity');

        if (!$identity) {
            if ($request->getMethod() !== RequestMethodInterface::METHOD_POST) {
                return null;
            }

            $body = $request->getParsedBody();

            if (empty($body[$this->identityParameter]) || empty($body[$this->passwordParameter])) {
                return null;
            }

            $identity = $body[$this->identityParameter];
            $password = call_user_func($this->userPasswordResolver, $identity);

            if (!$password || $password !== $body[$this->passwordParameter]) {
                return null;
            }

            $session->set('identity', $identity);
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
        $response = $this->responseFactory->createResponse(StatusCodeInterface::STATUS_FOUND);

        if (null !== $this->loginUri) {
            return $response->withHeader('Location', $this->loginUri);
        }

        return $response;
    }
}
