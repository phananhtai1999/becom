<?php

namespace App\Listeners;

use App\Events\SendByCampaignEvent;
use App\Notifications\BaseNotification;
use App\Services\CampaignService;
use App\Services\UserProfileService;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\ContactService;
use App\Services\CreditHistoryService;
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SendEmailScheduleLogService;
use App\Services\SmtpAccountService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendByCampaignListener implements ShouldQueue {
	/**
	 * @var MailTemplateVariableService
	 */
	private $mailTemplateVariableService;

	/**
	 * @var MailSendingHistoryService
	 */
	private $mailSendingHistoryService;

	/**
	 * @var SmtpAccountService
	 */
	private $smtpAccountService;

	/**
	 * @var CampaignService
	 */
	private $campaignService;

	/**
	 * @var EmailService
	 */
	private $emailService;

	/**
	 * @var SendEmailScheduleLogService
	 */
	private $sendEmailScheduleLogService;

	/**
	 * @var ContactService
	 */
	private $contactService;

	/**
	 * @var CreditHistoryService
	 */
	private $creditHistoryService;

	/**
	 * @var ConfigService
	 */
	private $configService;

	/**
	 * @var int
	 */
	private $creditReturn = 0;

	/**
	 * @var bool
	 */
	private $mailSuccess = true;

	/**
	 * @var int
	 */
	private $numberEmailSentPerDate = 0;

	/**
	 * @param MailTemplateVariableService $mailTemplateVariableService
	 * @param MailSendingHistoryService $mailSendingHistoryService
	 * @param SmtpAccountService $smtpAccountService
	 * @param CampaignService $campaignService
	 * @param EmailService $emailService
	 * @param SendEmailScheduleLogService $sendEmailScheduleLogService
	 * @param ContactService $contactService
	 * @param CreditHistoryService $creditHistoryService
	 * @param ConfigService $configService
	 */
	public function __construct(
		MailTemplateVariableService $mailTemplateVariableService,
		MailSendingHistoryService $mailSendingHistoryService,
		SmtpAccountService $smtpAccountService,
		CampaignService $campaignService,
		EmailService $emailService,
		SendEmailScheduleLogService $sendEmailScheduleLogService,
		ContactService $contactService,
		CreditHistoryService $creditHistoryService,
		ConfigService $configService,
        UserProfileService $userProfileService
	) {
		$this->mailTemplateVariableService = $mailTemplateVariableService;
		$this->mailSendingHistoryService = $mailSendingHistoryService;
		$this->smtpAccountService = $smtpAccountService;
		$this->campaignService = $campaignService;
		$this->emailService = $emailService;
		$this->sendEmailScheduleLogService = $sendEmailScheduleLogService;
		$this->contactService = $contactService;
		$this->creditHistoryService = $creditHistoryService;
		$this->configService = $configService;
		$this->userProfileService = $userProfileService;
	}

	/**
	 * @param SendByCampaignEvent $event
	 * @return void
	 * @throws \Illuminate\Contracts\Container\BindingResolutionException
	 */
	public function handle(SendByCampaignEvent $event) {
		$campaign = $event->campaign;
		$emailNotification = app()->makeWith(BaseNotification::class, ['campaign' => $campaign])->getAdapter();
		$creditNumberSendByCampaign = $emailNotification->calculatorCredit();

		if (!$this->userProfileService->checkCredit($creditNumberSendByCampaign, $campaign->user_uuid)) {
			$this->campaignService->update($campaign, [
				'was_stopped_by_owner' => true,
			]);
		} else {
			$config = $this->configService->findConfigByKey('send_by_connector');
			if ($config && $config->value_formatted) {
		        $emailNotification->sending_by_conecttor($emailNotification->getContacts(), null, $creditNumberSendByCampaign);
			} else {
				$emailNotification->send($emailNotification->getContacts(), null, $creditNumberSendByCampaign);
			}
		}
	}
}
