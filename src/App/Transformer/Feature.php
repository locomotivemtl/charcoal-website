<?php

namespace App\Transformer;

use App\Model\Feature as FeatureModel;
use App\Transformer\AbstractTransformer;

/**
 * Transformer: Feature
 */
class Feature extends AbstractTransformer
{
    /**
     * @param  FeatureModel $model
     * @return array
     */
    public function __invoke(FeatureModel $model)
    {
        $blocks = $this->getContentBlocks($model);

        return [
            'id'        => (int)$model['id'],
            'hasBlocks' => count($blocks) > 0,
            'blocks'    => $blocks,
            'title'     => $model['title'],
            'subtitle'  => $model['subtitle'],
            'thumbnail' => $model['thumbnailVideo'] ? : (string) $this->filePresenter->formatImageFor($model['thumbnail'], $model->p('thumbnail'), $model, 'default'),
            'url'       => $model['url']
        ];
    }
}
