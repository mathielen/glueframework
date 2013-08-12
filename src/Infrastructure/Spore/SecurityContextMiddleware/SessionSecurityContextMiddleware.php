<?php
namespace Infrastructure\Spore\SecurityContextMiddleware;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Slim\Middleware;
use Infrastructure\Persistence\Repository;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

class SessionSecurityContextMiddleware extends Middleware
{

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var Repository
     */
    private $sessionRepository;

    public function __construct(SecurityContextInterface $securityContext, Repository $sessionRepository)
    {
        $this->securityContext = $securityContext;
        $this->sessionRepository = $sessionRepository;
    }

    public function call()
    {
        $request = $this->app->request();

        //check x header first
        $sessionId = $request->headers('X-SESSION-ID');

        //no xheader, check get param
        if (empty($sessionId)) {
            $sessionId = $request->get('session');
        }

        if (!empty($sessionId)) {
            $session = $this->sessionRepository->get($sessionId);
            if ($session) {
                if ($session->isValid()) {
                    $token = new PreAuthenticatedToken($session->getUsername(), null, 'providerKey'); //TODO providerkey?
                    $this->securityContext->setToken($token);

                    $request->session = $session;
                } else {
                    //timeout
                    $this->sessionRepository->delete($sessionId);
                }
            }
        }

        $this->next->call();
    }

}
