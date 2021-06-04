<?php

namespace App\Transformer;

// From 'charcoal-contrib-api'
use Charcoal\Api\AbstractTransformer as CharcoalAbstractTransformer;

// From App
use App\Model\Shared\Contract\ModelInterface;
// use App\Transformer\Shared\TimestampableTrait;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;
use Charcoal\Translator\TranslatorAwareTrait;

/**
 * Transformer: Base Class
 */
abstract class AbstractTransformer extends CharcoalAbstractTransformer
{
    use TranslatorAwareTrait;

    /**
     * @var \App\Service\AttachmentPresenter
     */
    protected $attachmentPresenter;

    /**
     * @var \App\Services\FilePresenter
     */
    protected $filePresenter;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (isset($data['translator'])) {
            $this->translator = $data['translator'];
        }

        if (isset($data['attachmentPresenter'])) {
            $this->attachmentPresenter = $data['attachmentPresenter'];
        }

        if (isset($data['filePresenter'])) {
            $this->filePresenter = $data['filePresenter'];
        }

        if (isset($data['baseUrl'])) {
            $this->baseUrl = $data['baseUrl'];
        }
    }

    /**
     * @param  AbstractModel $model
     * @param  string $group
     * @return array
     */
    protected function getAttachmentsByGroup($model, string $group)
    {
        $attachments = $model->getAttachments([
            'group' => $group,
        ]);
        if (count($attachments) === 0) {
            return [];
        }
        return $this->attachmentPresenter->transform($attachments);
    }

    /**
     * @param  array $attachments The Attachments to transform
     * @return array
     */
    protected function prepareAttachmentContainer(array $attachments)
    {
        return [
            'hasContent' => (!count($attachments)) ? false : true,
            'content'    => $this->attachmentPresenter->transform($attachments),
        ];
    }

    /**
     * @param   ModelInterface $model The ModelInterface implementation of model.
     * @param   string $choice The property name
     * @return  array|null
     */
    protected function getPropertyLabelFromChoice(ModelInterface $model, $choice)
    {
        if (!$model[$choice]) {
            return null;
        }

        if (is_array($model[$choice])) {
            $values = [];
            foreach($model[$choice] as $choiceValue) {
                $values[] = $model->property($choice)->choiceLabel($choiceValue);
            }
            return $values;

        } else {
            return $model->property($choice)->choiceLabel($model[$choice]);
        }
    }
}
