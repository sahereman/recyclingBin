<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}


function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 * 计算起始坐标 至 到达坐标 的距离 (距离单位: 米)
 * @param Coordinate $start_coor
 * @param Coordinate $end_coor
 * @return int
 */
function calcDistance(\App\Handlers\Tools\Coordinate $start_coor, \App\Handlers\Tools\Coordinate $end_coor)
{
    $dLatitudeRadian = $end_coor->latitudeRadian - $start_coor->latitudeRadian;
    $dLongitudeRadian = $end_coor->longitudeRadian - $start_coor->longitudeRadian;
    if ($dLatitudeRadian == 0)
    {
        return 0;
    }

    //google maps里面实现的算法
    $distance = 2 * asin(sqrt(pow(sin($dLatitudeRadian / 2), 2) + cos($start_coor->latitudeRadian) *
            cos($end_coor->latitudeRadian) * pow(sin($dLongitudeRadian / 2), 2))); //google maps里面实现的算法
    $distance = $distance * \App\Handlers\Tools\Coordinate::EARTH_RADIUS;

    return intval($distance);
}