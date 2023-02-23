<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id', 'id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'user_id', 'id')->where('status', 'active');
    }

    public function getSubscriptionStatusAttribute()
    {
        if (!$this->subscription)
            return [
                'status' => 'inactive',
                'expire_date' => null,
                'humanity' => null
            ];

        $remain = Carbon::now()->locale('tr_TR')->diff($this->subscription->expire_date);

        if ($remain->invert) {
            return [
                'status' => 'inactive',
                'expire_date' => null,
                'humanity' => null
            ];
        }

        return [
            'status' => 'active',
            'expire_date' => $this->subscription->expire_date,
            'humanity' => $remain->d . 'Gün ' . $remain->h . 'saat ' . $remain->i . 'dakika ' . $remain->s . 'saniye Kaldı!'
        ];
    }
}
