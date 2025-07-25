<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class OrderShippingAddress extends BaseModel
{
    protected $table = 'order_shipping_address';

    protected $default = ['id', 'name'];

    protected $filterable = ['name'];

    protected $guarded = ['id'];

    protected $hidden = ['id', 'order_id'];

    protected $appends = ['xid', 'x_order_id'];

    protected $hashableGetterFunctions = [
        'getXOrderIdAttribute' => 'order_id'
    ];

    protected $casts = [
        'order_id' => Hash::class . ':hash'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}
