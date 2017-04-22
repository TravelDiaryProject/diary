<?php

namespace TravelDiary\RestBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\UserBundle\Model\UserInterface;
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
     *
     * @throws \Exception
     */
    public function getTopPlacesAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $cityId = (int) $request->query->get('city_id');

        $data = $em->getRepository('TDPlaceBundle:Place')->findByCityIdAndUser($cityId, null);

        $user = $this->getUserFromRequest($request);

        $places = $this->createPlacesList($data, $user);

        $view = $this->view($places, 200);

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

        $data = $em->getRepository('TDPlaceBundle:Place')->findByCityIdAndUser($cityId, $user);

        $places = $this->createPlacesList($data, $user);

        $view = $this->view($places, 200);

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

        $user = $this->getUserFromRequest($this->getRequest());

        $places = $this->createPlacesList($data, $user);

        $view = $this->view($places, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/my/place/remove")
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws \Exception
     */
    public function postPlaceRemoveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $placeId = (int) $request->request->get('placeId');

        if (false === 0 < $placeId) {
            $result = ['error' => sprintf('Place with id %d not found', $placeId)];

            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        $place = $em->getRepository('TDPlaceBundle:Place')->find($placeId);

        if (null === $place) {
            $result = ['error' => sprintf('Place with id %d not found', $placeId)];
            $view = $this->view($result, 404);

            return $this->handleView($view);
        }

        $user = $this->getUser();

        if ($user !== $place->getUser()) {
            $result = ['error' => sprintf('Place with id %d not found', $placeId)];
            $view = $this->view($result, 422);

            return $this->handleView($view);
        }

        $em->remove($place);
        $em->flush();

        $result = array(
            'success' => 'Place was removed successfully'
        );

        $view = $this->view($result, 201);

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
        $place = new Place();

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

        $place->setTrip($trip);
        $place->setUser($user);

        $form = $this->createCreateForm($place);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($place);
            $em->flush();

            $this->get('place.coordinates_resolver')->resolve($place);

            $this->get('place.thumbnail_creator')->create($place);

            $this->get('city_resolver')->resolve($place);

            $result = PlaceRepresentation::listItem($place);
        } else {
            $result = array(
                'error' => (string) $form->getErrors(true, false)
            );
        }

        $view = $this->view($result, 201);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return \FOS\UserBundle\Model\UserInterface
     *
     * @throws \Exception
     */
    private function getUserFromRequest(Request $request)
    {
        try {
            $payload = $this->get('lexik_jwt_authentication.encoder')
                ->decode(
                    $this->get('lexik_jwt_authentication.extractor.authorization_header_extractor')
                        ->extract($request)
                );

            $user = $this->get('fos_user.user_manager')->findUserByUsername($payload['username']);
        } catch (\Exception $e) {
            $user = null;
        }

        return $user;
    }

    /**
     * @param UserInterface $user
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getPlaceIdsCurrentUserLiked(UserInterface $user = null)
    {
        if (!$user) {
            return [];
        }

        $results = $this->getDoctrine()->getManager()
            ->getRepository('TDRatingBundle:UserLikesPlaces')
            ->findBy(
                ['user' => $user]
            );

        $placeIds = [];

        foreach ($results as $result) {
            $placeIds[$result->getPlace()->getId()] = $result->getPlace()->getId();
        }

        return $placeIds;
    }

    /**
     * @param UserInterface $user
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getPlaceIdsCurrentUserAddedToFuture(UserInterface $user = null)
    {
        if (!$user) {
            return [];
        }

        $results = $this->getDoctrine()->getManager()
            ->getRepository('TDPlaceBundle:FuturePlaces')
            ->findBy(
                ['user' => $user]
            );

        $placeIds = [];

        foreach ($results as $result) {
            $placeIds[$result->getOriginalPlace()->getId()] = $result->getOriginalPlace()->getId();
        }

        return $placeIds;
    }

    /**
     * @param Place[]       $places
     * @param UserInterface $user
     *
     * @return array
     *
     * @throws \Exception
     */
    private function createPlacesList($places, $user = null)
    {
        $result = [];

        $placeIdsCurrentUserLiked = $this->getPlaceIdsCurrentUserLiked($user);
        $placeIdsCurrentUserAddedToFuture = $this->getPlaceIdsCurrentUserAddedToFuture($user);

        foreach ($places as $place) {
            $extra = [
                'isLiked' => (int) array_key_exists($place->getId(), $placeIdsCurrentUserLiked),
                'isInFutureTrips' => (int) array_key_exists($place->getId(), $placeIdsCurrentUserAddedToFuture),
                'isMine' => $user ? (int) ($place->getUser()->getId() === $user->getId()) : 0,
            ];

            $result[] = PlaceRepresentation::listItem($place, $extra);
        }

        return $result;
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
