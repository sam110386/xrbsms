<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Smsapisetting;
class Smslog extends Model
{
  protected $fillable = [
    'id',
    'phone',
    'message',
    'client_id',
    'type',
    'status',
    'message_id',
    'error',
    'retry_count',
    'sender'
  ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
      $connection = config('admin.database.connection') ?: config('database.default');

      $this->setConnection($connection);

      $this->setTable(config('admin.database.smslog_table'));

      parent::__construct($attributes);
    }

    

    
    /**
     * Check user has permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function can(string $permission) : bool
    {
      return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check user has no permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function cannot(string $permission) : bool
    {
      return !$this->can($permission);
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
      parent::boot();

      static::deleting(function ($model) {});
    }

    /***send SMS API**/
    public function sendAndLogSms(array $data = [],$id=0){

      $clientModel = config('admin.database.client_model');
      $phone=$data['phone'];
      $phone="+255".substr(preg_replace("/[^0-9]/", "",$phone),-9);
      $msg=$data['message'];
      $dataToSave['phone']=$phone;
      $sms_variables = config('admin.sms_variables');
      if(isset($data['client_id']) && !empty($data['client_id'])){
        $dataToSave['client_id']=$data['client_id'];
        $ClientData=$clientModel::find($data['client_id']);
        foreach ($sms_variables as $k => $v) {
          if($ClientData->$v){
            $replacedValue = $ClientData->$v; 
          }else{
            $replacedValue = ""; 
          }
          $msg =  str_replace($k, $replacedValue,$msg );
        }
      }else{
        foreach ($sms_variables as $k => $v) {
          $msg =  str_replace($k,"",$msg );
        }
      }
      if(isset($data['type']) && !empty($data['type'])){
        $dataToSave['type']=$data['type'];
      }
      
      $dataToSave['message'] = $msg;
      $dataToSave['retry_count'] = (isset($data['retry_count'])) ? $data['retry_count'] : 0;

        // SEND SMS START
      $smsApiConfig = Smsapisetting::find(1);
      $authorization = "{$smsApiConfig->username}:{$smsApiConfig->passowrd}";
      $authorizationEncoded = base64_encode($authorization);
      $baseUrl = $smsApiConfig->apiurl;
      $sender=(isset($smsApiConfig->from)) ? $smsApiConfig->from : "INFOSMS"; 
      if(isset($data['sender']) && !empty($data['sender'])){
        $from = $data['sender'];
      }else{
        $from = $sender;      
      }
      $dataToSave['sender']=$from;

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "{$baseUrl}/sms/2/text/single",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{ \"from\":\"$sender\", \"to\":\"$phone\", \"text\":\"$msg\" }",
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
          // echo "cURL Error #:" . $err;
        $dataToSave['error'] = "cURL Error #:" . $err;
        $dataToSave['message_id'] = 'NA'; 
        $dataToSave['status'] = 0;       
      }else{
        $responseArr = json_decode($response,true);
        if(isset($responseArr['messages'][0])){
          $responseArr = $responseArr['messages'][0];
          $dataToSave['error'] = $responseArr['status']['description'];
          $dataToSave['status'] = $responseArr['status']['groupId'];
          $dataToSave['message_id'] = $responseArr['messageId'];
        }else{
          $dataToSave['error'] = 'Response not received';
          $dataToSave['message_id'] = 'NA'; 
          $dataToSave['status'] = 0;       
        }
      }
      // SEND SMS EMD
      if($id==0){
        $sms = self::create($dataToSave);
        $dataToSave['sms'] = $sms;
      }else{
        $msg = self::find($id)->update($dataToSave);
      }
      return $dataToSave;
    }
  }
