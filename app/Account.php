<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public static function getAccountIdFromCode($code)
    {
        $row = Account::where('code', $code)->pluck('id')->first();
        return $row;

    }

    public static function getRandomAccount()
    {
        $row = Account::all('id', 'name', 'code')->random();
        return $row;
    }
}
