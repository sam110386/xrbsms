<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Client;
use App\Models\Smsscheduletype;
use App\Models\ClientSmsCron;

class CronController extends Controller{
	/*
		get filtered Clients from Clients table and store client phone number,msg body in cron sms table.  
	*/
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
	}