<?php

namespace TravelDiary\PlaceBundle\Service\Place;

use TravelDiary\PlaceBundle\Entity\Place;
use TravelDiary\UserBundle\Entity\User;

/**
 * Class FuturePlacePhotoResolver
 */
class FuturePlacePhotoResolver
{
    public function resolve(Place $place, User $user)
    {
        $photoOfOldUser = $place->getAbsolutePath();
        $place->setUser($user);
        $photoOfNewUser = $place->getAbsolutePath();

        $result = copy($photoOfOldUser, $photoOfNewUser);

        return $result;
    }
}