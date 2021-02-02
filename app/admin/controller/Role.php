<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/3 0003
 * Time: 下午 3:57
 */

namespace app\admin\controller;


use app\common\base\AdminController;
use app\common\base\ErrorCode;
use think\response\Json;
use think\facade\Validate;
use think\app;

class Role extends AdminController
{
    protected $role;
    protected $middleware = ['auth', 'log'];

    public function __construct(App $app)
    {
        parent::__construct($app);
        //角色模型
        $this->role = app('adminRole');
    }

    /**
     * 角色列表
     * @dec:
     * @param array $data
     * @param string|null $sequence
     * @return Json
     * @author: 杨瑞
     */
    public function index()
    {
        $param = $this->param;
        if (!isset($param['role_id'])) {
            $where = [];
        } else {
            if (0 == $param['role_id']) {
                $where[] = ['parent_id', '=', 0];
            } elseif (-1 == $param['role_id']) {
                $where = [];
            } else {
                $where[] = ['parent_id', '=', $param['role_id']];
            }

        }
        $adminData = $this->getAdminLoginInfo();
        if (empty($adminData)) {
            return $this->returnJson(ErrorCode::LOGIN_IN);
        }
        if (empty($adminData['role_id'])) {
            return $this->returnJson(ErrorCode::LOGIN_IN);
        }
        if (!isset($param['role_id'])) {
            if (1 != $adminData['admin_id']) {
                $roleId = app('admin')->getAdminRoleInfo($adminData['admin_id']);
                $where[] = ['role_id', 'in', $roleId];
            }
        }

        $page = isset($param['page']) ? $param['page'] : 1;
        $limit = isset($param['limit']) ? $param['limit'] : 10;
        $data['count'] = $this->role->countWhere($where);
        $result = $this->role->selectAllData($where, 'role_id,role_name,status,parent_id,status,list_order,remark', $page, $limit, 'parent_id asc,list_order asc');
        foreach ($result as $k => &$v) {
            if (0 == $v['parent_id']) {
                $result[$k]['master_role'] = $v['role_name'] . '-主';
            } else {
                $result[$k]['master_role'] = $v['role_name'];
            }
        }
        $data['data'] = $result;
        return $this->returnJson(ErrorCode::SUCCESS_CODE, '成功', $data);
    }

