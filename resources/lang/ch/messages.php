<?php

$maximumCredit = config('limitcredit.maximum_credit');
$minimumCredit = config('limitcredit.minimum_credit');

return [

    'success' => '成功',
    'unauthorized' => '未经授权',
    'given_data_invalid' => '给定的数据无效',
    'internal_server_error' => '内部服务器错误',
    'secret_key_invalid' => '密钥无效',
    'email_already_verified' => '电子邮件已经过验证',
    'pin_invalid' => '无效的代码',
    'login_success' => '登录成功',
    'logout_success' => '注销成功',
    'change_password_success' => '修改密码成功',
    'token_does_not_exists' => '令牌不存在',
    'register_success' => '注册成功',
    'reset_password' => '新密码已发送至您的电子邮箱!',
    'email_does_not_exists' => '电子邮件不存在',
    'account_banned' => '您的帐户在系统中被禁止',
    'account_deleted' => '您的帐户在系统中不再可用',
    'date_format' => '出生日期与格式 Y-m-d 不对应。',
    'campaign_invalid' => '所选广告系列 uuid 无效',
    'smtp_account_invalid' => '所选广告系列 uuid 无效.',
    'credit_invalid' => "用户信用无效",
    'send_campaign_success' => '按活动成功发送电子邮件',
    'error_data' => '在线错误',
    'birthday_campaign_have_not_scenario' => '生日活动未选择场景活动',
    'minimum_money' => '最低金额为 2000 越南盾',
    'maximum_money' => '最高金额为 50,000,000 越南盾',
    'limit_maximum_and_minimum_credit' => "最大信用数为 $maximumCredit, 学分的最低数量是 $minimumCredit",
    'is_running_campaign_invalid' => "活动正在运行!",
    'type_campaign_invalid' => "活动类型无效!",
    'send_type_campaign_invalid' => "活动发送类型无效!",
    'from_date_campaign_invalid' => "活动还没有开始!",
    'to_date_campaign_invalid' => "活动已过期!",
    'was_finished_campaign_invalid' => "活动已结束!",
    'was_stopped_by_owner_campaign_invalid' => "活动被所有者停止了!",
    "status_campaign_invalid" => "活动状态无效!",
    "sent_mail_success" => "邮件发送成功y",
    "mail_username_already_taken" => "邮件用户名已被占用",
    "send_type_campaign_error" => "请输入与邮件模板相同的“类型”",
    "test_send_campaign_success" => "成功测试按活动发送电子邮件",
    "contact_list_uuid_invalid" => "所选联系人列表 uuid 无效。",
    "create_scenario_success" => "创建活动场景成功",
    "source_only_one_null" => "源中只有一个空值.",
    "parent_source_not_found" => "找不到上面的父源",
    "id_duplicated" => "所选 ID 不能重复.",
    "type_source_error" => '每个来源最多应有两种类型：“开放”和“未开放”。',
    "edit_scenario_success" => "编辑活动场景成功",
];
