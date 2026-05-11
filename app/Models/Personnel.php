<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personnel extends Model
{
    use SoftDeletes;
    protected $table = 'personnel';

    protected $fillable = [
        'rank',
        'personnel_type',
        'position_title',
        'last_name',
        'first_name',
        'middle_name',
        'badge_number',
        'unit',
        'station',
        'gender',
        'age',
    ];

    public function bmiRecords()
    {
        return $this->hasMany(BmiRecord::class);
    }

    public function latestBmiRecord()
    {
        return $this->hasOne(BmiRecord::class)->latestOfMany();
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
