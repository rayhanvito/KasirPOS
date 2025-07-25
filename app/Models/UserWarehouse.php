<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;

class UserWarehouse extends BaseModel
{
    protected $table = 'user_warehouse';

    protected $default = ['xid'];

    protected $filterable = [];

    protected $guarded = ['id'];

    protected $hidden = ['id', 'user_id', 'warehouse_id'];

    protected $appends = ['xid', 'x_user_id', 'x_warehouse_id'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXWarehouseIdAttribute' => 'warehouse_id',
    ];

    protected $casts = [
        'user_id' => Hash::class . ':hash',
        'warehouse_id' => Hash::class . ':hash'
    ];
}
