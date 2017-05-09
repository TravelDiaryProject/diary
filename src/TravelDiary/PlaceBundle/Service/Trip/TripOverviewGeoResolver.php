<?php

namespace TravelDiary\PlaceBundle\Service\Trip;

use Doctrine\ORM\EntityManager;
use TravelDiary\TripBundle\Entity\Trip;

/**
 * Class TripOverviewGeoResolver
 */
class TripOverviewGeoResolver
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Trip $trip
     *
     * @return string
     */
    public function resolve(Trip $trip)
    {
        $places = $this->em->getRepository('TDPlaceBundle:Place')
            ->findBy([
                'trip' => $trip
            ], ['shootedAt' => 'ASC']);

        $geo = [
            'countries' => [],
            'cities' => [],
        ];

        foreach ($places as $place) {
            $country = $place->getCountryName();
            $city = $place->getCityName();

            if ($country) {
                $geo['countries'][$country] = $country;
            }

            if ($city) {
                $geo['cities'][$city] = $city;
            }
        }

        if (1 < count($geo['countries'])) {
            return implode(' - ', $geo['countries']);
        }

        if (1 < count($geo['cities'])) {
            return sprintf(
                '%s (%s)',
                implode(' - ', $geo['cities']),
                end($geo['countries']));
        }

        if (1 === count($geo['cities'])) {
            return sprintf(
                '%s (%s)',
                end($geo['cities']),
                end($geo['countries']));
        }

        return '';
    }
}
