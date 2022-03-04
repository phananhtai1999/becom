<?php

namespace App\Abstracts;

abstract class AbstractService
{
    protected $modelClass;

    protected $model;

    public function __construct()
    {
        $this->model = app($this->modelClass);
    }

    public function findOrFailById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function resourceCollectionToData($resourceCollectionClass, $models)
    {
        return app($resourceCollectionClass, ['resource' => $models])
            ->toResponse(app('Request'))
            ->getData(true);
    }

    public function resourceToData($resourceClass, $model)
    {
        return app($resourceClass, ['resource' => $model])
            ->toResponse(app('Request'))
            ->getData(true);
    }

    public function getCollectionWithPagination($perPage = 15)
    {
        $perPage = request()->get('per_page', $perPage);

        return $this->model->paginate($perPage);
    }

    public function destroy($id)
    {
        $model = $this->findOrFailById($id);

        return $model->delete();
    }

    public function create($data = [])
    {
        return $this->model->create($data);
    }

    public function update($model, $data = [])
    {
        return $model->update($data);
    }
}
