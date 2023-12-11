<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Form;
use App\Models\QueryBuilders\FormQueryBuilder;
use App\Models\QueryBuilders\MyFormQueryBuilder;

class MyFormService extends AbstractService
{
    protected $modelClass = Form::class;

    protected $modelQueryBuilderClass = MyFormQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyForm($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyForm($id)
    {
        $form = $this->showMyForm($id);

        return $this->destroy($form->getKey());
    }
}
