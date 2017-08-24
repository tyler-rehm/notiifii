<?php

namespace App\Emails\Defaults;

use App\Emails\CampaignMonitor;

class Appointment extends CampaignMonitor
{
    protected $params;
    protected $template;
    protected $smart_id;
    protected $email;
    protected $data;

    public function __construct($email, $params, $template)
    {
        # The unique identifier for this smart email
        $this->smart_id = '82826da8-3e4c-4c1a-8481-78ca1ab633c9';
        $this->email = $email;
        $this->params = $this->getParams($params);
        $this->template = $template;
        $this->data = array(
            'name' => $this->getName($this->params),
            'domain' => url('/'),
            'guid' => $this->email->message->uuid,
            'intro' => null,
            'date' => null,
            'time' => null,
            'location' => null,
            'salutation' => null
        );
    }
}