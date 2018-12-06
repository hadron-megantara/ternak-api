<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class ProfileController extends Controller
{
    public function updateProfile(Request $request){
        $user = auth('api')->user();
        $user = $user->toArray();

        $userData = User::where('id', $user['id'])->first();

        if($request->has('name')){
            $userData->name = $request->name;
        }

        if($request->has('phone')){
            $userData->phone = $request->phone;
        }

        $userData->save();

        return response()->json([
            'success' => true,
            'data' => [
                'msg' => "Berhasil memperbarui Profil",
                'detail' => $userData
            ],
            'error' => null,
            'version' => env('API_VERSION', 'v1')
        ]);
    }
}
