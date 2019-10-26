<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

class Account extends Model
{
    public static function getAccountIdFromCode($code)
    {
        $row = Account::where('code', $code)->pluck('id')->first();
        return $row;
    }

    public static function getRandomAccount($suspended = null)
    {
        if($suspended == null) {
            $row = Account::all('id', 'name', 'code')->random();
        } elseif ($suspended) {
            $row = Account::where('suspended', 1)->get()->random();
        } elseif (!$suspended) {
            $row = Account::where('suspended', 0)->get()->random();
        }

        return $row;
    }

    public function isSuspended()
    {
        return (boolean) $this->suspended;
    }

}
