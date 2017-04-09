<?php

namespace TravelDiary\RatingBundle\Entity;

/**
 * UserLikesPlaces
 */
class UserLikesPlaces
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \TravelDiary\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \TravelDiary\PlaceBundle\Entity\Place
     */
    private $place;


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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return UserLikesPlaces
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set user
     *
     * @param \TravelDiary\UserBundle\Entity\User $user
     *
     * @return UserLikesPlaces
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
     * Set place
     *
     * @param \TravelDiary\PlaceBundle\Entity\Place $place
     *
     * @return UserLikesPlaces
     */
    public function setPlace(\TravelDiary\PlaceBundle\Entity\Place $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \TravelDiary\PlaceBundle\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }
}

