<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Tax extends BaseModel
{
    protected $table = 'taxes';

    protected $default = ['xid', 'name', 'rate','tax_type','parent_id'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $filterable = ['name', 'parent_id'];

    protected $hidden = ['id','parent_id'];

    protected $appends = ['xid','x_parent_id'];

    protected $hashableGetterFunctions = [
        'getXParentIdAttribute' => 'parent_id'
    ];

    protected $casts = [
        'parent_id' => Hash::class . ':hash',
        'rate' => 'double',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function multipleTax()
    {
        return $this->hasMany(Tax::class, 'parent_id', 'id');
    }
}
