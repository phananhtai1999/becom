<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Domain;
use App\Models\QueryBuilders\MyDomainQueryBuilder;

class MyDomainService extends AbstractService
{
    protected $modelClass = Domain::class;

    protected $modelQueryBuilderClass = MyDomainQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyDomain($id)
    {
        return  $this->findOneWhereOrFail([
            ['owner_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyDomain($id)
    {
        $website = $this->showMyDomain($id);

        return $this->destroy($website->getKey());
    }
}
