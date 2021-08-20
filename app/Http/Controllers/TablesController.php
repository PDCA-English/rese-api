<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TablesController extends Controller
{
    public function regTables(Request $request)
    {
        $shop_id = $request->shop_id;
        $numOfPeopleOne = $request->numOfPeopleOne;
        $numOfTableOne = $request->numOfTableOne;
        $numOfPeopleTwo = $request->numOfPeopleTwo;
        $numOfTableTwo = $request->numOfTableTwo;
        $numOfPeopleThree = $request->numOfPeopleThree;
        $numOfTableThree = $request->numOfTableThree;
        for ($i = 0; $i < $numOfTableOne; $i++) {
            $item = new Table;
            $item->shop_id = $shop_id;
            $item->capacity = $numOfPeopleOne;
            $item->save();
        }
        for ($j = 0; $j < $numOfTableTwo; $j++) {
            $item = new Table;
            $item->shop_id = $shop_id;
            $item->capacity = $numOfPeopleTwo;
            $item->save();
        }
        for ($k = 0; $k < $numOfTableThree; $k++) {
            $item = new Table;
            $item->shop_id = $shop_id;
            $item->capacity = $numOfPeopleThree;
            $item->save();
        }
        return response()->json([
            'message' => 'Table created successfully',
            'data' => $item
        ], 200);
    }

    public function getTables(Request $request)
    {
        $shop_id = $request->shop_id;
        $tables = Table::where('shop_id', $shop_id)->get();
        // $uniques = [];
        $allTable = [];
        foreach($tables as $table){
            // array_push($uniques, $table->capacity);
            // array_unique($uniques);
            array_push($allTable, $table->capacity);
        };
        // $tableInfo = [];
        // foreach($uniques as $unique){
        // }
        $tableInfo = array_count_values($allTable);
        return $tableInfo;
        return response()->json($tableInfo, 200);

        
        // array_push($items, [
        //     "shop" => User::where('id', $item->shop_id)->first(),
        //     "reservation" => [
        //         date('Y', strtotime($item["date_time"])),
        //         date('n', strtotime($item["date_time"])),
        //         date('j', strtotime($item["date_time"])),
        //         date('D', strtotime($item["date_time"])),
        //         date('H:i', strtotime($item["date_time"])),
        //         $item["number_of_people"],
        //         $item["id"],
        //     ]
        // ]);


    }

    public function deleteTable(Request $request)
    {
        $item = Table::where('shop_id', $request->shop_id)
                        ->where('capacity', $request->numberOfPeople)
                        ->delete();
        if ($item) {
            return response()->json(
                ['message' => 'Table deleted successfully'],
                200
            );
        } else {
            return response()->json(
                ['message' => 'Table not found'],
                404
            );
        }
    }
}
