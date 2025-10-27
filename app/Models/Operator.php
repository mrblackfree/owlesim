<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country_id',
        'region_id',
        'airaloOperatorId',
        'type',
        'is_prepaid',
        'esim_type',
        'apn_type',
        'apn_value',
        'info',
        'image',
        'plan_type',
        'activation_policy',
        'is_kyc_verify',
        'rechargeability',
        'is_active',
        'airalo_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function esimPackages()
    {
        return $this->hasMany(EsimPackage::class, 'operator_id');
    }

    public function getCreatedAtAttribute($value)
    {
        $timezone = systemflag('timezone');
        return Carbon::parse($value)->timezone($timezone);
    }
    public function getUpdatedAtAttribute($value)
    {
        $timezone = systemflag('timezone');
        return Carbon::parse($value)->timezone($timezone);
    }
}
