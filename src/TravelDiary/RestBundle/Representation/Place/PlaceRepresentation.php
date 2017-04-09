<?php

namespace TravelDiary\RestBundle\Representation\Place;

use TravelDiary\PlaceBundle\Entity\Place;

/**
 * Class PlaceRepresentation
 */
class PlaceRepresentation
{
    public static function listItem(Place $place)
    {
        return [
            'id'    => (int) $place->getId(),
            'title' => $place->getTitle(),
            'photo' => $place->getWebPath(),
            'latitude' => $place->getLatitude(),
            'longitude' => $place->getLongitude(),
            'cityId' => (int) $place->getCityId(),
            'countryId' => (int) $place->getCountryId(),
            'tripId' => (int) $place->getTrip()->getId(),
            'likes' => 0
        ];
    }
}