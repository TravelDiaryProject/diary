<?php

namespace TravelDiary\RestBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TravelDiary\GeoBundle\Entity\City;
use TravelDiary\GeoBundle\Entity\Country;

class GeoController extends FOSRestController
{
    /**
     * @Rest\Get("/cities")
     *
     * @return JsonResponse
     */
    public function getAllCitiesAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $search = $request->query->get('search');

        if ('' !== $search) {
            $data = $em->getRepository('TDGeoBundle:City')
                ->createQueryBuilder('city')
                ->where('city.name LIKE :search')
                ->setParameter('search', $search.'%')
                ->getQuery()
                ->getResult();
        } else {
            /** @var City[] $data */
            $data = $em->getRepository('TDGeoBundle:City')->findAll();
        }

        $result = array();

        foreach ($data as $entity) {
            $result[] = array(
                'id'    => $entity->getId(),
                'name' => $entity->getName(),
            );
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/countries")
     *
     * @return JsonResponse
     */
    public function getAllCountriesAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $search = $request->query->get('search');

        if ('' !== $search) {
            $data = $em->getRepository('TDGeoBundle:Country')
                ->createQueryBuilder('country')
                ->where('country.name LIKE :search')
                ->setParameter('search', $search.'%')
                ->getQuery()
                ->getResult();
        } else {
            /** @var Country[] $data */
            $data = $em->getRepository('TDGeoBundle:Country')->findAll();
        }

        $result = array();

        foreach ($data as $entity) {
            $result[] = array(
                'id'    => $entity->getId(),
                'name' => $entity->getName(),
            );
        }

        $view = $this->view($result, 200);

        return $this->handleView($view);
    }
}
