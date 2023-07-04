<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ParagraphType;
use App\Models\QueryBuilders\ParagraphTypeQueryBuilder;

class ParagraphTypeService extends AbstractService
{
    protected $modelClass = ParagraphType::class;

    protected $modelQueryBuilderClass = ParagraphTypeQueryBuilder::class;

    /**
     * @param $uuid
     * @return mixed
     */
    public function findByUuid($uuid)
    {
        return $this->findOneById($uuid);
    }

    /**
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function sortChildren()
    {
        $sortChildren = request()->get('sort_children');
        if ($sortChildren == '-sort') {
            return true;
        }

        return false;
    }
}
