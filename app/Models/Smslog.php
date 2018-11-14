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
        'slug'
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
    public function sendAndLogSms(array $data = []){
        $spikey=config('admin.smsgateway.spikey');
        $phone=$data['phone'];
        $msg=$data['message'];
        $dataToSave=array("phone"=>$phone,'message'=>$msg);
        if(isset($data['client_id']) && !empty($data['client_id'])){
            $dataToSave['client_id']=$data['client_id'];
        }
        if(isset($data['type']) && !empty($data['type'])){
            $dataToSave['type']=$data['type'];
        }


        // SEND SMS START
        $smsApiConfig = Smsapisetting::find(1);
        $authorization = "{$smsApiConfig->username}:{$smsApiConfig->passowrd}";
        $authorizationEncoded = base64_encode($authorization);
        $baseUrl = $smsApiConfig->apiurl;
        $from = (isset($smsApiConfig->from)) ? $smsApiConfig->from : "INFOSMS";       
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "{$baseUrl}/sms/2/text/single",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{ \"from\":\"$from\", \"to\":\"$phone\", \"text\":\"$msg\" }",
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
          //echo "cURL Error #:" . $err;
        } else {
          //echo $response;
          // $res = json_decode($response,true);
          // $res['messages'][0]['status'];
          $dataToSave['status'] = 1;
        }
        // SEND SMS EMD

        self::create($dataToSave);
        return;
    }

}
