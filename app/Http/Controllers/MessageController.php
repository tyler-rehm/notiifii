<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\SmsTrait;
use App\Traits\VoiceTrait;
use App\Traits\EmailTrait;
use App\Traits\MessageTrait;
use App\Contact;
use App\Company;
use App\Type;
use App\Message;


class MessageController extends Controller
{
    use SmsTrait;
    use VoiceTrait;
    use EmailTrait;
    use MessageTrait;

    protected $params;
    protected $contact;
    protected $company;
    protected $type;
    protected $message;

    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = Message::where('company_id', '=', 1)->get();
        return view('messages.index')->with(['messages' => $messages]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $message = Message::where('id', '=', $id)->first();
        return view('messages.show')->with(['message' => $message]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $message = Message::where('id', '=', $id)->first();
        return view('messages.edit')->with(['message' => $message]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Message::where('id', '=', $id)->delete();
        redirect('/messages');
    }

    public function process(Request $request)
    {
        $this->params = $request;
        $this->company = $this->_company($request);
        $this->contact = $this->_contact($request);
        $this->type = $this->_type($request);
        $this->message = $this->_generate($request, $this->params->all());

        $this->_sms();
        $this->_voice();
        $this->_email();
    }

    private function _generate(Request $request, $params)
    {
        $message = new Message();
        $message->uuid = str_random();
        $message->company_id = $this->company->id;
        $message->type_id = $this->type->id;
        $message->contact_id = $this->contact->id;
        $message->parameters = json_encode($params);
        $message->save();

        return $message;
    }

    private function _sms()
    {
        if(!empty($this->contact->phone_number) && $this->_channel_enabled('sms')){
            $this->_scheduleSms($this->message->uuid);
        }
    }

    private function _voice()
    {
        if(!empty($this->contact->phone_number) && $this->_channel_enabled('voice')) {
            $this->_scheduleVoice($this->message->uuid);
        }
    }

    private function _email()
    {
        if(!empty($this->contact->email_address) && $this->_channel_enabled('email')) {
            $this->_scheduleEmail($this->message->uuid);
        }
    }

    private function _channel_enabled($channel)
    {
        $enabled = true;

        if(!empty($this->params[$channel]['disabled'])){
            $enabled = false;
        } else if(empty($this->company->{"enabled_" . $channel})){
            $enabled = false;
        }

        return $enabled;
    }

    private function _contact(Request $request)
    {
        $contact = new Contact();
        $fillables = $contact->getFillable();
        $attributes = array();

        foreach($fillables as $key){
            if($request->has($key)){
                $val = $request->{$key};
            } else {
                $val = null;
            }
            $attributes[$key] = $val;
        }

        $contact = Contact::firstOrCreate([
            "phone_number" => $request->phone_number,
            "first_name" => $request->first_name,
            "company_id" => $this->company->id
        ], $attributes);
        return $contact;
    }

    private function _company(Request $request)
    {
        $comapny = Company::where(["access_token" => $request->access_token, "id" => $request->company_id])->firstOrFail();
        return $comapny;
    }

    private function _type(Request $request)
    {
        $type = Type::where("display_name", "LIKE", "%" . ucwords(strtolower($request->type)) . "%")->firstOrFail();
        return $type;
    }

}
