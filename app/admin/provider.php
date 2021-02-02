<?php
use app\ExceptionHandle;
use app\Request;

// 容器Provider定义文件

return [
    //后台管理员表
    'admin'                                => \app\admin\model\Admin::class,
    //后台菜单管理
    'adminMenu'                            =>  \app\admin\model\AdminMenu::class,
    //后台角色管理
    'adminRole'                            =>  \app\admin\model\AdminRole::class,
    //日志管理
    'adminLog'                             =>  \app\admin\model\AdminLog::class,
    //后台角色管理
    'adminRoleAccess'                      =>  \app\admin\model\AdminRoleAccess::class,
    //后台广告表
    'advertising'                          => \app\admin\model\Advertising::class,
    //后台广告位表
    'advertisingPosition'                  => \app\admin\model\AdvertisingPosition::class,
    //后台广告标识表
    'advertisingMark'                      => \app\admin\model\AdvertisingMark::class,
    //后台广告类型表
    'advertisingTasks'                      => \app\admin\model\AdvertisingTasks::class,
    //后台广告详情表
    'advertisingInfo'                      => \app\admin\model\AdvertisingInfo::class,
    //后台广告设备关联表
    'advertisingDeviceRelevance'           => \app\admin\model\AdvertisingDeviceRelevance::class,
    //后台广告播放记录表
    'advertisingPlayRecord'                => \app\admin\model\AdvertisingPlayRecord::class,
    //后台配置表
    'adminConfig'                          => \app\admin\model\Config::class,
    //城市表
    'city'                                 => \app\admin\model\City::class,
    //公司名称
    'company'                              => \app\admin\model\Company::class,
    //设备表
    'device'                               => \app\admin\model\Device::class,
    //设备基础配置表
    'deviceBaseConfig'                     => \app\admin\model\DeviceBaseConfig::class,
    //设备详情表
    'deviceInfo'                           => \app\admin\model\DeviceInfo::class,
    //设备识别码表
    'deviceCode'                           => \app\admin\model\DeviceCode::class,
    //设备识别码批次表
    'deviceBatch'                          => \app\admin\model\DeviceBatch::class,
    //设备配置表
    'deviceConfig'                         => \app\admin\model\DeviceConfig::class,
    //设备故障记录表
    'deviceFaultRecord'                    => \app\admin\model\DeviceFaultRecord::class,
    //机构表
    'organ'                                => \app\admin\model\Organ::class,
    //所属人群表
    'userCrowd'                            => \app\admin\model\UserCrowd::class,
    //所属场所表
    'place'                                => \app\admin\model\Place::class,
    //管理员操作记录
    'operateLog'                           => \app\admin\model\OperateLog::class,
    //版本表
    'version'                              => \app\admin\model\Version::class,
];
