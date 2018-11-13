<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'user_type',
        'gender',
        'language',
        'status',
        'registration_date',
        'registration_number',
        'address',
        'region',
        'district',
        'ward',
        'zipcode',
        'exempt',
        'tax_type',
        'filling_type',
        'filling_period',
        'filling_currency',
        'due_date',
        'total_amount',
        'penalty_amount',
        'certificate_printed',
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

        $this->setTable(config('admin.database.client_table'));

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
}
