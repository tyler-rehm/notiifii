<?php

namespace App\Jobs;

use App\Contact;
use Carbon\Carbon;
use Dompdf\Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Twilio\Rest\Client;
use App\Sms;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;
    protected $sms;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sms $sms)
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
        $this->sms = $sms;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $message = $this->client->messages->create(
                $this->sms->to_number, // Text this number
                array(
                    'from' => $this->sms->from_number, // From a valid Twilio number
                    'body' => $this->sms->body,
                    'statusCallback' => url('/sms/update')
                )
            );

            $this->sms->external_id = $message->sid;
            $this->sms->units = $message->numSegments;
            $this->sms->status = $message->status;
            $this->sms->{$message->status} = Carbon::now();

            if(!empty($message->errorCode)){
                $this->sms->error_code = $message->errorCode;
                $this->sms->error_message = $message->errorMessage;
            }

            $this->sms->fulfilled = true;
            $this->sms->save();

        } catch (Exception $exception){

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
