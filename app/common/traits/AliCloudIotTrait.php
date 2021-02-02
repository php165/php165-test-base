<?php
/**
 * Created by PhpStorm.
 * User: Yr
 * Date: 2020/8/10 0010
 * Time: 下午 8:56
 */

namespace app\common\traits;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;


define("PRODUCT", "Iot");
define("VERSION", "2018-01-20");
define("METHOD", "POST");
/**
 * 以下示例以华东2地域及其服务接入地址为例。您在设置时，需使用您的物联网平台地域和对应的服务接入地址。
 */
define("REGION_ID", "cn-beijing");
define("HOST", "iot." . REGION_ID . ".aliyuncs.com");

// 设置一个全局客户端
try {
    AlibabaCloud::accessKeyClient(config('app.aliyun')['iot']['accessKeyId'], config('app.aliyun')['iot']['accessKeySecret'])
        ->regionId(REGION_ID)// replace regionId as you need
        ->asDefaultClient();
} catch (ClientException $e) {
    echo $e->getErrorMessage() . PHP_EOL;
}

trait AliCloudIotTrait
{

    /**
     * 注册设备
     * @param $productKey
     * @return string
     */
    public static function registerDevice($param)
    {
        try {
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('RegisterDevice')
                ->options([
                    'query' => [
                        'IotInstanceId' => config('app.aliyun')['iot']['IotInstanceId'],
                        'RegionId' => REGION_ID,
                        'ProductKey' => $param['ProductKey'],
                        'DeviceName' => $param['DeviceName'],
                    ],
                ])
                ->request();
            $result2Array = $result->toArray();
            if (empty($result2Array)) {
                return ['code' => 400, 'msg' => '失败', 'data' => null];
            }
            if ($result2Array['Success']) {
                return ['code' => 200, 'msg' => '成功', 'data' => $result2Array['Data']];
            }
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
        return ['code' => 400, 'msg' => '注册设备失败', 'data' => null];
    }

    /**
     * 删除设备
     * @param $device
     */
    public static function deleteDeviceTest($device)
    {
        try {
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('DeleteDevice')
                ->options([
                    'query' => [
                        'RegionId' => REGION_ID,
                        'ProductKey' => $device['ProductKey'],
                        'DeviceName' => $device['DeviceName'],
//                        'IotId' => $device['IotId'], //如果传入该参数，则无需传入 ProductKey和 DeviceName。如果您同时传入 IotId和 ProductKey与 DeviceName组合，则以 IotId为准。
                        'IotInstanceId' => config('app.aliyun')['iot']['IotInstanceId'],
                    ],
                ])
                ->request();
            $result2Array = $result->toArray();
            if (!empty($result2Array) && $result2Array['Success']) {
                return ['code' => 200, 'msg' => '成功', 'data' => $result2Array];
            }
            return ['code' => 400, 'msg' => '失败', 'data' => null];
            if(empty($result2Array));
        } catch (ClientException $e) {
            return ['code' => 200, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 200, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }

    /**
     * 查询设备详情
     *
     * @param $device
     */
    public static function queryDeviceDetail($device)
    {
        try {
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('QueryDeviceDetail')
                ->options([
                    'query' => [
                        'RegionId' => REGION_ID,
                        'IotId' => $device['IotId'], //如果传入该参数，则无需传入 ProductKey和 DeviceName。如果您同时传入 IotId和 ProductKey与 DeviceName组合，则以 IotId为准。
                        'IotInstanceId' => config('app.aliyun')['iot']['IotInstanceId'],
//                        'ProductKey' => $device['ProductKey'],
//                        'DeviceName' => $device['DeviceName'],
                    ],
                ])
                ->request();
            $result2Array = $result->toArray();
            if (!empty($result2Array) && $result2Array['Success']) {
                return ['code' => 200, 'msg' => '成功', 'data' => $result2Array];
            }
            return ['code' => 400, 'msg' => '失败', 'data' => null];
        } catch (ClientException $e) {
            return ['code' => 200, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 200, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }

    /**
     * 查询设备列表
     * @param $productKey
     * @return array|null
     */
    public static function queryDevice($param)
    {
        try {
            $result = AlibabaCloud::rpc()
                ->product('Iot')
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('QueryDevice')
                ->options([
                    'query' => [
                        'RegionId' => REGION_ID,
                        'ProductKey' => $param['ProductKey'],
                        'PageSize' => $param['PageSize'],
                        'CurrentPage' => $param['CurrentPage'],
                        'IotInstanceId' => config('app.aliyun')['iot']['IotInstanceId'],
                    ],
                ])
                ->request();
            $result2Array = $result->toArray();
            if ($result2Array['Success']) {
                if ($result2Array['Total']) {
                    return ['code' => 200, 'msg' => '成功', 'data' => $result2Array];
                }
                return ['code' => 4001, 'msg' => '暂无数据', 'data' => null];
            }
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
        return ['code' => 4001, 'msg' => '暂无数据', 'data' => null];
    }

    /**
     * 查询设备运行状态
     *
     * @param $device
     */
    public static function getDeviceStatusTest($device)
    {
        try {
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('GetDeviceStatus')
                ->options([
                    'query' => [
                        'RegionId' => REGION_ID,
                        'IotId' => $device['IotId'], //如果传入该参数，则无需传入 ProductKey和 DeviceName。如果您同时传入 IotId和 ProductKey与 DeviceName组合，则以 IotId为准。
                        'IotInstanceId' => config('app.aliyun')['iot']['IotInstanceId'],
//                    'ProductKey' => $device['ProductKey'],
//                    'DeviceName' => $device['DeviceName'],
                    ],
                ])
                ->request();
            $result2Array = $result->toArray();
            if (!empty($result2Array) && $result2Array['Success']) {
                return ['code' => 200, 'msg' => '成功', 'data' => $result2Array];
            }
            return ['code' => 400, 'msg' => '失败', 'data' => null];
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }

    /**
     * 通知网关设备增加拓扑关系
     *
     * 返回的成功结果只表示添加拓扑关系的指令成功下发给网关，但并不表示网关成功添加拓扑关系。
     * 开发网关设备端时，需订阅通知添加拓扑关系消息的Topic。
     * @param $gwDevice
     * @param $deviceList
     */
    public static function notifyAddThingTopo($gwDevice, $deviceList)
    {
        try {
            $deviceArray = [];
            for ($i = 0; $i < count($deviceList); $i++) {
                $deviceInfo = [];
                $deviceInfo['IotId'] = $deviceList[$i]['IotId'];
//                $deviceInfo['productKey'] = $deviceList[$i]['ProductKey'];
//                $deviceInfo['deviceName'] = $deviceList[$i]['DeviceName'];
                array_push($deviceArray, $deviceInfo);
            }
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $query['RegionId'] = REGION_ID;
            $query['GwIotId'] = $gwDevice['IotId'];
//            $query['GwProductKey'] = $gwDevice['ProductKey'];
//            $query['GwDeviceName'] = $gwDevice['DeviceName'];
            $query['DeviceListStr'] = json_encode($deviceArray);
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//            ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('NotifyAddThingTopo')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
            print_r($result2Array);
            die;
//            echo "通知网关设备增加拓扑关系:" . PHP_EOL;
            if ($result2Array['Success']) {
                return ['code' => 200, 'msg' => '成功', 'data' => $result2Array['Data']];
            }
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
        return ['code' => 400, 'msg' => '失败', 'data' => null];
    }


    /**
     * 查询指定设备的拓扑关系
     * @param $device
     */
    public static function getThingTopoTest($device)
    {
        try {
            $query['RegionId'] = REGION_ID;
            $query['IotId'] = $device['IotId'];
//            $query['ProductKey'] = $device['ProductKey'];
//            $query['DeviceName'] = $device['DeviceName'];
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $query['PageSize'] = 10;
            $query['PageNo'] = 1;
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//            ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('GetThingTopo')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
            echo "查询指定设备的拓扑关系:" . PHP_EOL;
            print_r($result2Array);
            if (!$result->toArray()['Success'] == true) {
                echo '查询指定设备的拓扑关系失败' . PHP_EOL;
            }
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }


    /**
     * 查询指定设备的事件记录
     * @param $device
     */
    public static function queryDeviceEventDataTest($device)
    {
        try {
            $query = [];
            $query['RegionId'] = REGION_ID;
            $query['ProductKey'] = $device['ProductKey'];
            $query['DeviceName'] = $device['DeviceName'];
            $query['EventType'] = isset($device['EventType']) ? $device['EventType'] : ''; //可选参数
            $query['Identifier'] = isset($device['Identifier']) ? $device['Identifier'] : ''; //可选参数
            $query['StartTime'] = (isset($device['StartTime']) ? $device['StartTime'] : (strtotime(date('Y-m-d', time())) - 30 * 24 * 60 * 60) * 1000);
            $query['EndTime'] = (isset($device['EndTime']) ? $device['EndTime'] : strtotime(date('Y-m-d', time())) * 1000);
            $query['PageSize'] = isset($device['PageSize']) ? $device['PageSize'] : 10;
            $query['Asc'] = isset($device['Asc']) ? $device['Asc'] : 1;
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('QueryDeviceEventData')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
            if (!$result2Array['Success']) {
                return ['code' => 400, 'msg' => '失败', 'data' => null];
            }
            return ['code' => 200, 'msg' => '成功', 'data' => $result2Array['Data']];
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }

    /**
     * 查询指定设备的属性记录
     * @param $device
     */
    public static function queryDevicePropertyDataTest($device)
    {
        try {
            $query = [];
            $query['RegionId'] = REGION_ID;
            $query['IotId'] = $device['IotId'];
            $query['Identifier'] = $device['Identifier'];
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $query['StartTime'] = (isset($device['StartTime']) ? $device['StartTime'] : strtotime(date('Y-m-d', time())) * 1000);
            $query['EndTime'] = (isset($device['EndTime']) ? $device['EndTime'] : (strtotime(date('Y-m-d', time())) + 24 * 60 * 60) * 1000);
            $query['PageSize'] = isset($device['PageSize']) ? $device['PageSize'] : 10;
            $query['Asc'] =isset($device['Asc']) ? $device['Asc'] : 1;

            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('QueryDevicePropertyData')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
            if (empty($result2Array)) {
                return ['code' => 400, 'msg' => '失败', 'data' => null];
            }
            if (!$result2Array['Success']) {
                return ['code' => 400, 'msg' => '失败', 'data' => null];
            }
            return ['code' => 200, 'msg' => '成功', 'data' => $result2Array['PropertyDataInfos']['PropertyDataInfo']];
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }

    /**
     * 批量查询指定设备的属性记录
     *
     * 注意事项：
     * Identifier  string 要查的设备属性 字符串拼接到一起
     * 0：倒序。倒序查询时，endTime参数必须小于startTime
     * 1：正序。正序查询时，StartTime必须小于EndTime。
     * @param $device
     */
    public static function queryDevicePropertiesDataTest($device)
    {
        try {
            $query = [];
            $query['RegionId'] = REGION_ID;
            $query['ProductKey'] = $device['ProductKey'];
            $query['DeviceName'] = $device['DeviceName'];
            $arr = explode(',', $device['Identifier']);
            foreach ($arr as $k => &$v) {
                $query['Identifier.' . ($k + 1)] = $v;
            }
            $query['StartTime'] = (isset($device['StartTime']) ? $device['StartTime'] : strtotime(date('Y-m-d', time())) * 1000);
            $query['EndTime'] = (isset($device['EndTime']) ? $device['EndTime'] : (strtotime(date('Y-m-d', time())) + 24 * 60 * 60) * 1000);
            $query['PageSize'] = isset($device['PageSize']) ? $device['PageSize'] : 10;
            $query['Asc'] = isset($device['Asc']) ? $device['Asc'] : 1;
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('QueryDevicePropertiesData')
                ->options([
                    'query' => $query,
                ])
                ->request();
//            print_r($query);
//            print_r($device);die;

            $result2Array = $result->toArray();
//            print_r($result2Array);die;
            if (empty($result2Array)) {
                return ['code' => 400, 'msg' => '失败', 'data' => null];
            }
            if (!$result2Array['Success']) {
                return ['code' => 400, 'msg' => '失败', 'data' => null];
            }
            return ['code' => 200, 'msg' => '成功', 'data' => $result2Array];
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }

    /**
     * 查询指定设备的服务记录
     * @param $device
     */
    public static function queryDeviceServiceDataTest($device)
    {
        try {
            $query = [];
            $query['RegionId'] = REGION_ID;
            $query['ProductKey'] = $device['ProductKey'];
            $query['DeviceName'] = $device['DeviceName'];
            $query['Identifier'] = $device['Identifier'];
            $query['StartTime'] = ((time() - 7 * 24 * 60 * 60) * 1000);
            $query['EndTime'] = (time() * 1000);
            $query['PageSize'] = 10;
            $query['Asc'] = 0;

            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('QueryDeviceServiceData')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
            if (!$result2Array['Success']) {
                echo "查询指定设备的服务记录失败" . PHP_EOL;
            }
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }


    /**
     * 为指定设备设置属性值(必须是在线设备)
     * @param $device
     */
    public static function setDevicePropertyTest($device)
    {
        try {
            $query = [];
            $query['RegionId'] = REGION_ID;
            $query['IotId'] = $device['IotId'];
//            $query['ProductKey'] = $device['ProductKey'];
//            $query['DeviceName'] = $device['DeviceName'];
            $query['Items'] = json_encode($device['Items']);//只有读写类型属性可以设置成功
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('SetDeviceProperty')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
//            echo "为指定设备设置属性值:" . PHP_EOL;
            if (!$result2Array['Success']) {
                return ['code' => 400, 'msg' => '为指定设备设置属性值失败', 'data' => null];
            }
            return ['code' => 200, 'msg' => '成功', 'data' => $result2Array['Data']['MessageId']];
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }

    /**
     *
     * 批量设置设备属性值。
     * @param $ProductKey string 产品key
     * @param $DeviceName string 字符串拼接 'device_001,device_002'
     * @param $Items array 要修改的属性 ['co2'=>1]
     * @param $deviceNameList
     */
    public static function setDevicesPropertyTest($device)
    {
        try {
            $query = [];
            $query['RegionId'] = REGION_ID;
            $query['ProductKey'] = $device['ProductKey'];
            $arr = explode(',', $device['DeviceName']);
            foreach ($arr as $k => &$v) {
                $query['DeviceName.' . ($k + 1)] = $v;
            }
            if (!is_array($device['Items']) || empty($device['Items'])) {
                return ['code' => 400, 'msg' => '属性格式有误', 'data' => null];
            }
            $query['Items'] = json_encode($device['Items']);//只有读写类型属性可以设置成功
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('SetDevicesProperty')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
//            echo "批量设置设备属性值:" . PHP_EOL;
//            print_r($result2Array);die;
            if (!empty($result2Array) && $result2Array['Success']) {
                return ['code' => 200, 'msg' => '设置成功', 'data' => null];
            }
            return ['code' => 400, 'msg' => '属性格式有误', 'data' => null];
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }


    /**
     * 查询设备统计数据
     * @param $productKey
     */
    public static function queryDeviceStatisticsTest($productKey)
    {
        try {

            $query['RegionId'] = REGION_ID;
//        $query['GwIotId'] = $gwDevice['IotId'];
            $query['ProductKey'] = $productKey;
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//            ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('QueryDeviceStatistics')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
            if (!$result->toArray()['Success'] == true) {
                return ['code' => 400, 'msg' => '查询设备统计数据失败', 'data' => null];
            }
            return ['code' => 200, 'msg' => '成功', 'data' => $result2Array['Data']];
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }

    /**
     * 向订阅了指定Topic的所有设备发布广播消息
     * @param $productKey
     */
    public static function pubBroadcastTest($device)
    {
        try {
            $query['RegionId'] = REGION_ID;
            $query['ProductKey'] = $device['ProductKey'];
            $query['TopicFullName'] = isset($device['TopicFullName']) ? $device['TopicFullName'] : '';
            $query['MessageContent'] = base64_encode(strToBinStr($device['MessageContent']));
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $query['Qos'] = 1;
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('PubBroadcast')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
            if ($result->toArray()['Success'] == true) {
                return ['code' => 200, 'msg' => '成功', 'data' => $result2Array['MessageId']];
            }

        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
        return ['code' => 400, 'msg' => '广播失败', 'data' => null];
    }

    /**
     * @param $ProductKey string 产品key
     * @param $DeviceName string 设备名称
     * @param $RequestBase64Byte mixed 数组或字符串
     * @param $Timeout integer  响应时间 1000~8000  单位毫秒
     * 向指定设备发送请求消息，并同步返回响应
     *
     */
    public static function rrpcTest($device)
    {
        try {
            $query['RegionId'] = REGION_ID;
            $query['ProductKey'] = $device['ProductKey'];
            $query['DeviceName'] = $device['DeviceName'];
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            if (!is_array($device['RequestBase64Byte'])) {
                $query['RequestBase64Byte'] = base64_encode($device['RequestBase64Byte']);
            } else {
                $query['RequestBase64Byte'] = base64_encode(json_encode($device['RequestBase64Byte']));
            }
            $device['Timeout'] = isset($device['Timeout']) ? $device['Timeout'] : 1000;

            if ($device['Timeout'] < 1000 || $device['Timeout'] > 8000) {

                return ['code' => 400, 'msg' => '等待设备回复消息的时间，单位是毫秒，取值范围是1,000 ~8,000', 'data' => null];
            }
            $query['Topic'] = isset($device['Topic']) ? $device['Topic'] : '';//不传入此参数，则使用系统默认的RRPC Topic。
            $query['Timeout'] = $device['Timeout'];//不传入此参数，则使用系统默认的RRPC Topic。
//            print_r($query);die;
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('RRpc')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
            if (!empty($result2Array) && $result2Array['Success']) {
                return ['code' => 200, 'msg' => '设置成功', 'data' => $result2Array];
            } else {
                return ['code' => 400, 'msg' => $result2Array['ErrorMessage'], 'data' => null];
            }
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
    }

    /**
     * @param $ProductKey string 产品key
     * @param $DeviceName string 设备名称
     * @param $MessageContent string 消息内蓉
     * 向指定Topic发布消息
     * @param $productKey
     */
    public static function pubTest($device)
    {
        try {
            $query['RegionId'] = REGION_ID;
            $query['ProductKey'] = $device['ProductKey'];
            $query['TopicFullName'] = $device['TopicFullName'];
            if (!is_array($device['MessageContent'])) {
                $query['MessageContent'] = base64_encode(strToBinStr($device['MessageContent']));
            } else {
                $query['MessageContent'] = base64_encode(strToBinStr(json_encode($device['MessageContent'])));
            }
            $query['IotInstanceId'] = config('app.aliyun')['iot']['IotInstanceId'];
            $query['Qos'] = 1;
            $result = AlibabaCloud::rpc()
                ->product('Iot')
//        ->scheme('https') // https | http
                ->method('POST')
                ->version('2018-01-20')
                ->host(HOST)
                ->action('Pub')
                ->options([
                    'query' => $query,
                ])
                ->request();
            $result2Array = $result->toArray();
            if ($result2Array['Success']) {
                return ['code' => 200, 'msg' => '设置成功', 'data' => $result2Array];
            }
        } catch (ClientException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        } catch (ServerException $e) {
            return ['code' => 400, 'msg' => $e->getErrorMessage(), 'data' => null];
        }
        return ['code' => 400, 'msg' => '失败', 'data' => null];
    }
}