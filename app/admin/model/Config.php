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
namespace app\admin\model;

use app\common\base\ErrorCode;
use app\common\base\ModelBase;

class Config extends ModelBase
{
    protected $pk = 'config_id';

    /**
     * 配置列表
     */
    public function getConfigInfo()
    {
        $setting = [
            1   => '站点配置',
            2   => '短信配置',
            3   => '缓存配置',
            4   => '用户配置',
            5   => '第三方配置',

        ];
        //所有的配置信息
        $config = $this->findAllByWhere([],'config_id,config_name,config_info,config_groupid,config_type,config_value,status,remark','sort ASC');
        if(empty($config))
        {
            return ['code'=>ErrorCode::EXCEPTION_ERROR,'msg'=>'配置信息不存在'];
        }
        $configInfo = [];
        foreach ($setting as $k=>&$v)
        {
            $configInfo[$k]['name'] = $v;
            $configInfo[$k]['config_groupid'] = $k;
            $configInfo[$k]['list'] = [];
            foreach ($config as $kc=>$vc)
            {
                if($k == $vc['config_groupid'])
                {
                    $configInfo[$k]['list'][] = $vc;
                }
            }
        }
        unset($config);
        return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'','data'=>array_values($configInfo)];
    }

    /**
     * 添加配置
     */
    public function addSetting($data)
    {
        //配置名字存在时不能添加
        $data['create_time'] = time();
        $config = $this->findByAttributes(['config_name'=>$data['config_name']],'config_id');
        if(!empty($config))
        {
            return ['code'=>ErrorCode::EXCEPTION_ERROR,'msg'=>'该配置信息已存在'];
        }
        //添加
        $res = $this->insertGetId($data);
        if(!$res)
        {
            return ['code'=>ErrorCode::ERROR_CODE,'msg'=>''];
        }else
        {
            cache('configInfo',null);
            return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'添加成功'];
        }
    }

    /**
     * 修改配置
     */
    public function editSetting($data)
    {
        //编辑
        $res = $this->saveAll($data);
        if(!$res)
        {
            return ['code'=>ErrorCode::ERROR_CODE,'msg'=>''];
        }else
        {
            cache('configInfo',null);
            return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'设置成功'];
        }
    }
}