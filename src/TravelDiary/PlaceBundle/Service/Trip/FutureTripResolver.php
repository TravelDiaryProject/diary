<?php

namespace TravelDiary\PlaceBundle\Service\Trip;

use Doctrine\ORM\EntityManager;
use TravelDiary\GeoBundle\Entity\City;
use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\TripBundle\Entity\Trip;
use TravelDiary\UserBundle\Entity\User;

/**
 * Class FutureTripResolver
 */
class FutureTripResolver
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * CityResolver constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @param City|null $city
     *
     * @return Trip
     *
     * @throws \Exception
     */
    public function resolve(User $user, City $city = null)
    {
        if ($city) {
            $trip = $this->em->getRepository('TDTripBundle:Trip')
                ->findOneBy([
                    'city' => $city,
                    'isFuture' => true
                ]);

            if (!$trip) {
                $trip = new Trip();
                $trip->setTitle($city->getName());
                $trip->setCity($city);
                $trip->setUser($user);
                $trip->setIsFuture(true);
                $this->em->persist($trip);
                $this->em->flush($trip);
            }
        } else {
            $trip = $this->em->getRepository('TDTripBundle:Trip')
                ->findOneBy([
                    'title' => Trip::UNKNOWN_CITY_NAME,
                    'isFuture' => true
                ]);

            if (!$trip) {
                $trip = new Trip();
                $trip->setTitle(Trip::UNKNOWN_CITY_NAME);
                $trip->setUser($user);
                $trip->setIsFuture(true);
                $this->em->persist($trip);
                $this->em->flush($trip);
            }
        }

        return $trip;
    }
}