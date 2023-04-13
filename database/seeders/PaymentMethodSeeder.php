<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $paymentMethods = [
            [
                'uuid' => 1,
                'name' => 'paypal'
            ],
            [
                'uuid' => 2,
                'name' => 'stripe'
            ]
        ];

        foreach ($paymentMethods as $paymentMethod) {
            PaymentMethod::firstOrCreate($paymentMethod);
        }
    }
}
