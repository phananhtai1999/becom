<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Location;
use App\Models\QueryBuilders\LocationQueryBuilder;

class LocationService extends AbstractService
{
    protected $modelClass = Location::class;

    protected $modelQueryBuilderClass = LocationQueryBuilder::class;
}
