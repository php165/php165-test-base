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
// | DateTime: 2020/9/9 13:18
// +----------------------------------------------------------------------
// | Desc: 
// +----------------------------------------------------------------------

namespace app\common\traits\sms;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use app\common\base\ErrorCode;

trait AliSmsMessage
{

    private static function construct(){
        $smsConfig = cache('configInfo');
        if(empty($smsConfig) || !isset($smsConfig[2])){
            return false;
        }else{
            if($smsConfig[2]['smsIsOpen'] == 1 && $smsConfig[2]['smsPlatform'] == 1){
                try {
                    AlibabaCloud::accessKeyClient($smsConfig[2]['smsAccount'], $smsConfig[2]['smsPassword'])->regionId('cn-hangzhou')->asDefaultClient();
                    return cache('configInfo')[2];
                } catch (ClientException $e) {
                    return false;
                }
            }else{
                return false;
            }
        }
    }
    public static function aliCloudSendSms($phone,$templatCode,$body){
        $aliSmsConfig = self::construct();
        if(!$aliSmsConfig){
            return ['code' => ErrorCode::PARAM_ERROR,'data'=>'初始化阿里云短信失败'];
        }
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => trim($phone),
                        'SignName' => $aliSmsConfig['smsSignName'],
                        'TemplateCode' => $templatCode,
                        'TemplateParam' => json_encode($body),
                    ],
                ])
                ->request()->toArray();
            if(isset($result['Code']) && isset($result['Message'])){
                return ['code' => 200,'data'=>$result];
            }else{
                return ['code' => ErrorCode::PARAM_ERROR,'data'=>$result];
            }
        } catch (ClientException $e) {
            return ['code' => ErrorCode::PARAM_ERROR,'data'=>$e->getErrorMessage()];
        } catch (ServerException $e) {
            return ['code' => ErrorCode::PARAM_ERROR,'data'=>$e->getErrorMessage()];
        }

    }
}