<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = [
        'invoice',
        'customer',
        'amount',
        'date',
    ];

    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'customer_id', 'customer');
    }

    public function creditNote()
    {
        return $this->hasOne(CustomerCreditNotes::class, 'id', 'credit_note');
    }
}
