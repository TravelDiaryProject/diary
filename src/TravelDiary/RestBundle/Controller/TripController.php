<?php

namespace TravelDiary\RestBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use TravelDiary\RestBundle\Representation\Trip\TripRepresentation;
use TravelDiary\TripBundle\Entity\Trip;

class TripController extends FOSRestController
{
    /**
     * @Rest\Get("/trip/{tripId}")
     *
     * @param int $tripId
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getTripAction($tripId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

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

        $result = TripRepresentation::listItem(
            $trip,
            $this->get('user_by_request_resolver')->resolve($this->getRequest())
        );

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
        $trips = $em->getRepository('TDTripBundle:Trip')->getMyTrips($user);

        $result = array();

        foreach ($trips as $trip) {
            $result[] = TripRepresentation::listItem($trip, $user);
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

        $result = TripRepresentation::listItem($trip, $user);

        $view = $this->view($result, 201);

        return $this->handleView($view);
    }
}
