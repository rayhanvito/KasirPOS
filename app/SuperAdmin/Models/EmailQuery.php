<?php

namespace App\SuperAdmin\Models;

use Illuminate\Notifications\Notifiable;
use App\Models\BaseModel;

class EmailQuery extends BaseModel
{
    use Notifiable;

    protected $table = 'email_queries';

    protected $default = ['xid', 'date_time', 'email', 'name', 'message'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['id'];

    protected $appends = ['xid'];

    protected $filterable = ['name', 'email', 'replied'];

    protected $casts = [
        'replied' => 'integer',
    ];
}
