<?php

namespace App\Sockets;

use Hhxsv5\LaravelS\Swoole\Socket\TcpSocket;
use Swoole\Server;
class BinTcpSocket extends TcpSocket
{
    public function onConnect(Server $server, $fd, $reactorId)
    {
        \Log::info('New TCP connection', [$fd]);
        $server->send($fd, 'New TCP connection Success');
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        \Log::info('Close TcpSocket connection : ', [$fd]);
        $server->send($fd, 'Goodbye');
    }


    public function onReceive(Server $server, $fd, $reactorId, $data)
    {
        // 首次开机 收到60201 imei & imsi

        // 二次开机 收到60102 设备开关机通知


        \Log::info('TcpSocket  Received data', [$fd, $reactorId, $data]);


        $server->send($fd, 'TcpSocket  Received data : ' . $data);


        if ($data === "quit\r\n")
        {
            $server->send($fd, 'LaravelS: bye' . PHP_EOL);
            $server->close($fd);
        }
    }

}