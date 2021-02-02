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

class AdvertisingMark extends AdminController
{
    protected $advertisingMark = null;

    public function __construct(App $app)
    {
        $this->advertisingMark = app('advertisingMark');

        parent::__construct($app);
    }

    /**
     * 广告标识列表
     * 请求方式 GET
     * 请求地址 admin/advertisingMark
     * @params $mark_name string 标识名称
     */
    public function index()
    {
        $param = $this->param;
        $param['page'] = $param['page'] ?? 1;
        $param['limit'] = $param['limit'] ?? 10;
        $param['sort'] = $param['sort'] ?? 'create_time DESC';
        $param['field'] = 'mark_id,mark_name,mark,status,mark_content,create_time';
        $searchParam = $this->searchParam($param);
        $res = $this->advertisingMark->getAdvertisingMarkList($param, $searchParam['data']);
        if (ErrorCode::SUCCESS_CODE != $res['code']) {
            return $this->returnJson($res['code'], $res['msg']);
        } else {
            return $this->returnJson($res['code'], $res['msg'], $res['data']);
        }
    }

    /**
     * 广告标识搜索条件
     */
    public function searchParam($data)
    {
        $where = [];
        //广告标题
        if (isset($data['mark_name']) && !empty($data['mark_name'])) {
            $mark_name = trim($data['mark_name']);
            $where[] = ['mark_name', 'like', "% $mark_name %"];
        }

        return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => 'ok', 'data' => $where];
    }

    /**
     * 添加广告标识
     * 请求方式 POST
     * 请求地址 admin/advertisingMark/addAdvertisingMark
     * @params $mark_name string 标识名称
     * @params $mark_content string 标识内容
     * @params $status int 标识状态：1启用，2禁用
     * @params $mark string 标识英文
     */
    public function addAdvertisingMark()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'mark_name' => 'require|max:80',
            'mark' => 'require|max:60',
            'mark_content' => 'require|max:150',
            'status' => 'require',
        ];
        $msg = [
            'mark_name' => '标识名称',
            'mark_name.max' => '标识名称超过26',
            'mark_content' => '标识内容',
            'mark_content.max' => '标识内容超过50',
            'mark' => '标识英文',
            'mark.max' => '标识英文超过20',
            'status' => '状态',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        if (!in_array($param['status'], [1, 2])) {
            return $this->returnJson(ErrorCode::EXCEPTION_ERROR, ErrorCode::getErrorCodeMsg(ErrorCode::ABNORMAL_STATE));
        }

        $res = $this->advertisingMark->addAdvertisingMark($param);

        return $this->returnJson($res['code'], $res['msg']);
    }

    /**
     * 编辑广告标识
     * 请求方式 POST
     * 请求地址 admin/advertisingMark/editAdvertisingMark
     * @params $mark_id int 标识id
     * @params $mark_name string 标识名称
     * @params $mark_content string 标识内容
     * @params $status int 标识状态：1启用，2禁用
     */
    public function editAdvertisingMark()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'mark_id' => 'require',
            'mark_name' => 'require|max:80',
            'mark_content' => 'require|max:150',
            'status' => 'require',
        ];
        $msg = [
            'mark_id' => '标识ID',
            'mark_name' => '标识名称',
            'mark_name.max' => '标识名称超过26',
            'mark_content' => '标识内容',
            'mark_content.max' => '标识内容超过50',
            'status' => '状态',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $res = $this->advertisingMark->editAdvertisingMark($param);

        return $this->returnJson($res['code'], $res['msg']);
    }

    /**
     * 标识状态启用
     * 请求方式 POST
     * 请求地址 admin/advertisingMark/enableMark
     * @params $mark_id int 标识id
     */
    public function enableMark()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'mark_id' => 'require',
        ];
        $msg = [
            'mark_id' => '标识ID',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $res = $this->advertisingMark->enableMark($param);

        if (ErrorCode::SUCCESS_CODE != $res['code']) {
            return $this->returnJson($res['code'], $res['msg']);
        } else {
            return $this->returnJson($res['code'], $res['msg'], $res['data']);
        }
    }

    /**
     * 删除广告标识
     * 请求方式 DELETE
     * 请求地址 admin/advertisingMark/delete
     * @params $mark_id int 标识id
     */
    public function delete()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'mark_id' => 'require',
        ];
        $msg = [
            'mark_id' => '标识ID',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $res = $this->advertisingMark->delMark($param);

        return $this->returnJson($res['code'], $res['msg']);
    }
}