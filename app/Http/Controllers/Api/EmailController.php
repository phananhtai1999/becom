<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyEmailRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\UpdateMyEmailRequest;
use App\Http\Resources\EmailResourceCollection;
use App\Http\Resources\EmailResource;
use App\Services\EmailService;
use App\Services\MyEmailService;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class EmailController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @param EmailService $service
     * @param MyEmailService $myService
     */
    public function __construct(
        EmailService $service,
        MyEmailService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = EmailResourceCollection::class;
        $this->resourceClass = EmailResource::class;
        $this->storeRequest = EmailRequest::class;
        $this->editRequest = UpdateEmailRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if($request->has('user_uuid')){
            $data = $request->all();
        }else{
            $data = array_merge($request->all(), [
                'user_uuid' => auth()->user()->getkey(),
            ]);
        }
        
        $model = $this->service->create($data);

        $model->websites()->attach($request->get('websites'));

        return $this->sendCreatedJsonResponse(
          $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, $request->all());

        $model->websites()->sync($request->get('websites') ?? $model->websites);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function indexMyEmail(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination(
                    $request->get('per_page', '15'),
                    $request->get('page', '1'),
                    $request->get('columns', '*'),
                    $request->get('page_name', 'page'),
                )
            )
        );
    }

    /**
     * @param MyEmailRequest $request
     * @return JsonResponse
     */
    public function storeMyEmail(MyEmailRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $model->websites()->attach($request->get('websites'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMyEmail($id)
    {
        $model = $this->myService->findMyEmailByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyEmailRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editMyEmail(UpdateMyEmailRequest $request, $id)
    {
        $model = $this->myService->findMyEmailByKeyOrAbort($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $model->websites()->sync($request->get('websites') ?? $model->websites);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMyEmail($id)
    {
        $this->myService->deleteMyEmailByKey($id);

        return $this->sendOkJsonResponse();
    }
}
