<?php

namespace App\Models;


use App\Classes\Common;
use App\Models\BaseModel;
use App\Scopes\CompanyScope;

class Brand extends BaseModel
{
    protected $table = 'brands';

    protected $default = ['id', 'xid', 'name'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $appends = ['xid', 'image_url'];

    protected $hidden = ['id'];

    protected $filterable = ['id', 'name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }

    public function getImageUrlAttribute()
    {
        $brandLogoPath = Common::getFolderPath('brandImagePath');

        return $this->image == null ? asset('images/brand.png') : Common::getFileUrl($brandLogoPath, $this->image);
    }
}
