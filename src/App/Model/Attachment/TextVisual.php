<?php

namespace App\Model\Attachment;

// From App
use App\Model\Common\AbstractAttachment;

/**
 * Content Block: TextVisual
 */
class TextVisual extends AbstractAttachment
{

    private $thumbnailVideo;

    /**
     * @param  mixed $thumbnailVideo
     * @return void
     */
    public function setThumbnailVideo($thumbnailVideo)
    {
        $this->thumbnailVideo = $this->property('thumbnailVideo')->parseVal($thumbnailVideo);
    }

    /**
     * @return string|null
     */
    public function getThumbnailVideo()
    {
        return $this->thumbnailVideo;
    }

    public function hasVideo() {
        return $this->thumbnailVideo ? true : false;
    }
}
