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
                'name' => ['en' => 'silver', 'vi' => 'bạc'],
                'points' => 0,
                'user_uuid' => null,
            ],
            [
                'name' => ['en' => 'gold', 'vi' => 'vàng'],
                'points' => 2,
                'user_uuid' => null,
            ],
            [
                'name' => ['en' => 'platinum', 'vi' => 'bạch kim'],
                'points' => 4,
                'user_uuid' => null,
            ],
            [
                'name' => ['en' => 'diamond', 'vi' => 'kim cương'],
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
