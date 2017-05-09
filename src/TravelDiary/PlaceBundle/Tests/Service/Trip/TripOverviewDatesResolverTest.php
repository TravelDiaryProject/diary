<?php

namespace TravelDiary\PlaceBundle\Tests\Service\Trip;

use Doctrine\ORM\EntityManager;
use TravelDiary\GeoBundle\Entity\City;
use TravelDiary\GeoBundle\Entity\Country;
use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\PlaceBundle\Entity\PlaceRepository;
use TravelDiary\PlaceBundle\Service\Trip\TripOverviewDatesResolver;
use TravelDiary\TripBundle\Entity\Trip;

/**
 * Class TripOverviewDatesResolverTest
 */
class TripOverviewDatesResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveDatesDiffYears()
    {
        $trip = $this->createTrip('Different years');

        $places = [
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2015-02-17 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2016-05-24 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2017-05-24 10:10:10')
            )
        ];

        $resolver = new TripOverviewDatesResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('Feb 2015 - May 2017', $result);
    }

    public function testResolveDatesOneYearDiffMonths()
    {
        $trip = $this->createTrip('Different months');

        $places = [
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2017-02-17 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2017-05-24 10:10:10')
            )
        ];

        $resolver = new TripOverviewDatesResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('Feb - May 2017', $result);
    }

    public function testResolveDatesOneYearOneMonthDiffDays()
    {
        $trip = $this->createTrip('Different days');

        $places = [
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2017-05-17 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2017-05-24 10:10:10')
            )
        ];

        $resolver = new TripOverviewDatesResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('17-24 May 2017', $result);
    }

    public function testResolveDatesOneDay()
    {
        $trip = $this->createTrip('One day');

        $places = [
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2017-05-24 10:10:10')
            ),
            $this->createPlace(
                $this->createCity('Kiev', $this->createCountry('Ukraine')),
                new \DateTime('2017-05-24 12:10:10')
            )
        ];

        $resolver = new TripOverviewDatesResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

        $result = $resolver->resolve($trip);

        static::assertEquals('24 May 2017', $result);
    }

    public function testResolveDatesEmpty()
    {
        $trip = $this->createTrip('No places');

        $places = [];

        $resolver = new TripOverviewDatesResolver($this->createEntityManagerWithPlaceRepository($trip, $places));

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
