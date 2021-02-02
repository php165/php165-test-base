<?php

namespace app\admin\controller;

use app\common\base\AdminController;
use app\common\traits\BtPanelApiTrait;
use think\App;
use think\facade\Db;
use app\common\base\ErrorCode;
use think\captcha\facade\Captcha;

class Index extends AdminController
{
//    protected $middleware = ['auth','log'];

//    public function __construct(App $app)
//    {
//        parent::__construct($app);
//    }
//
//    public function verify()
//    {
////        dump(captcha_check(123));die;
////        $captcha = new Captcha();
//        return Captcha::create();
////        return $captcha->entry();
//    }

//TODO 待优化
    public function index()
    {
        $deviceManage = [
            'master' =>  [
                'onlineCount'   => 0,
                'deviceCount'   => 0,
                'offlineCount'  => 0,
                'unActiveCount' => 0
            ],
            'handle' => [
                'onlineCount'   => 0,
                'deviceCount'   => 0,
                'offlineCount'  => 0,
                'unActiveCount' => 0
            ]
        ];

//        //设备数量处理
//        $deviceInfo = [];
//
//        //最近周，月，年设备数据统计
//
//        //服务器CPU,内存处理

        //设备信息统计
        $deviceData = app('device')->findAllByWhere([],'device_id,device_type,status');
        $deviceId = [];
        if ($deviceData) {
            $deviceInfo = [];
            foreach ($deviceData as $deviceVal) {
                if (isset($deviceInfo[$deviceVal['device_type']][$deviceVal['status']])) {
                    $deviceInfo[$deviceVal['device_type']][$deviceVal['status']] = $deviceInfo[$deviceVal['device_type']][$deviceVal['status']] + 1;
                } else {
                    $deviceInfo[$deviceVal['device_type']][$deviceVal['status']] = 1;
                }

                $deviceId[] = $deviceVal['device_id'];
            }

            //数据统计处理
            foreach ($deviceInfo as $key => &$iVal) {
                if (1 === $key) {
                    if (isset($iVal[1])) {
                        $number = $iVal[1];
                        $deviceManage['handle']['onlineCount'] = $number;
                    } if (isset($iVal[2])) {
                        $number = $iVal[2];
                        $deviceManage['handle']['offlineCount'] = $number;
                    } if (isset($iVal[3])) {
                        $number = $iVal[3];
                        $deviceManage['handle']['unArtiveCount'] = $number;
                    }

                    $deviceManage['handle']['deviceCount'] = array_sum($iVal);
                } else {
                    if (isset($iVal[1])) {
                        $number = $iVal[1];
                        $deviceManage['master']['onlineCount'] = $number;
                    } if (isset($iVal[2])) {
                        $number = $iVal[2];
                        $deviceManage['master']['offlineCount'] = $number;
                    } if (isset($iVal[3])) {
                        $number = $iVal[3];
                        $deviceManage['master']['unArtiveCount'] = $number;
                    }

                    $deviceManage['master']['deviceCount'] = array_sum($iVal);
                }
            }
        }

        //版本处理
        $data['version'] = self::manageVersionInfo($deviceId);

        //时间处理
//        $week = getLatelyTime('week');
//
//        $month = getLatelyTime('month');
//        $year = getLatelyTime('year');

        $week  = get_week_num(time(),'m-d');
        $month = get_day_num(time(),'m-d');
        $year  = get_month();


        $nowYear = date('Y',time());

        $weekWhere = [['create_time','between',[strtotime($nowYear . '-' . $week[0]),strtotime($nowYear . '-' . end($week). ' 23:59:59')]]];
        $monthWhere = [['create_time','between',[strtotime($nowYear . '-' . $month[0]),strtotime($nowYear . '-' . end($month). ' 23:59:59')]]];
        $day = date('t',strtotime($year[0].'-01'));
        $yearWhere = [['create_time','between',[strtotime($year[0] . '-01'),strtotime(end($year).'-'.$day . ' 23:59:59')]]];

        //获取以往数据对比
        $prevWeekWhere = [['create_time','between',[strtotime('-1 week',strtotime($nowYear . '-' . $week[0])),strtotime('-1 week',strtotime($nowYear . '-' . end($week) . ' 23:59:59'))]]];
        $prevMonthWhere = [['create_time','between',[strtotime('-1 month',strtotime($nowYear . '-' . $month[0])),strtotime('-1 month',strtotime($nowYear . '-' . end($month) . ' 23:59:59'))]]];
        $prevYearWhere = [['create_time','between',[strtotime('-1 year',strtotime($year[0]. '-01')),strtotime('-1 year',strtotime(end($year) .'-'.$day . ' 23:59:59'))]]];
//        dump($yearWhere);die;

        $prevWeekDevice = app('device')->field('device_id')->where($prevWeekWhere)->count();
        $prevMonthDevice = app('device')->field('device_id')->where($prevMonthWhere)->count();
        $prevYearDevice = app('device')->field('device_id')->where($prevYearWhere)->count();

        $weekDevice = app('device')->findAllByWhere($weekWhere,'device_id,create_time');
        $monthDevice = app('device')->findAllByWhere($monthWhere,'device_id,create_time');
        $yearDevice = app('device')->findAllByWhere($yearWhere,'device_id,create_time');

        $wCount = count($weekDevice);
        $mCount = count($monthDevice);
        $yCount = count($yearDevice);
        $weekbfb = 0;
        if ($wCount != 0)
        {
            if ($prevWeekDevice != 0)
            {
                $weekbfb = sprintf('%.2f',round(($wCount - $prevWeekDevice) / $prevWeekDevice,4) * 100);
                if ($weekbfb < 0)
                {
                    $weekbfb = 0;
                }
            } else
            {
                $weekbfb = 100;
            }
        }

        $monthbfb = 0;
        if ($mCount != 0)
        {
            if ($prevMonthDevice != 0)
            {
                $monthbfb = sprintf('%.2f',round(($mCount - $prevMonthDevice) / $prevMonthDevice,4) * 100);
                if ($monthbfb < 0)
                {
                    $monthbfb = 0;
                }
            } else
            {
                $monthbfb = 100;
            }
        }

        $yearbfb = 0;
        if ($yCount != 0)
        {
            if ($prevYearDevice != 0)
            {
                $yearbfb = sprintf('%.2f',round(($yCount - $prevYearDevice) / $prevYearDevice,4) * 100);
                if ($yearbfb < 0)
                {
                    $yearbfb = 0;
                }
            } else
            {
                $yearbfb = 100;
            }
        }


        if ($yearDevice)
        {
            foreach ($yearDevice as &$yVal)
            {
                $yVal['create_time'] = date('Y-m',$yVal['create_time']);
            }

            if ($monthDevice)
            {
                foreach ($monthDevice as &$mVal)
                {
                    $mVal['create_time'] = date('m-d',$mVal['create_time']);
                }

                if ($weekDevice)
                {
                    foreach ($weekDevice as &$wVal)
                    {
                        $wVal['create_time'] = date('m-d',$wVal['create_time']);
                    }
                }
            }
        }

        foreach ($week as $val)
        {
            $newWeek[$val] = 0;
        }
        foreach ($month as $val)
        {
            $newMonth[$val] = 0;
        }
        foreach ($year as $val)
        {
            $newYear[$val] = 0;
        }

        $weekData = [];
        if ($weekDevice)
        {
            $weekDevice = array_merge($newWeek,array_count_values(array_column($weekDevice,'create_time')));
            foreach ($weekDevice as $key=>$wVal)
            {
                $weekData[] = ['time'=>$key,'value'=>$wVal];
            }
        } else
        {
            foreach ($newWeek as $key=>$wVal)
            {
                $weekData[] = ['time'=>$key,'value'=>$wVal];
            }
        }

        $monthData = [];
        if ($monthDevice)
        {
            $monthDevice = array_merge($newMonth,array_count_values(array_column($monthDevice,'create_time')));
            foreach ($monthDevice as $key=>$mVal)
            {
                $monthData[] = ['time'=>$key,'value'=>$mVal];
            }
        } else
        {
            foreach ($newMonth as $key=>$mVal)
            {
                $monthData[] = ['time'=>$key,'value'=>$mVal];
            }
        }

        $yearData = [];
        if($yearDevice)
        {
            $yearDevice = array_merge($newYear,array_count_values(array_column($yearDevice,'create_time')));
            foreach ($yearDevice as $key=>$yVal)
            {
                $yearData[] = ['time'=>$key,'value'=>$yVal];
            }
        } else
        {
            foreach ($newYear as $key=>$yVal)
            {
                $yearData[] = ['time'=>$key,'value'=>$yVal];
            }
        }
//            dump($weekbfb);die;
        $data['line_chart_statistics']['week'] = $weekData;
        $data['line_chart_statistics']['month'] = $monthData;
        $data['line_chart_statistics']['year'] = $yearData;
        $data['line_chart_statistics']['week_info'] = [
            'bfb' => $weekbfb,
            'number' => $wCount,
            'add_info' => '本周注册增长量',
            'bfb_info' => '相较上周新增注册量'
        ];
        $data['line_chart_statistics']['month_info'] = [
            'bfb' => $monthbfb,
            'number' => $mCount,
            'add_info' => '本月注册增长量',
            'bfb_info' => '相较上个月新增注册量'
        ];
        $data['line_chart_statistics']['year_info'] = [
            'bfb' => $yearbfb,
            'number' => $yCount,
            'add_info' => '今年注册增长量',
            'bfb_info' => '相较去年新增注册量'
        ];
//        }

        $time = '截至' . date('Y-m-d',time());
        $data['device_number'][0] = ['name'=>'当前在线设备（主机）','value'=>$deviceManage['master']['onlineCount'],'time'=>$time];
        $data['device_number'][1] = ['name'=>'当前离线设备（主机）','value'=>$deviceManage['master']['offlineCount'],'time'=>$time];
        $data['device_number'][2] = ['name'=>'当前在线设备（手持机）','value'=>$deviceManage['handle']['onlineCount'],'time'=>$time];
        $data['device_number'][3] = ['name'=>'当前离线设备（手持机）','value'=>$deviceManage['handle']['offlineCount'],'time'=>$time];

        $data['device_pie_chart'] = $deviceManage;

        //系统状态
        $systemInfo = BtPanelApiTrait::getSystemBaseInfo();
        if(!isset($systemInfo['status'])){
            $data['service_info'] = [
                'cpu' => $systemInfo['cpuRealUsed'],
                'internal' => round($systemInfo['memRealUsed']/($systemInfo['memTotal']),4)*100
            ];
        }else{
            $data['service_info'] = [
                'cpu' => 23.8,
                'internal' => 15.6
            ];
        }

        return $this->returnJson(ErrorCode::SUCCESS_CODE,'成功',$data);
    }

