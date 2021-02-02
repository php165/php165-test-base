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

class AdvertisingMark extends ModelBase
{
    protected $pk = 'mark_id';

    /**
     * 广告标识列表
     * @param $data array 数组参数
     * @param $where array 查询条件
     */
    public function getAdvertisingMarkList($data = [], $where = [], $field = '*')
    {
        $res = $this->selectAllData($where, $data['field'], $data['page'], $data['limit'], $data['sort']);
        if ($res) {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESS_CODE), 'data' => $res];
        } else {
            return ['code' => ErrorCode::NO_DATA_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::NO_DATA_CODE)];
        }
    }

    /**
     * 添加标识
     * @param $data array 标识参数
     */
    public function addAdvertisingMark($data = [])
    {
        //查询标识是否存在
        $mark = $this->findByAttributes(['mark' => $data['mark']], 'mark_id');
        if ($mark) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_MARK_ALREADY_EXIST)];
        }
        $res = $this->insertGetId([
            'mark_name' => $data['mark_name'],
            'mark_content' => $data['mark_content'],
            'mark' => $data['mark'],
            'status' => $data['status'],
            'create_time' => time()
        ]);

        if (!$res) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::FAIL_TO_EDIT)];
        } else {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESSFULLY_ADDED)];
        }
    }

    /**
     * 添加标识
     * @param $data array 标识参数
     */
    public function editAdvertisingMark($data = [])
    {
        //查询标识是否存在
        $mark = $this->findByAttributes(['mark_id' => $data['mark_id']], 'mark_id,mark');
        if (!$mark) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_MARK_NOT_FIND)];
        }

        $res = $this->updateByWhere([
            'mark_name' => $data['mark_name'],
            'mark_content' => $data['mark_content'],
            'status' => $data['status'],
            'update_time' => time()
        ], ['mark_id' => $data['mark_id']]);

        if (!$res) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::FAIL_TO_EDIT)];
        } else {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESSFULLY_EDIT)];
        }
    }

    /**
     * 启用禁用状态
     * @param $data array 请求数据
     */
    public function enableMark($data)
    {
        //查询标识是否存在
        $mark = $this->findByAttributes(['mark_id' => $data['mark_id']], 'mark_id,mark,status');
        if (!$mark) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_MARK_NOT_FIND)];
        }

        $mark['status'] = $mark['status'] == 1 ? 2 : 1;
        $res = $this->updateByWhere([
            'status' => $mark['status'],
            'update_time' => time()
        ], ['mark_id' => $data['mark_id']]);
        if ($res) {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::OPERATE_SUCCESSFULLY), 'data' => $mark];
        } else {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::OPERATION_FAILURE)];
        }
    }

    /**
     * 删除标识
     */
    public function delMark($data = [])
    {
        //查询标识是否存在
        $mark = $this->findByAttributes(['mark_id' => $data['mark_id']], 'mark_id,mark,status');
        if (!$mark) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_MARK_NOT_FIND)];
        }
        if (1 == $mark['status']) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ENABLE_NOT_DELETE)];
        }

        //广告中是否已经使用
        $advertising = app('advertising')->findByAttributes(['mark_id' => $data['mark_id']], 'ad_id');
        if ($advertising) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::USE_NOT_DELETE)];
        }
        $res = $this->deleteByWhere(['mark_id' => $data['mark_id']]);
        if ($res) {
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESSFULLY_DELETE)];
        } else {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::FAIL_TO_EDIT)];
        }
    }
}