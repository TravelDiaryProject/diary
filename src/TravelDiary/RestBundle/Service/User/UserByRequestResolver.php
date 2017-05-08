<?php

namespace TravelDiary\RestBundle\Service\User;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserByRequestResolver
 */
class UserByRequestResolver implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     *
     * @return \FOS\UserBundle\Model\UserInterface|null
     */
    public function resolve(Request $request)
    {
        try {
            $payload = $this->container->get('lexik_jwt_authentication.encoder')
                ->decode(
                    $this->container->get('lexik_jwt_authentication.extractor.authorization_header_extractor')
                        ->extract($request)
                );

            $user = $this->container->get('fos_user.user_manager')->findUserByUsername($payload['username']);
        } catch (\Exception $e) {
            $user = null;
        }

        return $user;
    }
}