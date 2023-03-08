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
                'name' => ['vi' => 'Liên hệ không giới hạn', 'en' => 'unlimited contacts']
            ],
            [
                'code' => '499_emails_day',
                'name' => ['vi' => '499 emails/ngày', 'en' => '499 emails/day']
            ],
            [
                'code' => 'drag_drop_editor',
                'name' => ['vi' => 'Trình soạn thảo kéo và thả', 'en' => 'Drag & Drop Editor']
            ],
            [
                'code' => 'birthday_campaigns',
                'name' => ['vi' => 'Chiến dịch sinh nhật', 'en' => 'Birthday Campaigns']
            ],
            [
                'code' => 'simple_automation_marketing',
                'name' => ['vi' => 'Tiếp thị tự động cơ bản', 'en' => 'Simple Automation Marketing']
            ],
            [
                'code' => 'tracking_open_emails',
                'name' => ['vi' => 'Theo dõi email mở', 'en' => 'Tracking Open Emails']
            ],
            [
                'code' => 'simple_sale_crm',
                'name' => ['vi' => 'Bán hàng cơ bản CRM', 'en' => 'Simple Sale CRM']
            ],
            [
                'code' => 'basic_reporting_analytics',
                'name' => ['vi' => 'Báo cáo và phân tích cơ bản', 'en' => 'Basic reporting & analytics']
            ],
            [
                'code' => 'no_daily_sending_limit',
                'name' => ['vi' => 'Không có giới hạn số chiến dịch gửi trong hàng ngày', 'en' => 'No daily sending limit']
            ],
            [
                'code' => 'no_mail_logo_add_on',
                'name' => ['vi' => 'Không có {{siteName}} logo (add-on)', 'en' => 'No Mail logo (add-on)']
            ],
            [
                'code' => 'sending_optimization',
                'name' => ['vi' => 'Tối ưu hóa gửi chiến dịch', 'en' => 'Sending optimization']
            ],
            [
                'code' => 'sms_campaigns',
                'name' => ['vi' => 'Chiến dịch SMS', 'en' => 'SMS Campaigns']
            ],
            [
                'code' => 'advance_automation_marketing',
                'name' => ['vi' => 'Tiếp thị tự động nâng cao', 'en' => 'Advance Automation Marketing']
            ],
            [
                'code' => 'advance_reporting_analytics',
                'name' => ['vi' => 'Báo cáo & Phân tích nâng cao', 'en' => 'Advance reporting & analytics']
            ],
            [
                'code' => 'advance_sale_crm',
                'name' => ['vi' => 'Bán hàng nâng cao CRM', 'en' => 'Advance Sale CRM']
            ],
            [
                'code' => 'multi_user_access',
                'name' => ['vi' => 'Hỗ trợ nhiều người dùng truy cập', 'en' => 'Multi-user access']
            ],
            [
                'code' => 'email_support',
                'name' => ['vi' => 'Hỗ trợ email', 'en' => 'Email support']
            ],
            [
                'code' => 'telegram_campaigns',
                'name' => ['vi' => 'Chiến dịch Telegram', 'en' => 'Telegram Campaigns']
            ],
            [
                'code' => 'viber_campaigns',
                'name' => ['vi' => 'Chiến dịch Viber', 'en' => 'Viber Campaigns']
            ],
            [
                'code' => 'fast_sending_optimization
',
                'name' => ['vi' => 'Tối ưu gửi chiến dịch nhanh chóng', 'en' => 'Fast sending optimization']
            ],
            [
                'code' => 'advanced_marketing_automation',
                'name' => ['vi' => 'Tự động tiếp thị nâng cao', 'en' => 'Advanced Marketing Automation']
            ],
            [
                'code' => 'send_gpt_marketing_automation',
                'name' => ['vi' => 'Sendgpt tiếp thị tự động', 'en' => 'SendGPT Marketing Automation']
            ],
            [
                'code' => 'send_gpt_marketing_automation',
                'name' => ['vi' => 'Sendgpt tiếp thị tự động', 'en' => 'Advanced Statistics
']
            ],
            [
                'code' => 'advanced_statistics',
                'name' => ['vi' => 'Thống kê nâng cao', 'en' => 'SendGPT Marketing Automation']
            ],
            [
                'code' => 'team_access_management',
                'name' => ['vi' => 'Quản lý truy cập nhóm', 'en' => 'Team Access Management']
            ],
            [
                'code' => 'form_integration',
                'name' => ['vi' => 'Biểu mẫu tích hợp', 'en' => 'Form Integration']
            ],
            [
                'code' => 'landing_page_creator',
                'name' => ['vi' => 'Tạo trang quảng cáo', 'en' => 'Landing Page Creator']
            ],
            [
                'code' => '247_chat_support',
                'name' => ['vi' => 'Chăm sóc khách hàng 247', 'en' => '247 chat support']
            ],
            [
                'code' => 'enterprise_grade_security',
                'name' => ['vi' => 'Bảo mật cấp doanh nghiệp', 'en' => 'Enterprise-grade Security']
            ],
            [
                'code' => 'advanced_integrations',
                'name' => ['vi' => 'Tích hợp nâng cao', 'en' => 'Advanced Integrations']
            ],
            [
                'code' => 'sub_account_management',
                'name' => ['vi' => 'Quản lý tài khoản phụ', 'en' => 'Sub-account Management']
            ],
            [
                'code' => 'tailored_onboarding',
                'name' => ['vi' => 'Phù hợp cho quá trình phát triển', 'en' => 'Tailored Onboarding']
            ],
            [
                'code' => 'personalized_support',
                'name' => ['vi' => 'Hỗ trợ cá nhân hóa', 'en' => 'Personalized support']
            ],
            [
                'code' => 'flexible_contract',
                'name' => ['vi' => 'Hợp đồng linh hoạt', 'en' => 'Flexible contract']
            ],
            [
                'code' => 'telephone_support',
                'name' => ['vi' => 'Hỗ trợ điện thoại', 'en' => 'Telephone Support']
            ],

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }
}
