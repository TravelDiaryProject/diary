<?php

namespace TravelDiary\RestBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\TripBundle\Entity\Trip;

class TripController extends FOSRestController
{
    /**
     * @Rest\Get("/trips")
     *
     * @return string
     */
    public function getTripsAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Trip[] $trips */
        $trips = $em->getRepository('TDTripBundle:Trip')->findAll();

        $result = array();

        foreach ($trips as $trip) {

            /** @var Place $places */
            $place = $em->getRepository('TDPlaceBundle:Place')->findOneBy(
                ['trip' => $trip]
            );

            $photo = $place ? $place->getWebPath() : '/templates/image/noimagefound.jpg';
            $thumbnail = $place ? $place->getWebPathThumbnail() : '/templates/image/noimagefound.jpg';

            $result[] = [
                'id'    => $trip->getId(),
                'title' => $trip->getTitle(),
                'photo' => $photo,
                'thumbnail' => $thumbnail
            ];
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/my/trips")
     *
     * @return string
     */
    public function getMyTripsAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        /** @var Trip[] $trips */
        $trips = $em->getRepository('TDTripBundle:Trip')->findBy([
            'user' => $user
        ]);

        $result = array();

        foreach ($trips as $trip) {

            /** @var Place $places */
            $place = $em->getRepository('TDPlaceBundle:Place')->findOneBy(
                ['trip' => $trip]
            );

            $photo = $place ? $place->getWebPath() : '/templates/image/noimagefound.jpg';
            $thumbnail = $place ? $place->getWebPathThumbnail() : '/templates/image/noimagefound.jpg';

            $result[] = [
                'id'    => $trip->getId(),
                'title' => $trip->getTitle(),
                'photo' => $photo,
                'thumbnail' => $thumbnail
            ];
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/my/trip/remove")
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws \Exception
     */
    public function postTripRemoveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tripId = (int) $request->request->get('tripId');

        if (false === 0 < $tripId) {
            $result = ['error' => sprintf('Trip with id %d not found', $tripId)];

            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        $trip = $em->getRepository('TDTripBundle:Trip')->find($tripId);

        if (null === $trip) {
            $result = ['error' => sprintf('Trip with id %d not found', $tripId)];
            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        $user = $this->getUser();

        if ($user !== $trip->getUser()) {
            $result = ['error' => 'You can remove only your trips'];
            $view = $this->view($result, 422);

            return $this->handleView($view);
        }

        $em->remove($trip);
        $em->flush();

        $result = array(
            'success' => 'Trip was removed successfully'
        );

        $view = $this->view($result, 201);

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/my/trip")
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws \Exception
     */
    public function postTripAction(Request $request)
    {
        $trip = new Trip();

        $title = $request->request->get('title');

        if (!$title) {
            $result = ['error' => 'Title is required'];

            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->getUser();

        $trip->setUser($user);
        $trip->setTitle($title);
        $trip->setDescription('');

        $em->persist($trip);
        $em->flush();

        $result = array(
            'id'    => $trip->getId(),
            'title' => $trip->getTitle(),
        );

        $view = $this->view($result, 201);

        return $this->handleView($view);
    }
}
