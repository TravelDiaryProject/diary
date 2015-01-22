<?php

namespace TravelDiary\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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
              $this->getUser()->getUsername() . DIRECTORY_SEPARATOR .
              $this->getTrip()->getId() . DIRECTORY_SEPARATOR .
              'places' . DIRECTORY_SEPARATOR .
              $this->photo;
    }

    public function getWebPath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadDir().'/'.$this->photo;
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
        return 'uploads/trip' . DIRECTORY_SEPARATOR .
                $this->getUser()->getUsername() . DIRECTORY_SEPARATOR .
                $this->getTrip()->getId() . DIRECTORY_SEPARATOR .
                'places';
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
        //$reader = \PHPExif\Reader::factory(\PHPExif\Reader::TYPE_NATIVE);

        // reader with Exiftool adapter
        //$reader = \PHPExif\Reader::factory(\PHPExif\Reader::TYPE_EXIFTOOL);

        //$exif = $reader->getExifFromFile($this->getFile()->getPathname());

        list($lat, $log) = $this->read_gps_location($this->getFile()->getPathname());

        $this->setLatitude($lat);
        $this->setLongitude($log);

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

    public function read_gps_location($file){
        if (is_file($file)) {
            $info = exif_read_data($file);
            if (isset($info['GPSLatitude']) && isset($info['GPSLongitude']) &&
                isset($info['GPSLatitudeRef']) && isset($info['GPSLongitudeRef']) &&
                in_array($info['GPSLatitudeRef'], array('E','W','N','S')) && in_array($info['GPSLongitudeRef'], array('E','W','N','S'))) {

                $GPSLatitudeRef  = strtolower(trim($info['GPSLatitudeRef']));
                $GPSLongitudeRef = strtolower(trim($info['GPSLongitudeRef']));

                $lat_degrees_a = explode('/',$info['GPSLatitude'][0]);
                $lat_minutes_a = explode('/',$info['GPSLatitude'][1]);
                $lat_seconds_a = explode('/',$info['GPSLatitude'][2]);
                $lng_degrees_a = explode('/',$info['GPSLongitude'][0]);
                $lng_minutes_a = explode('/',$info['GPSLongitude'][1]);
                $lng_seconds_a = explode('/',$info['GPSLongitude'][2]);

                $lat_degrees = $lat_degrees_a[0] / $lat_degrees_a[1];
                $lat_minutes = $lat_minutes_a[0] / $lat_minutes_a[1];
                $lat_seconds = $lat_seconds_a[0] / $lat_seconds_a[1];
                $lng_degrees = $lng_degrees_a[0] / $lng_degrees_a[1];
                $lng_minutes = $lng_minutes_a[0] / $lng_minutes_a[1];
                $lng_seconds = $lng_seconds_a[0] / $lng_seconds_a[1];

                $lat = (float) $lat_degrees+((($lat_minutes*60)+($lat_seconds))/3600);
                $lng = (float) $lng_degrees+((($lng_minutes*60)+($lng_seconds))/3600);

                //If the latitude is South, make it negative.
                //If the longitude is west, make it negative
                $GPSLatitudeRef  == 's' ? $lat *= -1 : '';
                $GPSLongitudeRef == 'w' ? $lng *= -1 : '';

                return array(
                    'lat' => $lat,
                    'lng' => $lng
                );
            }
        }
        return array(
            'lat' => '',
            'lng' => ''
        );
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


    public function bindDate()
    {
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime());
        }
    }
}
