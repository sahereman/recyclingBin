<?php

namespace App\Sockets;

use App\Handlers\SocketJsonHandler;
use App\Jobs\GenerateClientOrderSnapshot;
use App\Models\Bin;
use App\Models\BinToken;
use App\Models\BinTypeFabric;
use App\Models\BinTypePaper;
use App\Models\ClientOrder;
use App\Models\ClientPrice;
use App\Models\User;
use App\Models\UserMoneyBill;
use App\Notifications\Client\ClientOrderCompletedNotification;
use Hhxsv5\LaravelS\Swoole\Socket\TcpSocket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Swoole\Server;

class BinTcpSocket extends TcpSocket
{
    const CLIENT_LOGIN = 'yzs000';
    const CLIENT_TRANSACTION = 'yzs001';
    const CLIENT_LOGOUT = 'yzs002';
    const BEAT = 'yzs003';
    const QRCODE = 'yzs004';

    private $actions = [
        self::CLIENT_LOGIN,
        self::CLIENT_TRANSACTION,
        self::CLIENT_LOGOUT,
        self::BEAT,
        self::QRCODE,
    ];

    public function onConnect(Server $server, $fd, $reactorId)
    {
        Log::info('New TCP connection', [$fd, $reactorId]);
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        Log::info('Close TCP connection', [$fd, $reactorId]);
    }


    public function onReceive(Server $server, $fd, $reactorId, $data)
    {
        Log::info($fd, [$data]);

        //        $redis = app('redis.connection');
        //        $userId = array_first($redis->zrangebyscore($this->client_fd, $frame->fd, $frame->fd));
        $data = is_array(json_decode($data, true)) ? json_decode($data, true) : array();


        $validator = Validator::make($data, [
            'static_no' => ['required', Rule::in($this->actions)],
        ]);

        if ($validator->fails())
        {
            $server->send($fd, new SocketJsonHandler([
                'result_code' => '401' // static_no 错误/未找到
            ]));
        } else
        {
            switch ($data['static_no'])
            {
                case self::CLIENT_LOGIN :
                    $this->clientLoginAction($server, $fd, $data);
                    break;
                case self::CLIENT_TRANSACTION :
                    $this->clientTransactionAction($server, $fd, $data);
                    break;
                case self::CLIENT_LOGOUT :
                    $this->clientLogoutAction($server, $fd, $data);
                    break;
                case self::BEAT :
                    $this->beatAction($server, $fd, $data);
                    break;
                case self::QRCODE :
                    $this->qrcodeAction($server, $fd, $data);
                    break;
                default:
                    $server->send($fd, new SocketJsonHandler([
                        'result_code' => '401' // static_no 错误/未找到
                    ]));
                    break;
            }
        }

    }

    public function clientLoginAction($server, $fd, $data)
    {

    }

    /*
     {"static_no":"yzs001","equipment_no":"0532009","equipment_all":false,"user_card":"7","delivery_type":"1","delivery_weight":"3000","delivery_price":"50","delivery_money":"10","delivery_time":"20190923140001"}
     */
    public function clientTransactionAction($server, $fd, $data)
    {
        $bin = Bin::where('no', $data['equipment_no'])->first();
        $user = User::find($data['user_card']);
        $client_prices = ClientPrice::all();

        if (!$bin || !$user || !in_array($data['delivery_type'], [1, 2]))
        {
            $server->send($fd, new SocketJsonHandler([
                'result_code' => '400' // 用户未注册/json格式字段错误
            ]));
            return false;
        }

        switch ($data['delivery_type'])
        {
            case 1:
                $type = $bin->type_paper;
                $price = $client_prices->where('slug', 'paper')->first();
                break;
            case 2:
                $type = $bin->type_fabric;
                $price = $client_prices->where('slug', 'fabric')->first();
                break;
        }
        $weight = bcdiv($data['delivery_weight'], 1000, 2);
        $subtotal = bcmul($price['price'], $weight, 2);

        $order = ClientOrder::create([
            'status' => ClientOrder::STATUS_COMPLETED,
            'bin_id' => $bin->id,
            'user_id' => $user->id,
            'total' => $subtotal,
            'bin_snapshot' => [],
        ]);

        $order->items()->create([
            'type_slug' => $type::SLUG,
            'type_name' => $type::NAME,
            'number' => $weight,
            'unit' => $price['unit'],
            'subtotal' => $subtotal,
        ]);

        $type->update([
            'status' => $data['equipment_all'] ? $type::STATUS_FULL : $type->status,
            'number' => bcadd($type->number, $weight, 2),
        ]);

        $user->update([
            'money' => bcadd($user->money, $subtotal, 2),
            'total_client_order_money' => bcadd($user->total_client_order_money, $subtotal, 2),
            'total_client_order_count' => bcadd($user->total_client_order_count, 1),
            'total_client_order_number' => bcadd($user->total_client_order_number, $weight,2),
        ]);

        GenerateClientOrderSnapshot::dispatch($order, $bin);
        UserMoneyBill::change($user, UserMoneyBill::TYPE_CLIENT_ORDER, $order->total, $order);
        $order->user->notify(new ClientOrderCompletedNotification($order));

        $bin->token->update([
            'related_model' => $order->getMorphClass(),
            'related_id' => $order->id,
        ]);

        $server->send($fd, new SocketJsonHandler([
            'static_no' => self::CLIENT_TRANSACTION,
            'result_code' => '200',
        ]));
    }

    /*
     {"static_no":"yzs002","equipment_no":"0532001"}
     */
    public function clientLogoutAction($server, $fd, $data)
    {
        $bin = Bin::where('no', $data['equipment_no'])->first();
        if (!$bin)
        {
            $server->send($fd, new SocketJsonHandler([
                'result_code' => '400' // 用户未注册/json格式字段错误
            ]));
            return false;
        }


        BinToken::where('bin_id', $bin->id)->delete();// 清空已有token
        $server->send($fd, new SocketJsonHandler([
            'static_no' => self::CLIENT_LOGOUT,
            'result_code' => '200',
        ]));
    }

    /*
    {"static_no":"yzs003","equipment_no":"0532009","equipment_all":false,"device":"0000","send_time":"20190923150201"}
     */
    public function beatAction($server, $fd, $data)
    {
        $bin = Bin::where('no', $data['equipment_no'])->first();
        if (!$bin)
        {
            $server->send($fd, new SocketJsonHandler([
                'result_code' => '400' // 用户未注册/json格式字段错误
            ]));
            return false;
        }


        $server->send($fd, new SocketJsonHandler([
            'static_no' => self::BEAT,
            'result_code' => '200',
        ]));
    }


    /*
    {"static_no":"yzs004","equipment_no":"0532009"}
     */
    public function qrcodeAction($server, $fd, $data)
    {
        $bin = Bin::where('no', $data['equipment_no'])->first();
        if (!$bin)
        {
            $server->send($fd, new SocketJsonHandler([
                'result_code' => '400' // 用户未注册/json格式字段错误
            ]));
            return false;
        }

        BinToken::where('bin_id', $bin->id)->delete();// 清空已有token

        $bin_token = new BinToken();
        $bin_token->bin_id = $bin->id;
        $bin_token->fd = $fd;
        $bin_token->save();

        $server->send($fd, new SocketJsonHandler([
            'static_no' => self::QRCODE,
            'result_code' => '200',
            'set_url' => url('client/qr') . '?token=' . $bin_token->token
        ]));
    }
}