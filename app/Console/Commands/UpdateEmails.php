<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\MessageTrait;
use App\Traits\EmailTrait;

class UpdateEmails extends Command
{
    use MessageTrait;
    use EmailTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update sent Email messages';

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
        $this->emailsUpdate();
    }
}
