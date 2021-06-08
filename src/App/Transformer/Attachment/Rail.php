<?php

namespace App\Transformer\Attachment;

use App\Model\Attachment\Rail as RailBlock;
use App\Transformer\AbstractBlockTransformer;

/**
 * Block Transformer: Rail
 */
class Rail extends AbstractBlockTransformer
{
    const VIEW = 'app/template/web/partial/block/rail';

    /**
     * @param  TextBlock $model
     * @return array
     */
    public function __invoke(RailBlock $model)
    {
        return [
            'id'            => $model['id'],
            'title'         => $this->formatTranslation($model['title']),
            'description'   => $this->formatTranslation($model['description'])
        ];
    }
}
