<?php

namespace TravelDiary\PlaceBundle\Service\Trip;

use Doctrine\ORM\EntityManager;
use TravelDiary\TripBundle\Entity\Trip;

/**
 * Class TripOverviewResolver
 */
class TripOverviewResolver
{
    /**
     * @var TripOverviewGeoResolver
     */
    private $geoResolver;

    /**
     * @var TripOverviewDatesResolver
     */
    private $datesResolver;

    /**
     * @param TripOverviewGeoResolver   $geoResolver
     * @param TripOverviewDatesResolver $datesResolver
     */
    public function __construct(TripOverviewGeoResolver $geoResolver, TripOverviewDatesResolver $datesResolver)
    {
        $this->geoResolver = $geoResolver;
        $this->datesResolver = $datesResolver;
    }

    /**
     * @param Trip $trip
     *
     * @return string
     */
    public function resolve(Trip $trip)
    {
        $geoResolverResult = $this->geoResolver->resolve($trip);
        $datesResolverResult = $this->datesResolver->resolve($trip);

        $result = [];

        if ($geoResolverResult) {
            $result[] = $geoResolverResult;
        }

        if ($datesResolverResult) {
            $result[] = $datesResolverResult;
        }

        return implode(' | ', $result);
    }
}
