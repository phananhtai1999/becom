<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Invoice;

class InvoiceService extends AbstractService
{
    protected $modelClass = Invoice::class;
}
