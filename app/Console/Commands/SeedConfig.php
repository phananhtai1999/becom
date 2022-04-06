<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create seed config';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
