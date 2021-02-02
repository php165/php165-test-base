<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/3 0003
 * Time: 下午 3:59
 */

namespace app\admin\model;


use app\common\base\ErrorCode;
use app\common\base\ModelBase;
use think\facade\Db;
use app\common\traits\Tree;

class AdminRole extends ModelBase
{
    use Tree;

    public function setRoleMenu($param)
    {
        foreach ($param['menu_id'] as $k => $v) {
            $menuData[$k]['role_id'] = $param['role_id'];
            $menuData[$k]['menu_id'] = $v;
        }
        //查询是否有  有就删除再添加   没有就直接添加
        $menu = app('adminRoleAccess')->findByAttributes(['role_id' => $param['role_id']], 'menu_id');
        if (empty($menu)) {
            $res = app('adminRoleAccess')->insertAll($menuData);
        } else {
            $delRes = app('adminRoleAccess')->deleteByWhere(['role_id' => $param['role_id']]);
            if (!$delRes) {
                return ['code' => ErrorCode::ERROR_CODE];
            }
            $res = app('adminRoleAccess')->insertAll($menuData);
        }
        if (!$res) {
            return ['code' => ErrorCode::ERROR_CODE];
        }
        return ['code' => ErrorCode::SUCCESS_CODE];
    }

    public function getRoleMenuList($param,$adminInfo=[])
    {
        //登录信息
        $adminId = $adminInfo['admin_id'];
        //所有需要验证的权限
        $lists = app('adminMenu')->findAllByWhere(['is_access'=>1], 'menu_id,menu_name,parent_id');

        if (isset($param['role_id'])) {
            //查看是否有父级
            $role = $this->findByAttributes(['role_id' => $param['role_id']], 'parent_id');
            if (!empty($role)) {
                if ($role['parent_id'] == 0 && 1 == $adminId) {
                    //管理ID为1的有全部管理权限
                } else {
                    $ruleParent = app('adminRoleAccess')->findAllByWhere(['role_id' => $role['parent_id']], 'role_id,menu_id');
                    $arr = [];
                    foreach ($lists as $key => $v) {
                        if (in_array($v['menu_id'], array_column($ruleParent, 'menu_id'))) {
                            $arr[] = $v;
                        }
                    }
                    //改角色组所拥有的所有权限
                    $lists = $arr;
                }
            }
            //该角色所拥有的全部权限
            $access = app('adminRoleAccess')->findAllByWhere(['role_id' => $param['role_id']], 'menu_id');
        } else {
            $access = [];
        }
        $checkMenuId = [];
        foreach ($lists as $key => $value) {
            if (empty($access)) {
                $value['checkeds'] = false;
            } else {
                if (in_array($value['menu_id'], array_column($access, 'menu_id'))) {
                    //父级菜单也展示，
//                    $checkRes = app('adminMenu')->findByAttributes(['parent_id' => $value['menu_id']], 'menu_id');
//                    if (!$checkRes) {
                        $checkMenuId[] = $value['menu_id'];
//                    }
                    $value['checkeds'] = true;
                } else {
                    $value['checkeds'] = false;
                }
            }
            $lists[$key]['checkeds'] = $value['checkeds'];
        }
        $lists = $this->construct('menu_id', 'parent_id', 'Children')->load($lists)->DeepTree();

        $data = [
            'data' => $lists,
            'menu_id' => $checkMenuId
        ];
        return $data;

//        $parentId = $this->findByAttributes(['role_id' => $param['role_id']], 'parent_id');
//        if (empty($parentId)) {
//            return ['code' => ErrorCode::ERROR_CODE];
//        }
//        $menu = app('adminMenu')->findAllByWhere([], 'menu_id,menu_name,parent_id');
//        if (0 == $parentId['parent_id']) {
//            $menuData = app('adminMenu')->findAllByWhere([], 'menu_id,menu_name,parent_id');
//        } else {
//            $roleMenu = app('adminRoleAccess')->findAllByWhere(['role_id' => $parentId['parent_id']], 'role_id,menu_id');
//            $arr = [];
//            foreach ($menu as $key => &$v) {
//                if (in_array($v['menu_id'], array_column($roleMenu, 'menu_id'))) {
//                    $v['checked'] = true;
//                } else {
//                    $v['checked'] = false;
//                }
//                $arr[$key]['checked'] = $v['checked'];
//                $arr[] = $v;
//            }
//            $menuData = $arr;
//        }
//        //父级 自己的 以及全部的
////        print_r($menuData);die;
////        foreach ($menuData as $key => $vaule) {
////            if ($access['code'] != 200) {
////                $vaule['checked'] = false;
////            } else {
////                if (in_array($vaule['id'], json_decode($access['data']['rule_name'], true))) {
////                    $vaule['checked'] = true;
////                } else {
////                    $vaule['checked'] = false;
////                }
////            }
////            $menuData[$key]['checked'] = $vaule['checked'];
////        }
//        $lists = $this->construct('menu_id', 'parent_id', 'Children')->load($menuData)->DeepTree();
////        app('adminMenu');
//        print_r($lists);
//        die;
        //判断当前角色是否是父级 如果是 展示所有的菜单 如果不是 只展示父级拥有的菜单


//        $roleData = Db::name('admin_menu')->where(['status'=>1])->field('role_id,role_name')->select()->toArray();
//        die;
    }

    public function delRoleMenu($param)
    {
        $roleId = $this->findByAttributes(['role_id' => $param['role_id']], 'role_id');
        if (empty($roleId)) {
            return ['code' => ErrorCode::ERROR_CODE];
        }
        $adminRoleRes = app('admin')->whereOr([
            ['role_id', 'like', '%[' . $roleId['role_id'] . ',%'],
            ['role_id', 'like', '%[' . $roleId['role_id'] . ']%'],
            ['role_id', 'like', '%,' . $roleId['role_id'] . ']%'],
            ['role_id', 'like', '%,' . $roleId['role_id'] . ',%']
        ])->field('admin_id')->find();
        if (!empty($adminRoleRes)) {
            return ['code' => ErrorCode::DELETE_ROLE_CODE];
        }
        Db::startTrans();
        $roleRes = $this->deleteByWhere(['role_id' => $param['role_id']]);
        if (!$roleRes) {
            Db::rollback();
            return ['code' => ErrorCode::ERROR_CODE];
        }
       app('adminRoleAccess')->deleteByWhere(['role_id' => $param['role_id']]);
//        print_r($adminRoleAccessRes);die;
//        if (!$adminRoleAccessRes) {
//            Db::rollback();
//            return ['code' => ErrorCode::ERROR_CODE];
//        }
        Db::commit();
        return ['code' => ErrorCode::SUCCESS_CODE];
    }


}