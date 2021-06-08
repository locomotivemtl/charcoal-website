<?php

namespace App\Model\Attachment;

// From App
use App\Model\Common\AbstractAttachment;

/**
 * Content Block: TextVisual
 */
class TextVisual extends AbstractAttachment
{
    /**
     * Alias of {@see Attachment::thumbnail()}.
     *
     * @return string|null
     */
    public function imageSrc()
    {
        $src = $this->thumbnail();
        return $this->createAbsoluteUrl($src);
    }
}
