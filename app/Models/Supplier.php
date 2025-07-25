<?php

namespace App\Models;

use App\Classes\Common;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Casts\Hash;

class Supplier extends BaseModel
{
    use Notifiable, HasFactory;

    protected  $table = 'users';

    protected $default = ["xid", "name", "profile_image"];

    protected $guarded = ['id', 'warehouse_id', 'is_superadmin', 'opening_balance', 'opening_balance_type', 'credit_limit', 'credit_period', 'role_id', 'created_by', 'is_walkin_customer', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'role_id', 'warehouse_id', 'password', 'remember_token'];

    protected $filterable = ['users.name', 'name', 'user_type', 'email', 'status', 'phone', 'due_amount'];

    protected $appends = ['xid', 'profile_image_url', 'x_warehouse_id'];

    protected $hashableGetterFunctions = [
        'getXWarehouseIdAttribute' => 'warehouse_id',
    ];

    protected $casts = [
        'warehouse_id' => Hash::class . ':hash',
        'is_walkin_customer' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);

        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('users.user_type', '=', 'suppliers');
        });
    }

    public function setUserTypeAttribute($value)
    {
        $this->attributes['user_type'] = 'suppliers';
    }

    public function setRoleIdAttribute($value)
    {
        $this->attributes['role_id'] = null;
    }

    public function getProfileImageUrlAttribute()
    {
        $userImagePath = Common::getFolderPath('userImagePath');

        return $this->profile_image == null ? asset('images/user.png') : Common::getFileUrl($userImagePath, $this->profile_image);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function details()
    {
        return $this->belongsTo(UserDetails::class, 'id', 'user_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function userWarehouses()
    {
        return $this->hasMany(UserWarehouse::class, 'user_id', 'id');
    }
}
