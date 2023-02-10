<?php

$maximumCredit = config('limitcredit.maximum_credit');
$minimumCredit = config('limitcredit.minimum_credit');

return [

    'success' => 'Success',
    'unauthorized' => 'Unauthorized',
    'given_data_invalid' => 'The given data was invalid',
    'internal_server_error' => 'Internal server error',
    'secret_key_invalid' => 'Secret_key is invalid',
    'email_already_verified' => 'Email already was be verified',
    'pin_invalid' => 'Pin code is invalid',
    'login_success' => 'Login successfully',
    'logout_success' => 'Logout successfully',
    'change_password_success' => 'Change password successfully',
    'token_does_not_exists' => 'Token does not exists',
    'register_success' => 'Register successfully',
    'reset_password' => 'We have e-mailed your password reset link!',
    'email_does_not_exists' => 'Email does not exists',
    'account_banned' => 'Your account is banned in the system',
    'account_deleted' => 'Your account is no longer available in the system',
    'date_format' => 'The dob does not match the format Y-m-d.',
    'campaign_invalid' => 'The selected campaign uuid is invalid.',
    'smtp_account_invalid' => 'The selected smtp account uuid is invalid.',
    'credit_invalid' => "User's credit is invalid.",
    'send_campaign_success' => 'Send Email By Campaign Success.',
    'error_data' => 'Error on line',
    'birthday_campaign_have_not_scenario' => 'Birthday campaign is not selected scenario campaign',
    'scenario_campaign_only_one_contact_list'  => 'Scenario campaign choose only one contact list',
    'minimum_money' => 'Minimum money number is 2000 VND',
    'maximum_money' => 'Maximum money number is 50000000 VND',
    'limit_maximum_and_minimum_credit' => "Maximum credit number is $maximumCredit, Minimum credit number is $minimumCredit",
    'is_running_campaign_invalid' => "Campaign is running!",
    'type_campaign_invalid' => "Campaign type is invalid!",
    'send_type_campaign_invalid' => "Campaign send type is invalid!",
    'from_date_campaign_invalid' => "Campaign hasn't started yet!",
    'to_date_campaign_invalid' => "Campaign has expired!",
    'was_finished_campaign_invalid' => "Campaign was finished!",
    'was_stopped_by_owner_campaign_invalid' => "Campaign was stopped by owner!",
    "status_campaign_invalid" => "Campaign status is invalid!",
    "data_not_deleted" => "This data cannot be deleted",
    "website_uuid_not_changed" => "Website uuid cannot be changed",
    "sent_mail_success" => "Mail Sent Successfully",
    "mail_username_already_taken" => "The mail username has already been taken."
];
