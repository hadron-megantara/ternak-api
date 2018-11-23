<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\OauthClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Validator;

class LoginController extends Controller
{
    protected function validator(array $data)
    {
        $messages = [
            'required' => 'Field :attribute tidak boleh kosong',
        ];

        return Validator::make($data, [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ], $messages);
    }

    public function login(Request $request){
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

        if($request->has('email') && $request->has('password')){
            $user_login = request(['email', 'password']);

            $user = User::where('email', $request->email)->first();

            if(!$user){
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'error' => ['code' => 404, 'msg' => "User not found"],
                    'version' => env('API_VERSION', 'v1')
                ]);
            }

            if(!Hash::check($request->password, $user->password)){
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'error' => ['code' => null, 'msg' => "Wrong password"],
                    'version' => env('API_VERSION', 'v1')
                ]);
            }

            $Oauth = OauthClient::where('user_id',$user->id)
                                ->where('personal_access_client','0')
                                ->first();

            $client = new Client();
            $GuzzleBody = [
                "client_id" => $Oauth->id,
                "client_secret" => $Oauth->secret ,
                "grant_type" =>  "password",
                "username" => $user_login['email'],
                "password" => $user_login['password'],
                "scope" =>  "*",
            ];

            $api_host = env('APP_URL','http://api.ternak.local');
            $endpoint = $api_host . '/oauth/token';
            try {
                $clientResponse = $client->post( $endpoint, [ 'form_params' => $GuzzleBody ] );
                $userToken = json_decode( (string) $clientResponse->getBody() );

                Carbon::setLocale('Asia/Jakarta');
                $currDate = Carbon::now()->toDateTimeString();

                $user->last_login = $currDate;
                $user->save();

                $res = ['user' => $user, 'token' => $userToken];

                return response()->json([
                    'success' => true,
                    'data' => $res,
                    'error' => null,
                    'version' => env('API_VERSION', 'v1')
                ]);
            } catch ( GuzzleHttp\Exception\BadResponseException $e ) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'error' => ['code' => null, 'msg' => "Service error, please try again."],
                    'version' => env('API_VERSION', 'v1')
                ]);
            }
        } else{
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => ['code' => null, 'msg' => "User or Password can not be empty"],
                'version' => env('API_VERSION', 'v1')
            ]);
        }

    }
}
