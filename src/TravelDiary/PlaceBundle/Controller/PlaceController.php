<?php

namespace TravelDiary\PlaceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\PlaceBundle\Form\PlaceType;

/**
 * Place controller.
 *
 */
class PlaceController extends Controller
{
    /**
     * @param $tripId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($tripId)
    {
        $em = $this->getDoctrine()->getManager();

        $trip = $em->getRepository('TDTripBundle:Trip')->find($tripId);

        $entities = $em->getRepository('TDPlaceBundle:Place')->findByTrip($trip);

        return $this->render('TDPlaceBundle:Place:index.html.twig', array(
            'entities' => $entities,
            'tripId' => $tripId,
            'trip' => $trip
        ));
    }
    /**
     * Creates a new Place entity.
     *
     */
    public function createAction($tripId, Request $request)
    {
        $entity = new Place();

        $em = $this->getDoctrine()->getManager();

        $trip = $em->getRepository('TDTripBundle:Trip')->find($tripId);

        $entity->setTrip($trip);
        $entity->setUser($this->getUser());

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();

            $this->get('city_resolver')->resolve($entity);

            return $this->redirect($this->generateUrl('trip_place', array(
                'tripId' => $tripId
            )));
        }

        return $this->render('TDPlaceBundle:Place:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
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

    /**
     * Displays a form to create a new Place entity.
     *
     */
    public function newAction($tripId)
    {
        $entity = new Place();
        $em = $this->getDoctrine()->getManager();

        $trip = $em->getRepository('TDTripBundle:Trip')->find($tripId);

        $entity->setTrip($trip);
        $entity->setUser($this->getUser());
        $form   = $this->createCreateForm($entity);

        return $this->render('TDPlaceBundle:Place:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'tripId' => $tripId
        ));
    }

    /**
     * Displays a form to create a new Place entity.
     *
     */
    public function newTestAction($tripId)
    {
        $entity = new Place();
        $em = $this->getDoctrine()->getManager();

        $trip = $em->getRepository('TDTripBundle:Trip')->find($tripId);

        $entity->setTrip($trip);
        $entity->setUser($this->getUser());
        $form   = $this->createCreateForm($entity);

        return $this->render('TDPlaceBundle:Place:newTest.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'tripId' => $tripId
        ));
    }

    /**
     * Finds and displays a Place entity.
     *
     */
    public function showAction($tripId, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TDPlaceBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Place entity.');
        }

        $deleteForm = $this->createDeleteForm($tripId, $id);

        return $this->render('TDPlaceBundle:Place:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'tripId' => $tripId
        ));
    }

    /**
     * Displays a form to edit an existing Place entity.
     *
     */
    public function editAction($tripId, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TDPlaceBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Place entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($tripId, $id);

        return $this->render('TDPlaceBundle:Place:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'tripId' => $tripId
        ));
    }

    /**
    * Creates a form to edit a Place entity.
    *
    * @param Place $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Place $entity)
    {
        $form = $this->createForm(new PlaceType(), $entity, array(
            'action' => $this->generateUrl('trip_place_update', array(
                    'id' => $entity->getId(),
                    'tripId' => $entity->getTrip()->getId()
                )),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Place entity.
     *
     */
    public function updateAction($tripId, Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TDPlaceBundle:Place')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Place entity.');
        }

        $deleteForm = $this->createDeleteForm($tripId, $id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('trip_place', array(
                'tripId' => $tripId
            )));
        }

        return $this->render('TDPlaceBundle:Place:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'tripId' => $tripId
        ));
    }
    /**
     * Deletes a Place entity.
     *
     */
    public function deleteAction($tripId, Request $request, $id)
    {
        $form = $this->createDeleteForm($tripId, $id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TDPlaceBundle:Place')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Place entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('trip_place', array('tripId' => $tripId)));
    }

    /**
     * Creates a form to delete a Place entity by id.
     *
     * @param mixed $tripId The entity id
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($tripId, $id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('trip_place_delete', array(
                'id' => $id,
                'tripId' => $tripId
            )))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
