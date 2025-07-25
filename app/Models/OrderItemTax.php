<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class OrderItemTax extends BaseModel
{
    protected $table = 'order_item_taxes';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id','order_id','tax_id','order_item_id',];

    protected $appends = ['xid','x_order_id','x_tax_id','x_order_item_id'];

    protected $filterable = ['name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    protected $hashableGetterFunctions = [
        'getXOrderIdAttribute' => 'order_id',
        'getXTaxIdAttribute' => 'tax_id',
        'getXOrderItemIdAttribute' => 'order_item_id',
    ];

    protected $casts = [
        'order_id' => Hash::class . ':hash',
        'tax_id' => Hash::class . ':hash',
        'order_item_id' => Hash::class . ':hash',
        'tax_amount' => 'double',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,'id','order_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class,'id','tax_id');
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class,'id','order_item_id');
    }
}

