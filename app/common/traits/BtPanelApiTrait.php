<?php
// +----------------------------------------------------------------------
// | CoolCms [ DEVELOPMENT IS SO SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://www.coolcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Alan <alanstars@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2020/10/13 17:19
// +----------------------------------------------------------------------
// | Desc: 宝塔控制面板接口
// |    接口文档：https://www.bt.cn/api-doc.pdf
// +----------------------------------------------------------------------

namespace app\common\traits;

header('Content-type: application/json');

class BtPanelApiTrait
{
    private static $btPanel = 'http://123.56.104.246:39001';
    private static $btKey = 'AB9WCMo8vCSIKFQeMKWhXFBvAQRwOyWb';
//    private function __construct(){
//        $bt_panel = 'http://123.56.104.246:39001';
//        $bt_key = 'AB9WCMo8vCSIKFQeMKWhXFBvAQRwOyWb';
//    }

    /**
     * 获取系统基础统计
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @return mixed
     */
    public static function getSystemBaseInfo(){
        $url = self::$btPanel . "/system?action=GetSystemTotal";
        $data = [];
        $result = self::httpPostCookie($url,$data);
        return json_decode($result,true);
    }


    /**
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @param $url
     * @param $data
     * @param int $timeout
     * @return mixed
     */
    private static function httpPostCookie($url, $data = [],$timeout = 60){
        //定义cookie保存位置
        $cookie_file=app()->getRuntimePath().md5(self::$btPanel).'.cookie';
        if(!file_exists($cookie_file)){
            $fp = fopen($cookie_file,'w+');
            fclose($fp);
        }
        $data['request_token'] = md5(time().''.md5(self::$btKey));
        $data['request_time'] = time();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;

    }
}