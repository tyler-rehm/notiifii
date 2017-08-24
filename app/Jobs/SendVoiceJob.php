<?php

namespace App\Jobs;

use App\Contact;
use App\Message;
use App\Voice;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Twilio\Rest\Client;
use Twilio\Twiml;

class SendVoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;
    protected $voice;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Voice $voice)
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
        $this->voice = $voice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $call = $this->client->account->calls->create(
                $this->voice->phone_number,
                '17272034587',
                array(
                    "url" => url('/voice/outbound/' . $this->voice->script),
                    "statusCallbackEvent" => ["initiated", "ringing", "answered", "completed"],
                    "statusCallback" => url('/voice/update'),
                    "statusCallbackMethod" => "POST"
                )
            );

            $this->voice->external_id = $call->sid;
            $this->voice->status = $call->status;
            $this->voice->{$call->status} = Carbon::now();

            if(!empty($call->errorCode)){
                $this->voice->error_code = $call->errorCode;
                $this->voice->error_message = $call->errorMessage;
            }

            $this->voice->fulfilled = true;
            $this->voice->save();
        } catch (Exception $e) {

        }
    }

  /**
   * Handle a job failure.
   *
   * @return void
   */
  public function failed()
  {
    // Called when the job is failing...
  }
}
