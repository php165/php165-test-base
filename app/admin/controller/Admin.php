<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/9/2 0002
 * Time: 16:18
 *
 * @use [用户管理]
 */
declare (strict_types=1);
namespace app\admin\controller;

use AlibabaCloud\Client\Clients\EcsRamRoleClient;
use app\common\base\AdminController;
use app\common\base\ErrorCode;
use think\facade\Validate;
use think\App;
use think\facade\Db;

class Admin extends AdminController
{
    protected $admin;
    protected $middleware = ['auth','log'];

    //初始化
    public function __construct(App $app)
    {
        $this->admin = app('admin');

        parent::__construct($app);
    }

    /**
     * [列表]
     *
     * @return \think\response\Json
     */
    public function index()
    {
        $param = $this->request->get();

        $where = [];
        $whereOr = [];
        if (isset($param['username']) && $param['username'])
        {
            $where[] = ['username','like','%'.trim($param['username']).'%'];
        }

        if (isset($param['mobile']) && $param['mobile'])
        {
            $where[] = ['mobile','like','%'.trim($param['mobile']).'%'];
        }

        if (isset($param['admin_id']) && $param['admin_id'])
        {
            $where[] = ['admin_id','=',trim($param['admin_id'])];
        }

        $page  = isset($param['page']) ? $param['page'] : 1;
        $limit = isset($param['limit']) ? $param['limit'] : 10;

        //判断当前管理员权限
        $apiAuth = $this->request->header('apiAuth');
        $adminInfo = cache('Login:'.$apiAuth);
        $adminId = json_decode($adminInfo,true)['admin_id'];
        if (1 != $adminId)
        {
            $roleId = app('admin')->getAdminRoleInfo($adminId);
            if ($roleId)
            {
                //处理查询条件
                foreach ($roleId as $rVal)
                {
                    $whereOr[] = ['role_id','like','%['.$rVal.',%'];
                    $whereOr[] = ['role_id','like','%['.$rVal.']%'];
                    $whereOr[] = ['role_id','like','%,'.$rVal.']%'];
                    $whereOr[] = ['role_id','like','%,'.$rVal.',%'];
                }

            }

            $where[] = ['admin_id','>',1];
        }

        $whereSql = '';
        $sql = "select admin_id,username,mobile,truename,status,last_login_ip,last_login_time,create_time from `cool_admin`";

        if ($where)
        {
            $whereSql .= ' where';
            foreach($where as $key=>$val)
            {
                $whereSql .= " `$val[0]`"." $val[1] " . "'$val[2]' and";
            }
        }

        if ($whereOr)
        {
            if (empty($whereSql))
            {
                $whereSql .= ' where';
            }

            $whereSql .= ' (';

            foreach ($whereOr as $key => $wVal)
            {
                $whereSql .= " `$wVal[0]`". " $wVal[1] ". "'$wVal[2]'" . " or";
            }

            $whereSql = substr($whereSql,0,-3) . ')';
        } else
        {
            $whereSql = substr($whereSql,0,-4);
        }

        $limitPage = ($page-1)*10;
        $sql = $sql . $whereSql . " order by `create_time` desc limit $limitPage,$limit";

        $countSql = 'select count(*) as count from `cool_admin`'.$whereSql;

        $result['count'] = Db::query($countSql)[0]['count'];
        $result['data']  = Db::query($sql);


        //逻辑处理
//        $adminData = $this->admin->getAdminList($where,$page,$limit,$whereOr);

        return $this->returnJson(ErrorCode::SUCCESS_CODE,'成功',$result);
    }

