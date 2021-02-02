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
// | Desc: 短信验证码公共类
// +----------------------------------------------------------------------

namespace app\common\traits;

use app\common\base\ErrorCode;
use app\common\traits\sms\AliSmsMessage;

/**
 * 短信公共类
 * Trait SmsMessageTrait
 * @dec:
 * @author: Alan <alanstars@qq.com>
 * @package app\common\traits
 */
trait SmsMessageTrait
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
     * 公共发送短信息接口
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @param $phone    要发送的手机号，暂时只支持单手机号发送，如有批量发送，及时告知
     * @param $body     要发送的短信内容，根据不同平台，格式不同
     *                      阿里云平台需要数组形式即可(已做json处理)
     * @param $templatCode  发送短信的模板，如阿里云，该参数必填
     * @return array
     */
    public static function sendSmsMessage($phone,$body,$templatCode){
        $configSms = self::construct();
        if(!$configSms){
            return ['code' => ErrorCode::PARAM_ERROR,'data'=>'未开启短信发送功能'];
        }
        if($configSms['smsPlatform'] == 1){
            $result = AliSmsMessage::aliCloudSendSms($phone,$templatCode,$body);
        }else{
            $result = [];
        }
        return $result;
    }
}