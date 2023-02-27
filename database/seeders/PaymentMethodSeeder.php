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
                'name' => 'paypal'
            ],
            [
                'name' => 'stripe'
            ]
        ];

        foreach ($paymentMethods as $paymentMethod) {
            PaymentMethod::firstOrCreate($paymentMethod);
        }
    }
}
