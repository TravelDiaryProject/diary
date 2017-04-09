<?php

namespace TravelDiary\RestBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\PlaceBundle\Form\PlaceType;
use TravelDiary\RestBundle\Representation\Place\PlaceRepresentation;

class PlaceController extends FOSRestController
{
    /**
     * @Rest\Get("/top_places")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTopPlacesAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $cityId = (int) $request->query->get('city_id');

        if (0 < $cityId) {
            $data = $em->getRepository('TDPlaceBundle:Place')->findBy(
                ['city' => $cityId],
                ['likes' => 'DESC']
            );
        } else {
            /** @var Place[] $data */
            $data = $em->getRepository('TDPlaceBundle:Place')->findAllTopPlaces();
        }

        $result = array();

        foreach ($data as $entity) {
            $result[] = PlaceRepresentation::listItem($entity);
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/my/places")
     *
     * @param Request $request
     *
     * @return string
     */
    public function getMyPlacesAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $cityId = (int) $request->query->get('city_id');

        if (0 < $cityId) {
            $data = $em->getRepository('TDPlaceBundle:Place')->findBy(
                [
                    'city' => $cityId,
                    'user' => $user
                ],
                ['likes' => 'DESC']
            );
        } else {
            /** @var Place[] $data */
            $data = $em->getRepository('TDPlaceBundle:Place')->findBy(
                ['user' => $user],
                ['likes' => 'DESC']
            );
        }

        $result = array();

        foreach ($data as $entity) {
            $result[] = PlaceRepresentation::listItem($entity);
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/trip/{tripId}/places")
     *
     * @param int $tripId
     *
     * @return string
     */
    public function getPlacesByTripIdAction($tripId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Place[] $data */
        $data = $em->getRepository('TDPlaceBundle:Place')->findBy(
            ['trip' => $tripId],
            ['likes' => 'DESC']
        );

        $result = array();

        foreach ($data as $entity) {
            $result[] = PlaceRepresentation::listItem($entity);
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/my/place")
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws \Exception
     */
    public function postPlaceAction(Request $request)
    {
        $entity = new Place();

        $em = $this->getDoctrine()->getManager();

        $tripId = (int) $request->request->get('tripId');

        if (false === 0 < $tripId) {
            $result = ['error' => sprintf('Trip with id %d not found', $tripId)];

            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        $trip = $em->getRepository('TDTripBundle:Trip')->find($tripId);

        if (null === $trip) {
            $view = $this->view(sprintf('Trip with id %d not found', $tripId), 404);

            return $this->handleView($view);
        }

        $user = $this->getUser();

        $entity->setTrip($trip);
        $entity->setUser($user);

        $form = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('city_resolver')->resolve($entity);

            $result = PlaceRepresentation::listItem($entity);
        } else {
            $result = array(
                'error' => (string) $form->getErrors(true, false)
            );
        }

        $view = $this->view($result, 201);

        return $this->handleView($view);
    }

    /**
     * Creates a form to create a Place entity.
     *
     * @param Place $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Place $entity)
    {
        $form = $this->createForm(new PlaceType(), $entity, array(
            'action' => $this->generateUrl('trip_place_create', array('tripId' => $entity->getTrip()->getId())),
            'method' => 'POST',
        ));

        return $form;
    }
}
