<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'user_id',
        'subscriber_id',
        'status',
        'package',
        'expire_date',
        'cancellation_date',
        'renewal_date'
    ];

    public function renewed()
    {
        $date       = Carbon::now()->subDay();
        $startDate  = $date->copy()->startOfDay();
        $endDate    = $date->copy()->endOfDay();

        return $this->hasMany(Subscription::class);
    }

    public function purchased()
    {
        $date       = Carbon::now()->subDay();
        $startDate  = $date->copy()->startOfDay();
        $endDate    = $date->copy()->endOfDay();

        return $this->hasMany(Subscription::class);
    }

    public function canceled()
    {
        $date       = Carbon::now()->subDay();
        $startDate  = $date->copy()->startOfDay();
        $endDate    = $date->copy()->endOfDay();

        return $this->belongsTo(Subscription::class);
    }
}
