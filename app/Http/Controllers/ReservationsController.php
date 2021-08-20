<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Facade\Ignition\Tabs\Tab;

class ReservationsController extends Controller
{
    public function getReservationInfo(Request $request)
    {
        $item = Reservation::where('shop_id', $request->id)
            // ->where()
            ->get();
        return $item;
        return response()->json($item, 200);
    }

    public function confirmReservation(Request $request)
    {
        $item = Reservation::where('shop_id', $request->id)->get();
        return $item;
        $name = $item->name;
        $region = $item->region;
        $genre = $item->genre;
        $info = $item->info;
        $img_url = $item->img_url;
        $open = $item->open;
        $close = $item->close;
        $period = $item->period;
        $items = [
            "name" => $name,
            "region" => $region,
            "genre" => $genre,
            "info" => $info,
            "img_url" => $img_url,
            "open" => $open,
            "close" => $close,
            "period" => $period,
        ];
        return response()->json($items, 200);
    }

    public function getSlot(Request $request)
    {
        $shop_id = intval($request->shop_id);
        $user_id = intval($request->user_id);
        $startDate = date('Y-m-d', strtotime($request->startDate));
        $number = intval($request->number);
        // 時刻として認識させるために一度適当な日付（startDate）をくっつけてから時刻へ変換
        $open = date("H:i", strtotime($startDate ." ". $request->open));
        $close = date("H:i", strtotime($startDate ." ". $request->close));
        
        $period = intval($request->period);

        // 取得した予約希望時間と日付をまとめる
        // $dateAndTime = date('Y-m-d H:i', strtotime(date('Y-m-d', strtotime($request->date)) ." ". $request->time));

        // startDateから7日分の配列を作る
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            array_push($dates, [date("Y-m-d",strtotime("+{$i} day", strtotime($startDate))),date("Y",strtotime("+{$i} day", strtotime($startDate))),date("m",strtotime("+{$i} day", strtotime($startDate))),date("d",strtotime("+{$i} day", strtotime($startDate))),date("D",strtotime("+{$i} day", strtotime($startDate)))]);
        }
        // 今日から30日間の配列を作る
        $datesOneMonthAhead = [];
        $today = date('Y-m-d H:i:s', strtotime('+9hour'));
        for ($j = 0; $j < 30; $j++) {
            array_push($datesOneMonthAhead, [date("Y-m-d",strtotime("+{$j} day", strtotime($today))),date("Y",strtotime("+{$j} day", strtotime($today))),date("m",strtotime("+{$j} day", strtotime($today))),date("d",strtotime("+{$j} day", strtotime($today)))]);
        }

        // 時間枠の配列を定義
        $times = [];
        $nextTime = "";
        for ($k = 0; $nextTime < $close; $k++) {
            $nextTime = date("H:i",strtotime("+ ".($k * 30)." minute", strtotime($open)));
            array_push($times, $nextTime);
        }

        // 表示される予約枠すべてを取得
        // $reservings = [];
        // foreach($dates as $date){
        //     foreach($times as $time){
        //         array_push($reservings, [
        //             date('Y-m-d H:i', strtotime(date('Y-m-d', strtotime($date[0])) ." ". $time)),
        //             date('Y-m-d H:i',strtotime("+ ".($period)." minute", strtotime(date('Y-m-d H:i', strtotime(date('Y-m-d', strtotime($date[0])) ." ". $time)))))
        //         ]);
        //     }
        // }

        // 日付ごとにforeach ※foreachの予定だったが、126行目からのような形にはまとまらず、ひとまずforで代用した
        $day_available_array =[];
        for($i = 0; count($dates) > $i; $i++) {
            array_push($day_available_array, [$dates[$i]]);
            for($j = 0; count($times) > $j; $j++){
                // DD($dates[$i]);
                $startTime = date('Y-m-d H:i', strtotime(date('Y-m-d', strtotime($dates[$i][0])) ." ". $times[$j]));
                array_push($day_available_array[$i], [$times[$j]=> $this->judge($shop_id, $startTime, $number, $period)]);
            }
        }

