<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'uuid' => 1,
                'code' => 'unlimited_contacts',
                'name' => ['vi' => 'Liên hệ không giới hạn', 'en' => 'unlimited contacts'],
                'api_methods' => [
                    'create_contacts',
                    'update_contacts',
                    'delete_contacts',
                    'unlimited_contacts'
                ]
            ],
            [
                'uuid' => 2,
                'code' => '499_emails_day',
                'name' => ['vi' => '499 emails/ngày', 'en' => '499 emails/day'],
                'api_methods' => [
                    'create contacts',
                    'update_contacts',
                    'delete_contacts',
                    '499_emails_day'
                ]
            ],
            [
                'uuid' => 3,
                'code' => 'drag_drop_editor',
                'name' => ['vi' => 'Trình soạn thảo kéo và thả', 'en' => 'Drag & Drop Editor'],
                'api_methods' => [
                    'drag_drop_editor'
                ]
            ],
            [
                'uuid' => 4,
                'code' => 'birthday_campaigns',
                'name' => ['vi' => 'Chiến dịch sinh nhật', 'en' => 'Birthday Campaigns'],
                'api_methods' => [
                    'create_campaigns',
                    'update_campaigns',
                    'delete_campaigns',
                ]
            ],
            [
                'uuid' => 4,
                'code' => 'simple_automation_marketing',
                'name' => ['vi' => 'Tiếp thị tự động cơ bản', 'en' => 'Simple Automation Marketing'],
                'api_methods' => [
                    'create contacts',
                    'update_contacts',
                    'delete_contacts',
                    'unlimited_contacts'
                ]
            ],
            [
                'uuid' => 5,
                'code' => 'tracking_open_emails',
                'name' => ['vi' => 'Theo dõi email mở', 'en' => 'Tracking Open Emails'],
                'api_methods' => [
                    'mail_open_tracking'
                ]
            ],
            [
                'uuid' => 6,
                'code' => 'simple_sale_crm',
                'name' => ['vi' => 'Bán hàng cơ bản CRM', 'en' => 'Simple Sale CRM'],
                'api_methods' => [
                    'simple_sale_crm'
                ]
            ],
            [
                'uuid' => 7,
                'code' => 'basic_reporting_analytics',
                'name' => ['vi' => 'Báo cáo và phân tích cơ bản', 'en' => 'Basic reporting & analytics'],
                'api_methods' => [
                    'basic_reporting_analytics'
                ]
            ],
            [
                'uuid' => 8,
                'code' => 'no_daily_sending_limit',
                'name' => ['vi' => 'Không có giới hạn số chiến dịch gửi trong hàng ngày', 'en' => 'No daily sending limit'],
                'api_methods' => [
                    'create_campaigns',
                    'update_campaigns',
                    'delete_campaigns',
                    'unlimited_sending'
                ]
            ],
            [
                'uuid' => 9,
                'code' => 'no_mail_logo_add_on',
                'name' => ['vi' => 'Không có {{siteName}} logo (add-on)', 'en' => 'No Mail logo (add-on)'],
                'api_methods' => [
                    'no_mail_logo_add_on'
                ]
            ],
            [
                'uuid' => 10,
                'code' => 'sending_optimization',
                'name' => ['vi' => 'Tối ưu hóa gửi chiến dịch', 'en' => 'Sending optimization'],
                'api_methods' => [
                    'create_campaigns',
                    'update_campaigns',
                    'delete_campaigns',
                    'sending_optimization'
                ]
            ],
            [
                'uuid' => 11,
                'code' => 'sms_campaigns',
                'name' => ['vi' => 'Chiến dịch SMS', 'en' => 'SMS Campaigns'],
                'api_methods' => [
                    'create_campaigns',
                    'update_campaigns',
                    'delete_campaigns',
                    'send_campaigns_by_sms',
                ]
            ],
            [
                'uuid' => 12,
                'code' => 'advance_automation_marketing',
                'name' => ['vi' => 'Tiếp thị tự động nâng cao', 'en' => 'Advance Automation Marketing'],
                'api_methods' => [
                    'advance_automation_marketing'
                ]
            ],
            [
                'uuid' => 13,
                'code' => 'advance_reporting_analytics',
                'name' => ['vi' => 'Báo cáo & Phân tích nâng cao', 'en' => 'Advance reporting & analytics'],
                'api_methods' => [
                    'advance_reporting_analytics'
                ]
            ],
            [
                'uuid' => 14,
                'code' => 'advance_sale_crm',
                'name' => ['vi' => 'Bán hàng nâng cao CRM', 'en' => 'Advance Sale CRM'],
                'api_methods' => [
                    'advance_sale_crm'
                ]
            ],
            [
                'uuid' => 15,
                'code' => 'multi_user_access',
                'name' => ['vi' => 'Hỗ trợ nhiều người dùng truy cập', 'en' => 'Multi-user access'],
                'api_methods' => [
                    'multi_user_access'
                ]
            ],
            [
                'uuid' => 16,
                'code' => 'email_support',
                'name' => ['vi' => 'Hỗ trợ email', 'en' => 'Email support'],
                'api_methods' => [
                    'email_support'
                ]
            ],
            [
                'uuid' => 17,
                'code' => 'telegram_campaigns',
                'name' => ['vi' => 'Chiến dịch Telegram', 'en' => 'Telegram Campaigns'],
                'api_methods' => [
                    'create_campaigns',
                    'update_campaigns',
                    'delete_campaigns',
                    'send_campaigns_by_telegram',]
            ],
            [
                'uuid' => 18,
                'code' => 'viber_campaigns',
                'name' => ['vi' => 'Chiến dịch Viber', 'en' => 'Viber Campaigns'],
                'api_methods' => [
                    'create_campaigns',
                    'update_campaigns',
                    'delete_campaigns',
                    'send_campaigns_by_viber',
                ]
            ],
            [
                'uuid' => 19,
                'code' => 'fast_sending_optimization',
                'name' => ['vi' => 'Tối ưu gửi chiến dịch nhanh chóng', 'en' => 'Fast sending optimization'],
                'api_methods' => [
                    'fast_sending_optimization'
                ]
            ],
            [
                'uuid' => 20,
                'code' => 'advanced_marketing_automation',
                'name' => ['vi' => 'Tự động tiếp thị nâng cao', 'en' => 'Advanced Marketing Automation'],
                'api_methods' => [
                    'advanced_marketing_automation'
                ]
            ],
            [
                'uuid' => 21,
                'code' => 'send_gpt_marketing_automation',
                'name' => ['vi' => 'Sendgpt tiếp thị tự động', 'en' => 'SendGPT Marketing Automation'],
                'api_methods' => [
                    'send_gpt_marketing_automation'
                ]
            ],
            [
                'uuid' => 22,
                'code' => 'send_gpt_marketing_automation',
                'name' => ['vi' => 'Sendgpt tiếp thị tự động', 'en' => 'Advanced Statistics'],
                'api_methods' => [
                    'mail_open_tracking'
                ]
            ],
            [
                'uuid' => 23,
                'code' => 'advanced_statistics',
                'name' => ['vi' => 'Thống kê nâng cao', 'en' => 'SendGPT Marketing Automation'],
                'api_methods' => [
                    'advanced_statistics'
                ]
            ],
            [
                'uuid' => 24,
                'code' => 'team_access_management',
                'name' => ['vi' => 'Quản lý truy cập nhóm', 'en' => 'Team Access Management'],
                'api_methods' => [
                    'team_access_management'
                ]
            ],
            [
                'uuid' => 25,
                'code' => 'form_integration',
                'name' => ['vi' => 'Biểu mẫu tích hợp', 'en' => 'Form Integration'],
                'api_methods' => [
                    'form_integration'
                ]
            ],
            [
                'uuid' => 26,
                'code' => 'landing_page_creator',
                'name' => ['vi' => 'Tạo trang quảng cáo', 'en' => 'Landing Page Creator'],
                'api_methods' => [
                    'landing_page_creator'
                ]
            ],
            [
                'uuid' => 27,
                'code' => '247_chat_support',
                'name' => ['vi' => 'Chăm sóc khách hàng 247', 'en' => '247 chat support'],
                'api_methods' => [
                    'mail_open_tracking'
                ]
            ],
            [
                'uuid' => 28,
                'code' => 'enterprise_grade_security',
                'name' => ['vi' => 'Bảo mật cấp doanh nghiệp', 'en' => 'Enterprise-grade Security'],
                'api_methods' => [
                    'enterprise_grade_security'
                ]
            ],
            [
                'uuid' => 29,
                'code' => 'advanced_integrations',
                'name' => ['vi' => 'Tích hợp nâng cao', 'en' => 'Advanced Integrations'],
                'api_methods' => [
                    'advanced_integrations'
                ]
            ],
            [
                'uuid' => 30,
                'code' => 'sub_account_management',
                'name' => ['vi' => 'Quản lý tài khoản phụ', 'en' => 'Sub-account Management'],
                'api_methods' => [
                    'mail_open_tracking'
                ]
            ],
            [
                'uuid' => 31,
                'code' => 'tailored_onboarding',
                'name' => ['vi' => 'Phù hợp cho quá trình phát triển', 'en' => 'Tailored Onboarding'],
                'api_methods' => [
                    'tailored_onboarding'
                ]
            ],
            [
                'uuid' => 32,
                'code' => 'personalized_support',
                'name' => ['vi' => 'Hỗ trợ cá nhân hóa', 'en' => 'Personalized support'],
                'api_methods' => [
                    'personalized_support'
                ]
            ],
            [
                'uuid' => 33,
                'code' => 'flexible_contract',
                'name' => ['vi' => 'Hợp đồng linh hoạt', 'en' => 'Flexible contract'],
                'api_methods' => [
                    'flexible_contract'
                ]
            ],
            [
                'uuid' => 34,
                'code' => 'telephone_support',
                'name' => ['vi' => 'Hỗ trợ điện thoại', 'en' => 'Telephone Support'],
                'api_methods' => [
                    'telephone_support'
                ]
            ],

        ];
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['uuid' => $permission['uuid']], $permission);
        }
    }
}
