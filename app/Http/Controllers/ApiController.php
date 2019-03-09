<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Smslog;
use App\Models\Client;
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
      if(!$error && !in_array($request->token,$this->tokens)):
          $response = ['error' => 'Invalid Token!'];
          $error=true;
      endif;    
      if(!$error && !$request->phone):
          $response = ['error' => 'Phone Number is required!'];
          $error=true;
      endif;    


      if(!$error && !$request->message):
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

    public function addClient(Request $request){
        $response = [];
        $error=false;
        if(!$request->token):
            $response = ['error' => 'Token missing!'];
            $error=true;
        endif;
        if(!$error && !in_array($request->token,$this->tokens)):
            $response = ['error' => 'Invalid Token!'];
            $error=true;
        endif;    
        if(!$error && !$request->phone):
            $response = ['error' => 'Phone Number is required!'];
            $error=true;
        endif;  

        if(!$error){
            $phone=substr(preg_replace("/[^0-9]/", "",$request->phone),-9);
            
            $clientData=array();
            $clientData['name']=$request->name;
            $clientData['phone']=$request->phone;
            $clientData['user_type']=strtolower($request->user_type)=='company'?2:1;//company or indivisual
            $clientData['gender']=strtolower($request->gender)=='female'?'F':'M';//male or female
            $clientData['language']='en';//male or female
            $clientData['status']=$request->status?1:0;//active or inactive
            if(!empty($request->registration_date)):
                $clientData['registration_date']=date('Y-m-d',strtotime($request->registration_date));
            endif;
            if(!empty($request->registration_number)):
                $clientData['registration_number']=$request->registration_number;
            endif;
            if(!empty($request->address)):
                $clientData['address']=$request->address;
            endif;
            if(!empty($request->region)):
                $clientData['region']=$request->region;
            endif;
            if(!empty($request->district)):
                $clientData['district']=$request->district;
            endif;
            if(!empty($request->ward)):
                $clientData['ward']=$request->ward;
            endif;
            if(!empty($request->zipcode)):
                $clientData['zipcode']=$request->zipcode;
            endif;
            if(!empty($request->exempt)):
                $clientData['exempt']=strtolower($request->exempt)=='yes'?1:0;
            endif;
            if(!empty($request->tax_type)):
                $clientData['tax_type']=strtolower($request->tax_type)=='vat'?'VAT':'non-VAT';
            endif;
            $taxcategory=['Returns'=>"Returns",'Motor Vehicle' => 'Motor Vehicle','Driving Licence' => 'Driving Licence'];
            if(!empty($request->taxcategory)):
                //$clientData['taxcategory']=$taxcategory[$request->taxcategory]?$request->taxcategory:'Returns';
            endif;
            if(!empty($request->filling_type)):
                $clientData['filling_type']=strtolower($request->filling_type)=='regular'?'regular':'lamp-sum';
            endif;
            if(!empty($request->filling_period)):
                $clientData['filling_period']=strtolower($request->filling_period)=='annual'?'annual':'quarterly';
            endif;
            if(!empty($request->filling_currency)):
                $clientData['filling_currency']=strtolower($request->filling_currency)=='tsh'?'TSH':'USD';
            endif;
            if(!empty($request->due_date)):
                $clientData['due_date']=date('Y-m-d',strtotime($request->due_date));
            endif;
            if(!empty($request->total_amount)):
                $clientData['total_amount']=$request->total_amount;
            endif;
            if(!empty($request->penalty_amount)):
                $clientData['penalty_amount']=$request->penalty_amount;
            endif;
            if(isset($request->certificate_printed)):
                $clientData['certificate_printed']=$request->certificate_printed?1:0;
            endif;
            
            if(isset($request->returns_opt)):
                $clientData['returns_opt']=$request->returns_opt?1:0;
            endif;
            if(isset($request->return_due_date)):
                $clientData['return_due_date']=date('Y-m-d',strtotime($request->return_due_date));
            endif;
            
            if(isset($request->motor_vehicle_opt)):
                $clientData['motor_vehicle_opt']=$request->motor_vehicle_opt?1:0;
            endif;
            if(isset($request->motor_vehicle_due_date)):
                $clientData['motor_vehicle_due_date']=date('Y-m-d',strtotime($request->motor_vehicle_due_date));
            endif;
            if(isset($request->driving_licence_opt)):
                $clientData['driving_licence_opt']=$request->driving_licence_opt?1:0;
            endif;
            if(isset($request->driving_licence_due_date)):
                $clientData['driving_licence_due_date']=date('Y-m-d',strtotime($request->driving_licence_due_date));
            endif;
            $clientExists=Client::where('phone','like', "'%$phone'")->first();
            if(!empty($clientExists)){
                Client::findOrFail($clientExists->id)->update($clientData);
                $message="User updated successfully";
                $clientid=$clientExists->id;
            }else{
                $clientid=Client::create($clientData)->id;
                $message="User created successfully";
            }
            $response = ['message' => $message, 'error' =>'','userid'=>$clientid];
        }
        return response()->json($response,200);
    }
}

