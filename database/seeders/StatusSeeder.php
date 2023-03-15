<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = [
            [
                'name' => ['en' => 'Client', 'vi' => 'khách hàng'],
                'user_uuid' => null
            ]
        ];

        foreach ($status as $value) {
            Status::firstOrCreate([
                'name->en' => $value['name']['en'],
                'user_uuid' => null
            ], $value);
        }
    }
}
