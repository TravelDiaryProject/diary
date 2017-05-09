<?php

namespace TravelDiary\PlaceBundle\Service\Trip;

use Doctrine\ORM\EntityManager;
use TravelDiary\TripBundle\Entity\Trip;

/**
 * Class TripOverviewDatesResolver
 */
class TripOverviewDatesResolver
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

        $dates = [
            'years' => [],
            'months' => [],
            'days' => []
        ];

        foreach ($places as $place) {
            if ($place->getShootedAt() instanceof \DateTime) {
                $dates['years'][$place->getShootedAt()->format('Y')] = $place->getShootedAt();
                $dates['months'][$place->getShootedAt()->format('m')] = $place->getShootedAt();
                $dates['days'][$place->getShootedAt()->format('d')] = $place->getShootedAt();
            }
        }

        if (1 < count($dates['years'])) {
            return sprintf('%s - %s', reset($dates['years'])->format('M Y'), end($dates['years'])->format('M Y'));
        }

        if (1 < count($dates['months']) && 1 === count($dates['years'])) {
            return sprintf(
                '%s - %s %s',
                reset($dates['months'])->format('M'),
                end($dates['months'])->format('M'),
                end($dates['months'])->format('Y')
            );
        }

        if (1 < count($dates['days']) && 1 === count($dates['months'])) {
            return sprintf(
                '%s-%s %s %s',
                reset($dates['days'])->format('d'),
                end($dates['days'])->format('d'),
                end($dates['days'])->format('M'),
                end($dates['days'])->format('Y')
            );
        }

        if (1 === count($dates['days'])) {
            return sprintf(
                '%s %s %s',
                end($dates['days'])->format('d'),
                end($dates['days'])->format('M'),
                end($dates['days'])->format('Y')
            );
        }

        return '';
    }
}
