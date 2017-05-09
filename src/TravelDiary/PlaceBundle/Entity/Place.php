<?php

namespace TravelDiary\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Place
 */
class Place
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $photo;

    /**
     * @var string
     */
    private $longitude;

    /**
     * @var string
     */
    private $latitude;

    /**
     * @var \TravelDiary\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \TravelDiary\TripBundle\Entity\Trip
     */
    private $trip;

    /**
     * @var
     */
    private $file;

    /**
     * @var \TravelDiary\GeoBundle\Entity\City
     */
    private $city;

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
     * @return Place
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
     * Set description
     *
     * @param string $description
     * @return Place
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Place
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Place
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

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return Place
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return Place
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set user
     *
     * @param \TravelDiary\UserBundle\Entity\User $user
     * @return Place
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
     * Set trip
     *
     * @param \TravelDiary\TripBundle\Entity\Trip $trip
     * @return Place
     */
    public function setTrip(\TravelDiary\TripBundle\Entity\Trip $trip = null)
    {
        $this->trip = $trip;

        return $this;
    }

    /**
     * Get trip
     *
     * @return \TravelDiary\TripBundle\Entity\Trip 
     */
    public function getTrip()
    {
        return $this->trip;
    }




    public function getAbsolutePath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadRootDir() . DIRECTORY_SEPARATOR .
              $this->photo;
    }

    public function getAbsolutePathThumbnail()
    {
        return null === $this->photo
            ? null
            : $this->getUploadDirOfThumbnail() . DIRECTORY_SEPARATOR .
            $this->photo;
    }

    public function getWebPath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadDir().'/'.$this->photo;
    }

    public function getWebPathThumbnail()
    {
        return null === $this->photo
            ? null
            : $this->getUploadDirOfThumbnail().'/'.$this->photo;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/trip' . DIRECTORY_SEPARATOR .
                $this->getTrip()->getId() . DIRECTORY_SEPARATOR .
                'places' . DIRECTORY_SEPARATOR .
                $this->getId();
    }

    protected function getUploadDirOfThumbnail()
    {
        return $this->getUploadDir() . DIRECTORY_SEPARATOR .
               'thumbnail';
    }

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

        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->photo
        );

        // check if we have an old image
        if (isset($this->temp) && file_exists($this->getUploadRootDir().'/'.$this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
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
        $fs = new Filesystem();

        $targetDir = dirname($this->getAbsolutePath());

        if ($fs->exists($targetDir)) {
            $fs->remove($targetDir);
        }
    }


    public function bindDate()
    {
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime());
        }
    }

    /**
     * Set city
     *
     * @param \TravelDiary\GeoBundle\Entity\City $city
     *
     * @return Place
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
     * @return int|null
     */
    public function getCityId()
    {
        return $this->getCity() ? $this->getCity()->getId() : null;
    }

    /**
     * @return string
     */
    public function getCityName()
    {
        return $this->getCity() ? $this->getCity()->getName() : '';
    }

    /**
     * @return null|\TravelDiary\GeoBundle\Entity\Country
     */
    public function getCountry()
    {
        if (null === $this->getCity()) {
            return null;
        }

        $country = $this->getCity()->getCountry();

        if (null === $country) {
            return null;
        }

        return $country;
    }

    /**
     * @return int|null
     */
    public function getCountryId()
    {
        return $this->getCountry() ? $this->getCountry()->getId() : null;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->getCountry() ? $this->getCountry()->getName() : '';
    }
    /**
     * @var integer
     */
    private $likes;


    /**
     * Set likes
     *
     * @param integer $likes
     *
     * @return Place
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer
     */
    public function getLikes()
    {
        return $this->likes;
    }
    /**
     * @var \DateTime
     */
    private $shootedAt;


    /**
     * Set shootedAt
     *
     * @param \DateTime $shootedAt
     *
     * @return Place
     */
    public function setShootedAt($shootedAt)
    {
        $this->shootedAt = $shootedAt;

        return $this;
    }

    /**
     * Get shootedAt
     *
     * @return \DateTime
     */
    public function getShootedAt()
    {
        return $this->shootedAt;
    }
}
