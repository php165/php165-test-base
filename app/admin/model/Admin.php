<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/8/31 0031
 * Time: 13:54
 *
 * @use [管理员管理]
 */

namespace app\admin\model;

use app\common\base\ModelBase;
use app\common\base\ErrorCode;
use think\facade\Db;
use app\common\traits\Tree;
use think\model\concern\RelationShip;

class Admin extends ModelBase
{
    use Tree;

    protected $pk = 'admin_id';

    /**
     * [管理员列表]
     * @param array $where [查询条件]
     * @param int $page [页码]
     * @param int $limit [页码展示数量]
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminList($where , $page = 1 , $limit = 10 , $whereOr = [])
    {

//        dump($where);die;
        $field = 'admin_id,username,mobile,truename,status,last_login_ip,last_login_time,create_time';
        $adminData = $this->field($field)->where($where)->page($page,$limit)->order('create_time desc')->select();
        dump($this->getLastSql());die;
        $count = $this->where($where)->whereOr($whereOr)->count();

        return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'请求成功','data'=>['count'=>$count,'data'=>$adminData]];
    }

    /**
     * [添加管理员]
     *
     * @param array $data [入参集合]
     * @return array
     */
    public function addAdminInfo($data)
    {
        $adminData = $this->field('admin_id,mobile')->where(['mobile'=>$data['mobile']])->find();
        if ($adminData && $adminData['mobile'] == $data['mobile'])
        {
            return ['code'=>ErrorCode::PHONE_IS_EXIT,'msg'=>'手机号码已存在','data'=>null];
        }

        //密码加密
        $data['password'] = set_password_salt($data['password']);

        $data['admin_img'] = ''; //http://image.yiliantaihe.com/uploads/article/20190709/40a5c912925740a2d47ccdb15f75cc14.jpg
        $data['create_time'] = time();
        $data['last_login_ip'] = $_SERVER["REMOTE_ADDR"];
        $data['last_login_time'] = time();

        $result = $this->save($data);
        if (false === $result)
        {
            return ['code'=>ErrorCode::ERROR_CODE,'msg'=>'添加失败','data'=>null];
        }

        return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'添加成功','data'=>null];
    }

    /**
     * [编辑管理员]
     *
     * @param array $data [入参集合]
     * @return array
     */
    public function editAdminInfo($data)
    {
        $adminData = $this->findByAttributes(['admin_id'=>$data['admin_id']],'admin_id,mobile,username,truename,role_id');
        if (empty($adminData))
        {
            return ['code'=>ErrorCode::INVALID_USER_INFO,'msg'=>'无效的管理员','data'=>null];
        }

        if ($adminData['mobile'] == $data['mobile'] && $adminData['admin_id'] != $adminData['admin_id'])
        {
            return ['code'=>ErrorCode::PHONE_IS_EXIT,'msg'=>'手机号码已存在','data'=>null];
        }

        //密码加密
        if (isset($data['password']) && $data['password'])
        {
            $data['password'] = set_password_salt($data['password']);
        } else
        {
            unset($data['password']);
        }

        if (isset($data['permission_pwd']))
        {
            unset($data['permission_pwd']);
        }
        $result = $this->where(['admin_id'=>$data['admin_id']])->update($data);
        if (false === $result)
        {
            return ['code'=>ErrorCode::ERROR_CODE,'msg'=>'编辑失败','data'=>null];
        }

        //TODO 修改缓存
        $apiAuth = cache('Login:'.$data['admin_id']);
        if (isset($data['role_id']) && $data['role_id'] != $adminData['role_id'])
        {
            //角色发生变化，重新加载路由
            cache('MenuVersion:' . $adminData['admin_id'],0,0);
        }

        cache('Login:'.$apiAuth,json_encode($adminData),config('app.online_time'));

        return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'编辑成功','data'=>null];
    }

    /**
     * [登陆]
     *
     * @param array $data [mobile 账号 password 密码]
     * @return string
     */
    public function login($data)
    {
        $adminData = $this->findByAttributes(
            ['mobile'=>$data['mobile'],'password'=>$data['password']],'admin_id,role_id,status,username,truename,mobile,secret'
        );
        if (empty($adminData))
        {
            return ['code'=>ErrorCode::INVALID_PASSWORD_INFO,'msg'=>'用户名密码不正确','data'=>null];
        }

        if (0 === $adminData['status'])
        {
            return ['code'=>ErrorCode::USER_IS_DISABLED,'msg'=>'用户已被封禁，请联系管理员','data'=>null];
        }

        $saveData['last_login_time'] = time();
        $saveData['last_login_ip'] = $_SERVER["REMOTE_ADDR"];

        //更新数据
        $result = $this->updateByWhere($saveData,['admin_id'=>$adminData['admin_id']]);
        if (false === $result)
        {
            return ['code'=>ErrorCode::ERROR_CODE,'msg'=>'登陆失败','data'=>null];
        }

        $adminData['apiAuth'] = '';

        //获取用户菜单
        $adminData['menu'] = $this->getAdminMenuList($adminData['admin_id']);
        //获取授权的URL
        $adminData['access'] = $this->getAdminAccessList($adminData['admin_id']);
        //获取需要二次验证的URL
        $adminData['permission'] = app('adminMenu')->where(['permission'=>1])->column('url');

        //获取当前后台菜单版本
        $menuVersion = cache('SystemMenuVersion:');
//        if ($menuVersion)
//        {
//            $adminData['menu_version'] = $menuVersion;
//        }

        $apiAuth = md5(uniqid() . time());

        cache('Login:' . $apiAuth, json_encode($adminData), config('app.online_time'));
        cache('Login:' . $adminData['admin_id'], $apiAuth, config('app.online_time'));
        cache('MenuVersion:' . $adminData['admin_id'],$menuVersion,0);

        $adminData['apiAuth'] = $apiAuth;

        return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'登陆成功','data'=>$adminData];
    }

    /**
     * [处理角色默认选中]
     *
     * @param $role
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminRoleList($adminId,$role)
    {
        $roleId = app('admin')->getAdminRoleInfo($adminId);
        $roleData = app('adminRole')->findAllByWhere([['status','=',1],['role_id','in',$roleId]],'role_id,role_name,list_order','list_order asc');
//        $roleData = app('adminRole')->findAllByWhere([['status','=',1],['parent_id','>',0]],'role_id,role_name');
        if ($roleData && $role)
        {
            foreach ($roleData as &$roleVal)
            {
                $roleVal['is_checked'] = 0;
                if (in_array($roleVal['role_id'],$role))
                {
                    $roleVal['is_checked'] = 1;
                }
            }
        } else if ($roleData)
        {
            foreach ($roleData as &$roleVal)
            {
                $roleVal['is_checked'] = 0;
            }
        }

        return $roleData;
    }

    /**
     * [获取当前管理员菜单权限]
     *
     * @param $adminId
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminMenuList($adminId)
    {
        $adminData = $this->findByAttributes(['admin_id'=>$adminId],'role_id,admin_id');
        if (empty($adminData)) return [];

        //TODO 超级管理员admin_id = 1 时特殊权限
        $menuWhere = [];
        if (1 !== $adminData['admin_id'])
        {
            $roleId = !empty($adminData['role_id']) ? json_decode($adminData['role_id'],true) : [];
            if (empty($roleId)) return $roleId;

            $roleWhere = [['role_id','in',$roleId]];
            $menuId = app('adminRoleAccess')
                ->where($roleWhere)->group('menu_id')->column('menu_id');
            if (empty($menuId)) return [];

            $menuWhere = [['menu_id','in',$menuId]];
        }


        $field = 'menu_id,menu_name,parent_id,type,list_order,url,param,menu_icon,component,router,status,log,permission,method';
        $menuData = app('adminMenu')->field($field)
                        ->where($menuWhere)->where('status','=',1)
                        ->order('parent_id asc,list_order asc')->select()->toArray();

        $lists = $this->construct('menu_id','parent_id','Children')->load($menuData)->DeepTree();

        return $lists;
    }

    /**
     * [获取路由]
     *
     * @param $adminId
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminAccessList($adminId)
    {
        $adminData = $this->findByAttributes(['admin_id'=>$adminId],'role_id,admin_id');
        if (empty($adminData)) return [];

        //TODO 超级管理员admin_id = 1 时特殊权限
        $menuWhere = [];
        if (1 !== $adminData['admin_id'])
        {
            $roleId = !empty($adminData['role_id']) ? json_decode($adminData['role_id'],true) : [];
            if (empty($roleId)) return $roleId;

            $menuId = app('adminRoleAccess')
                ->where('role_id','in',$roleId)->group('menu_id')->column('menu_id');
            if (empty($menuId)) return [];

            $menuWhere = [['menu_id','in',$menuId]];
        }

        $field = 'menu_name,url';
        $menuData = app('adminMenu')->field($field)
            ->where($menuWhere)->where('status','=',1)
            ->select()->toArray();

        return $menuData;
    }

    public function getAdminRoleInfo($adminId)
    {
        if ($adminId == 1)
        {
            //查询当前所有岗位
            $allRoleId = app('adminRole')->where(['status'=>1])->column('role_id');

            return $allRoleId;
        }

        //查询当前岗位
        $adminRole = app('admin')->where(['admin_id'=>$adminId])->field('role_id')->find();
        if (empty($adminRole))
        {
            return [];
        }

        $roleId = json_decode($adminRole['role_id'],true);

        if ($roleId && in_array(2,$roleId))
        {
            //查询所有一级下的所有岗位
            $roleId = app('adminRole')->where([['status','=',1],['parent_id','>',0]])->column('role_id');
        } else if ($roleId)
        {
            $roleId = self::recursion($roleId);
        }

        return $roleId;
    }

    private static function recursion($res)
    {
        $output = array();
        foreach ($res as $k => $v)
        {
            $tmpRes = app('adminRole')->where(['parent_id'=>$v,'status'=>1])->column('role_id');
            $output []= $v;
            if (!empty($tmpRes))
            {
                $output = array_merge($output, self::recursion($tmpRes));
            }
        }
        return $output;
    }
}