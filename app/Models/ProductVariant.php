<?php

namespace App\Models;

use App\Casts\Hash;
use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;
use Vinkla\Hashids\Facades\Hashids;

class ProductVariant extends BaseModel
{
    protected $table = 'product_variants';

    protected $default = ['xid'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id', 'product_id', 'variant_id', 'variant_value_id'];

    protected $appends = ['xid', 'x_variant_id', 'x_variant_value_id', 'x_product_id'];

    protected $filterable = ['id'];

    protected $hashableGetterFunctions = [
        'getXProductIdAttribute' => 'product_id',
        'getXVariantIdAttribute' => 'variant_id',
        'getXVariantValueIdAttribute' => 'variant_value_id',
    ];

    protected $casts = [
        'product_id' => Hash::class . ':hash',
        'variant_id' => Hash::class . ':hash',
        'variant_value_id' => Hash::class . ':hash',
    ];

    protected static function boot()
    {
        parent::boot();
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variant_id', 'id');
    }

    public function variationType()
    {
        return $this->belongsTo(Variation::class, 'variant_value_id', 'id');
    }
}
