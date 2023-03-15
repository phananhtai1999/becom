<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Note;
use App\Models\QueryBuilders\NoteQueryBuilder;

class NoteService extends AbstractService
{
    protected $modelClass = Note::class;

    protected $modelQueryBuilderClass = NoteQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function withTrashed($id)
    {
        return $this->model->withTrashed()->where('uuid', $id)->first();
    }
}
