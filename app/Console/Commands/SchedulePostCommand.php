<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Pool;
use Carbon\Carbon;



class SchedulePostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schedule-post-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $sep_time = 10;
            $API = 'https://notify-api.line.me/api/notify';
            $now = Carbon::now();

            // 10分単位で切り捨て
            $date_down = $now->subMinutes($now->minute % $sep_time);
            $date_down = date('Y-m-d H:i', strtotime($date_down));

            Log::info('スケジュール配信 開始');

            // 配信対象メッセージ抽出
            $messages = DB::table('schedules')
            ->where('plan_at', $date_down)
            ->join('messages','schedules.message_id','=','messages.id')
            ->leftjoin('images','messages.id','=','images.message_id')
            ->select(
                'messages.store_id as store_id',
                'messages.id as message_id',
                'messages.title as title',
                'messages.content as content',
                'images.save_name as save_name',
                )
            ->get();

            if ($messages){
                Log::info('スケジュール配信 終了【0件】');
                return;
            }

            
            // 非同期リクエスト用パラメータリスト作成
            $requests_param = [];
            foreach($messages as $msg)
            {

                $history_id = DB::table('histories')
                ->insertGetId(
                    [
                        'store_id' => $msg->store_id,
                        'title'=> $msg->title,
                        'content'=> $msg->content,
                        'status'=> '配信中',
                        'start_at'=> Carbon::now(),
                        'img_url' => $msg->save_name == Null ? Null: url(config('app.access_storage.image').'/'.$msg->save_name),
                        'created_at'=> Carbon::now()
                    ]
                );
                
                $lines = DB::table('lines')
                ->select('id','token', 'user_name')
                ->where('is_valid', true)
                ->where('store_id', $msg->store_id
                )->get();

                foreach($lines as $line)
                {  
                    if($msg->save_name == null)
                    {
                        array_push($requests_param,
                        [
                            // 非同期リクエストの結果を特定するためのキー
                            'key' => $history_id. '_' . $msg->message_id . '_' . $line->id,
                            'history_id' => $history_id,
                            'user_name' => $line->user_name,
                            'params' =>  [
                                'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
                                'http_errors' => false,
                                'multipart' => [
                                    ['name' => 'message','contents' => $msg->content]
                                ]
                            ]
                        ]
                    );
                    } else {

                        array_push($requests_param,
                        [
                            'key' => $history_id. '_' . $msg->message_id . '_' . $line->id,
                            'history_id' => $history_id,
                            'user_name' => $line->user_name,
                            'params' =>  [
                                'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
                                'http_errors' => false,
                                'multipart' => [
                                    ['name' => 'message','contents' => $msg->content],
                                    ['name' => 'imageFile','contents' => Psr7\Utils::tryFopen(config('app.access_storage.image').'/'.$msg->save_name, 'r')]
                                ]
                            ]
                        ]);
                    }
                }
            }

            // dd($requests_param[0]);

            ini_set("max_execution_time",0);
            $client = new Client();
            $requests = function ($requests_param) use ($client, $API) {
                foreach ($requests_param as $param) {
                    yield function() use ($client, $API, $param) {
                        return $client->requestAsync('POST', $API, $param['params']);
                    };
                }
            };

            $contents = [];
            $pool = new Pool($client, $requests($requests_param), [
                'concurrency' => 10,
                'fulfilled' => function ($response, $index) use ($requests_param, &$contents) {
                    $contents[$requests_param[$index]['key']] = [
                    'html'             => $response->getBody()->getContents(),
                    'status_code'      => $response->getStatusCode(),
                    'response_header'  => $response->getHeaders()
                    ];

                    $contents[$requests_param[$index]['key']]['history_id'] = $requests_param[$index]['history_id'];
                    $contents[$requests_param[$index]['key']]['user_name'] = $requests_param[$index]['user_name'];
                },
                'rejected' => function ($reason, $index) use ($requests_param, &$contents) {
                    $contents[$requests_param[$index]['key']] = [
                    'html'   => '',
                    'reason' => $reason
                    ];
                    $contents[$requests_param[$index]['key']]['history_id'] = $requests_param[$index]['history_id'];
                    $contents[$requests_param[$index]['key']]['user_name'] = $requests_param[$index]['user_name'];
                },
            ]);
            $promise = $pool->promise();
            $promise->wait();



            // history_idでグルーピング
            function group_by(array $table, string $key): array
            {
                $groups = [];
                foreach ($table as $row) {
                    $groups[$row[$key]][] = $row;
                }
                return $groups;
            }
            $history_group = group_by($contents, 'history_id');

            // historyテーブルの更新
            foreach ($history_group as $key => $value)
            {
                $result = 'OK';
                $err = 'ー';
                
                $res = array_map(function ($col) {
                    $json = json_decode($col['html']);
                    return '['.$col['user_name'].']'.$json->status.'::'.$json->message;
                }, array_filter($value, function ($col) {
                    return $col['status_code'] != '200';
                }));

                if ($res)
                {
                    $result = 'NG';
                    $err = join('/', $res);
                }
                
                DB::table('histories')->where('id',$key)
                ->update(
                    [
                        'status'=> $result,
                        'end_at'=> Carbon::now(),
                        'err_info' => $err,
                        'updated_at'=> Carbon::now()
                    ]);
            }
            Log::info('スケジュール配信 終了');
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}