    //版本处理
    public static function manageVersionInfo($deviceId)
    {
        $countVersion = 0;

        //查询最新版本
        $newVersion = app('version')->field('version_name')->order('version_num desc')->find();
        if (empty($newVersion))
        {
            $newVersion['version_name'] = '其他';
        }

        //处理设备版本
        $versionData = [];
        $otherCount = 0;
        $newbfb = 0;
        if ($deviceId)
        {
            $version = app('deviceInfo')->alias('a')->join('version v','a.version_name=v.version_name')->where([['a.device_id','in',$deviceId]])->field('v.version_name,v.version_num')->order('v.version_num desc')->select()->toArray();
            if (empty($version))
            {
                $version[] = ['version_num'=>0,'version_name'=>'其他'];
            }
            $versionName = array_column($version,'version_name');

            $versionCount = array_count_values($versionName);;
            foreach ($versionCount as $key => $versionVal)
            {
                if ($key == '')
                {
                    $key = '其他';
                }

                $number = isset($versionCount[$key]) ? $versionCount[$key] : 0;
                $bfb = (round($number / array_sum($versionCount),4) * 100);

                if ($newVersion['version_name'] == $key)
                {
                    $newbfb = $bfb;

                    $countVersion += $number;
                }

                if (3 !== count($versionData))
                {
                    $versionData[] = [
                        'version_name' => $key,
                        'number' => $number,
                    ];
                } else
                {
                    $otherCount += $versionVal;
                }
            }

            $versionData[] = ['version_name'=>'其他','number'=>$otherCount];
        } else
        {
            $versionData[] = ['version_name'=>$newVersion['version_name'],'number'=>0];
        }

        return [
            'new_version'       => $newVersion['version_name'],
            'new_version_count' => $countVersion,
            'newbfb'            => $newbfb . '%',
            'otherbfb'          => (100 - $newbfb) . '%',
            'pie_chart'         => $versionData
        ];
    }


    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }

    public function read()
    {
        return $this->returnJson(8565);
//        $successMsg = [
//            0 => '请求成功',
//            1 => '添加成功',
//            2 => '更新成功',
//            3 => '删除成功',
//        ];
//        print_r(array_keys($successMsg));die;
//        if(in_array(6,array_keys($successMsg))){}
//        echo '不存在';die;
//        return 'readok' . $id;
    }

    public function save()
    {
        return 'ok_save';
    }

    public function add()
    {
        echo 'add';
        die;
    }

    public function edit()
    {
        echo 'edit';
        die;
    }

    public function del()
    {
        echo 'del';
        die;
    }

    public function changeStatus()
    {
        echo 'changeStatus';
        die;
    }
}