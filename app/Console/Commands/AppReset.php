<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

class AppReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all commands to reset app';

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
     * @return mixed
     */
    public function handle()
    {
        Artisan::call('clear-compiled');
        Artisan::call('optimize');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('config:clear');
        Artisan::call('queue:flush');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
    }
}
