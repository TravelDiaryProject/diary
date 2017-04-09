<?php

namespace TravelDiary\RestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use TravelDiary\RatingBundle\Entity\UserLikesPlaces;

/**
 * Class RatingController
 */
class RatingController extends FOSRestController
{
    /**
     * @Rest\Post("/my/like")
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws \Exception
     */
    public function addLikeAction(Request $request)
    {
        $user = $this->getUser();

        $placeId = (int) $request->request->get('placeId');

        if (false === 0 < $placeId) {
            $result = ['error' => sprintf('Place with id %d not found', $placeId)];

            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        $em = $this->getDoctrine()->getManager();

        $place = $em->getRepository('TDPlaceBundle:Place')->find($placeId);

        if (null === $place) {
            $result = ['error' => sprintf('Place with id %d not found', $placeId)];
            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        $userLike = $em->getRepository('TDRatingBundle:UserLikesPlaces')
            ->findBy([
                'user' => $user,
                'place' => $place
            ]);

        if ($userLike) {
            $result = ['error' => sprintf('User already liked place with id %d', $placeId)];
            $view = $this->view($result, 422);

            return $this->handleView($view);
        }

        $userLike = new UserLikesPlaces();
        $userLike->setUser($user);
        $userLike->setPlace($place);
        $userLike->setCreated(new \DateTime());

        $em->persist($userLike);

        $place->setLikes($place->getLikes() + 1);

        $em->persist($place);

        $em->flush();

        $result = ['id' => $userLike->getId()];

        $view = $this->view($result, 201);

        return $this->handleView($view);
    }
}
