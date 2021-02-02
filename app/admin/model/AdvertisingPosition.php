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

class AdvertisingPosition extends ModelBase
{
    protected $pk = 'position_id';

    /**
     * 广告位列表
     * @param $data array 数组参数
     * @param $where array 查询条件
     * @param $field string 查询字段
     */
    public function getAdvertisingPositionList($data = [], $where = [], $field = '*')
    {
        $res = $this->selectAllData($where, $data['field'], $data['page'], $data['limit'], $data['sort']);
        if ($res) {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESS_CODE), 'data' => $res];
        } else {
            return ['code' => ErrorCode::NO_DATA_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::NO_DATA_CODE)];
        }
    }

    /**
     * 添加广告位
     * @param $data array 数组参数
     */
    public function addAdvertisingPosition($data = [])
    {
        //广告位信息
        $position = $this->findByAttributes(['position_name' => $data['position_name']], 'position_id');
        if ($position) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_POSITION_ALREADY_EXIST)];
        }

        $res = $this->insertGetId([
            'type' => $data['type'],
            'position_name' => $data['position_name'],
            'position_desc' => $data['position_desc'],
            'ad_width' => $data['ad_width'],
            'ad_height' => $data['ad_height'],
            'create_time' => time()
        ]);
        if ($res) {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESSFULLY_ADDED)];
        } else {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::FAIL_TO_ADD)];
        }
    }

    /**
     * 编辑广告位
     * @param $data array 参数数组
     */
    public function editAdvertisingPosition($data = [])
    {
        //广告位信息
        $position = $this->findByAttributes(['position_id' => $data['position_id']], 'position_id');
        if (!$position) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_POSITION_NOT_FIND)];
        }

        $res = $this->updateByWhere([
            'type' => $data['type'],
            'position_desc' => $data['position_desc'],
            'ad_width' => $data['ad_width'],
            'ad_height' => $data['ad_height'],
            'status' => $data['status'],
            'update_time' => time()
        ], ['position_id' => $data['position_id']]);
        if ($res) {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESSFULLY_EDIT)];
        } else {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::FAIL_TO_EDIT)];
        }
    }

    /**
     * 启用与禁用广告位置
     * @param $data array 参数数组
     */
    public function enablePosition($data = [])
    {
        //查询标识是否存在
        $position = $this->findByAttributes(['position_id' => $data['position_id']], 'position_id,position_name,status');
        if (!$position) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_POSITION_NOT_FIND)];
        }

        $position['status'] = $position['status'] == 1 ? 2 : 1;
        $res = $this->updateByWhere([
            'status' => $position['status'],
            'update_time' => time()
        ], ['position_id' => $data['position_id']]);
        if ($res) {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::OPERATE_SUCCESSFULLY), 'data' => $position];
        } else {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::OPERATION_FAILURE)];
        }
    }

    /**
     * 删除广告位置
     */
    public function delPosition($data = [])
    {
        //查询广告位置是否存在
        $position = $this->findByAttributes(['position_id' => $data['position_id']], 'position_id,status');
        if (!$position) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_POSITION_NOT_FIND)];
        }
        if (1 == $position['status']) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ENABLE_NOT_DELETE)];
        }

        //广告中是否已经使用
        $advertising = app('advertising')->findByAttributes(['position_id' => $data['position_id']], 'ad_id');
        if ($advertising) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::USE_NOT_DELETE)];
        }
        $res = $this->deleteByWhere(['position_id' => $data['position_id']]);
        if ($res) {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESSFULLY_DELETE)];
        } else {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::FAIL_TO_EDIT)];
        }
    }
}