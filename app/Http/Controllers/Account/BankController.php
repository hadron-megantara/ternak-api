<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\UserBank;

class BankController extends Controller
{
    public function index(Request $request){
        $user = auth('api')->user();
        $user = $user->toArray();

        $bank = UserBank::where('user_id', $user['id'])->get();

        return response()->json([
            'success' => true,
            'data' => $bank,
            'error' => null,
            'version' => env('API_VERSION', 'v1')
        ]);
    }

    public function show(Request $request, $id){
        $bank = UserBank::find($id);
        return response()->json([
            'success' => true,
            'data' => $bank,
            'error' => null,
            'version' => env('API_VERSION', 'v1')
        ]);
    }

    protected function storeValidator(array $data)
    {
        $messages = [
            'required' => 'Field :attribute tidak boleh kosong',
        ];

        return Validator::make($data, [
            'bank_id' => 'required',
            'account_name' => 'required',
            'account_number' => 'required'
        ], $messages);
    }

    public function store(Request $request){
        $validator = $this->storeValidator($request->all());

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

        $user = auth('api')->user();
        $user = $user->toArray();

        $bank = new UserBank;
        $bank->user_id = $user['id'];
        $bank->bank_id = $request->bank_id;
        $bank->account_name = $request->account_name;
        $bank->account_number = $request->account_number;
        $bank->save();

        if($bank){
            return response()->json([
                'success' => true,
                'data' => [
                    'msg' => "Berhasil menambah rekening bank"
                ],
                'error' => null,
                'version' => env('API_VERSION', 'v1')
            ]);
        } else{
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => [
                    'code' => 500,
                    'msg' => 'Terjadi kesalahan sistem, gagal menambah rekening bank'
                ],
                'version' => env('API_VERSION', 'v1')
            ]);
        }
    }

    public function update(Request $request, $id){
        $user = auth('api')->user();
        $user = $user->toArray();

        $bank = UserBank::where('user_id', $user['id'])->first();

        if($request->has('bank_id')){
            $bank->bank_id = $request->bank_id;
        }

        if($request->has('account_name')){
            $bank->account_name = $request->account_name;
        }

        if($request->has('account_number')){
            $bank->account_number = $request->account_number;
        }

        $bank->save();

        if($bank){
            return response()->json([
                'success' => true,
                'data' => [
                    'msg' => "Berhasil memperbarui rekening bank"
                ],
                'error' => null,
                'version' => env('API_VERSION', 'v1')
            ]);
        } else{
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => [
                    'code' => 500,
                    'msg' => 'Terjadi kesalahan sistem, gagal memperbarui rekening bank'
                ],
                'version' => env('API_VERSION', 'v1')
            ]);
        }
    }
}
