<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Abstracts\AbstractService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\InvoiceResource;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

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
        $date = new DateTime($invoice->created_at);
        $time = $this->service->getConfigByKeyInCache('timezone')->value;
        $timezone = new DateTimeZone($time->value);
        $date->setTimezone($timezone);
        $invoice->created_date = $date->format('d/m/Y H:i:s');

        $data = [
            'invoice' => $invoice,
            'billingAddress' => $billingAddress,
            'paymentMethod' => $paymentMethod,
            'logo' => $this->configService->findConfigByKey('logo'),
            'companyName' => $this->configService->findConfigByKey('company_name'),
            'companyAddress' => $this->configService->findConfigByKey('company_address'),
            'supportEmail' => $this->configService->findConfigByKey('support_email'),
            'companyWebsite' => $this->configService->findConfigByKey('company_website')
        ];
        $pdf = Pdf::loadView('invoice.GenerateInvoice', $data);

        return $pdf->download('invoice-' . $invoice->uuid . '-' . strtotime(Carbon::now()) .'.pdf');
    }
}
