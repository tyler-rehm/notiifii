<?php

namespace App\Emails;

use App\Email;
use App\EmailTemplate;
use CS_REST_Transactional_SmartEmail;
use CS_REST_Transactional_Timeline;

class CampaignMonitor
{
    public function send()
    {
        # Create a new mailer and define your message
        $wrap = new CS_REST_Transactional_SmartEmail($this->smart_id, env('CAMPAIGN_MONITOR_API'));
        $this->data = $this->fillData($this->data, $this->params);

        $message = array(
            "To" => $this->getTo($this->params),
            "Data" => $this->data,
        );

        # Send the message and save the response
        $result = $wrap->send($message);
        return $result;
    }

    public function get($external_id)
    {
        $wrap = new CS_REST_Transactional_Timeline(array('api_key' => env('CAMPAIGN_MONITOR_API')));
        $update = $wrap->details($external_id);

        if(!empty($update)){
            $email = Email::where('external_id', '=', $external_id)->first();
            $email->status = strtolower($update->response->Status);
            $email->opens = $update->response->TotalOpens;
            $email->clicks = $update->response->TotalClicks;
            $email->save();
        }
    }

    public function getTo($params)
    {
        $to = "";
        $to .= $this->getName($params);
        $to .= " <" . $params['email_address'] . ">";
        return $to;
    }

    public function getName($params)
    {
        return $params['first_name'] . " " . $params['last_name'];
    }

    public function getParams($params)
    {
        return json_decode($params, true);
    }

    public function fillData($data, $filler)
    {
        foreach($data as $key => $val){
            if(empty($val) && !empty($filler[$key])){
                $data[$key] = $filler[$key];
            }
        }
        return $data;
    }
}