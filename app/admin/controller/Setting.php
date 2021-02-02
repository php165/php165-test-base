<?php
// +----------------------------------------------------------------------
// | CoolCms [ DEVELOPMENT IS SO SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.coolcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Myj <815081410@qq.com>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace app\admin\controller;


use app\common\base\ErrorCode;
use think\facade\Validate;
use app\common\base\AdminController;
use think\app;


class Setting extends AdminController
{
    //引入中间件
//    protected $middleware = ['auth'];
    protected $setting;
    protected $middleware = ['auth','log'];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->setting = app('adminConfig');
    }

    /**
     * 系统配置类型列表
     * 请求方式 GET
     * 接口地址 admin/setting
     */
    public function index()
    {
        $setting = $this->setting->getConfigInfo();
        if(ErrorCode::SUCCESS_CODE == $setting['code'])
        {
            return $this->returnJson($setting['code'],$setting['msg'],$setting['data']);
        }else
        {
            return $this->returnJson($setting['code'],$setting['msg']);
        }
    }

    /**
     * 系统配置类型列表
     */
//    public function settingList()
//    {
//        $setting = [
//            ['setting_type'=>1,'setting_name'=>'站点配置'],
//            ['setting_type'=>2,'setting_name'=>'短信配置'],
//            ['setting_type'=>3,'setting_name'=>'缓存配置'],
//            ['setting_type'=>4,'setting_name'=>'用户配置'],
//            ['setting_type'=>5,'setting_name'=>'第三方配置'],
//
//        ];
//        return $this->returnJson(ErrorCode::SUCCESS_CODE,'',$setting);
//    }

    /**
     * 配置类型下的配置信息列表
     */
//    public function settingDetailsList()
//    {
//        $param = $this->param;
//        if(!isset($param['config_groupid']) || !in_array($param['config_groupid'],[1,2,3,4,5]))
//        {
//            return $this->returnJson(ErrorCode::PARAM_ERROR,'配置类型错误');
//        }
//        //所有的配置信息
//        $config = app('adminConfig')->findAllByWhere(['config_groupid'=>$param['config_groupid']],'config_id,config_name,config_info,config_groupid,config_type,config_value,status,remark');
//        if(empty($config))
//        {
//            return $this->returnJson(ErrorCode::NO_DATA_CODE);
//        }else
//        {
//            return $this->returnJson(ErrorCode::SUCCESS_CODE,'',$config);
//        }
//    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        echo 'Setting create';
    }

    /**
     * 添加配置
     * 接口地址 admin/setting/save
     * 请求方式 POST
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        $param = $this->param;
            //校验参数
        $rule = [
            'config_name'      => 'require',
            'config_info'      => 'require',
            'config_groupid'   => 'require',
            'config_type'      => 'require',
            'config_value'     => 'require',
            'status'           => 'require',
        ];
        $msg = [
            'config_name'      => '配置名称',
            'config_info'      => '配置说明',
            'config_groupid'   => '分组id',
            'config_type'      => '配置类型',
            'status'           => '配置状态',
        ];
        $validate = Validate::rule($rule,$msg);
        if (!$validate->check($param))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,$validate->getError());
        }
        $add = $this->setting->addSetting($param);
        return $this->returnJson($add['code'],$add['msg']);
    }

    /**
     * 分类下的具体的配置列表
     * 请求方式 GET
     * 地址 admin/setting/read?config_groupid=1
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $param = $this->param;
        if(!isset($param['config_groupid']))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'配置类型错误');
        }
        //所有的配置信息
        $config = app('adminConfig')->findAllByWhere(['config_groupid'=>$param['config_groupid']],'config_id,config_name,config_info,config_groupid,config_type,config_value,status,remark');
        if(empty($config))
        {
            return $this->returnJson(ErrorCode::NO_DATA_CODE);
        }else
        {
            return $this->returnJson(ErrorCode::SUCCESS_CODE,'',$config);
        }
    }

    /**
     * 显示编辑资源表单页.
     * 请求方式
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        echo 'Setting edit';
    }

    /**
     * 编辑配置信息(只可以编辑值)
     * 请求方式 POST
     * 接口地址 admin/setting/update
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update()
    {
        $param = $this->param;
        //校验参数
        if(!isset($param['config']) || empty($param['config']))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'配置信息必传');
        }
        $config = json_decode($param['config'],true);
        if(empty($config))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'配置信息错误');
        }

        foreach ($config as $k=>$v)
        {
            if(!isset($v['config_id']) || !isset($v['config_info']) || !isset($v['config_value']) || !isset($v['remark']))
            {
                return $this->returnJson(ErrorCode::PARAM_ERROR,'参数错误');
            }
        }
        $add = $this->setting->editSetting($config);
        return $this->returnJson($add['code'],$add['msg']);
    }

    /**
     * 删除配置信息，请求方式delete
     * 请求方式 POST
     * 接口地址 admin/setting/delete
     * @param
     * @return \think\Response
     */
    public function delete()
    {
        $param = $this->param;
        if(!isset($param['config_id']))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'配置ID必传');
        }
        $res = $this->setting->deleteByWhere(['config_id'=>$param['config_id']]);
        if(!$res)
        {
            return $this->returnJson(ErrorCode::ERROR_CODE);
        }else
        {
            //删除配置缓存
            cache('configInfo',null);
            return $this->returnJson(ErrorCode::SUCCESS_CODE);
        }
    }
}