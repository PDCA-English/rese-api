<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersController extends Controller
{
    public function get(Request $request)
    {
        if ($request->has('email')) {
            $items = DB::table('users')->where('email', $request->email)->get();
            return response()->json([
                'message' => 'User got successfully',
                'data' => $items
            ], 200);
        } else {
            return response()->json(['status' => 'not found'], 404);
        }
    }

    public function getshops()
    {
        $items = User::where('type', 2)->get();
        return response()->json([
            'message' => 'Shops got successfully',
            'data' => $items
        ], 200);
    }

    public function getShopInfo(Request $request)
    {
        $shop_id = $request->id;
        $shop = User::where('id', $shop_id)->first();
        $table = Table::where('shop_id', $shop_id)->get();
        $now = date('Y-m-d H:i:s', strtotime('+9hour'));
        $reservation = Reservation::where('shop_id', $shop_id)
                                        ->whereDate('date_time', '>=' , $now)
                                        ->get();
        $favorite = Favorite::where('shop_id', $shop_id)->get();
        $shops = [
            "shop" => $shop,
            "table" => $table,
            "reservation" => $reservation,
            "favorite" => $favorite,
        ];
        return response()->json($shops, 200);
    }

    public function getShopDetail(Request $request)
    {
        $shopDetail = [];
        $user_id = $request->user_id;
        $shops = User::where('type', 2)->get();
        foreach($shops as $shop) {
            $shop_id = $shop->id;
            $table = Table::where('shop_id', $shop_id)->get();
            $reservation = Reservation::where('shop_id', $shop_id)->get();
            $favorite = Favorite::where('shop_id', $shop_id)->where('user_id', $user_id)->first();
            $eachShopDetail = [
                "shop" => $shop,
                "table" => $table,
                "reservation" => $reservation,
                "favorite" => $favorite,
            ];
            array_push($shopDetail, $eachShopDetail);
        }
        return response()->json($shopDetail, 200);
    }


    public function put(Request $request)
    {
        $param = [
            'profile' => $request->profile,
            'email' => $request->email
        ];
        DB::table('users')->where('email', $request->email)->update($param);
        return response()->json([
            'message' => 'User updated successfully',
            'data' => $param
        ], 200);
    }

    public function deleteShop(Request $request)
    {
        DB::table('users')->where('id', $request->id)->delete();
        return response()->json([
            'message' => 'Shop deleted successfully',
        ], 200);
    }

    public function updateShopInfo(Request $request)
    {
        $param = [
            'name' => $request->name,
            'img_url' => $request->img_url,
            'region' => $request->region,
            'genre' => $request->genre,
            'info' => $request->info,
        ];
        DB::table('users')->where('id', $request->id)->update($param);
        return response()->json([
            'message' => 'Shop updated successfully',
            'data' => $param
        ], 200);
    }


    
}