    /**
     * 角色添加
     * @dec:
     * @param array $data
     * @return Json
     * @author: 杨瑞
     */
    public function save()
    {
        $param = $this->param;
        //校验参数
        $rule = [
            'role_name' => 'require',
            'parent_id' => 'require',
            'status' => 'require',
        ];
        $msg = [
            'role_name' => '角色名称',
            'parent_id' => '上级',
            'status' => '状态',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $param['create_time'] = time();
        $result = $this->role->add($param);
        if (!$result) {
            return $this->returnJson(ErrorCode::ERROR_CODE);
        }
        return $this->returnJson(ErrorCode::SUCCESS_CODE);
    }

    /**
     * 角色编辑
     * @dec:
     * @param array $data
     * @return Json
     * @author: 杨瑞
     */
    public function update()
    {
        $param = $this->param;
        //校验参数
        $rule = [
            'role_id' => 'require',
        ];
        $msg = [
            'role_id' => '角色',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $param['update_time'] = time();
        $result = $this->role->updateByWhere($param, ['role_id' => $param['role_id']]);
        if (!$result) {
            return $this->returnJson(ErrorCode::ERROR_CODE);
        }
        return $this->returnJson(ErrorCode::SUCCESS_CODE);
    }

    /**
     * 角色详情
     * @dec:
     * @param array $data
     * @return Json
     * @author: 杨瑞
     */
    public function read()
    {
        $param = $this->param;
        //校验参数
        $rule = [
            'role_id' => 'require',
        ];
        $msg = [
            'role_id' => '角色',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $result = $this->role->findByAttributes(['role_id' => $param['role_id']], 'parent_id,role_id,role_name,status,list_order,remark');
        if (!$result) {
            return $this->returnJson(ErrorCode::ERROR_CODE);
        }
        return $this->returnJson(ErrorCode::SUCCESS_CODE, null, $result);
    }

    /**
     * 授权列表
     * @dec:
     * @param array $data
     * @return Json
     * @author: 杨瑞
     */
    public function getRoleAdminMenu()
    {
        $param = $this->param;
        //校验参数
        $rule = [
            'role_id' => 'require',
        ];
        $msg = [
            'role_id' => '角色',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        //去授权
        if ($this->request->isPost()) {
            $data = $this->role->getRoleMenuList($param, $this->getAdminLoginInfo());
            return $this->returnJson(ErrorCode::SUCCESS_CODE, null, $data);
        }
        return $this->returnJson(ErrorCode::ERROR_CODE);
    }

    /**
     * 授权
     * @dec:
     * @param array $data
     * @return Json
     * @author: 杨瑞
     */
    public function setRoleAdminMenu()
    {
        $param = $this->param;

        //校验参数
        $rule = [
            'role_id' => 'require',
            'menu_id' => 'require',
        ];
        $msg = [
            'role_id' => '角色',
            'menu_id' => '菜单',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }

        $param['menu_id'] = json_decode($param['menu_id'], true);

        if (empty($param['menu_id'])) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, '请选择角色');
        }

        if (!is_array($param['menu_id'])) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, '请输入正确的数据格式');
        };
        $res = $this->role->setRoleMenu($param);
        if ($res['code'] == 200) {
//            //TODO 菜单缓存 bear
//            $this->setMenuVserionCache();
//            //TODO 重新分发路由到该权限下的管理员
            $this->setRoleAdminMenuCache($param['role_id']);
        }
        return $this->returnJson($res['code']);
    }

    /**
     * 一级角色列表
     * @dec:
     * @param array $data
     * @return Json
     * @author: 杨瑞
     */
    public function getParentRoleList()
    {
        $adminData = $this->getAdminLoginInfo();
        if (empty($adminData)) {
            return $this->returnJson(ErrorCode::LOGIN_IN);
        }
        $arr = [];
        if (1 == $adminData['admin_id']) {
            $where[] = ['parent_id', '=', 0];
            $arr[0] = [
                'role_id' => -1,
                'role_name' => '全部'
            ];
            $arr[1] = [
                'role_id' => 0,
                'role_name' => '顶级岗位'
            ];
        } else {
            $roleId = json_decode($adminData['role_id'], true);
            if (empty($roleId)) {
                return $this->returnJson(ErrorCode::LOGIN_IN);
            }
            $role_id = [];
            foreach ($roleId as $key => &$item) {
                $pId = $this->role->findByAttributes(['role_id' => $item], 'parent_id,role_id');
                if (empty($pId)) {
                    return $this->returnJson(ErrorCode::ERROR_CODE);
                }
                if (0 == $pId['parent_id']) {
                    $role_id[$key]['parent_id'] = $pId['role_id'];
                }
                $role_id[$key]['parent_id'] = $pId['parent_id'];
            }
            $where[] = ['role_id', 'in', array_column($role_id, 'parent_id')];
        }
//        print_r($where);die;
        $data = $this->role->findAllByWhere($where, 'role_id,role_name', 'role_id');
        if (empty($data)) {
            return $this->returnJson(ErrorCode::NO_DATA_CODE);
        }
        return $this->returnJson(ErrorCode::SUCCESS_CODE, null, array_merge($arr, $data));
    }

    /**
     * 删除角色
     * @dec:
     * @param array $data
     * @return Json
     * @author: 杨瑞
     */
    public function delete()
    {
        $param = $this->param;
        $rule = [
            'role_id' => 'require',
        ];
        $msg = [
            'role_id' => '角色',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $res = $this->role->delRoleMenu($param);
        return $this->returnJson($res['code']);
    }

}