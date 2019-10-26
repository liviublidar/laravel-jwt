<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\EventListener\ValidateRequestListener;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Account;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'dob', 'email', 'password', 'suspended', 'account_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setPasswordAttribute($password)
    {
        if ( !empty($password) ) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public static function getRandomUser($suspended = null)
    {
        if($suspended === null) {
            $row = User::all()->random();
        } else if ($suspended === true) {
            $row = User::where('suspended', 1 )->get()->random();
        } else if ($suspended === false) {
            $row = User::where('users.suspended', 0)
                ->join('accounts', 'users.account_id', '=', 'accounts.id')
                ->select('users.*', 'accounts.name')
                ->where('accounts.suspended', 0)
                ->get()
                ->random();
        }
        return $row;
    }

    public static function getRandomUserFromAccountId($accountId, $suspended)
    {
        $matchThese = [
            'account_id' => $accountId,
            'suspended' => (int) $suspended
        ];
        $user = User::where($matchThese)->get()->random();
        return $user;
    }

    public function isSuspended()
    {
        return (boolean) $this->suspended;
    }

    public function getAccount()
    {
        return Account::findOrFail($this->account_id);
    }

}
