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
            'id'    => $place->getId(),
            'title' => $place->getTitle(),
            'photo' => $place->getWebPath(),
            'latitude' => $place->getLatitude(),
            'longitude' => $place->getLongitude(),
            'cityId' => $place->getCityId(),
            'countryId' => $place->getCountryId(),
            'tripId' => $place->getTrip()->getId(),
            'likes' => ''
        ];
    }
}