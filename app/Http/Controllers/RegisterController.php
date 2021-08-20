<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function post(Request $request)
    {
        $now = Carbon::now();
        $hashed_password = Hash::make($request->password);
        $param = [
            "name" => $request->name,
            "email" => $request->email,
            "password" => $hashed_password,
            "type" => $request->type,
            "created_at" => $now,
            "updated_at" => $now,
        ];
        DB::table('users')->insert($param);
        return response()->json([
            'message' => 'User created successfully',
            'data' => $param
        ], 200);
    }

    public function registerShop(Request $request)
    {
        $now = Carbon::now();
        $hashed_password = Hash::make($request->password);
        $param = [
            "name" => $request->name,
            "email" => $request->email,
            "password" => $hashed_password,
            "type" => $request->type,
            "region" => $request->region,
            "genre" => $request->genre,
            "info" => $request->info,
            "img_url" => $request->img_url,
            "open" => $request->open,
            "close" => $request->close,
            "period" => $request->period,
            "created_at" => $now,
            "updated_at" => $now,
        ];
        DB::table('users')->insert($param);
        return response()->json([
            'message' => 'Shop created successfully',
            'data' => $param
        ], 200);
    }
}
