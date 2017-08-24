<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\MessageTrait;
use App\Traits\VoiceTrait;

class SendVoice extends Command
{
    use MessageTrait;
    use VoiceTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voice:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send available Voice messages';

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
        $this->_sendAllVoices();
    }
}