        // for($i = 0; count($dates) > $i; $i++) {
        //     array_push($day_available_array, $dates[$i]);
        //     for($j = 0; count($times) > $j; $j++){
        //         array_push($day_available_array[$i], [$times[$j]=> $this->judge($shop_id, $dateAndTime, $number, $period)]);
        //     }
        // }


        // $day_available_array =[];
        // foreach($dates as $date){
        //     array_push($day_available_array, $date);
        //     foreach($times as $time){
        //         array_push($day_available_array, [$time=> $this->judge($shop_id, $dateAndTime, $number, $period)]);
        //     }
        // }

        // 理想的な$day_available_arrayの形
        // 
        // [
        //     "2021-06-20"=>[
        //         '10:00'=>true, //その時間の空いている席が一個でもあればtrue
        //         '11:00'=>true,
        //         '12:00'=>false,
        //     ],
        //     "2021-06-21"=>[
        //         '10:00'=>true,
        //         '11:00'=>true,
        //         12:00=>false,
        //     ],
        //     "2021-06-21"=>[
        //         '10:00'=>true,
        //         '11:00'=>true,
        //         12:00=>false,
        //     ],
        //     ]

        $items = [
            // "shop_id" => $shop_id,
            // "user_id" => $user_id,
            // "startDate" => $startDate,
            // "number" => $number,
            // "open" => $open,
            // "close" => $close,
            // "period" => $period,
            // "dateAndTime" => $dateAndTime,
            "startDate" => $startDate,
            "dates" => $dates,
            "datesOneMonthAhead" => $datesOneMonthAhead,
            "times" => $times,
            "day_available_array" => $day_available_array,
        ];
        return response()->json($items, 200);
    }

    public function judge($shop_id, $startTime, $number, $period)
    {
        $availableIds = $this->getAvailableTableId($shop_id, $startTime, $number, $period);
        // 各テーブルに対して(以下の中で1個でもtrueが出てきたらtrue)
        // そのままreturn count($availableIds) > 0;とすると$availableIdsが空のときにエラーになるのでis_arrayを使ってエラーになるときを避ける
        // https://qiita.com/masaki-ogawa/items/1671d110b2286ececd09#:~:text=if%20(is_array(%24hoge))%20%7B%0A%20%20%20%20count(%24hoge)%3B%0A%7D
        if (is_array($availableIds)) {
            return count($availableIds) > 0;
        }
    }

    public function getAvailableTableId($shop_id, $startTime, $number, $period)
    // public function getAvailableTableId(Request $request)
    {
        // $shop_id = intval($request->shop_id);
        // $number = intval($request->number);
        // $period = intval($request->period);
        // $dateAndTime = date('Y-m-d H:i', strtotime(date('Y-m-d', strtotime($request->date)) ." ". $request->time));

        // ①対象となるテーブル群を人数（number）とshop_idをもとに取得($tables)
        $tables = Table::where('capacity', '>=', $number)
            ->where('shop_id', $shop_id)
            ->get();

        // 空いている席のIDを取得する
        $availableTableIds = [];

        $reserving = [
            "start"=> $startTime,
            "end"=> date('Y-m-d H:i:s',strtotime("+ 30 minute", strtotime($startTime))),
        ];
        // var_dump($reserving);

        // DD("reserving",$reserving);

        $now = date('Y-m-d H:i:s', strtotime('+9hour'));

        // おそらくここの時間でフィルターしている部分がうまく動いていない
        // ②各テーブルに対して今までの予約を見る
        // フロントで選択した店かつ予約人数よりもキャパの大きい席の空き状況を１つ１つ調べる
        foreach($tables as $table){
            // $reservationsOnTheDayその日の予約群(reservations)を検索 (wheredateを使うとできる DDでworkしてるか確認)
            // $reservationsOnTheDay = Reservation::where('table_id', $table->id)->get();
            $reservationsOnTheDay = Reservation::whereDate('date_time', '=' , date("Y-m-d", strtotime($reserving["start"])))
                                                    ->where('table_id', $table["id"])
                                                    ->where('number_of_people', '<=', $number)
                                                    ->get();

            $judge = true;

            // 現在の時刻から１時間半先までは予約できないようにする
            if($judge = $reserving["end"] < date('Y-m-d H:i:s',strtotime("+ 85 minute", strtotime($now)))
            )
                return false;{
            }
            // 全ての表示されるコマが現在の予約状況に照らし合わせて空いているかを判断する
            foreach($reservationsOnTheDay as $reservation){
                // var_dump($reserving["end"]);
                // dd($reserving["end"]);
                // var_dump($reservation["date_time"]);
                if($judge = ($reserving["end"] > $reservation["date_time"]
                    && $reserving["start"] < date('Y-m-d H:i:s',strtotime("+ ".($period-1)." minute", strtotime($reservation["date_time"]))))
                    // && $reservation["table_id"] !== $table["id"]
                )
                return false;{
                }
            }
            array_push($availableTableIds, $table->id);
            // var_dump($availableTableIds);
        }
        return $availableTableIds;
        // dd($availableTableIds);
        // $test = count($availableTableIds);

        // $items = [
        //     "shop_id" => $shop_id,
        //     "period" => $period,
        //     "number" => $number,
        //     "tables" => $tables,
        //     "reserving" => $reserving,
        //     "dateAndTime" => $dateAndTime,
        //     "availableTableIds" => $availableTableIds,
        //     "test" => $test, 
        // ];
        // return response()->json($items, 200);

    }

    public function confirmDateTime (Request $request){

        // フロントから送ったものをまとめる
        $user_id = intval($request->user_id);
        $shop_id = intval($request->shop_id);
        $dateAndTime = date('Y-m-d H:i', strtotime(date('Y-m-d', strtotime($request->date)) ." ". $request->time));
        $number = intval($request->number);
        // DD("$user_id",$user_id);

        // 空いているtable_idを見つける
        $table_id = Table::where('capacity', '>=', $number)
            ->where('shop_id', $shop_id)
            ->first()
            // postにすると下のidがとれなくなる
            ->id;
        
        // DD("$table_id",$table_id);

        // $items = [
        //     "user_id" => $user_id,
        //     "shop_id" => $shop_id,
        //     "dateAndTime" => $dateAndTime,
        //     "number" => $number,
        //     "table_id" => $table_id,
        // ];

        // return response()->json($items, 200);

        $item = new Reservation;
        $item->user_id = $user_id;
        $item->shop_id = $shop_id;
        $item->table_id = $table_id;
        $item->date_time = $dateAndTime;
        $item->number_of_people = $number;

        // return response()->json($item, 200);

        $item->save();
        return response()->json([
            'message' => 'Reservation created successfully',
            'data' => $item
        ], 200);



        // $item = new Reservation;
        // $item->user_id = intval($request->user_id);
        // $item->shop_id = intval($request->shop_id);
        // $item->table_id = Table::where('capacity', '>=', intval($request->number))
        //                         ->where('shop_id', intval($request->shop_id))
        //                         ->first();
        // $item->date_time = date('Y-m-d H:i', strtotime(date('Y-m-d', strtotime($request->date)) ." ". $request->time));
        // $item->number_of_people = intval($request->number)
        // $item->save();
        // return response()->json([
        //     'message' => 'Reservation created successfully',
        //     'data' => $item
        // ], 200);
    }

    public function getMyReservation(Request $request)
    {
        $user_id = intval($request->user_id);
        $now = date('Y-m-d H:i:s', strtotime('+9hour'));
        $data = Reservation::where('user_id', $user_id)
                                ->whereDate('date_time', '>=' , $now)
                                ->get();
        $items = [];
        foreach($data as $item){
            array_push($items, [
                "shop" => User::where('id', $item->shop_id)->first(),
                "reservation" => [
                    date('Y', strtotime($item["date_time"])),
                    date('n', strtotime($item["date_time"])),
                    date('j', strtotime($item["date_time"])),
                    date('D', strtotime($item["date_time"])),
                    date('H:i', strtotime($item["date_time"])),
                    $item["number_of_people"],
                    $item["id"],
                ]
            ]);
        };
        return response()->json($items, 200);
    }


    public function deleteReservation(Request $request)
    {
        $item = Reservation::where('id', $request->id)->delete();
        if ($item) {
            return response()->json(
                ['message' => 'Reservation deleted successfully'],
                200
            );
        } else {
            return response()->json(
                ['message' => 'Reservation not found'],
                404
            );
        }
    }

}
