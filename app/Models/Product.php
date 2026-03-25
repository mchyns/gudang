<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        $image = $this->image;

        if (!$image) {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://', '/storage/'])) {
            return $image;
        }

        return Storage::url($image);
    }

    public static function generateBinLocation(?string $movementType, ?string $categoryName): string
    {
        $zone = $movementType === 'fast' ? 'A' : 'B';
        $categoryCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $categoryName), 0, 3));
        $categoryCode = $categoryCode !== '' ? $categoryCode : 'GEN';
        $rack = str_pad((string) random_int(1, 24), 2, '0', STR_PAD_LEFT);
        $slot = str_pad((string) random_int(1, 12), 2, '0', STR_PAD_LEFT);

        return $zone . '-' . $categoryCode . '-R' . $rack . '-S' . $slot;
    }
}
