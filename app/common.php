<?php
// 应用公共文件

/**
 * 用户密码加密
 * @param string $password 加密字符串
 * @param string $salt 混淆参数
 * @return string
 */
function set_password_salt($password = '', $salt = '')
{
    if (empty($salt)) {
        return sha1(md5($password));
    } else {
        return sha1(md5($password) . $salt);
    }
}

if (!function_exists('transformSystemSlash')) {
    /**
     * 转换WIN系统中的反斜杠'\'为 /
     * @param $path
     * @return mixed
     */
    function transformSystemSlash($path)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $path = str_replace("\\", '/', $path);
        }
        return $path;
    }
}

if (!function_exists('decryptData')) {
    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     *  * <ul>
     *    <li>-41001: encodingAesKey 非法</li>
     *    <li>-41003: aes 解密失败</li>
     *    <li>-41004: 解密后得到的buffer非法</li>
     *    <li>-41005: base64加密失败</li>
     *    <li>-41016: base64解密失败</li>
     * </ul>
     */
    function decryptData($sessionKey, $appid, $encryptedData, $iv, &$data)
    {
        if (strlen($sessionKey) != 24) {
            return -41001;
        }
        $aesKey = base64_decode($sessionKey);
        if (strlen($iv) != 24) {
            return -41002;
        }
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return -41003;
        }
        if ($dataObj->watermark->appid != $appid) {
            return -41003;
        }
        $data = $result;
        return 0;
    }
    /**
     * 通过地址解析经纬度
     * address 地址信息
     */
    function getLocalToLatLng($address)
    {

        $key = 'e7386e5de8be52fd4ce3c2d283abf560';
        $url = 'https://restapi.amap.com/v3/geocode/geo?key=' . $key . '&address=' . $address;
        $res = file_get_contents($url);
        $res = json_decode($res, true);

        if ($res['status'] == '1') {
            if (!isset($res['geocodes'][0]['location'])) {
                return false;//
                exit();
            }
            $arr = explode(',', $res['geocodes'][0]['location']);
            $result['lng'] = $arr[0];//经度
            $result['lat'] = $arr[1];//纬度
            return $result;
            exit();
        } else {
            return false;
            exit();
        }
    }

    //字符串转换为二进制字符串
    function strToBinStr($str)
    {
        //1.列出每个字符
        $arr = preg_split('/(?<!^)(?!$)/u', $str);
        //2.unpack字符
        foreach ($arr as &$v) {
            $temp = unpack('H*', $v);
            $v = base_convert($temp[1], 16, 2);
            unset($temp);
        }

        return join(' ', $arr);
    }

    /**
     * 获取指定长度的随机数字组合的字符串
     *
     * @param  int $length
     * @return string
     */
    function random_all_num($length = 4)
    {
        $pool = '012356789abcdefghijklmnopqrstuvwsyzABCDEFGHJKLMNPQRSTUVWXYZ';

        return n_substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * 截取字符串
     *
     * @param  string $string
     * @param  int $start
     * @param  int|null $length
     * @return string
     */
    function n_substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /* 毫秒时间戳转换成日期 */
    function msecdate($time)
    {
        $tag='Y-m-d H:i:s';
        $a = substr($time,0,10);
//        $b = substr($time,10);
        $date = date($tag,$a);
        return $date;
    }

    /**
     * 获取最近一周，一个月，一年
     * */
    function getLatelyTime($type = '')
    {
        date_default_timezone_set('PRC');
        $now = time();
        $result = [];
        if ($type == 'week')
        {
            //最近一周
            for($i=0;$i<7;$i++){
                $result[] = date('m-d',strtotime('-'.$i.' day', $now));
            }
        } elseif ($type == 'month')
        {
            //最近一个月
            for($i=0;$i<30;$i++){
                $result[] = date('m-d',strtotime('-'.$i.' day', $now));
            }
        } elseif ($type == 'year')
        {
            //最近一年
            for ($i = 0; $i < 12; $i++) {
                $result[] = date('Y-m', strtotime('-' . $i . ' month', $now));
            }
        }

        return $result;
    }

    /**
     * 校验签名
     * @param array $param 所有入参
     *                          sign    加密签名
     *                          ts      时间戳
     * @param $key          加密key
     * @param int $time_out 超时时间
     * @return bool
     */
    function checkSign($param = [], $key, $time_out = 3600)
    {
        if (!isset($param['sign']) || !isset($param['ts'])) {
            return ['code' => 521, 'msg' => '参数异常，请重试'];
            exit();
        }
        //签名超时
        if ((time() - $param['ts']) > $time_out) {
            return ['code' => 400, 'msg' => '请求超时，请重试'];
            exit();
        }
        $getSign = $param['sign'];
        //剔除sign并进行排序
        unset($param['sign']);
        $param['key'] = $key;
        $result = formatBizQueryParaMap($param);
        if (!$result) {
            return ['code' => 400, 'msg' => '请求超时，请重试'];
        } else {
            $makeSign = sha1(strtolower($result));
            if ($getSign == $makeSign) {
                return ['code' => 200, 'msg' => '验签成功'];
            } else {
                return ['code' => 400, 'msg' => '签名错误'];
            }
        }
    }

    /**
     * 将数组转成uri字符串
     * @param $paraMap      入参数组
     * @param $urlencode    是否urlencode()
     * @return bool|string
     */
    function formatBizQueryParaMap($paraMap, $urlsafe = true)
    {
        $buff = "";
        // 对数组按照键名排序
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = "";
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
            return $reqPar;
        } else {
            return false;
        }
    }


//本月所有日期
    function get_day_num($time = '', $format='Y-m-d'){
        $time = $time != '' ? $time : time();
        //获取当前周几
        $week = date('d', $time);
        $date = [];
        for ($i=1; $i<=date('t'); $i++){
            $date[$i-1] = date($format ,strtotime( '+' . $i-$week .' days', $time));
        }
        return $date;
    }

//本周所有日期
    function get_week_num($time = '', $format='Y-m-d'){
        $time = $time != '' ? $time : time();
        //获取当前周几
        $week = date('w', $time);
        $date = [];
        for ($i=1; $i<=7; $i++){
            $date[$i-1] = date($format ,strtotime( '+' . $i-$week .' days', $time));
        }
        return $date;
    }

//本年所有月份        开始和结束
    function get_month(){
        $year = date('Y');
        $yeararr = [];
        $month = [];
        for ($i=1; $i <=12 ; $i++) {
            $yeararr[$i] = $year.'-'.$i;
        }
        foreach ($yeararr as $key => $value) {
            $timestamp = strtotime( $value );
            $start_time = date( 'Y-m', $timestamp );
            $month[] = $start_time;

        }
        return $month;
    }
}