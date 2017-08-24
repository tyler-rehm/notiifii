<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\SendEmailJob;
use App\Schedule;
use App\Setting;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Artisan;
use App\Message;

trait MessageTrait
{
    use DispatchesJobs;

    public function _sendAllMessages()
    {
        Artisan::call('email:send');
        Artisan::call('sms:send');
        Artisan::call('voice:send');
    }

    public function _getSchedule($company_id, $type_id, $channel_id, $field = false)
    {
        $schedule = Schedule::where('company_id', '=', $company_id)
            ->where('type_id', '=', $type_id)
            ->where('channel_id', '=', $channel_id)
            ->first();

        if(empty($field)){
            return $schedule;
        } else {
            return $schedule->{$field};
        }
    }

    public function _allowSend(Schedule $schedule, Message $message)
    {
        $return = false;
        $timezone = $this->_getTimezone($schedule->company_id);
        $today = strtolower(Carbon::today($timezone)->format('l'));
        $time = Carbon::now($timezone)->format('H:i:s');

        $start_column = $today . "_start";
        $end_column = $today . "_end";
        $enabled_column = $today . "_enabled";

        if(!empty($enabled_column) && ($time >= $schedule->{$start_column}) && ($time <= $schedule->{$end_column})){
            $parameters = json_decode($message->parameters, true);
            if(!empty($parameters['date'])){
                $lead_column = $today . "_lead_days";
                $window = Carbon::createFromFormat('m/d/Y', $parameters['date'])
                            ->subDays($schedule->{$lead_column})
                            ->format('Y-m-d');
                $event = Carbon::createFromFormat('m/d/Y', $parameters['date'])
                            ->format('Y-m-d');
                $today = Carbon::now($timezone)->format('Y-m-d');

                if($today >= $window && $today <= $event){
                    if(!empty($parameters['time'])){
                        $now = Carbon::now($timezone)->subMinutes(60)->format("H:i");
                        $time = Carbon::createFromFormat("h:i A", $parameters['time'])->format("H:i");
                        if($now <= $time){
                            $return = true;
                        }
                    }
                    $return = true;
                }
            }
            $return = true;
        }
        return $return;
    }

    private function _getTimezone($company_id)
    {
        $setting = Setting::where('company_id', '=', $company_id)->first();
        return $setting->timezone;
    }
}