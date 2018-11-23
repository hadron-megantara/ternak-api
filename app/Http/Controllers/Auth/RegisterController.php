<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Carbon\Carbon;

class RegisterController extends Controller
{
    protected function validator(array $data)
    {
        $messages = [
            'required' => 'Field :attribute tidak boleh kosong',
        ];

        return Validator::make($data, [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
            'fullname' => 'required|max:255',
        ], $messages);
    }

    protected function register(Request $request)
    {
        dd($request->all());
        $validator = $this->validator($request->all());

        if ($validator->fails())
        {
            foreach($validator->messages()->getmessages() as $message){
                $errorMessage[] = $message[0];
            }

            return response()->json([
                'success' => false,
                'data' => null,
                'error' => ['code' => 422, 'msg' => $errorMessage],
                'version' => env('API_VERSION', 'v1')
            ]);
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
