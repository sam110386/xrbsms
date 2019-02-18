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
	public function getClients(){
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
			echo "No Schedule Found!";
		}
		echo "Cron Executed";
	}

	private function addClientInSmsCron($clients,$msg){
		foreach ($clients as $client) {
			$smsCronRaw = ['phone' => $client->phone, 'client_id' => $client->id, 'message' => $msg];
			if(ClientSmsCron::create($smsCronRaw)){
				echo "\nRecord added: " . json_encode($smsCronRaw);
			}
		}
	}

	public function sendSms(){
		$settings = GeneralSetting::findOrFail(1);
		$fromTime = $settings->time_from;
		$toTime = $settings->time_to;
		$time = Carbon::now()->setTimezone('Asia/Calcutta')->format('H');
		if($time >= $fromTime && $time <= $toTime){
			echo "Sending sms :)<br>";
			$smslogModel = config('admin.database.smslog_model');
			$smslogModel= new $smslogModel();

			$crons = ClientSmsCron::where('sent',0)->get();
			foreach ($crons as $cron) {
				$sms = ['phone' => $cron->phone , 'client_id' => $cron->client_id , 'message' => $cron->message ];
				$smslogModel->sendAndLogSms($sms);
				$cron->update(['sent' => 1]);
				echo "<br>--> Sms Sent via cron: " . json_encode($sms);
			}			
		}else{
			die("This is not right time to send sms ;-)");
		}
		echo "Cron Executed";
	}

	public function checkSmsStatus(){
		$msgids = Smslog::where('status',1)->get();
		foreach($msgids as $msg){
			$msgId = $msg->message_id;
			if($msg->retry_count < 3){
				$smsApiConfig = Smsapisetting::find(1);
				$authorization = "{$smsApiConfig->username}:{$smsApiConfig->passowrd}";
				$authorizationEncoded = base64_encode($authorization);
				$baseUrl = $smsApiConfig->apiurl;
				$from = (isset($smsApiConfig->from)) ? $smsApiConfig->from : "INFOSMS";       
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => "{$baseUrl}/sms/1/reports",
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "GET",
					CURLOPT_POSTFIELDS => "{ \"messageId\":\"$msgId\"}",
					CURLOPT_HTTPHEADER => array(
						"accept: application/json",
						"authorization: Basic {$authorizationEncoded}",
						"content-type: application/json"
					),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				if ($err) {
					echo "<br>MessageId: {$msgId} <==> cURL Error #:" . $err;
				} else {
					$responseArr = json_decode($response,true);
					if(isset($responseArr['results'][0])){
						$responseArr = $responseArr['results'][0];
						$msg['error'] = $responseArr['status']['description'];
						$msg['status'] = $responseArr['status']['groupId'];
						$msg['message_id'] = $responseArr['messageId'];
						$msg->retry_count++;
						$msg->update();
					}else{
						echo "<br>MessageId: {$msgId} <==> Error: Response not received";
					}
				}
			}else{
				$smslogModel = config('admin.database.smslog_model');
				$smslogModel= new $smslogModel();
				$sms = ['phone' => $msg->phone , 'message' => $msg->message];
				$smslogModel->sendAndLogSms($sms,$msg->id);
				echo "<br>MessageId: {$msgId} <==> ID:{$msg->id} <==> Error: SMS Resent";
			}
		}

	}

}