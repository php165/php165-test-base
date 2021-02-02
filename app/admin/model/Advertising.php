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
use app\common\traits\FilesTrait;

class Advertising extends ModelBase
{
    protected $pk = 'ad_id';
    protected $adType = ['','图片','视频'];
    protected $isValidity = ['永久','计时'];

    /**
     * 广告列表
     * @$data array 数组参数
     * @$where array 查询条件
     */
    public function getAdvertisingList($data = [], $where = [], $field = '*')
    {
        $res = $this->selectAllData($where, $data['field'], $data['page'], $data['limit'], $data['sort']);
        if ($res) {
            $newData = $this->getNewData($res);
            return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESS_CODE), 'data' => $newData];
        } else {
            return ['code' => ErrorCode::NO_DATA_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::NO_DATA_CODE)];
        }
    }

    /**
     * 广告详情
     * @param array $data 数组参数
     */
    public function getAdvertisingDetails($data = [])
    {
        $field = 'ad_id,title,ad_type,position_id,mark_id,mark_content,url,subhead,slogan,click_count,running_time,is_validity,begin_time,end_time,status,sort';
        $advertising = $this->findByAttributes(['ad_id' => $data['ad_id']], $field);
        if (!$advertising) {
            return ['code' => ErrorCode::ADVERTISING_NOT_FIND, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_NOT_FIND)];
        }

        $res = $this->getNewData([$advertising]);
        return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESS_CODE), 'data' => $res[0]];
    }

    /**
     * 广告数据处理
     */
    public function getNewData($data = [])
    {
        $position_ids = array_unique(array_column($data,'position_id'));
        $mark_ids = array_unique(array_column($data,'mark_id'));
        $position = app('advertisingPosition')->findAllByWhere(['position_id'=>$position_ids],'position_id,position_name');
        $positionArr = array_column($position,null,'position_id');
        $mark = app('advertisingMark')->findAllByWhere(['mark_id'=>$mark_ids],'mark_id,mark_name');
        $markArr = array_column($mark,null,'mark_id');
        foreach ($data as $k=>&$v){
            $v['position_name'] = $positionArr[$v['position_id']]['position_name'] ?? '';
            $v['mark_name'] = $markArr[$v['mark_id']]['mark_name'] ?? '';
            $v['ad_type_name'] = $this->adType[$v['ad_type']];
            $v['is_validity_name'] = $this->isValidity[$v['is_validity']] ?? '';
        }
        return $data;
    }

    /**
     * 添加广告
     * @param array $data 数组数据
     */
    public function addAdvertising($data = [])
    {
        try {
            //广告是否存在
            $advertising = $this->findByAttributes(['title' => $data['title']], 'ad_id');
            if ($advertising) {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_ALREADY_EXIST)];
            }

            //广告位置是否存在
            $position = app('advertisingPosition')->findByAttributes(['position_id' => $data['position_id'], 'status' => 1], 'position_id');
            if (!$position) {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_POSITION_NOT_FIND)];
            }

            //广告标识是否存在
            $mark = app('advertisingMark')->findByAttributes(['mark_id' => $data['mark_id'], 'status' => 1], 'mark_id');
            if (!$mark) {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_MARK_NOT_FIND)];
            }

            $res = $this->insertGetId([
                'position_id' => $data['position_id'],
                'mark_id' => $data['mark_id'],
                'mark_content' => $data['mark_content'],
                'title' => $data['title'],
                'url' => $data['url'],
                'subhead' => $data['subhead'],
                'status' => $data['status'],
                'slogan' => $data['slogan'],
                'ad_type' => $data['ad_type'],
                'sort' => $data['sort'],
                'click_count' => $data['click_count'],
                'running_time' => $data['running_time'],
                'is_validity' => $data['is_validity'],
                'begin_time' => $data['begin_time'] ?? 0,
                'end_time' => $data['end_time'] ?? 0,
                'create_time' => time()
            ]);

            if ($res) {
                return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESSFULLY_ADDED)];
            } else {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::FAIL_TO_ADD)];
            }

        } catch (\Exception  $e) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑广告
     * @param array $data 数组参数
     */
    public function editAdvertising($data = [])
    {
        try {
            //广告是否存在
            $advertising = $this->findByAttributes(['title' => $data['title']], 'ad_id,url');
            if (!$advertising) {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_NOT_FIND)];
            }

            $res = $this->updateByWhere([
                'mark_content' => $data['mark_content'],
                'url' => $data['url'],
                'subhead' => $data['subhead'],
                'status' => $data['status'],
                'slogan' => $data['slogan'],
                'ad_type' => $data['ad_type'],
                'sort' => $data['sort'],
                'click_count' => $data['click_count'],
                'running_time' => $data['running_time'],
                'is_validity' => $data['is_validity'],
                'begin_time' => $data['begin_time'] ?? 0,
                'end_time' => $data['end_time'] ?? 0,
                'update_time' => time()
            ], ['ad_id' => $data['ad_id']]);

            if ($res) {
                //原文件删除
                FilesTrait::delFileManager($advertising['url']);
                return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESSFULLY_EDIT)];
            } else {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::FAIL_TO_EDIT)];
            }

        } catch (\Exception  $e) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 启用禁用状态
     * @param $data array 请求数据
     */
    public function enableAdvertising($data = [])
    {
        try {
            //查询广告是否存在
            $advertising = $this->findByAttributes(['ad_id' => $data['ad_id']], 'ad_id,title,status');
            if (!$advertising) {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_NOT_FIND)];
            }

            $advertising['status'] = $advertising['status'] == 1 ? 2 : 1;
            $res = $this->updateByWhere([
                'status' => $advertising['status'],
                'update_time' => time()
            ], ['ad_id' => $data['ad_id']]);
            if ($res) {
                return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::OPERATE_SUCCESSFULLY), 'data' => $advertising];
            } else {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::OPERATION_FAILURE)];
            }
        } catch (\Exception  $e) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => $e->getMessage()];
        }
    }

    /**
     * 删除广告
     * @param array $data 数组参数
     */
    public function delAdvertising($data = [])
    {
        try {
            //查询广告是否存在
            $advertising = $this->findByAttributes(['ad_id' => $data['ad_id']], 'ad_id,status');
            if (!$advertising) {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ADVERTISING_NOT_FIND)];
            }
            if (1 == $advertising['status']) {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::ENABLE_NOT_DELETE)];
            }

            $res = $this->deleteByWhere(['ad_id' => $data['ad_id']]);
            if ($res) {
                return ['code' => ErrorCode::SUCCESS_CODE, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::SUCCESSFULLY_DELETE)];
            } else {
                return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => ErrorCode::getErrorCodeMsg(ErrorCode::FAIL_TO_EDIT)];
            }
        } catch (\Exception  $e) {
            return ['code' => ErrorCode::EXCEPTION_ERROR, 'msg' => $e->getMessage()];
        }
    }
}