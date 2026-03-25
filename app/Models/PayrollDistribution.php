<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDistribution extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'pendapatan_total' => 'float',
        'gaji_internal_kepala_dapur' => 'float',
        'gaji_internal_asisten_lapangan' => 'float',
        'gaji_internal_ahli_gizi' => 'float',
        'gaji_internal_akuntan' => 'float',
        'gaji_internal' => 'float',
        'gaji_eksternal_kodim' => 'float',
        'gaji_eksternal_koramil' => 'float',
        'gaji_eksternal' => 'float',
        'total_gaji' => 'float',
        'sisa_setelah_gaji' => 'float',
        'kepala_dapur_percent' => 'float',
        'kepala_dapur_nominal' => 'float',
        'sisa_untuk_staf' => 'float',
        'staff_1_percent' => 'float',
        'staff_2_percent' => 'float',
        'staff_3_percent' => 'float',
        'staff_4_percent' => 'float',
        'staff_1_nominal' => 'float',
        'staff_2_nominal' => 'float',
        'staff_3_nominal' => 'float',
        'staff_4_nominal' => 'float',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
