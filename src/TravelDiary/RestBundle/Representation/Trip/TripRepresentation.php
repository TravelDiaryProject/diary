<?php

namespace TravelDiary\RestBundle\Representation\Trip;

use FOS\UserBundle\Model\UserInterface;
use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\TripBundle\Entity\Trip;

/**
 * Class TripRepresentation
 */
class TripRepresentation
{
    public static function listItem(Trip $trip, UserInterface $user = null)
    {
        /** @var Place[] $places */
        $place = $trip->getPlaces()->first();

        $photo = $place ? $place->getWebPath() : '/templates/image/noimagefound.jpg';
        $thumbnail = $place ? $place->getWebPathThumbnail() : '/templates/image/noimagefound.jpg';

        return [
            'id'    => (int) $trip->getId(),
            'title' => $trip->getTitle(),
            'description' => $trip->getDescription() ?: '',
            'photo' => $photo,
            'thumbnail' => $thumbnail,
            'isMine' => $user ? (int) ($user->getId() === $trip->getUser()->getId()) : 0,
            'isFuture' => (int) $trip->getIsFuture()
        ];
    }
}