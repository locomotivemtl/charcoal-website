<?php

namespace App\Transformer\Attachment;

use App\Model\Attachment\QuoteSlider as QuoteSliderBlock;
use App\Transformer\AbstractBlockTransformer;

// From 'charcoal-attachment'
use Charcoal\Attachment\Interfaces\AttachmentAwareInterface;

/**
 * Block Transformer: Quote Slider
 */
class QuoteSlider extends AbstractBlockTransformer
{
    const VIEW = 'app/template/web/partial/block/quote-slider';

    /**
     * @param  ImageGalleryBlock $model
     * @return array
     */
    public function __invoke(QuoteSliderBlock $model)
    {
        $quotes = $this->getQuotes($model, 'quoteSlider', 'quoteSlider');
        return [
            'id'        => $model['id'],
            'hasQuotes' => count($quotes) > 0,
            'quotes'    => $quotes,
        ];
    }

    /**
     * @param  AttachmentAwareInterface $model
     * @param  string|null              $group
     * @return array
     */
    protected function getQuotes(AttachmentAwareInterface $model, $group = null, $size = null)
    {
        $transformer = $this->getAttachmentTransformer('quote');
        return array_map(function ($image) use ($transformer, $size) {
            return $transformer($image, $size);
        }, $model->getAttachments([
            'group' => $group,
        ])->values());
    }
}
