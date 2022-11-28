<?php

$maximumCredit = config('limitcredit.maximum_credit');
$minimumCredit = config('limitcredit.minimum_credit');

return [

    'success' => 'Thành công',
    'unauthorized' => 'Không được phép',
    'given_data_invalid' => 'Dữ iệu không hợp lệ',
    'internal_server_error' => 'Lỗi nội bộ máy chủ',
    'secret_key_invalid' => 'Khóa không hợp lệ',
    'email_already_verified' => 'Email đã được xác minh',
    'pin_invalid' => 'Mã không hợp lệ',
    'login_success' => 'Đăng nhập thành công',
    'logout_success' => 'Đăng xuất thành công',
    'change_password_success' => 'Thay đổi mật khẩu thành công',
    'token_does_not_exists' => 'Token không tồn tại',
    'register_success' => 'Đăng ký thành công',
    'reset_password' => 'Mật khẩu mới đã được gửi đến email của bạn!',
    'email_does_not_exists' => 'Email không tồn tại',
    'account_banned' => 'Tài khoản của bạn đã bị cấm trong hệ thống',
    'account_deleted' => 'Tài khoản của bạn không còn trong hệ thống',
    'date_format' => 'Ngày sinh không khớp với định dạng Y-m-d.',
    'campaign_invalid' => 'Campaign Uuid đã chọn không hợp lệ.',
    'credit_invalid' => "Tín dụng của người dùng không hợp lệ.",
    'smtp_account_invalid' => 'Smtp Account Uuid đã chọn không hợp lệ.',
    'send_campaign_success' => 'Gửi email theo chiến dịch thành công.',
    'error_data' => 'Lỗi trên dòng',
    'birthday_campaign_have_not_scenario' => 'Chiến dịch sinh nhật không có chiến dịch kịch bản',
    'minimum_money' => 'Số tiền tối thiểu là 2000 VND',
    'maximum_money' => 'Số tiền tối đa là 50000000 VND',
    'limit_maximum_and_minimum_credit' => "Số credit nạp tối da là $maximumCredit, Số credit nạp tối thiểu là $minimumCredit",
    'is_running_campaign_invalid' => "Chiến dịch đang chạy!",
    'type_campaign_invalid' => "Loại chiến dịch không hợp lệ!",
    'send_type_campaign_invalid' => "Loại gửi chiến dịch không hợp lệ!",
    'from_date_campaign_invalid' => "Chiến dịch vẫn chưa bắt đầu!",
    'to_date_campaign_invalid' => "Chiến dịch đã hết hạn",
    'was_finished_campaign_invalid' => "Chiến dịch đã kết thúc!",
    'was_stopped_by_owner_campaign_invalid' => "Chiến dịch đã dừng bởi chủ sở hữu!",
    "status_campaign_invalid" => "Trạng thái chiến dịch không hợp lệ!"
];
