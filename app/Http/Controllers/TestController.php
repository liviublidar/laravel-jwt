<?php

namespace App\Http\Controllers;

use App\Traits\UserTrait;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use UserTrait;

    public function open()
    {
        $data = "This data is open and can be accessed without the client being authenticated";
        return response()->json(compact('data'),200);

    }

    public function closed()
    {
        $loggedInUser = $this->getAuthenticatedUser();
        $data = [
            "message" => "Only authorized users can see this",
            "debug" => $loggedInUser
        ];



        return response()->json(compact('data'),200);
    }
}
