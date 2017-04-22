<?php

namespace TravelDiary\PlaceBundle\Service\Geo;

use TravelDiary\PlaceBundle\Entity\Place;
use Doctrine\ORM\EntityManager;
use Geocoder\Provider\GoogleMaps;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;
use TravelDiary\GeoBundle\Entity\Country;
use TravelDiary\GeoBundle\Entity\City;

class CityResolver
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

    public function resolve(Place $place)
    {
        if (!$place->getLatitude() || !$place->getLongitude()) {
            return;
        }

        $adapter  = new Guzzle6HttpAdapter();
        $geocoder = new GoogleMaps($adapter);

        try {
            $result = $geocoder->reverse($place->getLatitude(), $place->getLongitude());

            /** @var \Geocoder\Model\Address $geoRecord */
            $geoRecord = $result->first();

            if (!$geoRecord) {
                return;
            }

            $cityName = $geoRecord->getLocality();
            $countryName = $geoRecord->getCountry()->getName();
            $placeTitle = $geoRecord->getStreetName() . ' ' . $geoRecord->getStreetNumber();

            $country = $this->em->getRepository('TDGeoBundle:Country')->findOneBy(
                ['name' => $countryName]
            );

            if (null === $country) {
                $country = new Country();
                $country->setName($countryName);

                $this->em->persist($country);
                $this->em->flush();
            }

            $city = $this->em->getRepository('TDGeoBundle:City')->findOneBy(
                ['name' => $cityName]
            );

            if (null === $city) {
                $city = new City();
                $city->setName($cityName);
            }

            if (null === $city->getCountry()) {
                $city->setCountry($country);
            }

            if (null === $city->getCapitalLatitude()) {
                $city->setCapitalLatitude($geoRecord->getLatitude());
            }

            if (null === $city->getCapitalLongitude()) {
                $city->setCapitalLongitude($geoRecord->getLongitude());
            }

            $this->em->persist($city);
            $this->em->flush();

            $place->setCity($city);
            $place->setTitle($placeTitle);

            $this->em->persist($place);
            $this->em->flush();

        } catch (\Exception $e) {

        }
    }
}
