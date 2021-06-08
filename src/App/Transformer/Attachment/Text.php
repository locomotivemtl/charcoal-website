<?php

namespace App\Transformer\Attachment;

use App\Model\Attachment\Text as TextBlock;
use App\Transformer\AbstractBlockTransformer;

/**
 * Block Transformer: Text
 */
class Text extends AbstractBlockTransformer
{
    const VIEW = 'app/template/web/partial/block/text';

    /**
     * @param  TextBlock $model
     * @return array
     */
    public function __invoke(TextBlock $model)
    {
        return [
            'id'            => $model['id'],
            'description'   => $this->formatTranslation($model['description']),
        ];
    }
}
