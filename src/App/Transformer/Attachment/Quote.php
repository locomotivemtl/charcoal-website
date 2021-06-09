<?php

namespace App\Transformer\Attachment;

use App\Model\Attachment\Quote as QuoteBlock;
use App\Transformer\AbstractBlockTransformer;

/**
 * Block Transformer: Quote
 */
class Quote extends AbstractBlockTransformer
{
    const VIEW = 'app/template/web/partial/block/quote';

    /**
     * @param  TextBlock $model
     * @return array
     */
    public function __invoke(QuoteBlock $model)
    {
        return [
            'id'            => $model['id'],
            'author'         => $this->formatTranslation($model['title']),
            'author_position'         => $this->formatTranslation($model['subtitle']),
            'quote'   => $this->formatTranslation($model['description'])
        ];
    }
}
