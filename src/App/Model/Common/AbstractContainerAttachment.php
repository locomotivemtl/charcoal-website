<?php

namespace App\Model\Common;

use App\Model\Common\AbstractAttachment;
use Charcoal\Attachment\Interfaces\AttachmentAwareInterface;
use Charcoal\Attachment\Interfaces\AttachmentContainerInterface;
use Charcoal\Attachment\Traits\AttachmentAwareTrait;
use Charcoal\Attachment\Traits\AttachmentContainerTrait;
use Charcoal\Attachment\Traits\ConfigurableAttachmentsTrait;
use Charcoal\Config\ConfigurableInterface;
use Pimple\Container;

/**
 * Content Block: Base Container Block
 */
abstract class AbstractContainerAttachment extends AbstractAttachment implements
    AttachmentAwareInterface,
    AttachmentContainerInterface,
    ConfigurableInterface
{
    use AttachmentAwareTrait;
    use AttachmentContainerTrait;
    use ConfigurableAttachmentsTrait;

    /**
     * @param  Container $container
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        if (isset($container['attachments/config'])) {
            $this->setConfig($container['attachments/config']);
        } elseif (isset($container['config']['attachments'])) {
            $this->setConfig($container['config']['attachments']);
        }
    }

    /**
     * Retrieve the objects associated to the current object.
     *
     * @param  mixed ...$args Filter the attachments;
     *     options accepted by {@see AttachmentAwareTrait::getAttachments()}.
     * @return Collection
     */
    public function attachments(...$args)
    {
        $attachables = $this->attachableObjects();
        $attachments = call_user_func_array([ $this, 'getAttachments' ], $args);

        foreach ($attachments as $attachment) {
            if (isset($attachables[$attachment->objType()])) {
                $attachment->attachmentType = $attachables[$attachment->objType()];
            } else {
                $attachment->attachmentType = [];
            }
        }

        return $attachments;
    }

    /**
     * @return boolean
     */
    public function preDelete()
    {
        foreach ($this->attachments()->values() as $attachment) {
            $attachment->delete();
        }

        return parent::preDelete();
    }
}
