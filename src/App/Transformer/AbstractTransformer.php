<?php

namespace App\Transformer;

// From 'psr/log'
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

// From 'pimple/pimple'
use Pimple\Container;

// From 'charcoal-contrib-api'
use Charcoal\Api\AbstractTransformer as CharcoalAbstractTransformer;

// From App
// use App\Model\Shared\Contract\ModelInterface;
// use App\Transformer\Shared\TimestampableTrait;

// From 'charcoal-attachment'
use Charcoal\Attachment\Interfaces\AttachmentAwareInterface;

// From 'charcoal-core'
use Charcoal\Model\ModelInterface;

// From 'charcoal-translator'
use Charcoal\Translator\Translation;
use Charcoal\Translator\TranslatorAwareTrait;

// From App
use App\Support\TransformersAwareTrait;
use function App\Support\fill_missing_translations;

/**
 * Transformer: Base Class
 */
abstract class AbstractTransformer extends CharcoalAbstractTransformer implements
    LoggerAwareInterface
{
    use TranslatorAwareTrait,
        LoggerAwareTrait;

    /**
     * @var \App\Services\FilePresenter
     */
    protected $filePresenter;

    /**
     * @var Container
     */
    private $attachmentTransformers;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if (isset($data['logger'])) {
            $this->setLogger($data['logger']);
        }
        if (isset($data['translator'])) {
            $this->translator = $data['translator'];
        }
        if (isset($data['transformers'])) {
            $this->setTransformers($data['transformers']);
        }
        if (isset($data['attachmentTransformers'])) {
            $this->setAttachmentTransformers($data['attachmentTransformers']);
        }
        if (isset($data['filePresenter'])) {
            $this->filePresenter = $data['filePresenter'];
        }
        if (isset($data['baseUrl'])) {
            $this->baseUrl = $data['baseUrl'];
        }
    }

    // Attachments
    // -------------------------------------------------------------------------

    /**
     * @param Container $transformers
     * @return void
     */
    protected function setAttachmentTransformers(Container $transformers)
    {
        $this->attachmentTransformers = $transformers;
    }

    /**
     * @param  string $key The transformer to retrieve.
     * @return \App\Service\Transformer\AbstractTransformer
     */
    protected function getAttachmentTransformer($key)
    {
        return $this->attachmentTransformers[$key];
    }

    // Content Blocks
    // -------------------------------------------------------------------------

    /**
     * @param  AttachmentAwareInterface $model
     * @param  string                   $group
     * @return array
     */
    protected function getContentBlocks(AttachmentAwareInterface $model, $group = 'content-blocks')
    {
        if (!is_string($group) || empty($group)) {
            var_dump($group);
            throw new InvalidArgumentException('Cannot fetch attachments, group ident is invalid.');
        }

        $blocks = [];

        $attachments = $model->getAttachments([
            'group' => $group,
        ]);
        foreach ($attachments as $attachment) {
            // Convert pascal case to kebab case.
            $attClass = (new \ReflectionClass($attachment))->getShortName();
            $transformerKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $attClass));

            try {
                $presenter = $this->getAttachmentTransformer($transformerKey);
                $block     = $presenter($attachment);

                try {
                    $view = (new \ReflectionClassConstant($presenter, 'VIEW'))->getValue();
                } catch (\ReflectionException $error) {
                    $view = null;
                }

                $block['__VIEW__'] = $view;

                $blocks[] = $block;
            } catch (Throwable $t) {
                $this->logger->warning(sprintf(
                    '[App] Failed to transform "%s" (%s): %s',
                    $transformerKey,
                    $attachment['id'],
                    $t->getMessage()
                ));
            }
        }

        return $blocks;
    }

    // Translations
    // -------------------------------------------------------------------------

    /**
     * Retrieves a string from a Translation object. French is used as a fallback.
     *
     * @param  Translation|null $translation
     * @return string
     */
    final protected function formatTranslation($translation = null)
    {
        if ($translation instanceof Translation) {
            fill_missing_translations($translation, 'en');
            return (string)$translation;
        } else if (is_string($translation)) {
            return $translation;
        } else {
            return '';
        }
    }
}
