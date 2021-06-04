<?php

namespace App\Transformer;

use App\Model\CaseStudy as CaseStudyModel;
use App\Transformer\AbstractTransformer;

/**
 * Transformer: Case Study
 */
class CaseStudy extends AbstractTransformer
{
    /**
     * @param  CaseStudyModel $model
     * @return array
     */
    public function __invoke(CaseStudyModel $model)
    {

        $tags = $model['tags'][$this->translator()->getLocale()];

        return [
            'id'        => (int)$model['id'],
            'title'     => $model['title'],
            'url'       => $model['url'],
            'thumbnail' => (string) $this->filePresenter->formatImageFor($model['thumbnail'], $model->p('thumbnail'), $model, 'default'),
            'tags'      => explode('|',$tags)
        ];
    }
}
