<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Province;
use App\City;
use App\District;
use App\Village;

class MasterController extends Controller
{
    public function getProvince(Request $request){
        $province = Province::all();

        if($province){
            return response()->json([
                'success' => true,
                'data' => $province,
                'error' => null,
                'version' => env('API_VERSION', 'v1')
            ]);
        } else{
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => ['code' => 404, 'msg' => "Gagal mendapatkan data Provinsi"],
                'version' => env('API_VERSION', 'v1')
            ]);
        }
    }

    public function getCity(Request $request){
        if($request->has('provinceId')){
            $city = City::where('province_id', $request->provinceId)->get();

            if($city){
                return response()->json([
                    'success' => true,
                    'data' => $city,
                    'error' => null,
                    'version' => env('API_VERSION', 'v1')
                ]);
            } else{
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'error' => ['code' => 404, 'msg' => "Gagal mendapatkan data Kota"],
                    'version' => env('API_VERSION', 'v1')
                ]);
            }
        } else{
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => ['code' => 422, 'msg' => "Gagal mendapatkan data Kota, Provinsi tidak boleh kosong"],
                'version' => env('API_VERSION', 'v1')
            ]);
        }
    }

    public function getDistrict(Request $request){
        if($request->has('cityId')){
            $district = District::where('city_id', $request->cityId)->get();

            if($district){
                return response()->json([
                    'success' => true,
                    'data' => $district,
                    'error' => null,
                    'version' => env('API_VERSION', 'v1')
                ]);
            } else{
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'error' => ['code' => 404, 'msg' => "Gagal mendapatkan data Kecamatan"],
                    'version' => env('API_VERSION', 'v1')
                ]);
            }
        } else{
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => ['code' => 422, 'msg' => "Gagal mendapatkan data Kecamatan, Kota tidak boleh kosong"],
                'version' => env('API_VERSION', 'v1')
            ]);
        }
    }

    public function getVillage(Request $request){
        if($request->has('districtId')){
            $village = Village::where('district_id', $request->districtId)->get();

            if($village){
                return response()->json([
                    'success' => true,
                    'data' => $village,
                    'error' => null,
                    'version' => env('API_VERSION', 'v1')
                ]);
            } else{
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'error' => ['code' => 404, 'msg' => "Gagal mendapatkan data Kelurahan"],
                    'version' => env('API_VERSION', 'v1')
                ]);
            }
        } else{
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => ['code' => 422, 'msg' => "Gagal mendapatkan data Kelurahan, Kecamatan tidak boleh kosong"],
                'version' => env('API_VERSION', 'v1')
            ]);
        }
    }
}
