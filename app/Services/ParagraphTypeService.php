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

    /**
     * @param $uuid
     * @return mixed
     */
    public function pluckField($uuid)
    {
        return $this->model->whereIn('uuid', $uuid)->pluck('title', 'uuid');
    }

    /**
     * @param $arrayUuids
     * @return void
     */
    public function updateSortFieldByUuid($arrayUuids)
    {
        if($arrayUuids)
        {
            $point = 1;
            foreach ($arrayUuids as $uuid) {
                $this->model->where('uuid', $uuid)->update(['sort' => $point]);
                $point++;
            }
        }
    }
}
