<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Vinkla\Hashids\Facades\Hashids;

class Expense extends BaseModel
{
    protected $table = 'expenses';

    protected $default = ['xid'];

    protected $dates = ['date'];

    protected $guarded = ['id', 'warehouse_id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'warehouse_id', 'user_id', 'expense_category_id'];

    protected $appends = ['xid', 'x_warehouse_id', 'x_user_id', 'x_expense_category_id', 'bill_url'];

    protected $filterable = ['warehouse_id', 'expense_category_id', 'user_id'];

    protected $hashableGetterFunctions = [
        'getXUserIdAttribute' => 'user_id',
        'getXWarehouseIdAttribute' => 'warehouse_id',
        'getXExpenseCategoryIdAttribute' => 'expense_category_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'warehouse_id' => Hash::class . ':hash',
        'user_id' => Hash::class . ':hash',
        'expense_category_id' => Hash::class . ':hash',
        'amount' => 'double',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getBillUrlAttribute()
    {
        $expenseBillPath = Common::getFolderPath('expenseBillPath');

        return $this->bill == null ? null : Common::getFileUrl($expenseBillPath, $this->bill);
    }

    public function expenseCategory()
    {
        return $this->hasOne(ExpenseCategory::class, 'id', 'expense_category_id');
    }

    public function user()
    {
        return $this->hasOne(StaffMember::class, 'id', 'user_id');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}
