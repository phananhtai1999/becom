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
                'code' => 'drag_drop_editor',
                'name' => ['vi' => 'Trình soạn thảo kéo và thả', 'en' => 'Drag & Drop Editor'],
                'api_methods' => [
                    'drag_drop_editor'
                ]
            ],
            [
                'code' => 'birthday_campaigns',
                'name' => ['vi' => 'Chiến dịch sinh nhật', 'en' => 'Birthday Campaigns'],
                'api_methods' => [
                    'create_campaigns',
                    'update_campaigns',
                    'delete_campaigns',
                ]
            ],
            [
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
                'code' => 'tracking_open_emails',
                'name' => ['vi' => 'Theo dõi email mở', 'en' => 'Tracking Open Emails'],
                'api_methods' => [
                    'mail_open_tracking'
                ]
            ],
            [
                'code' => 'simple_sale_crm',
                'name' => ['vi' => 'Bán hàng cơ bản CRM', 'en' => 'Simple Sale CRM'],
                'api_methods' => [
                    'simple_sale_crm'
                ]
            ],
            [
                'code' => 'basic_reporting_analytics',
                'name' => ['vi' => 'Báo cáo và phân tích cơ bản', 'en' => 'Basic reporting & analytics'],
                'api_methods' => [
                    'basic_reporting_analytics'
                ]
            ],
            [
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
                'code' => 'no_mail_logo_add_on',
                'name' => ['vi' => 'Không có {{siteName}} logo (add-on)', 'en' => 'No Mail logo (add-on)'],
                'api_methods' => [
                    'no_mail_logo_add_on'
                ]
            ],
            [
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
                'code' => 'advance_automation_marketing',
                'name' => ['vi' => 'Tiếp thị tự động nâng cao', 'en' => 'Advance Automation Marketing'],
                'api_methods' => [
                    'advance_automation_marketing'
                ]
            ],
            [
                'code' => 'advance_reporting_analytics',
                'name' => ['vi' => 'Báo cáo & Phân tích nâng cao', 'en' => 'Advance reporting & analytics'],
                'api_methods' => [
                    'advance_reporting_analytics'
                ]
            ],
            [
                'code' => 'advance_sale_crm',
                'name' => ['vi' => 'Bán hàng nâng cao CRM', 'en' => 'Advance Sale CRM'],
                'api_methods' => [
                    'advance_sale_crm'
                ]
            ],
            [
                'code' => 'multi_user_access',
                'name' => ['vi' => 'Hỗ trợ nhiều người dùng truy cập', 'en' => 'Multi-user access'],
                'api_methods' => [
                    'multi_user_access'
                ]
            ],
            [
                'code' => 'email_support',
                'name' => ['vi' => 'Hỗ trợ email', 'en' => 'Email support'],
                'api_methods' => [
                    'email_support'
                ]
            ],
            [
                'code' => 'telegram_campaigns',
                'name' => ['vi' => 'Chiến dịch Telegram', 'en' => 'Telegram Campaigns'],
                'api_methods' => [
                    'create_campaigns',
                    'update_campaigns',
                    'delete_campaigns',
                    'send_campaigns_by_telegram',                ]
            ],
            [
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
                'code' => 'fast_sending_optimization',
                'name' => ['vi' => 'Tối ưu gửi chiến dịch nhanh chóng', 'en' => 'Fast sending optimization'],
                'api_methods' => [
                    'fast_sending_optimization'
                ]
            ],
            [
                'code' => 'advanced_marketing_automation',
                'name' => ['vi' => 'Tự động tiếp thị nâng cao', 'en' => 'Advanced Marketing Automation'],
                'api_methods' => [
                    'advanced_marketing_automation'
                ]
            ],
            [
                'code' => 'send_gpt_marketing_automation',
                'name' => ['vi' => 'Sendgpt tiếp thị tự động', 'en' => 'SendGPT Marketing Automation'],
                'api_methods' => [
                    'send_gpt_marketing_automation'
                ]
            ],
            [
                'code' => 'send_gpt_marketing_automation',
                'name' => ['vi' => 'Sendgpt tiếp thị tự động', 'en' => 'Advanced Statistics'],
                'api_methods' => [
                    'mail_open_tracking'
                ]
            ],
            [
                'code' => 'advanced_statistics',
                'name' => ['vi' => 'Thống kê nâng cao', 'en' => 'SendGPT Marketing Automation'],
                'api_methods' => [
                    'advanced_statistics'
                ]
            ],
            [
                'code' => 'team_access_management',
                'name' => ['vi' => 'Quản lý truy cập nhóm', 'en' => 'Team Access Management'],
                'api_methods' => [
                    'team_access_management'
                ]
            ],
            [
                'code' => 'form_integration',
                'name' => ['vi' => 'Biểu mẫu tích hợp', 'en' => 'Form Integration'],
                'api_methods' => [
                    'form_integration'
                ]
            ],
            [
                'code' => 'landing_page_creator',
                'name' => ['vi' => 'Tạo trang quảng cáo', 'en' => 'Landing Page Creator'],
                'api_methods' => [
                    'landing_page_creator'
                ]
            ],
            [
                'code' => '247_chat_support',
                'name' => ['vi' => 'Chăm sóc khách hàng 247', 'en' => '247 chat support'],
                'api_methods' => [
                    'mail_open_tracking'
                ]
            ],
            [
                'code' => 'enterprise_grade_security',
                'name' => ['vi' => 'Bảo mật cấp doanh nghiệp', 'en' => 'Enterprise-grade Security'],
                'api_methods' => [
                    'enterprise_grade_security'
                ]
            ],
            [
                'code' => 'advanced_integrations',
                'name' => ['vi' => 'Tích hợp nâng cao', 'en' => 'Advanced Integrations'],
                'api_methods' => [
                    'advanced_integrations'
                ]
            ],
            [
                'code' => 'sub_account_management',
                'name' => ['vi' => 'Quản lý tài khoản phụ', 'en' => 'Sub-account Management'],
                'api_methods' => [
                    'mail_open_tracking'
                ]
            ],
            [
                'code' => 'tailored_onboarding',
                'name' => ['vi' => 'Phù hợp cho quá trình phát triển', 'en' => 'Tailored Onboarding'],
                'api_methods' => [
                    'tailored_onboarding'
                ]
            ],
            [
                'code' => 'personalized_support',
                'name' => ['vi' => 'Hỗ trợ cá nhân hóa', 'en' => 'Personalized support'],
                'api_methods' => [
                    'personalized_support'
                ]
            ],
            [
                'code' => 'flexible_contract',
                'name' => ['vi' => 'Hợp đồng linh hoạt', 'en' => 'Flexible contract'],
                'api_methods' => [
                    'flexible_contract'
                ]
            ],
            [
                'code' => 'telephone_support',
                'name' => ['vi' => 'Hỗ trợ điện thoại', 'en' => 'Telephone Support'],
                'api_methods' => [
                    'telephone_support'
                ]
            ],

        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['code' =>  $permission['code']], $permission);
        }
    }
}
