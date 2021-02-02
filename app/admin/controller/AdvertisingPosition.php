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
// | Use: 广告位管理类
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace app\admin\controller;

use app\common\base\AdminController;
use app\common\base\ErrorCode;
use think\App;
use think\facade\Validate;

class AdvertisingPosition extends AdminController
{
    protected $advertisingPosition = null;

    public function __construct(App $app)
    {
        $this->advertisingPosition = app('advertisingPosition');

        parent::__construct($app);
    }

    /**
     * 广告位列表
     * 请求方式 GET
     * 请求地址 admin/advertisingPosition
     * @params $position_name string 广告位名称
     */
    public function index()
    {
        $param = $this->param;

        $param['page'] = $param['page'] ?? 1;
        $param['limit'] = $param['limit'] ?? 10;
        $param['sort'] = 'position_id DESC';
        $param['field'] = 'position_id,position_name,position_desc,ad_width,ad_height,status,create_time';
        $searchParam = $this->searchParam($param);
        $res = $this->advertisingPosition->getAdvertisingPositionList($param, $searchParam['data']);
        if (ErrorCode::SUCCESS_CODE != $res['code']) {
            return $this->returnJson($res['code'], $res['msg']);
        } else {
            return $this->returnJson($res['code'], $res['msg'], $res['data']);
        }
    }

    /**
     * 广告位搜索条件
     */
    public function searchParam($data)
    {
        $where = [];
        //广告标题
        if (isset($data['position_name']) && !empty($data['position_name'])) {
            $position_name = trim($data['position_name']);
            $where[] = ['position_name', 'like', "% $position_name %"];
        }

        return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => 'ok', 'data' => $where];
    }

    /**
     * 添加广告位
     * 请求方式 POST
     * 请求地址 admin/advertisingPosition/addAdvertisingPosition
     * @params $type int 广告位所在的客户端：1pc
     * @params $position_name string 广告位置名称
     * @params $position_desc string 广告位置描述
     * @params $ad_width int 广告位宽度
     * @params $ad_height int 广告位高度
     * @params $status int 状态：1启用，2禁用
     */
    public function addAdvertisingPosition()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'type' => 'require',
            'position_name' => 'require|max:80',
            'position_desc' => 'require|max:200',
            'ad_width' => 'require',
            'ad_height' => 'require',
            'status' => 'require',
        ];
        $msg = [
            'type' => '广告位类型',
            'position_name' => '广告位名称',
            'position_name.max' => '广告位名称不能超过80字',
            'position_desc' => '广告位描述',
            'position_desc.max' => '广告位描述不能超过200字',
            'ad_width' => '广告位宽度',
            'ad_height' => '广告位高度',
            'status' => '状态',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        if (!in_array($param['status'], [1, 2])) {
            return $this->returnJson(ErrorCode::EXCEPTION_ERROR, ErrorCode::getErrorCodeMsg(ErrorCode::ABNORMAL_STATE));
        }

        $res = $this->advertisingPosition->addAdvertisingPosition($param);

        return $this->returnJson($res['code'], $res['msg']);
    }

    /**
     * 编辑广告位
     * 请求方式 POST
     * 请求地址 admin/advertisingPosition/editAdvertisingPosition
     * @params $type int 广告位所在的客户端：1pc
     * @params $position_name string 广告位置名称
     * @params $position_desc string 广告位置描述
     * @params $ad_width int 广告位宽度
     * @params $ad_height int 广告位高度
     * @params $status int 状态：1启用，2禁用
     * @params $position_id int 广告位置id
     */
    public function editAdvertisingPosition()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'position_id' => 'require',
            'position_desc' => 'require|max:200',
            'ad_width' => 'require',
            'ad_height' => 'require',
            'status' => 'require',
        ];
        $msg = [
            'position_id' => '广告位id',
            'position_desc' => '广告位描述',
            'position_desc.max' => '广告位描述不能超过200字',
            'ad_width' => '广告位宽度',
            'ad_height' => '广告位高度',
            'status' => '状态',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        if (!in_array($param['status'], [1, 2])) {
            return $this->returnJson(ErrorCode::EXCEPTION_ERROR, ErrorCode::getErrorCodeMsg(ErrorCode::ABNORMAL_STATE));
        }

        $res = $this->advertisingPosition->editAdvertisingPosition($param);

        return $this->returnJson($res['code'], $res['msg']);
    }

    /**
     * 启用禁用
     * 请求方式 POST
     * 请求地址 admin/advertisingPosition/enablePosition
     * @params $position_id int 广告位置id
     */
    public function enablePosition()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'position_id' => 'require',
        ];
        $msg = [
            'position_id' => '广告位置ID',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $res = $this->advertisingPosition->enablePosition($param);

        if (ErrorCode::SUCCESS_CODE != $res['code']) {
            return $this->returnJson($res['code'], $res['msg']);
        } else {
            return $this->returnJson($res['code'], $res['msg'], $res['data']);
        }
    }

    /**
     * 删除广告位
     * 请求方式 DELETE
     * 请求地址 admin/advertisingPosition/delete
     * @params $position_id int 广告位置id
     */
    public function delete()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'position_id' => 'require',
        ];
        $msg = [
            'position_id' => '广告位置ID',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $res = $this->advertisingPosition->delPosition($param);

        return $this->returnJson($res['code'], $res['msg']);
    }
}