<?php

namespace TravelDiary\PlaceBundle\Entity;

/**
 * FuturePlaces
 */
class FuturePlaces
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \TravelDiary\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \TravelDiary\PlaceBundle\Entity\Place
     */
    private $futurePlace;

    /**
     * @var \TravelDiary\PlaceBundle\Entity\Place
     */
    private $originalPlace;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \TravelDiary\UserBundle\Entity\User $user
     *
     * @return FuturePlaces
     */
    public function setUser(\TravelDiary\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \TravelDiary\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set futurePlace
     *
     * @param \TravelDiary\PlaceBundle\Entity\Place $futurePlace
     *
     * @return FuturePlaces
     */
    public function setFuturePlace(\TravelDiary\PlaceBundle\Entity\Place $futurePlace = null)
    {
        $this->futurePlace = $futurePlace;

        return $this;
    }

    /**
     * Get futurePlace
     *
     * @return \TravelDiary\PlaceBundle\Entity\Place
     */
    public function getFuturePlace()
    {
        return $this->futurePlace;
    }

    /**
     * Set originalPlace
     *
     * @param \TravelDiary\PlaceBundle\Entity\Place $originalPlace
     *
     * @return FuturePlaces
     */
    public function setOriginalPlace(\TravelDiary\PlaceBundle\Entity\Place $originalPlace = null)
    {
        $this->originalPlace = $originalPlace;

        return $this;
    }

    /**
     * Get originalPlace
     *
     * @return \TravelDiary\PlaceBundle\Entity\Place
     */
    public function getOriginalPlace()
    {
        return $this->originalPlace;
    }
}

