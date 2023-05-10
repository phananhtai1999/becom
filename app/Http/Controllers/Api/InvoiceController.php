<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Abstracts\AbstractService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\InvoiceResource;
use App\Services\ConfigService;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InvoiceController extends AbstractRestAPIController
{
    use RestShowTrait;

    public function __construct(
        InvoiceService   $service,
        ConfigService $configService
    )
    {
        $this->service = $service;
        $this->configService = $configService;
        $this->resourceClass = InvoiceResource::class;
    }

    public function download($id) {
        $invoice = $this->service->findOrFailById($id);
        $billingAddress = $invoice->billingAddress;
        $billingAddress->name = $this->checkVietnamese($billingAddress->name);
        $paymentMethod = $invoice->paymentMethod;
        $data = [
            'invoice' => $invoice,
            'billingAddress' => $billingAddress,
            'paymentMethod' => $paymentMethod,
            'configLogo' => $this->configService->findConfigByKey('logo')
        ];
        $pdf = Pdf::loadView('invoice.GenerateInvoice', $data);

        return $pdf->download('invoice-' . $invoice->uuid . '-' . strtotime(Carbon::now()) .'.pdf');
    }
}
