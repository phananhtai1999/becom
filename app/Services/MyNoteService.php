<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Company;
use App\Models\QueryBuilders\MyNoteQueryBuilder;

class MyNoteService extends AbstractService
{
    protected $modelClass = Company::class;

    protected $modelQueryBuilderClass = MyNoteQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyNote($id)
    {
        return  $this->findOneWhereOrFail([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyNote($id)
    {
        $note = $this->showMyNote($id);

        return $this->destroy($note->getKey());
    }
}
