<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\MessageTrait;
use App\Traits\SmsTrait;

class SendSms extends Command
{
    use MessageTrait;
    use SmsTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send available SMS messages';

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
        $this->_sendAllSms();
    }
}
