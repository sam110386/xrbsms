<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Smslog;

class ApiController extends Controller{

	protected $token;

    protected $statusCodes = [1=>"PENDING" ,2=>"UNDELIVERABLE" ,3=>"DELIVERED" ,4=>"EXPIRED",5=>"REJECTED"];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->token = "EQecyxKih5vAb73pG8qdzqbN7W6p9LuU";
    }
    public function sendSms(Request $request){
    	$response = [];
    	if(!$request->token)
    		$response = ['error' => 'Token missing!'];
    	elseif($request->token != $this->token)
    		$response = ['error' => 'Invalid Token!'];
    	elseif(!$request->phone)
    		$response = ['error' => 'Phone Number is required!'];
    	elseif(!$request->sender)
    		$response = ['error' => 'Sender is required!'];
    	elseif(!$request->message)
    		$response = ['error' => 'Message is required!'];
    	else{	
    		$sms=['phone'=>$request->phone,'message' => $request->message,'sender' => $request->sender];
    		if($request->userid) $msg['client_id'] = $request->userid;

    		$smslogModel = config('admin.database.smslog_model');
    		$smslogModel= new $smslogModel();
    		$smsRecord = $smslogModel->sendAndLogSms($sms);
    		$smsRecord = $smsRecord['sms'];
    		$smsRecord['description'] = $smsRecord['error'];
            $smsRecord['status'] = $this->statusCodes[$smsRecord['status']];

            unset($smsRecord['error']);
            unset($smsRecord['type']);
            unset($smsRecord['client_id']);
            unset($smsRecord['retry_count']);
            unset($smsRecord['message_id']);
            unset($smsRecord['created_at']);
            unset($smsRecord['updated_at']);
            $response = ['message' => $smsRecord,'error' => []];
        }
        return response()->json($response,200);
    }

    public function getSmsStatus(Request $request){
    	$response = [];
    	if(!$request->token)
    		$response = ['error' => 'Token missing!'];
    	elseif($request->token != $this->token)
    		$response = ['error' => 'Invalid Token!'];
    	elseif(!$request->messageId)
    		$response = ['error' => 'Message ID Required!'];
    	elseif(!is_numeric($request->messageId))
    		$response = ['error' => 'Invalid Message ID!'];
    	else{
    		$sms = Smslog::find($request->messageId);
    		$sms['description'] = $sms['error'];
            // $sms['statusCode'] = $sms['status'];
            $sms['status'] = $this->statusCodes[$sms['status']];
            unset($sms['error']);
            unset($sms['retry_count']);
            unset($sms['message_id']);
            unset($sms['client_id']);
            unset($sms['type']);
            unset($sms['created_at']);
            unset($sms['updated_at']);            	
            $response = ['message' => $sms, 'error' => []];
        }
        return response()->json($response,200);
    }
}

