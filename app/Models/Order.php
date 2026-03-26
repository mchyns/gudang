<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'drop_date' => 'date',
        'operational_extras' => 'array',
        'operational_bensin' => 'float',
        'operational_kuli' => 'float',
        'operational_makan_minum' => 'float',
        'operational_listrik' => 'float',
        'operational_wifi' => 'float',
        'total_price' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getOperationalTotalAttribute(): float
    {
        $extraTotal = collect($this->operational_extras ?? [])->sum(function ($extra) {
            return (float) ($extra['amount'] ?? 0);
        });

        return (float) (
            ($this->operational_bensin ?? 0)
            + ($this->operational_kuli ?? 0)
            + ($this->operational_makan_minum ?? 0)
            + ($this->operational_listrik ?? 0)
            + ($this->operational_wifi ?? 0)
            + $extraTotal
        );
    }
}
