<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Trebol\Entrust\Traits\EntrustUserTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash as FacadesHash;

class User extends BaseModel implements AuthenticatableContract, JWTSubject
{
    use Notifiable, EntrustUserTrait, Authenticatable, HasFactory;

    protected $default = ["xid", "name", "profile_image"];

    protected $guarded = [
        'id', 'warehouse_id', 'company_id', 'is_superadmin', 'opening_balance', 'opening_balance_type', 'credit_limit', 'credit_period', 'created_by', 'is_walkin_customer', 'created_at', 'updated_at', 'warehouses'
    ];

    protected $dates = ['last_active_on'];

    protected $hidden = [
        'id', 'company_id', 'role_id',  'warehouse_id', 'password', 'remember_token',

        // For HRM Module
        'department_id',
        'designation_id',
        'shift_id',
    ];

    protected $appends = [
        'xid', 'x_company_id', 'x_warehouse_id', 'x_role_id', 'profile_image_url',

        // For HRM Module
        'x_department_id', 'x_designation_id', 'x_shift_id'
    ];

    protected $filterable = [
        'users.name', 'name', 'user_type', 'email', 'status', 'phone',

        // For HRM Module
        'shift_id'
    ];

    protected $hashableGetterFunctions = [
        'getXCompanyIdAttribute' => 'company_id',
        'getXRoleIdAttribute' => 'role_id',
        'getXWarehouseIdAttribute' => 'warehouse_id',

        // For HRM Module
        'getXDepartmentIdAttribute' => 'department_id',
        'getXDesignationIdAttribute' => 'designation_id',
        'getXShiftIdAttribute' => 'shift_id',
    ];

    protected $casts = [
        'last_active_on' => 'datetime',
        'company_id' => Hash::class . ':hash',
        'role_id' => Hash::class . ':hash',
        'warehouse_id' => Hash::class . ':hash',
        'login_enabled' => 'integer',
        'is_walkin_customer' => 'integer',
        'is_superadmin' => 'integer',

        // For HRM Module
        'department_id' => Hash::class . ':hash',
        'designation_id' => Hash::class . ':hash',
        'shift_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        // static::addGlobalScope(new CompanyScope);
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = FacadesHash::make($value);
        }
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setUserTypeAttribute($value)
    {
        $this->attributes['user_type'] = 'staff_members';
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

    // For HRM Module
    public function department()
    {
        return $this->belongsTo(\Modules\StockiflyHrm\Entities\Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(\Modules\StockiflyHrm\Entities\Designation::class);
    }

    public function shift()
    {
        return $this->belongsTo(\Modules\StockiflyHrm\Entities\Shift::class);
    }
}
