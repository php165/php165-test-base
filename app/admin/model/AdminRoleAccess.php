<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/4 0004
 * Time: 上午 10:47
 */

namespace app\admin\model;


use app\common\base\ErrorCode;
use app\common\base\ModelBase;

class AdminRoleAccess extends ModelBase
{

    public function deleteMenuRole($param)
    {
        $menu = json_decode($param['menu_id'], true);
        if (is_array($menu)) {
            //批量删除
//            print_r($menu);
            foreach ($menu as $k => $v) {
                $pid = app('adminMenu')->findByAttributes(['menu_id' => $v], 'parent_id');
                if (empty($pid)) {
                    return ['code' => ErrorCode::ERROR_CODE];
                }

                $is_delete = app('adminMenu')->findByAttributes(['parent_id' => $v], 'menu_id');
                if (!empty($is_delete)) {
                    return ['code' => ErrorCode::DELETE_MENU_NOTICE_CODE];
                }
                //查询已授权的菜单
                $access = $this->findByAttributes(['menu_id' => $v], 'access_id');
                if (!empty($access)) {
                    $roleRes = $this->deleteByWhere(['menu_id' => $v]);
                    if (!$roleRes) {
                        return ['code' => ErrorCode::ERROR_CODE];
                    }
                }
                $res = app('adminMenu')->deleteByWhere(['menu_id' => $v]);
            }
            if (!$res) {
                return ['code' => ErrorCode::ERROR_CODE];
            }
            return ['code' => ErrorCode::SUCCESS_CODE];
        }

        $pid = app('adminMenu')->findByAttributes(['menu_id' => $param['menu_id']], 'parent_id');
        if (empty($pid)) {
            return ['code' => ErrorCode::ERROR_CODE];
        }

        $is_delete = app('adminMenu')->findByAttributes(['parent_id' => $param['menu_id']], 'menu_id');
        if (!empty($is_delete)) {
            return ['code' => ErrorCode::DELETE_MENU_NOTICE_CODE];
        }
        //查询已授权的菜单
        $access = $this->findByAttributes(['menu_id' => $param['menu_id']], 'access_id');
        if (!empty($access)) {
            $roleRes = $this->deleteByWhere(['menu_id' => $param['menu_id']]);
            if (!$roleRes) {
                return ['code' => ErrorCode::ERROR_CODE];
            }
        }
        $res = app('adminMenu')->deleteByWhere(['menu_id' => $param['menu_id']]);
        if (!$res) {
            return ['code' => ErrorCode::ERROR_CODE];
        }
        return ['code' => ErrorCode::SUCCESS_CODE];
    }
}