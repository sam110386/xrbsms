<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Client;
use App\Models\Smsscheduletype;
use App\Models\ClientSmsCron;
use App\Models\Smslog;
use App\Models\Smsapisetting;
class CronController extends Controller{
	/* get filtered Clients from Clients table and store client phone number,msg body in cron sms table.  */
	public static function getClients(){
		$smsscheduletypes = DB::table('smsscheduletypes')->select('frequency', 'en_smsbody','category');
		$smsscheduletypes->where('status',1);
		$smsscheduletypes->where('category', '!=' , NULL);
		$schedules = $smsscheduletypes->get();
		if($schedules){
			foreach ($schedules as $schedule) {
				$smsBody = $schedule->en_smsbody;
				$field = $schedule->category;
				$date = Carbon::today();
				if($schedule->frequency > 1) $date->addDays($schedule->frequency);
				$date = $date->format('Y-m-d');
				$clients = Client::where($field, 'LIKE', $date . "%")->get();
				if($clients) $this->addClientInSmsCron($clients,$smsBody);
			}
		}else{
			//echo "No Schedule Found!";
		}
		//echo "Cron Executed";
		return;
	}

	private function addClientInSmsCron($clients,$msg){
		foreach ($clients as $client) {
			$smsCronRaw = ['phone' => $client->phone, 'client_id' => $client->id, 'message' => $msg];
			if(ClientSmsCron::create($smsCronRaw)){
				//echo "\nRecord added: " . json_encode($smsCronRaw);
			}
		}
	}

	public static function sendSms(){
		$settings = GeneralSetting::findOrFail(1);
		$fromTime = $settings->time_from;
		$toTime = $settings->time_to;
		$time = Carbon::now()->setTimezone('Africa/Nairobi')->format('H');
		if($time >= $fromTime && $time <= $toTime){
			
			$smslogModel = config('admin.database.smslog_model');
			$smslogModel= new $smslogModel();

			$crons = ClientSmsCron::where('sent',0)->limit(50)->get();
			foreach ($crons as $cron) {
				$sms = ['phone' => $cron->phone , 'client_id' => $cron->client_id , 'message' => $cron->message ];
				$smslogModel->sendAndLogSms($sms);
				$cron->update(['sent' => 1]);
				
			}			
		}else{
			//die("This is not right time to send sms ;-)");
		}
		return;
	}
	/*****/
	public static function checkSmsStatus(){
		$msgids = Smslog::where('status',1)->get();
		foreach($msgids as $msg){
			$msgId = $msg->message_id;
			if(empty($msgId)) continue;//skip if message ID is empty
			if($msg->retry_count < 3){
				$smsApiConfig = Smsapisetting::find(1);
				$authorization = "{$smsApiConfig->username}:{$smsApiConfig->passowrd}";
				$authorizationEncoded = base64_encode($authorization);
				$baseUrl = $smsApiConfig->apiurl;
				$from = (isset($smsApiConfig->from)) ? $smsApiConfig->from : "INFOSMS";       
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => "{$baseUrl}/sms/1/reports?messageId=$msgId",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_HTTPHEADER => array(
						"accept: application/json",
						"authorization: Basic {$authorizationEncoded}",
						"content-type: application/json"
					),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				$responseArr = json_decode($response,true);
				//echo "<pre>";print_r($responseArr);
				if (!$err) {
					if(isset($responseArr['results'][0])){
						$responseArr = $responseArr['results'][0];
						$msg['error'] = $responseArr['status']['description'];
						$msg['status'] = $responseArr['status']['groupId'];
						$msg['message_id'] = $responseArr['messageId'];
						$msg->retry_count++;
						$msg->update();
					}else{
						//echo "<br>MessageId: {$msgId} <==> Error: Response not received";
						//forcefully done if no response recieved
						$msg['status'] = 3;
						$msg->update();
					}
				}
			}else{
				$smslogModel = config('admin.database.smslog_model');
				$smslogModel= new $smslogModel();
				$sms = ['phone' => $msg->phone , 'message' => $msg->message];
				$smslogModel->sendAndLogSms($sms,$msg->id);
				//echo "<br>MessageId: {$msgId} <==> ID:{$msg->id} <==> Error: SMS Resent";
			}
		}
		return;
	}

}