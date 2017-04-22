<?php

namespace TravelDiary\PlaceBundle\Service\Geo;

use Doctrine\ORM\EntityManager;
use TravelDiary\PlaceBundle\Entity\Place;

/**
 * Class CoordinatesResolver
 */
class CoordinatesResolver
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * CityResolver constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Place $place
     *
     * @throws \Exception
     */
    public function resolve(Place $place)
    {
        $file = $place->getAbsolutePath();

        $geo = $this->readGpsLocation($file);

        $place->setLatitude($geo['lat']);
        $place->setLongitude($geo['lng']);
        $place->setShootedAt($geo['dateTime']);

        $this->em->persist($place);
        $this->em->flush($place);
    }

    /**
     * @param string $file
     *
     * @return array
     */
    public function readGpsLocation($file){
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

                $dateTime = isset($info['DateTime']) ? new \DateTime(trim($info['DateTime'])) : null;

                return array(
                    'lat' => $lat,
                    'lng' => $lng,
                    'dateTime' => $dateTime
                );
            }
        }
        return array(
            'lat' => '',
            'lng' => '',
            'dateTime' => null
        );
    }
}
