<?php

namespace TravelDiary\PlaceBundle\Tests\Service\Trip;

use Doctrine\ORM\EntityManager;
use TravelDiary\GeoBundle\Entity\City;
use TravelDiary\GeoBundle\Entity\Country;
use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\PlaceBundle\Entity\PlaceRepository;
use TravelDiary\PlaceBundle\Service\Trip\TripOverviewGeoResolver;
use TravelDiary\TripBundle\Entity\Trip;

/**
 * Class TripOverviewGeoResolverTest
 */
class TripOverviewGeoResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveGeo2Countries()
    {
        $trip = $this->createTrip('2 Countries');

        $places = [
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2015-02-17 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Paris', $this->createCountry('Spain')),
                new \DateTime('2016-05-24 10:10:10')
            )
        ];

        $resolver = new TripOverviewGeoResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('Ukraine - Spain', $result);
    }

    public function testResolveGeo3Countries()
    {
        $trip = $this->createTrip('3 Countries');

        $places = [
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2015-02-17 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Paris', $this->createCountry('Spain')),
                new \DateTime('2016-05-24 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('France')),
                new \DateTime('2017-05-24 10:10:10')
            )
        ];

        $resolver = new TripOverviewGeoResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('Ukraine - Spain - France', $result);
    }

    public function testResolveGeo2Cities1Country()
    {
        $trip = $this->createTrip('2Cities1Country');

        $places = [
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2015-02-17 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Lviv', $this->createCountry('Ukraine')),
                new \DateTime('2016-05-24 10:10:10')
            )
        ];

        $resolver = new TripOverviewGeoResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('Kiev - Lviv (Ukraine)', $result);
    }

    public function testResolveGeo3Cities1Country()
    {
        $trip = $this->createTrip('3Cities1Country');

        $places = [
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2015-02-17 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Lviv', $this->createCountry('Ukraine')),
                new \DateTime('2016-05-24 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Zhitomir', $this->createCountry('Ukraine')),
                new \DateTime('2017-05-24 10:10:10')
            )
        ];

        $resolver = new TripOverviewGeoResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('Kiev - Lviv - Zhitomir (Ukraine)', $result);
    }

    public function testResolveGeo1City1Country()
    {
        $trip = $this->createTrip('1 City');

        $places = [
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2015-02-17 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2016-05-24 10:10:10')
            )
        ];

        $resolver = new TripOverviewGeoResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('Kiev (Ukraine)', $result);
    }

    public function testResolveGeoEmpty()
    {
        $trip = $this->createTrip('No places');

        $places = [];

        $resolver = new TripOverviewGeoResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('', $result);
    }

    private function createTrip($name)
    {
        $trip = new Trip();
        $trip->setTitle($name);

        return $trip;
    }

    private function createEntityManagerWithPlaceRepository(Trip $trip, array $places)
    {
        $repository = $this->getMockBuilder(PlaceRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(static::once())
            ->method('findBy')
            ->with(['trip' => $trip], ['shootedAt' => 'ASC'])
            ->willReturn($places);

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects(static::once())->method('getRepository')
            ->with('TDPlaceBundle:Place')
            ->willReturn($repository);

        return $em;
    }

    private function createPlace(City $city, \DateTime $shootedAt)
    {
        $place = new Place();
        $place->setCity($city);
        $place->setShootedAt($shootedAt);

        return $place;
    }

    private function createCity($name, Country $country)
    {
        $city = new City();
        $city->setName($name);
        $city->setCountry($country);

        return $city;
    }

    private function createCountry($name)
    {
        $country = new Country();
        $country->setName($name);

        return $country;
    }
}
