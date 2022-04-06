<?php

namespace App\Abstracts;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractService
{
    protected $modelClass;

    /**
     * @var Application|mixed
     */
    protected $model;

    /**
     * AbstractService constructor.
     */
    public function __construct()
    {
        $this->model = app($this->modelClass);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFailById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param $resourceCollectionClass
     * @param $models
     * @return mixed
     */
    public function resourceCollectionToData($resourceCollectionClass, $models)
    {
        return app($resourceCollectionClass, ['resource' => $models])
            ->toResponse(app('Request'))
            ->getData(true);
    }

    /**
     * @param $resourceClass
     * @param $model
     * @return mixed
     */
    public function resourceToData($resourceClass, $model)
    {
        return app($resourceClass, ['resource' => $model])
            ->toResponse(app('Request'))
            ->getData(true);
    }

    /**
     * @param int $perPage
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getCollectionWithPagination($perPage = 15)
    {
        $perPage = request()->get('per_page', $perPage);

        return $this->model->paginate($perPage);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $model = $this->findOrFailById($id);

        return $model->delete();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create($data = [])
    {
        return $this->model->create($data);
    }

    /**
     * @param $model
     * @param array $data
     * @return mixed
     */
    public function update($model, $data = [])
    {
        return $model->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOneById($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param $where
     * @return Builder|Model|object|null
     */
    public function findOneWhere($where)
    {
        return $this->model->where($where)->first();
    }

    /**
     * @param $where
     * @return mixed
     */
    public function findOneWhereOrFail($where)
    {
        return $this->model->where($where)->firstOrFail();
    }
}
