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
                'points' => 0,
                'user_uuid' => null,
            ],
            [
                'name' => ['en' => 'Lead', 'vi' => 'khách hàng tiềm năng'],
                'points' => 2,
                'user_uuid' => null,
            ],
            [
                'name' => ['en' => 'Customer', 'vi' => 'khách hàng thân thiết'],
                'points' => 4,
                'user_uuid' => null,
            ],
            [
                'name' => ['en' => 'Loyal Customer', 'vi' => 'khách vip'],
                'points' => 6,
                'user_uuid' => null,
            ],
        ];

        foreach ($status as $value) {
            Status::firstOrCreate([
                'name->en' => $value['name']['en'],
                'points' => $value['points'],
                'user_uuid' => null
            ], $value);
        }
    }
}
