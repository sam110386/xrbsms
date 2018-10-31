<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        self::create($dataToSave);
        return;
    }

}