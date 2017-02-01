<?php

namespace TravelDiary\RestBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\PlaceBundle\Form\PlaceType;

class PlaceController extends FOSRestController
{
    /**
     * @Rest\Get("/trip/{tripId}/places")
     *
     * @param int $tripId
     *
     * @return string
     */
    public function getTripsAction($tripId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var Place[] $data */
        $data = $em->getRepository('TDPlaceBundle:Place')->findBy(
            ['trip' => $tripId]
        );

        $result = array();

        foreach ($data as $entity) {
            $result[] = array(
                'id'    => $entity->getId(),
                'title' => $entity->getTitle(),
                'photo' => $entity->getWebPath(),
                'latitude' => $entity->getLatitude(),
                'longitude' => $entity->getLongitude()
            );
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/place")
     *
     * @param Request $request
     *
     * @return string
     */
    public function postPlaceAction(Request $request)
    {
        $entity = new Place();

        $em = $this->getDoctrine()->getManager();

        $trip = current($em->getRepository('TDTripBundle:Trip')->findAll());
        $user = current($em->getRepository('TDUserBundle:User')->findAll());

        $entity->setTrip($trip);
        $entity->setUser($user);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            $result = array(
                'id'    => $entity->getId(),
                'title' => $entity->getTitle(),
                'description' => $entity->getDescription()
            );
        } else {
            $result = array(
                'error' => 'form is not valid'
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

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }
}
