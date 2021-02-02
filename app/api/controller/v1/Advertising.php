<?php

namespace app\api\controller\v1;

use app\common\base\ApiController;
use app\common\base\ErrorCode;
use think\App;
use think\facade\Validate;
use app\common\traits\WeatherTrait;

class Advertising extends ApiController
{
    protected $advertising;
    //初始化
    public function __construct(App $app)
    {
        $this->advertising = app('advertising');

        parent::__construct($app);
    }

    public function read()
    {
        return $this->returnJson(8565);
    }

    /**
     * 广告播放数据上传
     * 地址 api/advertising/uploadAdvertisingPlayData
     * 请求地址 POST
     */
    public function uploadAdvertisingPlayData()
    {
        $param = $this->param;
        $rule = [
            'advertising' => 'require',
            'iot_id' => 'require',
        ];
        $msg = [
            'advertising.require' => '广告ID必传',
            'iot_id.require' => '设备ID必传',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }

        $ad_id = json_decode($param['advertising'],true);

        if(empty($ad_id) || !is_array($ad_id))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'数据错误');
        }else
        {
            $param['advertising'] = $ad_id;
        }
        $adPlayRecord = app('advertisingPlayRecord')->uploadAdvertisingPlayData($param);

        return $this->returnJson($adPlayRecord['code'], $adPlayRecord['msg']);
    }

    /**
     * 广告播放数据上传
     */
    public function lists()
    {
        echo 111;
    }

    /**
     * 广告播放数据上传
     */
    public function index()
    {
        echo 'Advertising index';
    }

    /**
     * 广告信息上传
     * 地址 api/advertising/save
     */
    public function save()
    {
        $param = $this->param;
        $rule = [
            'advertising' => 'require',
            'iot_id' => 'require',
        ];
        $msg = [
            'advertising.require' => '广告ID必传',
            'iot_id.require' => '设备ID必传',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }

        $ad_id = json_decode($param['advertising'],true);

        if(empty($ad_id) || !is_array($ad_id))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'数据错误');
        }else
        {
            $param['advertising'] = $ad_id;
        }
        $adPlayRecord = app('advertisingPlayRecord')->uploadAdsNumber($param);

        return $this->returnJson($adPlayRecord['code'], $adPlayRecord['msg']);
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