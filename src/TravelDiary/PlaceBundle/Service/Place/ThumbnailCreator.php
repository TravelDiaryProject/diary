<?php

namespace TravelDiary\PlaceBundle\Service\Place;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Symfony\Component\Filesystem\Filesystem;
use TravelDiary\PlaceBundle\Entity\Place;

/**
 * Class ThumbnailCreator
 */
class ThumbnailCreator
{
    /**
     * @param Place  $place
     *
     * @throws \Exception
     */
    public function create(Place $place)
    {
        $imagine = new Imagine();

        $image = $imagine->open($place->getAbsolutePath());

        $this->createThumbnailDir($place);

        $size = new Box(500, 500);
        $mode = ImageInterface::THUMBNAIL_INSET;

        $image->thumbnail($size, $mode)
            ->save($place->getAbsolutePathThumbnail());
    }

    /**
     * @param Place $place
     */
    private function createThumbnailDir(Place $place)
    {
        $targetDir = dirname($place->getAbsolutePathThumbnail());

        $fs = new Filesystem();

        if (!$fs->exists($targetDir)) {
            $fs->mkdir($targetDir);
        }
    }
}
