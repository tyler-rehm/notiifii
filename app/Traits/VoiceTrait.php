<?php

namespace App\Traits;

use App\VoiceScript;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\SendVoiceJob;
use App\Message;
use App\Contact;
use App\Voice;
use App\Schedule;
use Twilio\Twiml;

trait VoiceTrait
{
    private $voice_channel = 3;

    public function _sendAllVoices()
    {
        $all = Voice::where(['fulfilled' => 'false'])->with(['schedule', 'message'])->get();
        foreach($all as $voice){
            $allow = $this->_allowSend($voice->schedule, $voice->message);
            $this->_sendVoice($voice);
        }
    }

    public function _sendVoice(Voice $voice)
    {
        $job = (new SendVoiceJob($voice))->onQueue('voice')->delay(60);
        $this->dispatch($job);
    }

    public function _scheduleVoice($message_id)
    {
        $message = Message::where('uuid', $message_id)->with(['company', 'contact', 'type', 'status'])->firstOrFail();

        $voice = new Voice();
        $voice->message_id = $message->id;
        $voice->phone_number = $message->contact->phone_number;
        $voice->direction = 'outbound';
        $voice->schedule_id = $this->_getSchedule($message->company_id, $message->type_id, $this->voice_channel, 'id');
        $voice->save();

        $call = $voice->with('message')->first();
        $this->_composeScript($call);
    }

    public function voiceUpdate(Request $request)
    {
        $voice = Voice::where('external_id', '=', $request->CallSid)->first();
        $voice->status = $request->CallStatus;
        $voice->{$request->CallStatus} = Carbon::now();
        if(!empty($request->CallDuration)){
            $voice->duration = $request->CallDuration;
        }
        $voice->save();
    }

    private function _composeScript(Voice $voice)
    {
        $file_name = 'c' . $voice->message->company_id . '_v' . $voice->id . '_' . str_random() . '.xml';
        $file = app_path('Scripts/' . $file_name);

        $script = VoiceScript::where('type_id', '=', $voice->message->type_id)->with('parts')->first();
        $twiml = new Twiml();

        foreach($script->parts as $part){
            $options = json_decode($part->options, true);

            if(!empty($part->input)){
                $input = $this->replaceVariables($part->input, $voice->message);
                $twiml->{$part->action}($input, $options);
            } else {
                $twiml->{$part->action}($options);
            }
        }

        $result = file_put_contents($file, $twiml);
        $voice->script = $file_name;
        $voice->save();
    }

    private function replaceVariables($string, Message $message)
    {
        $attributes = $message->getAttributes();
        $variables = json_decode($attributes['parameters'], true);
        $variables['date_string'] = Carbon::createFromFormat('m/d/Y', $variables['date'])->format('l F j');

        preg_match_all('/{(\w+)}/', $string, $matches);
        $body = $string;
        foreach ($matches[1] as $index => $var_name) {
            if(!empty($variables[$var_name])) {
                $body = str_replace($matches[0][$index], $variables[$var_name], $body);
            }
        }
        return $body;
    }

    public function voiceGather(Request $request)
    {
        dd($request);
    }

    public function voiceOutbound($file_name, Request $request)
    {
        echo file_get_contents(app_path('Scripts/' . $file_name));
    }

    public function _receiveVoice(Request $request, $id)
    {

    }
}