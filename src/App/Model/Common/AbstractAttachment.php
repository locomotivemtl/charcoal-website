<?php

namespace App\Model\Common;

use App\Model\Common\AttachmentInterface;
use App\Model\Common\ValidatablePropertiesTrait;
use Charcoal\Attachment\Object\Attachment as CharcoalAttachment;
use Charcoal\Validator\ValidatorInterface;

/**
 * Content Block: Base Block
 */
abstract class AbstractAttachment extends CharcoalAttachment implements
    AttachmentInterface
{
    use ValidatablePropertiesTrait;



    // Properties
    // -------------------------------------------------------------------------

    /**
     * @param  string $path
     * @return self
     */
    public function setThumbnail($path)
    {
        $this->thumbnail = $this->property('thumbnail')->parseVal($path);

        return $this;
    }

    /**
     * @param  string $path
     * @return self
     */
    public function setFile($path)
    {
        $this->file = $this->property('file')->parseVal($path);

        return $this;
    }

    /**
     * @param  string $value
     * @return self
     */
    public function setFileType($value)
    {
        $this->fileType = $this->property('fileType')->parseVal($value);

        return $this;
    }



    // Aliases/Implementations
    // -------------------------------------------------------------------------

    // /**
    //  * Alias of {@see Attachment::thumbnail()}.
    //  *
    //  * @return string|null
    //  */
    // public function imageSrc()
    // {
    //     $src = $this->thumbnail();
    //     return $this->createAbsoluteUrl($src);
    // }

    // /**
    //  * {@inheritdoc}
    //  *
    //  * @return string|null
    //  */
    // public function choiceLabel()
    // {
    //     return $this->parseChoiceLabel('name');
    // }



    // Validation
    // -------------------------------------------------------------------------

    /**
     * @param ValidatorInterface $v
     * @return boolean
     */
    public function validate(ValidatorInterface &$v = null)
    {
        if (parent::validate($v)) {
            return $this->validateProperties();
        } else {
            return false;
        }
    }
}
