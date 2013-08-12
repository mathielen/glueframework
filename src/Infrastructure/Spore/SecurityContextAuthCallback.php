<?php
namespace Infrastructure\Spore;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Symfony\Component\Security\Core\SecurityContextInterface;

class SecurityContextAuthCallback
{

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function userHasRole($roles)
    {
        if (empty($roles)) {
            return true;
        }

        try {
            foreach ($roles as $role) {
                if ($this->securityContext->isGranted($role)) {
                    return true;
                }
            }
        } catch (AuthenticationException $e) {
            return false;
        }

        return false;
    }

}
