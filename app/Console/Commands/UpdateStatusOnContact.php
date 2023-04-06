<?php

namespace App\Console\Commands;

use App\Events\UpdateContactByStatusEvent;
use App\Services\ActivityHistoryService;
use App\Services\ContactService;
use App\Services\StatusService;
use Illuminate\Console\Command;

class UpdateStatusOnContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:contact';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Status On All Contact';

    /**
     * @var ActivityHistoryService
     */
    public $contactService;

    /**
     * @var StatusService
     */
    public $statusService;

    /**
     * @param ContactService $contactService
     * @param StatusService $statusService
     */
    public function __construct(
        ContactService $contactService,
        StatusService  $statusService
    )
    {
        parent::__construct();
        $this->contactService = $contactService;
        $this->statusService = $statusService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        UpdateContactByStatusEvent::dispatch();
    }
}
