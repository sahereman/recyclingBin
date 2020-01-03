<?php

namespace App\Sockets;

use App\Handlers\SocketJsonHandler;
use App\Http\Controllers\Client\SmsController;
use App\Jobs\ClearBinToken;
use App\Jobs\GenerateCleanOrderSnapshot;
use App\Jobs\GenerateClientOrderSnapshot;
use App\Jobs\SendSms;
use App\Models\Bin;
use App\Models\BinToken;
use App\Models\CleanOrder;
use App\Models\CleanPrice;
use App\Models\ClientOrder;
use App\Models\ClientOrderItemTemp;
use App\Models\ClientPrice;
use App\Models\Recycler;
use App\Models\RecyclerMoneyBill;
use App\Models\User;
use App\Models\UserMoneyBill;
use App\Notifications\Clean\CleanOrderCompletedNotification;
use App\Notifications\Client\ClientOrderCompletedNotification;
use Hhxsv5\LaravelS\Swoole\Socket\TcpSocket;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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
    const CLEAN_TRANSACTION = 'yzs006';
    const PASSWORD_LOGIN = 'yzs007';

    private $actions = [
        self::CLIENT_LOGIN,
        self::CLIENT_TRANSACTION,
        self::CLIENT_LOGOUT,
        self::BEAT,
        self::QRCODE,
        self::CLEAN_TRANSACTION,
        self::PASSWORD_LOGIN,
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
                case self::CLEAN_TRANSACTION:
                    $this->cleanTransactionAction($server, $fd, $data);
                    break;
                case self::PASSWORD_LOGIN:
                    $this->passwordLoginAction($server, $fd, $data);
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
     {"static_no":"yzs007","equipment_no":"00020","account":"18600982820","login_type":"1","password":"1111"}
     {"static_no":"yzs007","equipment_no":"00020","account":"18600982820","login_type":"2","password":"123456"}
     */
    public function passwordLoginAction($server, $fd, $data)
    {
        $bin = Bin::where('no', $data['equipment_no'])->first();
        $username = $data['account'];
        $password = empty($data['password']) ? '' : $data['password'];
        $type = $data['login_type'];

        if (!$bin || !$username || !$type || !(boolean)preg_match('/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/',$username)) // 校验手机号正确性
        {
            if (!$bin)
            {
                info('$bin not find');
            }
            if (!$username)
            {
                info('$username not find');
            }
            if(!(boolean)preg_match('/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\d{8}$/',$username))
            {
                info('$username is bad phone number');
                $server->send($fd, new SocketJsonHandler([
                    'result_code' => '400', // 用户未注册/json格式字段错误
                    'message' => '手机号格式不正确',
                ]));
                return false;
            }
            if (!$type)
            {
                info('$type not find');
            }
            $server->send($fd, new SocketJsonHandler([
                'result_code' => '400' // 用户未注册/json格式字段错误
            ]));
            return false;
        }

        if ($type == '1')
        {
            // 普通用户
            $user = User::where('phone', $username)->first();

            // 无用户,创建用户,发送验证码
            if (!$user)
            {
                $user = User::create([
                    'name' => '',
                    'gender' => '男',
                    'phone' => $username,
                    'avatar' => url('defaults/user_avatar.png'),
                ]);

                // 发送验证码
                $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);// 生成4位随机数，左侧补0
                $content = "您的验证码为：{$code}，该验证码 5 分钟内有效，请勿泄漏于他人。";
                SendSms::dispatch($user->phone, (new SmsController())->verification_template_code, $content, [
                    'code' => $code
                ]);

                // 存储验证码key
                $key = 'SmsVerification_' . $user->phone;
                $expiredAt = now()->addMinutes(10); // 缓存验证码 10分钟过期。
                \Cache::put($key, ['phone' => $user->phone, 'code' => $code], $expiredAt);

                $server->send($fd, new SocketJsonHandler([
                    'static_no' => BinTcpSocket::CLIENT_LOGIN,
                    'status' => '1', //需验证,通知设备请输入短信验证码
                    'result_code' => '200',
                    'user_card' => '0',
                    'user_type' => '1', // 1:用户
                    'paper_price' => '0',
                    'cloth_price' => '0',
                    'money' => '0',
                    'paper_money' => '0',
                    'cloth _money' => '0',
                ]));
                return false;
            } // 已通过微信授权用户,登录成功
            elseif (!empty($user->wx_openid))
            {
                // 清空token
                ClearBinToken::dispatchNow($bin);

                $bin_token = new BinToken();
                $bin_token->bin_id = $bin->id;
                $bin_token->fd = $fd;
                $bin_token->related_model = null;
                $bin_token->related_id = null;
                $bin_token->auth_model = $user->getMorphClass();
                $bin_token->auth_id = $user->id;
                $bin_token->save();

                $client_prices = ClientPrice::all();

                $server->send($bin_token->fd, new SocketJsonHandler([
                    'static_no' => BinTcpSocket::CLIENT_LOGIN,
                    'status' => '0', //正常,通知设备无需验证码
                    'result_code' => '200',
                    'user_card' => (string)$user->id,
                    'user_type' => '1', // 1:用户
                    'paper_price' => bcmul($client_prices->where('slug', 'paper')->first()['price'], 100),
                    'cloth_price' => bcmul($client_prices->where('slug', 'fabric')->first()['price'], 100),
                    'money' => bcmul($user->money, 100),
                    'paper_money' => '0',
                    'cloth _money' => '0',
                ]));
                return false;
            } // 未微信授权用户,检查验证码,正确=>登录成功, 错误=>重新发送验证码
            elseif (empty($user->wx_openid))
            {
                $key = 'SmsVerification_' . $user->phone;
                $verify_data = \Cache::get($key);

                if (!empty($verify_data) && hash_equals($verify_data['code'], $password))
                {
                    // 清空token
                    ClearBinToken::dispatchNow($bin);

                    $bin_token = new BinToken();
                    $bin_token->bin_id = $bin->id;
                    $bin_token->fd = $fd;
                    $bin_token->related_model = null;
                    $bin_token->related_id = null;
                    $bin_token->auth_model = $user->getMorphClass();
                    $bin_token->auth_id = $user->id;
                    $bin_token->save();

                    $client_prices = ClientPrice::all();

                    $server->send($bin_token->fd, new SocketJsonHandler([
                        'static_no' => BinTcpSocket::CLIENT_LOGIN,
                        'status' => '0', //正常,通知设备无需验证码
                        'result_code' => '200',
                        'user_card' => (string)$user->id,
                        'user_type' => '1', // 1:用户
                        'paper_price' => bcmul($client_prices->where('slug', 'paper')->first()['price'], 100),
                        'cloth_price' => bcmul($client_prices->where('slug', 'fabric')->first()['price'], 100),
                        'money' => bcmul($user->money, 100),
                        'paper_money' => '0',
                        'cloth _money' => '0',
                    ]));
                    return false;
                } else
                {
                    // 发送验证码
                    $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);// 生成4位随机数，左侧补0
                    $content = "您的验证码为：{$code}，该验证码 5 分钟内有效，请勿泄漏于他人。";
                    SendSms::dispatch($user->phone, (new SmsController())->verification_template_code, $content, [
                        'code' => $code
                    ]);

                    // 存储验证码key
                    $key = 'SmsVerification_' . $user->phone;
                    $expiredAt = now()->addMinutes(10); // 缓存验证码 10分钟过期。
                    \Cache::put($key, ['phone' => $user->phone, 'code' => $code], $expiredAt);

                    $server->send($fd, new SocketJsonHandler([
                        'static_no' => BinTcpSocket::CLIENT_LOGIN,
                        'status' => '1', //需验证,通知设备请输入短信验证码
                        'result_code' => '200',
                        'user_card' => '0',
                        'user_type' => '1', // 1:用户
                        'paper_price' => '0',
                        'cloth_price' => '0',
                        'money' => '0',
                        'paper_money' => '0',
                        'cloth _money' => '0',
                    ]));
                    return false;
                }
            } //未知问题
            else
            {
                $server->send($fd, new SocketJsonHandler([
                    'result_code' => '400', // 用户未注册/json格式字段错误
                    'message' => '未知错误',
                ]));
                return false;
            }

        } else
        {
            // 回收员
            $recycler = Recycler::where('phone', $username)->first();
            if (!$recycler || !Hash::check($password, $recycler->password))
            {
                $server->send($fd, new SocketJsonHandler([
                    'result_code' => '400', // 用户未注册/json格式字段错误
                    'message' => '回收员手机号或密码错误',
                ]));
                return false;
            } else
            {
                // 清空token
                ClearBinToken::dispatchNow($bin);

                $bin_token = new BinToken();
                $bin_token->bin_id = $bin->id;
                $bin_token->fd = $fd;
                $bin_token->related_model = null;
                $bin_token->related_id = null;
                $bin_token->auth_model = $recycler->getMorphClass();
                $bin_token->auth_id = $recycler->id;
                $bin_token->save();

                $clean_prices = CleanPrice::all();
                $clean_paper_price = $clean_prices->where('slug', 'paper')->first()['price'];
                $clean_fabric_price = $clean_prices->where('slug', 'fabric')->first()['price'];

                /*货物的价值金额*/
                $type_paper = $bin->type_paper;
                $type_paper_money = bcmul($type_paper->number, $clean_paper_price, 2);

                $type_fabric = $bin->type_fabric;
                $type_fabric_money = bcmul($type_fabric->number, $clean_fabric_price, 2);


                $server->send($bin_token->fd, new SocketJsonHandler([
                    'static_no' => BinTcpSocket::CLIENT_LOGIN,
                    'status' => '0', //正常,通知设备无需验证码
                    'result_code' => '200',
                    'user_card' => (string)$recycler->id,
                    'user_type' => '2', // 2:回收员
                    'paper_price' => bcmul($clean_paper_price, 100),
                    'cloth_price' => bcmul($clean_fabric_price, 100),
                    'money' => bcmul($recycler->money, 100),
                    'paper_money' => bcmul($type_paper_money, 100),
                    'cloth _money' => bcmul($type_fabric_money, 100),
                ]));
                return false;
            }

        }

    }

    /*
     {"static_no":"yzs006","equipment_no":"00020","user_card":"7","admin":true,"type":"1","weight":"3000"}
     */
    public function cleanTransactionAction($server, $fd, $data)
    {
        $recycler = Recycler::find($data['user_card']);
        $bin = $recycler ? $recycler->bins->where('no', $data['equipment_no'])->first() : null;
        $clean_prices = CleanPrice::all();
        $token = $bin ? $bin->token : null;

        if(!$bin)
        {
            $server->send($fd, new SocketJsonHandler([
                'static_no' => self::CLEAN_TRANSACTION,
                'open_door' => false,
                'description' => '权限限制,请联系平台',
                'money' => bcmul($recycler['money'], 100),
                'result_code' => '200',
            ]));
            return false;
        }

        if (!$bin || !$recycler || !$token || $token->auth_id != $recycler->id || !in_array($data['type'], [1, 2]))
        {
            if (!$bin)
            {
                info('$bin not find');
            }
            if (!$recycler)
            {
                info('$recycler not find');
            }
            if (!$token)
            {
                info('$token not find');
            }
            if ($token && $recycler && $token->auth_id != $recycler->id)
            {
                info('$token->auth_id != $recycler->id');
                info($token);
            }
            $server->send($fd, new SocketJsonHandler([
                'result_code' => '400' // 用户未注册/json格式字段错误
            ]));
            return false;
        }

        switch ($data['type'])
        {
            case 1:
                $type = $bin->type_paper;
                $price = $clean_prices->where('slug', 'paper')->first();
                $permission = $bin->pivot->paper_permission;
                break;
            case 2:
                $type = $bin->type_fabric;
                $price = $clean_prices->where('slug', 'fabric')->first();
                $permission = $bin->pivot->fabric_permission;
                break;
        }

        $weight = $type->number;
        $subtotal = bcmul($price['price'], $weight, 2);

        // 没有权限开门
        if ($permission != true)
        {
            $server->send($fd, new SocketJsonHandler([
                'static_no' => self::CLEAN_TRANSACTION,
                'open_door' => false,
                'description' => '权限限制,请联系平台',
                'money' => bcmul($recycler['money'], 100),
                'result_code' => '200',
            ]));
            return false;
        }

        // 余额不足
        if (bcsub($recycler->money, $subtotal, 2) < 0)
        {
            $server->send($fd, new SocketJsonHandler([
                'static_no' => self::CLEAN_TRANSACTION,
                'open_door' => false,
                'description' => '账户余额不足',
                'money' => bcmul($recycler['money'], 100),
                'result_code' => '200',
            ]));
            return false;
        }

        // 正在维护
        if ($type->status == $type::STATUS_REPAIR)
        {
            $server->send($fd, new SocketJsonHandler([
                'static_no' => self::CLEAN_TRANSACTION,
                'open_door' => false,
                'description' => '分类箱正在维护',
                'money' => bcmul($recycler['money'], 100),
                'result_code' => '200',
            ]));
            return false;
        }


        // 创建订单
        $order = CleanOrder::create([
            'status' => CleanOrder::STATUS_COMPLETED,
            'bin_id' => $bin->id,
            'recycler_id' => $recycler->id,
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

        // 清空类型箱
        $type->update([
            'status' => $type::STATUS_NORMAL,
            'number' => 0,
        ]);

        // 更新回收员余额
        $recycler->update([
            'money' => bcsub($recycler->money, $subtotal, 2),
        ]);

        // 分配任务
        GenerateCleanOrderSnapshot::dispatch($order, $bin);
        RecyclerMoneyBill::change($recycler, RecyclerMoneyBill::TYPE_CLEAN_ORDER, $order->total, $order);
        Notification::send($recycler, new CleanOrderCompletedNotification($order));

        // 更新bin_token
        $bin->token->update([
            'related_model' => $order->getMorphClass(),
            'related_id' => $order->id,
        ]);

        $server->send($fd, new SocketJsonHandler([
            'static_no' => self::CLEAN_TRANSACTION,
            'open_door' => true,
            'description' => '开箱成功,已支付开箱金额',
            'money' => bcmul($recycler['money'], 100),
            'result_code' => '200',
        ]));

    }

    /*
     {"static_no":"yzs001","equipment_no":"00020","equipment_all":false,"user_card":"6","delivery_type":"2","delivery_weight":"200","delivery_time":"20190923140001"}
     */
    public function clientTransactionAction($server, $fd, $data)
    {
        $bin = Bin::where('no', $data['equipment_no'])->first();
        $user = User::find($data['user_card']);
        $client_prices = ClientPrice::all();
        $token = $bin->token;

        if (!$bin || !$user || !$token || $token->auth_model != User::class || $token->auth_id != $user->id || !isset($data['delivery_weight']) || !in_array($data['delivery_type'], [1, 2]))
        {
            if (!$bin)
            {
                info('$bin not find');
            }
            if (!$user)
            {
                info('$user not find');
            }
            if (!$token)
            {
                info('$token not find');
            }
            if ($token && $token->auth_model != User::class)
            {
                info('$token->auth_model != App\Models\User');
                info($token);
            }
            if ($token && $user && $token->auth_id != $user->id)
            {
                info('$token->auth_id != $user->id');
                info($token);
            }
            $server->send($fd, new SocketJsonHandler([
                'result_code' => '400' // 用户未注册/json格式字段错误
            ]));
            return false;
        }

        if ($data['delivery_weight'] < 0)
        {
            $data['delivery_weight'] = 0;
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

        // 加入临时订单缓存表
        $bin->clientOrderItemTemps()->create([
            'type_slug' => $type::SLUG,
            'type_name' => $type::NAME,
            'number' => $weight,
            'unit' => $price['unit'],
            'subtotal' => $subtotal,
        ]);


        $server->send($fd, new SocketJsonHandler([
            'static_no' => self::CLIENT_TRANSACTION,
            'delivery_price' => bcmul($price['price'], 100),
            'delivery_money' => bcmul($subtotal, 100),
            'money' => bcmul($user['money'], 100),
            'result_code' => '200',
        ]));
    }

    /*
     {"static_no":"yzs002","equipment_no":"00020","admin":false}
     {"static_no":"yzs002","equipment_no":"00020","admin":true}
     */
    public function clientLogoutAction($server, $fd, $data)
    {
        $bin = Bin::where('no', $data['equipment_no'])->first();


        if ($bin && isset($data['admin']) && $data['admin'] == true)
        {
            // 清空token
            ClearBinToken::dispatchNow($bin);
            $server->send($fd, new SocketJsonHandler([
                'static_no' => self::CLIENT_LOGOUT,
                'result_code' => '200',
            ]));
            return false;
        }

        if (!$bin || !$bin->token || $bin->token->auth_model != User::class)
        {
            if (!$bin->token)
            {
                info('$bin->token not find');
            }

            if ($bin->token && $bin->token->auth_model != User::class)
            {
                info('$bin->token->auth_model != App\Models\User');
            }
            $server->send($fd, new SocketJsonHandler([
                'result_code' => '400' // 用户未注册/json格式字段错误
            ]));
            return false;
        }
        $user = $bin->token->auth;

        // 获取临时订单缓存
        $item_temps = $bin->clientOrderItemTemps->groupBy('type_slug');

        if ($item_temps->isEmpty())
        {
            // 清空token
            ClearBinToken::dispatchNow($bin);
            $server->send($fd, new SocketJsonHandler([
                'static_no' => self::CLIENT_LOGOUT,
                'result_code' => '200',
            ]));
            return false;
        }

        $total = 0;
        $weight = 0;

        // 生成订单
        $order = ClientOrder::create([
            'status' => ClientOrder::STATUS_COMPLETED,
            'bin_id' => $bin->id,
            'user_id' => $user->id,
            'total' => 0,
            'bin_snapshot' => [],
        ]);

        $item_temps->each(function ($slug, $key) use ($bin, $order, &$total, &$weight) {

            // order_item
            $item = $order->items()->create([
                'type_slug' => $slug->first()['type_slug'],
                'type_name' => $slug->first()['type_name'],
                'number' => $slug->sum('number'),
                'unit' => $slug->first()['unit'],
                'subtotal' => $slug->sum('subtotal'),
            ]);

            $total += $item->subtotal;
            $weight += $item->number;

            // 更新类型箱重量
            switch ($key)
            {
                case 'paper':
                    $type = $bin->type_paper;
                    break;
                case 'fabric':
                    $type = $bin->type_fabric;
                    break;
            }
            $type->update([
                'number' => bcadd($type->number, $item->number, 2),
            ]);
        });

        // 更新订单总金额
        $order->update([
            'total' => $total,
        ]);


        // 更新用户信息
        $user->update([
            'money' => bcadd($user->money, $total, 2),
            'total_client_order_money' => bcadd($user->total_client_order_money, $total, 2),
            'total_client_order_count' => bcadd($user->total_client_order_count, 1),
            'total_client_order_number' => bcadd($user->total_client_order_number, $weight, 2),
        ]);

        // 分配任务
        GenerateClientOrderSnapshot::dispatchNow($order, $bin);
        UserMoneyBill::change($user, UserMoneyBill::TYPE_CLIENT_ORDER, $order->total, $order);
        $user->notify(new ClientOrderCompletedNotification($order));

        // 更新token 防止二次交易 , 并且立即删除token
        $bin->token->update([
            'related_model' => $order->getMorphClass(),
            'related_id' => $order->id,
            'auth_model' => null,
            'auth_id' => null,
        ]);
        // 清空token
         ClearBinToken::dispatchNow(Bin::find($bin->id));

        ClientOrderItemTemp::where('bin_id', $bin->id)->delete();// 清空订单缓存
        $server->send($fd, new SocketJsonHandler([
            'static_no' => self::CLIENT_LOGOUT,
            'result_code' => '200',
        ]));
        return false;
    }

    /*
    {"static_no":"yzs003","equipment_no":"00020","equipment_all":false,"device":"0000","send_time":"20190923150201"}
    {"static_no":"yzs003","equipment_no":"00020","equipment_all_paper":false,"equipment_all_cloth":false,"paper_weight":0,"cloth_weight":0,"send_time":"19700101000324"}
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
    {"static_no":"yzs004","equipment_no":"00020"}
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

        // 清空token
        ClearBinToken::dispatchNow($bin);

        $bin_token = new BinToken();
        $bin_token->bin_id = $bin->id;
        $bin_token->fd = $fd;
        $bin_token->save();

        $server->send($fd, new SocketJsonHandler([
            'static_no' => self::QRCODE,
            'result_code' => '200',
            'set_url' => 'https://www.gongyihuishou.com/client/qr?token=' . $bin_token->token,
            //            'set_url' => url('client/qr') . '?token=' . $bin_token->token
        ]));
    }
}