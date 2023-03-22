<?php

namespace Database\Seeders;

use App\Models\Config;
use App\Models\Group;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $configs = [
            [
                'key' => 'smtp_auto',
                'value' => 1,
                'type' => 'boolean',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'email_price',
                'value' => 1,
                'type' => 'number',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'sms_price',
                'value' => 5,
                'type' => 'number',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'footer_linkedin',
                'value' => 'https://www.linkedin.com',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'footer_twitter',
                'value' => 'https://www.twitter.com',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'footer_instagram',
                'value' => 'https://www.instagram.com',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'footer_facebook',
                'value' => 'https://www.facebook.com',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'site_name',
                'value' => 'Mail',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'public'
            ],
            [
                'key' => 'logo',
                'value' => 'https://file.storage.techupzone.com/linkstar-stg/public/upload/c690285d-4348-4ba4-b982-1e12d2334a5f_1677036055.png',
                'type' => 'image',
                'group_id' => 1,
                'status' => 'public'
            ],
            [
                'key' => 'favicon_icon',
                'value' => 'https://file.storage.techupzone.com/linkstar-stg/public/upload/c690285d-4348-4ba4-b982-1e12d2334a5f_1677036055.png',
                'type' => 'image',
                'group_id' => 1,
                'status' => 'public'

            ],
            [
                'key' => 'telegram_price',
                'value' => 1,
                'type' => 'number',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'viber_price',
                'value' => 1,
                'type' => 'number',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'paypal_sandbox_client_id',
                'value' => 'AQfFudqpGq23ZIWGvKobd3a78GX_UdfwOx-w9Ui9TwQnen2l_5X66pu9wG2Yp1fpcLS03GChh-4lm_tw',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'paypal_sandbox_client_secret',
                'value' => 'EOSUPJirjSyoq9mbKYRxFUDCyrjO35J_VpjRfIzWo7dYBc338Div_jBkMEeH2RbnDK5Cs_jo7SctXP2z',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => 'sk_test_51Kd3VBICYCkcIoDDo8WsLk3tSPwU3VATNZJxXPSwHCzW2raGtYIWsUNFPK5cxdgNCxNAEGU51oevF8YwtKKTRlsT00ffEHXQF3',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'stripe_endpoint_secret_key',
                'value' => 'whsec_ffee58f7a216e44bc30f933a1721f2015fdc59cfad949e6dc019bfd14ea4b28b',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'user_default_avatar',
                'value' => 'https://file.storage.techupzone.com/linkstar-stg/public/upload/f1846881-7c9c-4d4a-84e7-77436444f6c1_1678936901.jpg',
                'type' => 'image',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'smtp_account',
                'value' => [
                    'mail_host' => 'smtp.mailtrap.io',
                    'mail_port' => 2525,
                    'mail_username' => '42ba760e85ce70',
                    'mail_password' => 'e1357279191d55',
                    'mail_encryption' => 'tls',
                    'mail_from_address' => 'Admin123@techupcorp.com',
                    'mail_from_name' => 'Sending Email',
                ],
                'type' => 'array',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'expired_time',
                'value' => 5,
                'type' => 'number',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'blocked_time',
                'value' => 1,
                'type' => 'number',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'refresh_time',
                'value' => 90,
                'type' => 'number',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'refresh_count',
                'value' => 3,
                'type' => 'number',
                'group_id' => 1,
                'status' => 'system'
            ],
            [
                'key' => 'wrong_count',
                'value' => 5,
                'type' => 'number',
                'group_id' => 1,
                'status' => 'system'
            ],
        ];

        foreach ($configs as $config) {
            Config::updateOrCreate(
                [
                    'key' => $config['key'],
                ],
                [
                    'value' => $config['value'],
                    'group_id' => $config['group_id'],
                    'type' => $config['type'],
                    'status' => $config['status']
                ]
            );
        }
    }
}
