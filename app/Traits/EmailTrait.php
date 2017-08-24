<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Requsest;
use App\Jobs\SendEmailJob;
use App\Message;
use App\Contact;
use App\Email;
use App\Schedule;
use App\Emails\CampaignMonitor;

trait EmailTrait
{
    private $email_channel = 4;

    public function _sendAllEmails()
    {
        $all = Email::where(['fulfilled' => 'false'])->with('schedule')->with('message.contact')->get();
        foreach($all as $email){
            if($this->_allowSend($email->schedule, $email->message)){
                $this->_sendEmail($email);
            }
        }
    }

    public function _sendEmail(Email $email)
    {
        $job = (new SendEmailJob($email))->onQueue('email')->delay(60);
        $this->dispatch($job);
    }

    public function _scheduleEmail($message_id)
    {
        $message = Message::where('uuid', $message_id)->with(['company', 'contact', 'type', 'status'])->firstOrFail();

        $email = new Email();
        $email->message_id = $message->id;
        $email->email_address = $message->contact->email_address;
        $email->direction = 'outbound';
        $email->schedule_id = $this->_getSchedule($message->company_id, $message->type_id, $this->email_channel, 'id');

        $email->save();
    }

    public function emailsUpdate()
    {
        $all = Email::where(['fulfilled' => true, 'status' => 'sent'])->get();
        foreach($all as $email){
            $client = new CampaignMonitor();
            $client->get($email->external_id);
        }
    }


    public function _receiveEmail(Request $request, $id)
    {

    }
}