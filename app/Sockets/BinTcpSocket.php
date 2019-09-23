<?php

namespace App\Sockets;

use App\Handlers\SocketJsonHandler;
use App\Models\Bin;
use App\Models\BinToken;
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
        Log::info('TCP Received', [$fd, $reactorId, $data]);

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

    public function clientTransactionAction($server, $fd, $data)
    {

    }

    public function clientLogoutAction($server, $fd, $data)
    {

    }

    public function beatAction($server, $fd, $data)
    {

    }


    /*
    {"static_no":"yzs004","equipment_no":"0532001"}
    */
    public function qrcodeAction($server, $fd, $data)
    {
        $bin = Bin::where('no', $data['equipment_no'])->first();
        if (!$bin)
        {
            return false;
        }

        BinToken::where('bin_id', $bin->id)->delete();// 清空已有token

        $bin_token = new BinToken();
        $bin_token->bin_id = $bin->id;
        $bin_token->save();

        $server->send($fd, new SocketJsonHandler([
            'result_code' => '200',
            'set_url' => url('client/qrlogin') . '?token=' . $bin_token->token
        ]));
    }
}