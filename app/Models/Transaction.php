<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';

    protected $fillable = [
        'amount',
        'type',
        'destination_iban',
        'transaction_date',
        'id_user',
        'receiver',
        'receiver_number_phone',
        'sender',
        'status',
        'fee',
        'stripe_payment_intent_id',
        'amount_sent',
        'operator',
        'om_order_id',
        'om_pay_token',
        'om_notif_token',
        'created_date',
        'currency',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
        'amount_sent'      => 'decimal:2',
        'fee'              => 'float',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
