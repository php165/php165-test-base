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
// | DateTime: 2020/9/9 09:49
// +----------------------------------------------------------------------
// | Desc: 天气公共类
// +----------------------------------------------------------------------

namespace app\common\traits;

use app\common\base\ErrorCode;
use app\common\traits\CurlTrait;
/**
 * 天气公共类
 * Trait WeatherTrait
 * @dec:
 * @author: Myj <815081410@qq.com>
 * @package app\common\traits
 */
trait WeatherTrait
{

    private static function construct(){
        $smsConfig = cache('configInfo');
        if(empty($smsConfig) || !isset($smsConfig[2])){
            return false;
        }else{
            if($smsConfig[2]['smsIsOpen'] == 1){
                return cache('configInfo')[2];
            }else{
                return false;
            }
        }
    }

    /**
     * 实况天气（免费版）
     * @dec:该接口最快3小时更新一次，包含基本天气信息、湿度、能见度、气压、空气质量指数等，可按地名、城市编号、IP查询。
     * @author: Myj <815081410@qq.com>
     * @param $cityid   城市ID 请参考 城市ID列表
     * @param $city     城市名称	不要带市和区; 如: 青岛、铁西
     * @param $ip     IP地址	查询IP所在城市天气
     *
     * @desc  cityid、city和ip参数3选一提交，如果不传，默认返回当前ip城市天气，cityid优先级最高。
     * @return array
     */
    public static function WeatherNowForFree($cityid='',$city='',$ip=''){
        //城市名称过滤
        $city = trim($city,'市');
        $city = trim($city,'区');
        $appid = config('app.weather.appid');
        $appsecret = config('app.weather.appsecret');
        $getUrl = 'https://tianqiapi.com/api?veriosn=v61&appid='.$appid.'&appsecret='.$appsecret.'&cityid='.$cityid.'&city='.$city.'&ip='.$ip;
//        echo $getUrl.PHP_EOL;
        $result = CurlTrait::get($getUrl);
        $res = json_decode($result,true);
        return $res;
    }
}