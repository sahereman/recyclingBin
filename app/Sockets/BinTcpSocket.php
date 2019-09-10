<?php

namespace App\Sockets;

use Hhxsv5\LaravelS\Swoole\Socket\TcpSocket;
use Swoole\Server;
class BinTcpSocket extends TcpSocket
{
    public function onConnect(Server $server, $fd, $reactorId)
    {
        \Log::info('New TCP connection', [$fd]);
        $server->send($fd, 'New TCP connection OK');
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        \Log::info('Close TcpSocket connection', [$fd]);
        $server->send($fd, 'Goodbye');
    }


    public function onReceive(Server $server, $fd, $reactorId, $data)
    {
        // 首次开机 收到60201 imei & imsi

        // 二次开机 收到60102 设备开关机通知


//        $command = 60201;

//        echo int_helper::uInt8(107) . PHP_EOL;  // k
//        echo int_helper::uInt8("\x6b") . PHP_EOL . PHP_EOL;  // 107
//
//        echo int_helper::uInt16(4101) . PHP_EOL;  // \x05\x10
//        echo int_helper::uInt16("\x05\x10") . PHP_EOL;  // 4101
//        echo int_helper::uInt16("\x05\x10", true) . PHP_EOL . PHP_EOL;  // 1296
//
//        echo int_helper::uInt32(2147483647) . PHP_EOL;  // \xff\xff\xff\x7f
//        echo int_helper::uInt32("\xff\xff\xff\x7f") . PHP_EOL . PHP_EOL;  // 2147483647


        info($data);

        \Log::info('TcpSocket  Received data', [$fd, $reactorId, $data]);


//        $server->send($fd, 'LaravelS: ' . $data);


//        if ($data === "quit\r\n")
//        {
//            $server->send($fd, 'LaravelS: bye' . PHP_EOL);
//            $server->close($fd);
//        }
    }

}