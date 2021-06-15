<?php

namespace App\Transformer;

use App\Model\Feature as FeatureModel;
use App\Transformer\AbstractTransformer;

/**
 * Transformer: Feature
 */
class FeatureSummaryTransformer extends AbstractTransformer
{
    /**
     * @param  FeatureModel $model
     * @return array
     */
    public function __invoke(FeatureModel $model)
    {
        return [
            'id'        => (int)$model['id'],
            'title'     => $model['title'],
            'subtitle'  => $model['subtitle'],
            'thumbnail' => (string) $this->filePresenter->formatImageFor($model['thumbnail'], $model->p('thumbnail'), $model, 'default'),
            'thumbnailVideo' => $model['thumbnailVideo'],
            'url'       => $model['url']
        ];
    }
}
