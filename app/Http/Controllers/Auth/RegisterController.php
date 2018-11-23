<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\OauthClient;
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
            'name' => 'required|max:255',
        ], $messages);
    }

    protected function register(Request $request)
    {
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

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if($user){
            $oauth_client= new OauthClient;
            $oauth_client->user_id = $user->id;
            $oauth_client->name = $user->name;
            $oauth_client->secret = base64_encode(hash_hmac('sha256',$request->password, 'secret', true));
            $oauth_client->password_client=1;
            $oauth_client->personal_access_client=0;
            $oauth_client->redirect = env('DEFAULT_REDIRECT', 'http://localhost');
            $oauth_client->revoked=0;
            $oauth_client->save();

            if($oauth_client){
                return response()->json([
                    'success' => true,
                    'data' => ['msg' => 'Daftar akun berhasil, silahkan cek email Anda untuk verifikasi email'],
                    'error' => null,
                    'version' => env('API_VERSION', 'v1')
                ]);
            }
        }
    }
}
