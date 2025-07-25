<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Variation extends BaseModel
{
    protected $table = 'variations';

    protected $default = ['id', 'xid','name','parent_id'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'parent_id'];

    protected $appends = ['xid', 'x_parent_id'];

    protected $filterable = ['id', 'name','parent_id',];

    protected $hashableGetterFunctions = [
        'getXParentIdAttribute' => 'parent_id',
    ];

    protected $casts = [
        'parent_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function subVariations()
    {
        return $this->hasMany(Variation::class, 'parent_id', 'id');
    }

}
