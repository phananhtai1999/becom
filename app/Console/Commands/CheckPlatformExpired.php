<?php

namespace App\Console\Commands;

use App\Models\App;
use App\Services\UserAddOnService;
use App\Services\UserAppService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckPlatformExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:platform-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Platform Expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserAddOnService $userAddOnService, UserAppService $userPlatformPackageService)
    {
        $this->userAddOnService = $userAddOnService;
        $this->userPlatformPackageService = $userPlatformPackageService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userAddOns = $this->userAddOnService->findAllWhere();
        foreach ($userAddOns as $userAddOn) {
            if($userAddOn->expiration_date < Carbon::now()) {
                $this->userAddOnService->destroy($userAddOn->uuid);
            }
        }

        $userPlatformPackages = $this->userPlatformPackageService->findAllWhere([['platform_package_uuid' , '!=', 'starter']]);
        foreach ($userPlatformPackages as $userPlatformPackage) {
            if($userPlatformPackage->expiration_date < Carbon::now()) {
                $this->userPlatformPackageService->destroy($userPlatformPackage->uuid);
                $this->userPlatformPackageService->create([
                    'platform_package_uuid' => App::DEFAULT_PLATFORM_PACKAGE_1,
                    'user_uuid' => $userPlatformPackage->user_uuid
                ]);
            }
        }
    }
}
