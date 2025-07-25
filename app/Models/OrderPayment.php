<?php

namespace App\Models;

use App\Casts\Hash;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class OrderPayment extends BaseModel
{
    protected $table = 'order_payments';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'order_id', 'payment_id'];

    protected $appends = ['xid', 'x_order_id', 'x_payment_id'];

    protected $filterable = ['id', 'amount', 'orders.order_type', 'user_id', 'date', 'order_date', 'warehouse_id', 'user_id'];

    protected $hashableGetterFunctions = [
        'getXOrderIdAttribute' => 'order_id',
        'getXPaymentIdAttribute' => 'payment_id',
    ];

    protected $casts = [
        'order_id' => Hash::class . ':hash',
        'payment_id' => Hash::class . ':hash',
        'amount' => 'double',
    ];

    // protected $filterable = ['id', 'amount', 'orders.order_type'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
