<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSmsCron extends Model
{
    protected $fillable = [
        'client_id',
        'message',
        'phone',
        'sent'
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

        $this->setTable(config('client_sms_cron'));

        parent::__construct($attributes);
    }
}
