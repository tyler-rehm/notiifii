<?php

namespace App\Traits;

use App\SmsTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\SendSmsJob;
use App\Message;
use App\Contact;
use App\Sms;
use App\Schedule;

trait SmsTrait
{
    private $sms_channel = 2;

    public function _sendAllSms()
    {
        $all = Sms::where(['fulfilled' => 'false'])->with(['schedule', 'message'])->get();
        foreach($all as $sms){
            if($this->_allowSend($sms->schedule, $sms->message)) {
                $this->_sendSms($sms);
            }
        }
    }

    public function _sendSms(Sms $sms)
    {
        $job = (new SendSmsJob($sms))->onQueue('sms');
        $this->dispatch($job);
    }

    public function smsUpdate(Request $request)
    {
        $sms = Sms::where('external_id', '=', $request->SmsSid)->first();
        $sms->status = $request->MessageStatus;
        $sms->{$request->MessageStatus} = Carbon::now();
        $sms->save();
    }

    public function _scheduleSms($message_id)
    {
        $message = Message::where('uuid', $message_id)->with(['company', 'contact', 'type', 'status'])->firstOrFail();

        $sms = new Sms();
        $sms->message_id = $message->id;
        $sms->to_number = $message->contact->phone_number;
        $sms->body = $this->composeBody($message->company->id, $message->type->id, $message->contact);
        $sms->schedule_id = $this->_getSchedule($message->company_id, $message->type_id, $this->sms_channel, 'id');

        if(!empty($message->errorCode)){
            $this->sms->error_code = $message->errorCode;
            $this->sms->error_message = $message->errorMessage;
        }

        $sms->save();
    }

    private function composeBody($company_id, $type_id, $contact)
    {
        $script = SmsTemplate::where(["company_id" => $company_id, "type_id" => $type_id])->first();
        $template = $script->template;

        $variables = $contact->getAttributes();

        preg_match_all('/{(\w+)}/', $template, $matches);
        $body = $template;
        foreach ($matches[1] as $index => $var_name) {
            if(!empty($variables[$var_name])) {
                $body = str_replace($matches[0][$index], $variables[$var_name], $body);
            }
        }

        return $body;
    }

    public function _receiveSms(Request $request, $id)
    {

    }
}