<?php

namespace Database\Seeders;

use App\Models\Config;
use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ConfigSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $companyGroupUuid = Group::where('name', 'company')->first()->uuid;
        $paymentGroupUuid = Group::where('name', 'payment')->first()->uuid;
        $otpGroupUuid = Group::where('name', 'otp')->first()->uuid;
        $s3GroupUuid = Group::where('name', 's3')->first()->uuid;
        $siteGroupUuid = Group::where('name', 'site')->first()->uuid;
        $assetGroupUuid = Group::where('name', 'asset')->first()->uuid;
        $payoutGroupUuid = Group::where('name', 'payout')->first()->uuid;
        $generalGroupUuid = Group::where('name', 'general')->first()->uuid;
        $mailboxGroupUuid = Group::where('name', 'mailbox')->first()->uuid;
        $configs = [
            [
                'key' => 'smtp_auto',
                'value' => 1,
                'type' => 'boolean',
                'group_id' => 1,
                'status' => 'public',
            ],
            [
                'key' => 'email_price',
                'value' => 1,
                'type' => 'numeric',
                'group_id' => 1,
                'status' => 'system',
            ],
            [
                'key' => 'sms_price',
                'value' => 5,
                'type' => 'numeric',
                'group_id' => 1,
                'status' => 'system',
            ],
            [
                'key' => 'footer_linkedin',
                'value' => 'https://www.linkedin.com',
                'type' => 'string',
                'group_id' => $siteGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'footer_twitter',
                'value' => 'https://www.twitter.com',
                'type' => 'string',
                'group_id' => 1,
                'status' => 'system',
            ],
            [
                'key' => 'footer_instagram',
                'value' => 'https://www.instagram.com',
                'type' => 'string',
                'group_id' => $siteGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'footer_facebook',
                'value' => 'https://www.facebook.com',
                'type' => 'string',
                'group_id' => $siteGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'site_name',
                'value' => 'Mail',
                'type' => 'string',
                'group_id' => $siteGroupUuid,
                'status' => 'public',
            ],
            [
                'key' => 'logo',
                'value' => 'https://file.storage.techupzone.com/linkstar-stg/public/upload/c690285d-4348-4ba4-b982-1e12d2334a5f_1677036055.png',
                'type' => 'image',
                'group_id' => $siteGroupUuid,
                'status' => 'public',
            ],
            [
                'key' => 'favicon_icon',
                'value' => 'https://file.storage.techupzone.com/linkstar-stg/public/upload/c690285d-4348-4ba4-b982-1e12d2334a5f_1677036055.png',
                'type' => 'image',
                'group_id' => $siteGroupUuid,
                'status' => 'public',

            ],
            [
                'key' => 'telegram_price',
                'value' => 1,
                'type' => 'numeric',
                'group_id' => 1,
                'status' => 'system',
            ],
            [
                'key' => 'viber_price',
                'value' => 1,
                'type' => 'numeric',
                'group_id' => 1,
                'status' => 'system',
            ],
            [
                'key' => 'paypal_client_id',
                'value' => 'AQfFudqpGq23ZIWGvKobd3a78GX_UdfwOx-w9Ui9TwQnen2l_5X66pu9wG2Yp1fpcLS03GChh-4lm_tw',
                'type' => 'string',
                'group_id' => $paymentGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'paypal_client_secret',
                'value' => 'EOSUPJirjSyoq9mbKYRxFUDCyrjO35J_VpjRfIzWo7dYBc338Div_jBkMEeH2RbnDK5Cs_jo7SctXP2z',
                'type' => 'string',
                'group_id' => $paymentGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'payment_mode',
                'value' => 'sandbox',
                'type' => 'string',
                'group_id' => $paymentGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'paypal_app_id',
                'value' => 'APP-80W284485P519543T',
                'type' => 'string',
                'group_id' => $paymentGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => 'sk_test_51Kd3VBICYCkcIoDDo8WsLk3tSPwU3VATNZJxXPSwHCzW2raGtYIWsUNFPK5cxdgNCxNAEGU51oevF8YwtKKTRlsT00ffEHXQF3',
                'type' => 'string',
                'group_id' => $paymentGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'stripe_endpoint_secret_key',
                'value' => 'whsec_ffee58f7a216e44bc30f933a1721f2015fdc59cfad949e6dc019bfd14ea4b28b',
                'type' => 'string',
                'group_id' => $paymentGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'user_default_avatar',
                'value' => 'https://file.storage.techupzone.com/linkstar-stg/public/upload/f1846881-7c9c-4d4a-84e7-77436444f6c1_1678936901.jpg',
                'type' => 'image',
                'group_id' => 1,
                'status' => 'system',
            ],
            [
                'key' => 'smtp_account',
                'value' => [
                    'mail_host' => 'smtp.mailtrap.io',
                    'mail_port' => '2525',
                    'mail_username' => '42ba760e85ce70',
                    'mail_password' => 'e1357279191d55',
                    'mail_encryption' => 'tls',
                    'mail_from_address' => 'Admin123@techupcorp.com',
                    'mail_from_name' => 'Sending Email',
                ],
                'type' => 'smtp_account',
                'group_id' => 1,
                'status' => 'system',
            ],
            [
                'key' => 'expired_time',
                'value' => 5,
                'type' => 'numeric',
                'group_id' => $otpGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'blocked_time',
                'value' => 1,
                'type' => 'numeric',
                'group_id' => $otpGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'refresh_time',
                'value' => 90,
                'type' => 'numeric',
                'group_id' => $otpGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'refresh_count',
                'value' => 3,
                'type' => 'numeric',
                'group_id' => $otpGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'wrong_count',
                'value' => 5,
                'type' => 'numeric',
                'group_id' => $otpGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'otp_status',
                'value' => true,
                'type' => 'boolean',
                'group_id' => $otpGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'send_by_connector',
                'value' => true,
                'type' => 'boolean',
                'status' => 'system',
                'group_id' => '1',
            ],
            [
                'key' => 'paypal_method',
                'value' => true,
                'type' => 'boolean',
                'status' => 'system',
                'group_id' => $paymentGroupUuid,
            ],
            [
                'key' => 'stripe_method',
                'value' => true,
                'type' => 'boolean',
                'status' => 'system',
                'group_id' => $paymentGroupUuid,
            ],
            [
                'key' => 's3_system',
                'value' => [
                    'driver' => 's3',
                    'key' => 'coFdon1aBuxcLZkI',
                    'secret' => 'iLO4jb2brk2sAJbsUsajWtr8xUPzeL4Y',
                    'region' => 'us-east-1',
                    'bucket' => 'linkstar-stg',
                    'url' => null,
                    'endpoint' => 'https://file.storage.techupzone.com',
                    'use_path_style_endpoint' => true,
                ],
                'type' => 's3',
                'group_id' => $s3GroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 's3_user',
                'value' => [
                    'driver' => 's3',
                    'key' => 'coFdon1aBuxcLZkI',
                    'secret' => 'iLO4jb2brk2sAJbsUsajWtr8xUPzeL4Y',
                    'region' => 'us-east-1',
                    'bucket' => 'linkstar-stg',
                    'url' => null,
                    'endpoint' => 'https://file.storage.techupzone.com',
                    'use_path_style_endpoint' => true,
                ],
                'type' => 's3',
                'group_id' => $s3GroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 's3_website',
                'value' => [
                    'driver' => 's3',
                    'key' => 'coFdon1aBuxcLZkI',
                    'secret' => 'iLO4jb2brk2sAJbsUsajWtr8xUPzeL4Y',
                    'region' => 'us-east-1',
                    'bucket' => 'linkstar-stg',
                    'url' => null,
                    'endpoint' => 'https://file.storage.techupzone.com',
                    'use_path_style_endpoint' => true,
                ],
                'type' => 's3',
                'group_id' => $s3GroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'company_name',
                'value' => 'Techup Zone',
                'type' => 'string',
                'status' => 'system',
                'group_id' => $companyGroupUuid,
            ],
            [
                'key' => 'company_website',
                'value' => 'www.send.techupzone.com',
                'type' => 'string',
                'status' => 'system',
                'group_id' => $companyGroupUuid,
            ],
            [
                'key' => 'support_email',
                'value' => 'support@send.techupzone.com',
                'type' => 'string',
                'status' => 'system',
                'group_id' => $companyGroupUuid,
            ],
            [
                'key' => 'company_address',
                'value' => '123 Street A - District 12 - HCM',
                'type' => 'string',
                'status' => 'system',
                'group_id' => $companyGroupUuid,
            ],
            [
                'key' => 'success_url',
                'value' => 'http://success.com',
                'type' => 'string',
                'status' => 'system',
                'group_id' => $companyGroupUuid,
            ],
            [
                'key' => 'main_url',
                'value' => 'https://send.techupzone.com/',
                'type' => 'string',
                'status' => 'system',
                'group_id' => $assetGroupUuid,
            ],
            [
                'key' => 'payout_fee',
                'value' => 1,
                'type' => 'numeric',
                'status' => 'system',
                'group_id' => $payoutGroupUuid,
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Ho_Chi_Minh',
                'type' => 'string',
                'status' => 'system',
                'group_id' => 1,
            ],
            [
                'key' => 'SITE_URL',
                'value' => 'https://site.techupzone.com',
                'type' => 'string',
                'status' => 'public',
                'group_id' => $generalGroupUuid,
            ],
            [
                'key' => 'BUILDER_URL',
                'value' => 'https://builder.techupzone.com',
                'type' => 'string',
                'status' => 'public',
                'group_id' => $generalGroupUuid,
            ],
            [
                'key' => 'mailbox_mx_domain',
                'value' => [
                    'name' => '@',
                    'type' => 'MX',
                    'priority' => '10',
                    'value' => 'box.mail.au1.sendgpt.ai',
                ],
                'type' => 'mailbox',
                'group_id' => $mailboxGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'mailbox_dmarc_domain',
                'value' => [
                    'name' => '@',
                    'type' => 'TXT',
                    'priority' => null,
                    'value' => 'v=DMARC1; p=quarantine',
                ],
                'type' => 'mailbox',
                'group_id' => $mailboxGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'mailbox_dkim_domain',
                'value' => [
                    'name' => '@',
                    'type' => 'TXT',
                    'priority' => null,
                    'value' => 'v=spf1 mx -all',
                ],
                'type' => 'mailbox',
                'group_id' => $mailboxGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'time_allowed_view_articles_of_editor',
                'value' => 10,
                'type' => 'numeric',
                'group_id' => $generalGroupUuid,
                'status' => 'system',
            ],
            [
                'key' => 'meta_tags',
                'value' => [
                    'titles' => [
                        'en' => 'TechupZone Email Marketing'
                    ],
                    'descriptions' => [
                        'en' => 'TechupZone Email Marketing'
                    ],
                    'keywords' => [
                        'en' => 'TechupZone Email Marketing'
                    ],
                    'image' => '',
                ],
                'type' => 'meta_tag',
                'group_id' => $generalGroupUuid,
                'status' => 'public',
            ],
        ];
        Cache::forget('config');
        foreach ($configs as $config) {
            Config::firstOrCreate(
                [
                    'key' => $config['key'],
                ],
                [
                    'value' => $config['value'],
                    'group_id' => $config['group_id'],
                    'type' => $config['type'],
                    'status' => $config['status'],
                ]
            );
        }
    }
}
