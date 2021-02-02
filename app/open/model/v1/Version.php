<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/2 0002
 * Time: 上午 9:51
 */
namespace app\open\model\v1;

use app\common\base\ErrorCode;
use app\common\base\ModelBase;
use think\facade\Db;
use app\common\traits\AliCloudIotTrait;

class Version extends ModelBase
{
    protected $pk = 'version_id';

    /**
     * [设置设备版本信息]
     *
     * @param string $iotId [物联网设备id]
     * @param string $versionName [版本名称]
     * @return array
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    public function setDeviceVersionInfo($iotId , $versionName)
    {
        $aliCloudIotData = AliCloudIotTrait::queryDeviceDetail(['IotId'=>$iotId]);
        if ($aliCloudIotData['code'] != ErrorCode::SUCCESS_CODE)
        {
            return ['code'=>ErrorCode::EXCEPTION_ERROR,'msg'=>$aliCloudIotData['msg']];
        }

        $deviceData = app('device')->findByAttributes(['iot_id'=>$iotId],'device_id,status,iot_id');
        if (empty($deviceData))
        {
            return ['code'=>ErrorCode::EXCEPTION_ERROR,'msg'=>'无效的设备信息'];
        }

        Db::startTrans();
        try
        {
            $deviceInfo = app('deviceInfo')->findByAttributes(['device_id'=>$deviceData['device_id']],'device_id');
            if (empty($deviceInfo))
            {
                $result = app('deviceInfo')->insert(
                    ['device_id'=>$deviceData['device_id'],'version_name'=>$versionName]
                );
                if ($result)
                {
                    Db::commit();
                    return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'SUCCESS'];
                }
            } else
            {
                //更新设备版本信息
                $result = app('deviceInfo')->updateByWhere(['version_name'=>$versionName],
                    ['device_id'=>$deviceData['device_id']]
                );
                if ($result)
                {
                    Db::commit();
                    return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'SUCCESS'];
                }
            }
        } catch (\Exception $e)
        {
            Db::rollback();
            return ['code'=>ErrorCode::EXCEPTION_ERROR,'msg'=>'记录失败'];
        }
    }
}