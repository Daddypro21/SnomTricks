<?php

namespace App\EventListener\Doctrine;

use App\Entity\Video;
use RuntimeException;

class VideoListener
{
    public function prePersist(Video $video): void
    {
        if (!$video->getUrl()) {
            return;
        }
        //https://www.youtube.com/watch?v=wnr2A4aKnPU
        $regexYoutube = '/https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_\-]*)/m';
        $resultYoutube = preg_match($regexYoutube, $video->getUrl(), $matches);

        if ($resultYoutube) {
            $video->setPlatform(Video::YOUTUBE);
            $video->setPlatformId($matches[1]);

            return;
        }

        throw new RuntimeException('Should not be reached');
    }
}