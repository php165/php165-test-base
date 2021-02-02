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
// | Use: 广告管理类
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace app\admin\controller;

use app\common\base\AdminController;
use app\common\base\ErrorCode;
use think\App;
use think\facade\Validate;

class Advertising extends AdminController
{
    protected $advertising = null;

    public function __construct(App $app)
    {
        $this->advertising = app('advertising');

        parent::__construct($app);
    }

    /**
     * 广告列表
     * 请求方式 GET
     * 请求地址 admin/advertising
     * @params $title string 广告标题
     */
    public function index()
    {
        $param = $this->param;

        $param['page'] = $param['page'] ?? 1;
        $param['limit'] = $param['limit'] ?? 10;
        $param['sort'] = $param['sort'] ?? 'sort ASC';
        $param['field'] = 'ad_id,title,ad_type,position_id,mark_id,status,sort,is_validity,create_time';
        $searchParam = $this->searchParam($param);
        $res = $this->advertising->getAdvertisingList($param, $searchParam['data']);
        if (ErrorCode::SUCCESS_CODE != $res['code']) {
            return $this->returnJson($res['code'], $res['msg']);
        } else {
            return $this->returnJson($res['code'], $res['msg'], $res['data']);
        }
    }

    /**
     * 广告搜索条件
     */
    public function searchParam($data)
    {
        $where = [];
        //广告标题
        if (isset($data['title']) && !empty($data['title'])) {
            $title = trim($data['title']);
            $where[] = ['title', 'like', "% $title %"];
        }

        return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => 'ok', 'data' => $where];
    }

    /**
     * 广告详情
     * 请求方式 POST
     * 请求地址 admin/advertising/getAdvertisingDetails
     * @params $ad_id int 广告ID
     */
    public function getAdvertisingDetails()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'ad_id' => 'require',
        ];
        $msg = [
            'ad_id' => '广告ID',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }

        $res = $this->advertising->getAdvertisingDetails($param);
        if(ErrorCode::SUCCESS_CODE == $res['code']){
            return $this->returnJson($res['code'], $res['msg'],$res['data']);
        }else{
            return $this->returnJson($res['code'], $res['msg']);
        }
    }

    /**
     * 添加广告
     * 请求方式 POST
     * 请求地址 admin/advertising/addAdvertising
     * @params $position_id int 广告位置id
     * @params $mark_id string 广告跳转标识id
     * @params $mark_content string 广告跳转标识内容
     * @params $title int 广告标题
     * @params $url int 广告url地址
     * @params $subhead int 广告副标题
     * @params $status int 状态：1启用，2禁用
     * @params $slogan int 广告语
     * @params $ad_type int 广告类型：1图片，2视频
     * @params $sort int 排序，值越小越靠前
     * @params $click_count int 点击次数
     * @params $running_time int 广告展示的更换时间
     * @params $is_validity int 是否永久展示：0永久，1按照开始和结束时间
     * @params $begin_time int 广告开始播放时间
     * @params $end_time int 广告结束播放时间
     */
    public function addAdvertising()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'position_id' => 'require',
            'mark_id' => 'require',
            'mark_content' => 'require|max:50',
            'title' => 'require|max:50',
            'url' => 'require',
            'subhead' => 'require|max:100',
            'status' => 'require',
            'slogan' => 'require|max:200',
            'ad_type' => 'require',
            'sort' => 'require',
            'click_count' => 'require',
            'running_time' => 'require',
            'is_validity' => 'require',
