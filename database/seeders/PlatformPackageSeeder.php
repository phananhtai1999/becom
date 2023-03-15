<?php

namespace Database\Seeders;

use App\Models\PlatformPackage;
use App\Services\PaypalService;
use App\Services\PlatformPackageService;
use App\Services\StripeService;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;

class PlatformPackageSeeder extends Seeder
{
    public function __construct(StripeService $stripeService, PaypalService $paypalService, PlatformPackageService $platformPackageService)
    {
        $this->stripeService = $stripeService;
        $this->paypalService = $paypalService;
        $this->platformPackageService = $platformPackageService;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'uuid' => 'starter',
                'monthly' => 0,
                'yearly' => 0,
                'permission_uuid' => [1, 2, 3, 4, 5, 6, 7, 8],
                'description' => 'This is description!!',
            ],
            [
                'uuid' => 'professional',
                'monthly' => 19,
                'yearly' => 168,
                'permission_uuid' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17],
                'description' => 'This is description!!',
            ],
            [
                'uuid' => 'business',
                'monthly' => 39,
                'yearly' => 408,
                'permission_uuid' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27],
                'description' => 'This is description!!',
            ],
        ];

        foreach ($data as $platformPackage) {
            $request = new Request($platformPackage);
            $paypalProduct = $this->paypalService->createProduct($request);
            $stripeProduct = $this->stripeService->createProduct($request);
            $product = [
                'paypal' => $paypalProduct['id'],
                'stripe' => $stripeProduct['id']
            ];
            $model = PlatformPackage::updateOrCreate(['uuid' => $request->get('uuid')], [
                'uuid' => $request->get('uuid'),
                'description' => $request->get('description'),
                'monthly' => $request->get('monthly'),
                'yearly' => $request->get('yearly'),
                'status' => 'publish',
                'payment_product_id' => json_encode($product)
            ]);
            $model->permissions()->sync($request->get('permission_uuid'));
        }
    }
}
