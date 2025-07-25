<?php

namespace App\Models;

use App\Scopes\CompanyScope;

class CustomField extends BaseModel
{
    protected  $table = 'custom_fields';

    public $timestamps = false;

    protected $default = ['xid', 'name'];

    protected $guarded = ['id'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected $filterable = ['name'];

    protected $casts = [
        'active' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}
