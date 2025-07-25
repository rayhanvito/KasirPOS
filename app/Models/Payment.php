<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;

class Payment extends BaseModel
{
    protected $table = 'payments';

    protected $default = ['xid'];

    protected $dates = ['date'];

    protected $guarded = ['id', 'warehouse_id', 'payment_type', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'payment_mode_id', 'user_id'];

    protected $appends = ['xid', 'x_payment_mode_id', 'x_user_id'];

    protected $filterable = ['id', 'payment_number', 'payment_type', 'user_id'];

    protected $hashableGetterFunctions = [
        'getXPaymentModeIdAttribute' => 'payment_mode_id',
        'getXUserIdAttribute' => 'user_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'payment_mode_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'amount' => 'double',
        'unused_amount' => 'double',
        'paid_amount' => 'double',
        'unused_amount' => 'double',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);

        static::addGlobalScope('current_warehouse', function (Builder $builder) {
            $warehouse = warehouse();

            if ($warehouse) {
                $builder->where('payments.warehouse_id', $warehouse->id);
            }
        });
    }

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function user()
    {
        return $this->belongsTo(StaffMember::class)->withoutGlobalScopes();
    }
}
