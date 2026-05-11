<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BmiRecord extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'personnel_id',
        'age',
        'height',
        'weight',
        'waist',
        'wrist',
        'hip',
        'bmi_value',
        'bmi_category',
        'weight_to_lose',
        'normal_weight_min',
        'normal_weight_max',
        'body_frame',
        'waist_hip_ratio',
        'assessed_date',
        'assessment_period',
        'photo_front',
        'photo_right',
        'photo_left',
    ];

    protected $casts = [
        'assessed_date' => 'date',
    ];

    public function personnel()
    {
        return $this->belongsTo(Personnel::class)->withTrashed();
    }
}
