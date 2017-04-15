<?php

namespace TravelDiary\RestBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\TripBundle\Entity\Trip;

/**
 * Class FutureTripsController
 */
class FutureTripsController extends FOSRestController
{
    /**
     * @Rest\Get("/my/future-trips")
     *
     * @return string
     */
    public function getMyFutureTripsAction()
    {
        $user = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Trip[] $trips */
        $trips = $em->getRepository('TDTripBundle:Trip')->findBy([
            'user' => $user,
            'isFuture' => true
        ]);

        $result = array();

        foreach ($trips as $trip) {

            /** @var Place $places */
            $place = $em->getRepository('TDPlaceBundle:Place')->findOneBy(
                ['trip' => $trip]
            );

            $photo = $place ? $place->getWebPath() : '/templates/image/noimagefound.jpg';

            $result[] = [
                'id'    => $trip->getId(),
                'title' => $trip->getTitle(),
                'photo' => $photo
            ];
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/my/add-place-to-future-trips")
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws \Exception
     */
    public function addPlaceToFutureTripsAction(Request $request)
    {
        $user = $this->getUser();

        $placeId = (int) $request->request->get('placeId');

        if (false === 0 < $placeId) {
            $result = ['error' => sprintf('Place with id %d not found', $placeId)];

            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        $em = $this->getDoctrine()->getManager();

        /** @var Place $place */
        $place = $em->getRepository('TDPlaceBundle:Place')->find($placeId);

        if (null === $place) {
            $result = ['error' => sprintf('Place with id %d not found', $placeId)];
            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        /** @var Place $futurePlace */
        $futurePlace = clone $place;

        $futureTrip = $this->get('trip_resolver')->resolve($user, $futurePlace->getCity());

        $futurePlace->setTrip($futureTrip);

        $futurePlace->setUser($user);

        $em->persist($futurePlace);
        $em->flush();

        $source = $place->getAbsolutePath();
        $target = $futurePlace->getAbsolutePath();
        $targetDir = dirname($target);

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        copy($source, $target);

        $result = ['success' => sprintf('Place with id %d was added to your future trips', $futurePlace->getId())];
        $view = $this->view($result, 201);

        return $this->handleView($view);
    }
}