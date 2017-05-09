<?php

namespace TravelDiary\TripBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use TravelDiary\PlaceBundle\Entity\Place;

class Trip
{
    const UNKNOWN_CITY_NAME = 'Unknown city';

    private $id;

    private $title;

    private $startDate;

    private $photo;


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
     * Set title
     *
     * @param string $title
     * @return Trip
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Trip
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Trip
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    public function getAbsolutePath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadRootDir() .
              DIRECTORY_SEPARATOR . $this->getUser()->getUsername() . DIRECTORY_SEPARATOR . $this->photo;
    }

    public function getWebPath()
    {
        if (!$this->getUser()) {
            return '';
        }

        return null === $this->photo
            ? null
            : $this->getUploadDir().'/'. $this->getUser()->getUsername() . '/' . $this->photo;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/trip';
    }

    private $file;

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move(
            $this->getUploadRootDir() . DIRECTORY_SEPARATOR . $this->getUser()->getUsername(),
            $this->photo
        );

        // check if we have an old image
        if (isset($this->temp) && file_exists($this->getUploadRootDir().'/'.$this->temp)) {
            // delete the old image
            //unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    private $temp;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->photo)) {
            // store the old name to delete after the update
            $this->temp = $this->photo;
            $this->photo = null;
        } else {
            $this->photo = 'initial';
        }
    }

    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->photo = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file && file_exists($file)) {
            unlink($file);
        }
    }
    /**
     * @var \TravelDiary\UserBundle\Entity\User
     */
    private $user;


    /**
     * Set user
     *
     * @param \TravelDiary\UserBundle\Entity\User $user
     * @return Trip
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

    public function bindDate()
    {
        if (null === $this->getStartDate()) {
            $this->setStartDate(new \DateTime());
        }
    }
    /**
     * @var string
     */
    private $description;


    /**
     * Set description
     *
     * @param string $description
     * @return Trip
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }
    /**
     * @var boolean
     */
    private $isFuture;

    /**
     * @var \TravelDiary\GeoBundle\Entity\City
     */
    private $city;


    /**
     * Set isFuture
     *
     * @param boolean $isFuture
     *
     * @return Trip
     */
    public function setIsFuture($isFuture)
    {
        $this->isFuture = $isFuture;

        return $this;
    }

    /**
     * Get isFuture
     *
     * @return boolean
     */
    public function getIsFuture()
    {
        return $this->isFuture;
    }

    /**
     * Set city
     *
     * @param \TravelDiary\GeoBundle\Entity\City $city
     *
     * @return Trip
     */
    public function setCity(\TravelDiary\GeoBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \TravelDiary\GeoBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $places;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->places = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add place
     *
     * @param \TravelDiary\PlaceBundle\Entity\Place $place
     *
     * @return Trip
     */
    public function addPlace(\TravelDiary\PlaceBundle\Entity\Place $place)
    {
        $this->places[] = $place;

        return $this;
    }

    /**
     * Remove place
     *
     * @param \TravelDiary\PlaceBundle\Entity\Place $place
     */
    public function removePlace(\TravelDiary\PlaceBundle\Entity\Place $place)
    {
        $this->places->removeElement($place);
    }

    /**
     * Get places
     *
     * @return \Doctrine\Common\Collections\Collection|Place[]
     */
    public function getPlaces()
    {
        return $this->places;
    }
}