    /**
     * [查看]
     *
     * @return \think\response\Json
     */
    public function read()
    {
        $param = $this->request->get();

        if (!isset($param['admin_id']) || empty($param['admin_id']))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR);
        }

        $result = $this->admin->findByAttributes(['admin_id'=>$param['admin_id']]);
        if ($result)
        {
            $apiAuth = $this->request->header('apiAuth');
            $adminInfo = cache('Login:'.$apiAuth);
            $adminId = json_decode($adminInfo,true)['admin_id'];

            $role = !empty($result['role_id']) ? json_decode($result['role_id'],true) : [];

            //处理角色
            $result['role_id'] = $this->admin->getAdminRoleList($adminId , $role);

            return $this->returnJson(ErrorCode::SUCCESS_CODE,'SUCCESS',$result);
        }

        return $this->returnJson(ErrorCode::ERROR_CODE);
    }

    /**
     * [添加]
     *
     * @return \think\response\Json
     */
    public function save()
    {
        $param = $this->request->post();

        //校验参数
        $rule = [
            'username' => 'require',
            'password' => 'require',
            'mobile'   => 'require',
            'truename' => 'require',
            'secret'   => 'require|number|length:6'
        ];
        $msg = [
            'username'       => '请输入用户名称',
            'password'       => '请输入密码',
            'mobile'         => '请输入手机号',
            'truename'       => '请输入真实姓名',
            'secret.require' => '请输入6位数字安全密码',
            'secret.number'  => '安全码必须为6位数字',
            'secret.length'  => '安全码必须为6位数字'
        ];
        $validate = Validate::rule($rule,$msg);
        if (!$validate->check($param))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,$validate->getError());
        }

        //逻辑处理
        $result = $this->admin->addAdminInfo($param);

        return $this->returnJson($result['code'],$result['msg'],$result['data']);
    }

    /**
     * [删除]
     *
     * @return \think\response\Json
     */
    public function delete()
    {
        $param = $this->request->delete();

        if (!isset($param['admin_id']) || empty($param['admin_id']))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'缺少致命参数');
        }

        $result = $this->admin->where(['admin_id'=>$param['admin_id']])->delete();
        if ($result)
        {
            return $this->returnJson(ErrorCode::SUCCESS_CODE);
        }

        return $this->returnJson(ErrorCode::ERROR_CODE);
    }

    /**
     * [编辑]
     *
     * @return \think\response\Json
     */
    public function update()
    {
        $param = $this->request->post();

        //校验参数
        $rule = [
            'admin_id' => 'require',
            'username' => 'require',
//            'password' => 'require',
            'mobile'   => 'require',
            'truename' => 'require',
            'secret'   => 'require|number|length:6'
        ];
        $msg = [
            'admin_id'       => '无效的管理员',
            'username'       => '请输入用户名称',
//            'password'       => '请输入密码',
            'mobile'         => '请输入手机号',
            'truename'       => '请输入真实姓名',
            'secret.require' => '请输入6位数字安全密码',
            'secret.number'  => '安全码必须为6位数字',
            'secret.length'  => '安全码必须为6位数字'
        ];
        $validate = Validate::rule($rule,$msg);
        if (!$validate->check($param))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,$validate->getError());
        }

        //逻辑处理
        $result = $this->admin->editAdminInfo($param);

        return $this->returnJson($result['code'],$result['msg'],$result['data']);
    }

    public function getUserInfo()
    {
        $param = $this->request->post();

        if (!isset($param['admin_id']) || empty($param['admin_id']))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR);
        }

        $result = $this->admin->findByAttributes(['admin_id'=>$param['admin_id']]);
        if ($result)
        {
            return $this->returnJson(ErrorCode::SUCCESS_CODE,'SUCCESS',$result);
        }

        return $this->returnJson(ErrorCode::ERROR_CODE);
    }

    /**
     * [编辑]
     *
     * @return \think\response\Json
     */
    public function editUserInfo()
    {
        $param = $this->request->post();

        //校验参数
        $rule = [
            'admin_id' => 'require',
            'username' => 'require',
            'mobile'   => 'require',
            'truename' => 'require',
//            'secret'   => 'require|number|length:6'
        ];
        $msg = [
            'admin_id'       => '无效的管理员',
            'username'       => '请输入用户名称',
            'mobile'         => '请输入手机号',
            'truename'       => '请输入真实姓名',
//            'secret.require' => '请输入6位数字安全密码',
//            'secret.number'  => '安全码必须为6位数字',
//            'secret.length'  => '安全码必须为6位数字'
        ];
        $validate = Validate::rule($rule,$msg);
        if (!$validate->check($param))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,$validate->getError());
        }

        //逻辑处理
        $result = $this->admin->editAdminInfo($param);

        return $this->returnJson($result['code'],$result['msg'],$result['data']);
    }

    /**
     * [角色列表]
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRoleList()
    {
        $apiAuth = $this->request->header('apiAuth');
        $adminInfo = cache('Login:'.$apiAuth);
        $adminId = json_decode($adminInfo,true)['admin_id'];

        $roleId = app('admin')->getAdminRoleInfo($adminId);

        $roleData = app('adminRole')->findAllByWhere([['status','=',1],['role_id','in',$roleId]],'role_id,role_name,list_order','list_order asc');

        return $this->returnJson(ErrorCode::SUCCESS_CODE,'SUCCESS',$roleData);
    }
}