//            'begin_time' => 'require',
//            'end_time' => 'require',
        ];
        $msg = [
            'position_id' => '广告位ID',
            'mark_id' => '广告跳转标识ID',
            'mark_content' => '广告跳转内容',
            'mark_content.max' => '广告跳转内容不能超过50个字',
            'title' => '广告标题',
            'title.max' => '广告标题不能超过50字',
            'url' => '广告url地址',
            'subhead' => '广告副标题',
            'subhead.max' => '广告副标题不能超过100字',
            'status' => '广告状态',
            'slogan' => '广告标语',
            'slogan.max' => '广告标语不能超过200字',
            'ad_type' => '广告类型',
            'sort' => '广告排序',
            'click_count' => '广告点击次数',
            'running_time' => '广告展示时间',
            'is_validity' => '有效期',
//            'begin_time' => '广告开始时间',
//            'end_time' => '广告结束时间',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $checkParam = $this->checkAdvertisingParam($param);
        if (ErrorCode::SUCCESS_CODE != $checkParam['code']) {
            return $this->returnJson($checkParam['code'], $checkParam['msg']);
        }

        $res = $this->advertising->addAdvertising($param);

        return $this->returnJson($res['code'], $res['msg']);
    }

    /**
     * 广告部分参数验证
     * @param array $param 参数数组
     */
    public function checkAdvertisingParam($param = [])
    {
        if (!in_array($param['status'], [1, 2])) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::VALUE_OUT_OF_RANGE)];
        }
        //广告类型
        if (!in_array($param['ad_type'], [1, 2])) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::VALUE_OUT_OF_RANGE)];
        }
        //是否永久播放0是，1按照开始和结束时间
        if (!in_array($param['is_validity'], [0, 1])) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::VALUE_OUT_OF_RANGE)];
        }
        if (1 == $param['is_validity']) {
            if (!isset($param['begin_time']) || !isset($param['end_time']) || $param['begin_time'] > $param['end_time'] || $param['end_time'] < time()) {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_IS_RUNNING_AT_WRONG_TIME)];
            }
        }
        return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESS_CODE)];
    }

    /**
     * 编辑广告
     * 请求方式 POST
     * 请求地址 admin/advertising/editAdvertising
     * @params $ad_id int 广告id
     * @params $mark_content string 广告跳转标识内容
     * @params $url int 广告url地址
     * @params $subhead int 广告副标题
     * @params $status int 状态：1启用，2禁用
     * @params $slogan int 广告语
     * @params $ad_type int 广告类型：1图片，2视频
     * @params $sort int 排序，值越小越靠前
     * @params $click_count int 点击次数
     * @params $running_time int 广告展示的更换时间
     * @params $is_validity int 是否永久展示：0永久，1按照开始和结束时间
     * @params $begin_time int 广告开始播放时间
     * @params $end_time int 广告结束播放时间
     */
    public function editAdvertising()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'ad_id' => 'require',
            'mark_content' => 'require|max:50',
            'url' => 'require',
            'subhead' => 'require|max:100',
            'status' => 'require',
            'slogan' => 'require|max:200',
            'ad_type' => 'require',
            'sort' => 'require',
            'click_count' => 'require',
            'running_time' => 'require',
            'is_validity' => 'require',
//            'begin_time' => 'require',
//            'end_time' => 'require',
        ];
        $msg = [
            'ad_id' => '广告ID',
            'mark_content' => '广告跳转内容',
            'mark_content.max' => '广告跳转内容不能超过50字',
            'url' => '广告url地址',
            'subhead' => '广告副标题',
            'subhead.max' => '广告副标题不能超过100字',
            'status' => '广告状态',
            'slogan' => '广告标语',
            'slogan.max' => '广告标语不能超过200字',
            'ad_type' => '广告类型',
            'sort' => '广告排序',
            'click_count' => '广告点击次数',
            'running_time' => '广告展示时间',
            'is_validity' => '有效期',
//            'begin_time' => '广告开始时间',
//            'end_time' => '广告结束时间',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $checkParam = $this->checkAdvertisingParam($param);
        if (ErrorCode::SUCCESS_CODE != $checkParam['code']) {
            return $this->returnJson($checkParam['code'], $checkParam['msg']);
        }

        $res = $this->advertising->editAdvertising($param);

        return $this->returnJson($res['code'], $res['msg']);
    }

    /**
     * 启用禁用
     * 请求方式 POST
     * 请求地址 admin/advertising/enableAdvertising
     * @params $ad_id int 广告id
     */
    public function enableAdvertising()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'ad_id' => 'require',
        ];
        $msg = [
            'ad_id' => '广告ID',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $res = $this->advertising->enableAdvertising($param);

        if (ErrorCode::SUCCESS_CODE != $res['code']) {
            return $this->returnJson($res['code'], $res['msg']);
        } else {
            return $this->returnJson($res['code'], $res['msg'], $res['data']);
        }
    }

    /**
     * 删除广告位
     * 请求方式 DELETE
     * 请求地址 admin/advertising/delete
     * @params $ad_id int 广告位置id
     */
    public function delete()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'ad_id' => 'require',
        ];
        $msg = [
            'ad_id' => '广告ID',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $res = $this->advertising->delAdvertising($param);

        return $this->returnJson($res['code'], $res['msg']);
    }
}