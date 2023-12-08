<?php

namespace App\Abstracts;

use App\Models\Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractService
{
    protected $modelClass;

    protected $modelQueryBuilderClass;

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
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCollectionWithPagination($perPage = 15, $page = 1, $columns = '*', $pageName = 'page')
    {
        $perPage = request()->get('per_page', $perPage);
        $page = request()->get('page', $page);
        $columns = request()->get('columns', $columns);
        $pageName = request()->get('page_name', $pageName);
        $search = request()->get('search', '');
        $searchBy = request()->get('search_by', '');

        return $this->modelQueryBuilderClass::searchQuery($search, $searchBy)
            ->paginate($perPage, $columns, $pageName, $page);
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

    /**
     * @param $where
     * @param array|null $select
     * @return mixed
     */
    public function findAllWhere($where = null, array $select = null, $distinct = false)
    {
        if ($distinct) {
            return $this->model->select($select ?? '*')->where($where)->distinct()->get();
        }

        return $this->model->select($select ?? '*')->where($where)->get();
    }

    public function firstOrCreate($where, $data)
    {
        return $this->model->firstOrCreate($where, $data);
    }

    /**
     * @param string|null $whereColumn
     * @param null $arrayValues
     * @param array|null $select
     * @return mixed
     */
    public function findAllWhereIn(string $whereColumn = null, $arrayValues = null, array $select = null)
    {
        return $this->model->select($select ?? '*')->whereIn($whereColumn, $arrayValues)->get();
    }

    public function collectionPagination($results, $perPage, $page = null)
    {
        $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);

        $results = $results instanceof Collection ? $results : Collection::make($results);

        return new LengthAwarePaginator($results->forPage($page, $perPage)->values(), $results->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    public function getIndexRequest($request)
    {
        return [
            'per_page' => $request->get('per_page', 15),
            'page' => $request->get('page', 1),
            'columns' => $request->get('columns', '*'),
            'page_name' => $request->get('page_name', 'page'),
            'search' => $request->get('search', ''),
            'search_by' => $request->get('search_by', ''),
        ];
    }

    /**
     * @param $request
     * @param $where
     * @return mixed
     */
    public function getCollectionWithPaginationByCondition($request, $where = null)
    {
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where($where)
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    /**
     * @param $request
     * @param $select
     * @return mixed
     */
    public function getPluckModel($request, $select = [])
    {
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->select($select)
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getConfigInCache()
    {
        return Cache::rememberForever('config', function () {
            return Config::all();
        });
    }

    public function getConfigByKeyInCache($key)
    {
        $configs = Cache::rememberForever('config', function () {
            return Config::all();
        });
        foreach ($configs as $config) {
            if ($config->key == $key) {
                return $config;
            }
        }

        return false;
    }

    public function getCollectionByUserIdAndAppIdWithPagination($request)
    {
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where([
                ['user_uuid', auth()->user()],
                ['app_id', auth()->appId()],
            ])
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function findOneWhereOrFailByUserUuidAndAppId($id)
    {
        return $this->model->where([
            ['user_uuid', auth()->user()],
            ['app_id', auth()->appId()],
            ['uuid', $id],
        ])->firstOrFail();
    }

    public function destroyByUserIdAndAppId($id)
    {
        $model = $this->findOneWhereOrFailByUserUuidAndAppId($id);

        return $model->delete();
    }
}
