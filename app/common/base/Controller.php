<?php
// +----------------------------------------------------------------------
// | CoolCms [ DEVELOPMENT IS SO SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://www.coolcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Alan <251956250@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2020/8/26 16:51
// +----------------------------------------------------------------------
// | Desc: 
// +----------------------------------------------------------------------

namespace app\common\base;


use app\BaseController;
use think\App;
use think\Response;

class Controller extends BaseController
{
    protected $param = null;
    protected $header = null;

    public function __construct(App $app)
    {
        parent::__construct($app);
//        $this->param = $this->request->param();
        $this->param = $this->param();
        $this->header = $this->request->header();

        //整站配置参数，key值分别对应
        /*
            1   => '站点配置',
            2   => '短信配置',
            3   => '缓存配置',
            4   => '用户配置',
            5   => '第三方配置',
        */
        $this->configInfo = cache('configInfo');
        if (!$this->configInfo) {
            $arr = [];
            $config = app('adminConfig')->findAllByWhere([], 'config_name,config_groupid,config_value','config_id asc');
            if (!empty($config)) {
                foreach ($config as $key => $value) {
                    $arr[$value['config_groupid']][$value['config_name']] = $value['config_value'];
                }
                $this->configInfo = $arr;
                cache('configInfo', $this->configInfo);
            }
        }

        /**
         * 设备配置参数
         *
         */
        $this->configDeviceInfo = cache('configDeviceInfo');
        if(!$this->configDeviceInfo)
        {
            $res = [];
            $deviceConfig = app('deviceBaseConfig')->findAllByWhere([],'config_name,config_groupid,config_value');
            if (!empty($deviceConfig)) {
                foreach ($deviceConfig as $k => $v) {
                    $res[$v['config_groupid']][$v['config_name']] = $v['config_value'];
                }
                $this->configDeviceInfo = $res;
                cache('configDeviceInfo', $this->configDeviceInfo);
            }
        }
    }

    /**
     * 返回用户登录信息
     */
    public function getAdminLoginInfo()
    {
        $apiAuth = $this->request->header('apiAuth');
        if(!empty($apiAuth))
        {
            $adminInfo = json_decode(cache('Login:'.$apiAuth),true);
        }else
        {
            $adminInfo = null;
        }
        return $adminInfo;
    }

    /**
     * 返回JSON格式数据
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @param null $code
     * @param null $msg
     * @param null $data
     * @param array $header
     * @param string $ident
     * @return \think\response\Json
     */
    public function returnJson($code = null, $msg = null, $data = null, $header = [])
    {
        if($code == 200 && !empty($msg)){
            $data = ['code' => $code, 'msg' => $msg, 'data' => $data];
        }else{
            $msg = ErrorCode::getErrorCodeMsg($code, $msg);
            $data = ['code' => $code, 'msg' => $msg, 'data' => $data];
        }
        return json($data);
    }

    private function param()
    {
        $data = [];
        switch ($this->request->method()) {
            case 'GET':
                $data = $this->request->get();
                break;
            case 'POST':
                $data = $this->request->post();
                break;
            case 'PUT':
                $data = $this->request->put();
                break;
            case 'DELETE':
                $data = $this->request->delete();
                break;
            default:
                $data = $this->request->request();
                break;
        }
        return $data;
    }

    /**
     * 文件目录
     * @param type 1系统文件目录，2用户文件目录，3后台文件目录，4广告文件目录
     */
    public function getFileDirectoryName($type)
    {
        switch ($type)
        {
            case 1:
                $files_name = 'system';
                break;
            case 2:
                $files_name = 'user';
                break;
            case 3:
                $files_name = 'admin';
                break;
            case 4:
                $files_name = 'advertising';
                break;
            default:
                $files_name = 'default';
                break;
        }
        return $files_name;
    }

    /**
     * 上传文件类型
     */
    public function getFilesType($files_type)
    {
        switch ($files_type)
        {
            case 1:
                //图片
                $files_type = 'image';
                break;
            case 2:
                //视频
                $files_type = 'video';
                break;
            case 3:
                $files_type = 'file';
                break;
            default:
                $files_type = 'file';
                break;
        }
        return $files_type;
    }

    //菜单目录版本处理
    public function setMenuVserionCache()
    {
        $menuVersion = cache('SystemMenuVersion:','');

        $menuVersion = !empty($menuVersion) ? $menuVersion + 1 : 1;

        cache('SystemMenuVersion:',$menuVersion,0);
    }
}