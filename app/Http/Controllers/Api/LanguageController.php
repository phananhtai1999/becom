<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\LanguageRequest;
use App\Http\Requests\SaveTranslatesJsonRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\LanguageResourceCollection;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class LanguageController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait;

    /**
     * @param LanguageService $service
     */
    public function __construct(LanguageService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = LanguageResourceCollection::class;
        $this->resourceClass = LanguageResource::class;
        $this->storeRequest = LanguageRequest::class;
        $this->editRequest = UpdateLanguageRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        File::put(storage_path("translate_json/".strtolower($request->get('code')).".json"), $request->get('fe'));
        $model = $this->service->create($request->all());

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        if ($request->get('fe')) {
            File::put(storage_path("translate_json/".strtolower($id).".json"), $request->get('fe'));
        }

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showTranslates()
    {
        $translates = [];
        $languagesCode = $this->service->getAllCodeLanguage();
        foreach ($languagesCode as $code) {
            $files = File::allFiles(resource_path("lang/$code"));
            foreach ($files as $file) {
                if ($file->getExtension() === "php") {
                    $fileName = $file->getFilenameWithoutExtension();
                    $translates[$code][$fileName] = __($fileName, [], $code);
                }
            }
        }
        return $this->sendOkJsonResponse(["data" => json_encode($translates)]);
    }

    /**
     * @param SaveTranslatesJsonRequest $request
     * @return JsonResponse
     */
    public function saveTranslates(SaveTranslatesJsonRequest $request)
    {
        $translates = json_decode($request->get('translates_json'), true);
        $languagesSupport = $this->service->languagesSupport();
        foreach ($translates as $code => $translate) {
            if (!in_array($code, $languagesSupport)) {
                $this->sendValidationFailedJsonResponse(["error" => __('messages.not_change_code')]);
            }
            $path = resource_path("lang/$code");
            foreach ($translate as $file => $messages) {
                $arrayString = "<?php\n\nreturn " . var_export($messages, true) . ";\n";
                $filename = "$file.php";
                File::put($path . '/' . $filename, $arrayString);
            }
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @return JsonResponse
     */
    public function languageSupport()
    {
        $languagesSupport = $this->service->languagesSupport();
        $listLanguages = [];
        foreach ($languagesSupport as $languageSupport) {
            $listLanguages[] = ["code" => $languageSupport, "name" => config('languages.codes')[$languageSupport]];
        }
        return $this->sendOkJsonResponse(["data" => json_encode($listLanguages)]);
    }
}
