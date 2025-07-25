<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Vinkla\Hashids\Facades\Hashids;

class OrderItem extends BaseModel
{
    protected $table = 'order_items';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'order_id', 'user_id', 'order_id', 'product_id', 'unit_id', 'tax_id'];

    protected $appends = ['xid', 'x_order_id', 'x_user_id', 'x_order_id', 'x_product_id', 'x_unit_id', 'x_tax_id'];

    protected $filterable = ['id', 'product_id'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXOrderIdAttribute' => 'order_id',
        'getXProductIdAttribute' => 'product_id',
        'getXUnitIdAttribute' => 'unit_id',
        'getXTaxIdAttribute' => 'tax_id',
    ];

    protected $casts = [
        'user_id' => Hash::class . ':hash',
        'order_id' => Hash::class . ':hash',
        'product_id' => Hash::class . ':hash',
        'unit_id' => Hash::class . ':hash',
        'tax_id' => Hash::class . ':hash',
        'quantity' => 'double',
        'mrp' => 'double',
        'unit_price' => 'double',
        'single_unit_price' => 'double',
        'tax_rate' => 'double',
        'discount_rate' => 'double',
        'total_tax' => 'double',
        'total_discount' => 'double',
        'subtotal' => 'double',
        'total_sales_price' => 'double',
        'unit_sold' => 'double',
    ];

    protected static function boot()
    {
        parent::boot();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function orderItemTaxes()
    {
        return $this->hasMany(OrderItemTax::class, 'order_item_id', 'id');
    }
}
