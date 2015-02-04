<?php

namespace TravelDiary\RestBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
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

        /** @var Trip[] $data */
        $data = $em->getRepository('TDTripBundle:Trip')->findAll();

        $result = array();

        foreach ($data as & $entity) {
            $result[] = array(
                'id'    => $entity->getId(),
                'title' => $entity->getTitle(),
                'photo' => $entity->getWebPath()
            );
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/trip")
     *
     * @param Request $request
     *
     * @return string
     */
    public function postTripAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getEntityManager();

        $user = current($em->getRepository('TDUserBundle:User')->findAll());

        $trip = new Trip();

        $trip->setUser($user);
        $trip->setTitle(!empty($data['title']) ? $data['title'] : 'empty title');
        $trip->setDescription(!empty($data['description']) ? $data['description'] : 'empty description');

        $em->persist($trip);
        $em->flush();

        $result = array(
            'id'    => $trip->getId(),
            'title' => $trip->getTitle(),
            'description' => $trip->getDescription()
        );

        $view = $this->view($result, 201);

        return $this->handleView($view);
    }
}
