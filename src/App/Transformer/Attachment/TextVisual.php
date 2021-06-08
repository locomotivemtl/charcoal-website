<?php

namespace App\Transformer\Attachment;

use App\Model\Attachment\TextVisual as TextVisualBlock;
use App\Transformer\AbstractBlockTransformer;

/**
 * Block Transformer: TextVisual
 */
class TextVisual extends AbstractBlockTransformer
{
    const VIEW = 'app/template/web/partial/block/text-visual';

    /**
     * @param  TextVisualBlock $model
     * @return array
     */
    public function __invoke(TextVisualBlock $model)
    {
        return [
            'id'            => $model['id'],
            'title'         => $this->formatTranslation($model['title']),
            'subtitle'      => $this->formatTranslation($model['subtitle']),
            'description'   => $this->formatTranslation($model['description']),
            'hasVideo'      => $model['thumbnailVideo'] ? true : false,
            'thumbnail'     => (string) $this->filePresenter->formatImageFor($model['thumbnail'], $model->p('thumbnail'), $model, 'default'),
            'video'         => $model['thumbnailVideo'],
            'isReversed'    => $model['show_title']
        ];
    }
}
