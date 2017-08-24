<?php

namespace App\Jobs;

use App\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use App\EmailTemplate;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $client;
    protected $email;
    protected $template;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->template = EmailTemplate::where(["company_id" => $this->email->message->company_id, "type_id" => $this->email->message->type_id])->first();
        $parts = explode('.', $this->template->template);
        $class = '\\App' . '\\Emails';
        foreach($parts as $part){
            $class .= '\\' . $part;
        }

        if(class_exists($class)){
            $email = new $class($this->email, $this->email->message->parameters, $this->template);

            $result = $email->send();

            $this->email->status = $result->response[0]->Status;
            $this->email->external_id = $result->response[0]->MessageID;
            $this->email->fulfilled = true;
            $this->email->save();
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
