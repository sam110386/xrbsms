<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Smslog;

class ApiController extends Controller{

	protected $token;

    protected $statusCodes = [1=>"PENDING" ,2=>"UNDELIVERABLE" ,3=>"DELIVERED" ,4=>"EXPIRED",5=>"REJECTED"];
    private $tokens=array('ETAX'=>'EQecyxKih5vAb73pG8qdzqbN7W6p9LuU','ZRB'=>'cc27e7c0fb156b967c54f83e781bb054bd204875','HMS'=>'128daa5afcf8bb33efb51c53a93f509e48a6318a','ADM'=>'2f71695bc4ed3b76609cc0968a9fbf488877a08c');
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
        $error=false;
    	if(!$request->token):
    		$response = ['error' => 'Token missing!'];
            $error=true;
        endif;
    	if(!in_array($request->token,$this->tokens)):
    		$response = ['error' => 'Invalid Token!'];
            $error=true;
        endif;    
    	if(!$request->phone):
    		$response = ['error' => 'Phone Number is required!'];
            $error=true;
        endif;    

    	
    	if(!$request->message):
    		$response = ['error' => 'Message is required!'];
            $error=true;
        endif; 
            
    	if(!$error){
            $sender=array_flip($this->tokens)[$request->token];
    		$sms=['phone'=>$request->phone,'message' => $request->message,'sender' => $sender];
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

