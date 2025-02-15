<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'payment_method',
        'gateway_transaction_id',
        'value',
        'due_date',
        'status',
        'metadata',
    ];

    public function isPix()
    {
        return $this->payment_method === 'pix';
    }

    public function isBoleto()
    {
        return $this->payment_method === 'boleto';
    }

    public function isCreditCard()
    {
        return $this->payment_method === 'credit_card';
    }

    /**
     * Get the user's first name.
     */
    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
        );
    }
}
