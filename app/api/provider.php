<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/8/31 0031
 * Time: 16:03
 */
use think\facade\Request;

$version = Request::header('version', 'v1');
switch ($version)
{
    case 'v1':
        return [
            //后台配置表
            'adminConfig'                          => \app\admin\model\Config::class,
            //广告表
            'advertising'                          => \app\api\model\v1\Advertising::class,
            //后台广告类型表
            'advertisingType'                      => \app\api\model\v1\AdvertisingType::class,
            //后台广告详情表
            'advertisingInfo'                      => \app\api\model\v1\AdvertisingInfo::class,
            //后台广告设备关联表
            'advertisingDeviceRelevance'           => \app\api\model\v1\AdvertisingDeviceRelevance::class,
            //后台广告播放记录表
            'advertisingPlayRecord'                => \app\api\model\v1\AdvertisingPlayRecord::class,
//            //城市表
            'city'                                 => \app\api\model\v1\City::class,
//            //公司名称
//            'company'                              => \app\api\model\v1\Company::class,
            //设备表
            'device'                               => \app\api\model\v1\Device::class,
            //设备基础配置表
            'deviceBaseConfig'                     => \app\api\model\v1\DeviceBaseConfig::class,
            //设备详情表
            'deviceInfo'                           => \app\api\model\v1\DeviceInfo::class,
            //设备识别码表
            'deviceCode'                           => \app\api\model\v1\DeviceCode::class,
            //设备配置表
            'deviceConfig'                         => \app\api\model\v1\DeviceConfig::class,
            //设备故障记录表
            'deviceFaultRecord'                    => \app\api\model\v1\DeviceFaultRecord::class,
            //机构表
//            'organ'                                => \app\api\model\v1\Organ::class,
            //所属人群表
//            'userCrowd'                            => \app\api\model\v1\UserCrowd::class,
            //所属场所表
//            'place'                                => \app\api\model\v1\Place::class,
            //管理员操作记录
//            'operateLog'                           => \app\api\model\v1\OperateLog::class,
            //版本表
            'version'                              => \app\api\model\v1\Version::class,
        ];
        break;
    case 'v2':
        return [
            //后台配置表
            'adminConfig'                          => \app\admin\model\Config::class,
            //广告表
            'advertising'                          => \app\api\model\v2\Advertising::class,
            //后台广告类型表
            'advertisingType'                      => \app\api\model\v2\AdvertisingType::class,
            //广告任务表
            'advertisingTasks'                      => \app\api\model\v2\AdvertisingTasks::class,
            //后台广告详情表
            'advertisingInfo'                      => \app\api\model\v2\AdvertisingInfo::class,
            //后台广告设备关联表
            'advertisingDeviceRelevance'           => \app\api\model\v2\AdvertisingDeviceRelevance::class,
            //后台广告播放记录表
            'advertisingPlayRecord'                => \app\api\model\v2\AdvertisingPlayRecord::class,
//            //城市表
            'city'                                 => \app\api\model\v2\City::class,
//            //公司名称
//            'company'                              => \app\api\model\v1\Company::class,
            //设备表
            'device'                               => \app\api\model\v2\Device::class,
            //设备广告表
            'deviceAdvertising'                     => \app\api\model\v2\DeviceAdvertising::class,
            //设备基础配置表
            'deviceBaseConfig'                     => \app\api\model\v2\DeviceBaseConfig::class,
            //设备详情表
            'deviceInfo'                           => \app\api\model\v2\DeviceInfo::class,
            //设备识别码表
            'deviceCode'                           => \app\api\model\v2\DeviceCode::class,
            //设备配置表
            'deviceConfig'                         => \app\api\model\v2\DeviceConfig::class,
            //设备故障记录表
            'deviceFaultRecord'                    => \app\api\model\v2\DeviceFaultRecord::class,
            //机构表
//            'organ'                                => \app\api\model\v1\Organ::class,
            //所属人群表
//            'userCrowd'                            => \app\api\model\v1\UserCrowd::class,
            //所属场所表
//            'place'                                => \app\api\model\v1\Place::class,
            //管理员操作记录
//            'operateLog'                           => \app\api\model\v1\OperateLog::class,
            //版本表
            'version'                              => \app\api\model\v2\Version::class,
        ];
        break;
    case 'v3':
        return [
            //后台配置表
            'adminConfig'                          => \app\admin\model\Config::class,
            //广告表
            'advertising'                          => \app\api\model\v3\Advertising::class,
            //后台广告类型表
            'advertisingType'                      => \app\api\model\v3\AdvertisingType::class,
            //广告任务表
            'advertisingTasks'                      => \app\api\model\v3\AdvertisingTasks::class,
            //后台广告详情表
            'advertisingInfo'                      => \app\api\model\v3\AdvertisingInfo::class,
            //后台广告设备关联表
            'advertisingDeviceRelevance'           => \app\api\model\v3\AdvertisingDeviceRelevance::class,
            //后台广告播放记录表
            'advertisingPlayRecord'                => \app\api\model\v3\AdvertisingPlayRecord::class,
//            //城市表
            'city'                                 => \app\api\model\v3\City::class,
//            //公司名称
//            'company'                              => \app\api\model\v1\Company::class,
            //设备表
            'device'                               => \app\api\model\v3\Device::class,
            //设备广告表
            'deviceAdvertising'                     => \app\api\model\v3\DeviceAdvertising::class,
            //设备基础配置表
            'deviceBaseConfig'                     => \app\api\model\v3\DeviceBaseConfig::class,
            //设备识别码批次表
            'deviceBatch'                          => \app\admin\model\DeviceBatch::class,
            //设备详情表
            'deviceInfo'                           => \app\api\model\v3\DeviceInfo::class,
            //设备识别码表
            'deviceCode'                           => \app\api\model\v3\DeviceCode::class,
            //设备配置表
            'deviceConfig'                         => \app\api\model\v3\DeviceConfig::class,
            //设备故障记录表
            'deviceFaultRecord'                    => \app\api\model\v3\DeviceFaultRecord::class,
            //机构表
//            'organ'                                => \app\api\model\v1\Organ::class,
            //所属人群表
//            'userCrowd'                            => \app\api\model\v1\UserCrowd::class,
            //所属场所表
//            'place'                                => \app\api\model\v1\Place::class,
            //管理员操作记录
//            'operateLog'                           => \app\api\model\v1\OperateLog::class,
            //版本表
            'version'                              => \app\api\model\v3\Version::class,
        ];
        break;
    case 'v4':
        return [
            //后台配置表
            'adminConfig'                          => \app\admin\model\Config::class,
            //广告表
            'advertising'                          => \app\api\model\v4\Advertising::class,
            //后台广告类型表
            'advertisingType'                      => \app\api\model\v4\AdvertisingType::class,
            //广告任务表
            'advertisingTasks'                      => \app\api\model\v4\AdvertisingTasks::class,
            //后台广告详情表
            'advertisingInfo'                      => \app\api\model\v4\AdvertisingInfo::class,
            //后台广告设备关联表
            'advertisingDeviceRelevance'           => \app\api\model\v4\AdvertisingDeviceRelevance::class,
            //后台广告播放记录表
            'advertisingPlayRecord'                => \app\api\model\v4\AdvertisingPlayRecord::class,
//            //城市表
            'city'                                 => \app\api\model\v4\City::class,
//            //公司名称
//            'company'                              => \app\api\model\v1\Company::class,
            //设备表
            'device'                               => \app\api\model\v4\Device::class,
            //设备广告表
            'deviceAdvertising'                     => \app\api\model\v4\DeviceAdvertising::class,
            //设备基础配置表
            'deviceBaseConfig'                     => \app\api\model\v4\DeviceBaseConfig::class,
            //设备详情表
            'deviceInfo'                           => \app\api\model\v4\DeviceInfo::class,
            //设备识别码表
            'deviceCode'                           => \app\api\model\v4\DeviceCode::class,
            //设备配置表
            'deviceConfig'                         => \app\api\model\v4\DeviceConfig::class,
            //设备故障记录表
            'deviceFaultRecord'                    => \app\api\model\v4\DeviceFaultRecord::class,
            //机构表
//            'organ'                                => \app\api\model\v1\Organ::class,
            //所属人群表
//            'userCrowd'                            => \app\api\model\v1\UserCrowd::class,
            //所属场所表
//            'place'                                => \app\api\model\v1\Place::class,
            //管理员操作记录
//            'operateLog'                           => \app\api\model\v1\OperateLog::class,
            //版本表
            'version'                              => \app\api\model\v4\Version::class,
        ];
        break;
}



