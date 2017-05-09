<?php

namespace TravelDiary\RestBundle\Representation\Place;

use TravelDiary\PlaceBundle\Entity\Place;

/**
 * Class PlaceRepresentation
 */
class PlaceRepresentation
{
    public static function listItem(Place $place, array $extra = [])
    {
        return array_merge([
            'id'    => (int) $place->getId(),
            'title' => $place->getTitle(),
            'photo' => $place->getWebPath(),
            'thumbnail' => $place->getWebPathThumbnail(),
            'latitude' => $place->getLatitude(),
            'longitude' => $place->getLongitude(),
            'shootedAt' => $place->getShootedAt() ? $place->getShootedAt()->format('U') : '',
            'cityId' => (int) $place->getCityId(),
            'cityName' => $place->getCityName(),
            'countryId' => (int) $place->getCountryId(),
            'countryName' => $place->getCountryName(),
            'tripId' => (int) $place->getTrip()->getId(),
            'likes' => (int) $place->getLikes(),
        ], $extra);
    }
}