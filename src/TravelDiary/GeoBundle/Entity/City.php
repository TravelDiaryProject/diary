<?php

namespace TravelDiary\GeoBundle\Entity;

/**
 * City
 */
class City
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $capital_longitude;

    /**
     * @var string
     */
    private $capital_latitude;

    /**
     * @var \TravelDiary\GeoBundle\Entity\Country
     */
    private $country;


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
     * Set name
     *
     * @param string $name
     *
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set capitalLongitude
     *
     * @param string $capitalLongitude
     *
     * @return City
     */
    public function setCapitalLongitude($capitalLongitude)
    {
        $this->capital_longitude = $capitalLongitude;

        return $this;
    }

    /**
     * Get capitalLongitude
     *
     * @return string
     */
    public function getCapitalLongitude()
    {
        return $this->capital_longitude;
    }

    /**
     * Set capitalLatitude
     *
     * @param string $capitalLatitude
     *
     * @return City
     */
    public function setCapitalLatitude($capitalLatitude)
    {
        $this->capital_latitude = $capitalLatitude;

        return $this;
    }

    /**
     * Get capitalLatitude
     *
     * @return string
     */
    public function getCapitalLatitude()
    {
        return $this->capital_latitude;
    }

    /**
     * Set country
     *
     * @param \TravelDiary\GeoBundle\Entity\Country $country
     *
     * @return City
     */
    public function setCountry(\TravelDiary\GeoBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \TravelDiary\GeoBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }
}

