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
    'limit_maximum_and_minimum_credit' => "最大信用数为 $maximumCredit, 学分的最低数量是 $minimumCredit"
];
