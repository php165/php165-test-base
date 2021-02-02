<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/9/3 0003
 * Time: 9:23
 *
 * @use [登陆管理]
 */

namespace app\admin\controller;


use app\BaseController;
use app\common\base\ErrorCode;
use think\facade\Validate;
use think\App;

class Login extends BaseController
{
//    protected $middleware = ['auth'];
    private $admin;

    //初始化
    public function __construct(App $app)
    {
        $this->admin = app('admin');

        parent::__construct($app);
    }


    public function index()
    {
        $param = $this->request->get();

        if (!isset($param['mobile']) || empty($param['mobile']))
        {
            return json(['code'=>ErrorCode::MOBILE_IS_EMPTY,'msg'=>'账号不能为空','data'=>null]);
        }

        if (!isset($param['password']) || empty($param['password']))
        {
            return json(['code'=>ErrorCode::PASSWORD_IS_EMPTY,'msg'=>'密码不能为空','data'=>null]);
        } else
        {
            $param['password'] = set_password_salt($param['password']);
        }

        //逻辑处理
        $result = $this->admin->login($param);

        return json(['code'=>$result['code'],'msg'=>$result['msg'],'data'=>$result['data']]);
    }

    /**
     * [注销]
     *
     * @return \think\response\Json
     */
    public function outLogin()
    {
        $apiAuth = $this->request->header('apiAuth','');
        if (empty($apiAuth))
        {
            return json(['code'=>ErrorCode::INVALID_HEADER_INFO,'msg'=>'无效的apiAuth']);
        }

        $adminInfo = cache('Login:'.$apiAuth);
        if (empty($adminInfo) || $adminInfo == null)
        {
            return json(['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'注销成功']);
        }

        cache('Login:'.$apiAuth,null);
        cache('Login:'.json_decode($adminInfo,true)['admin_id'],null);

        return json(['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'注销成功']);
    }

    /**
     * [当前管理员菜单列表]
     *
     * @return \think\response\Json
     */
    public function getAdminMenuList()
    {
        $apiAuth = $this->request->header('apiAuth','');
        if (empty($apiAuth))
        {
            return json(['code'=>ErrorCode::INVALID_HEADER_INFO,'msg'=>'无效的apiAuth','data'=>null]);
        }

        $adminData = cache('Login:'.$apiAuth);
        if (empty($adminData) || !is_array(json_decode($adminData,true)))
        {
            return json(['code'=>ErrorCode::INVALID_USER_INFO,'msg'=>'无效的用户信息','data'=>null]);
        }

        $result['menuList'] = $this->admin->getAdminMenuList(json_decode($adminData,true)['admin_id']);
        //获取需要二次验证的URL
        $result['permission'] = app('adminMenu')->where(['permission'=>1])->column('url');

        return json(['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'SUCCESS','data'=>$result]);
    }

    public function setAdminMenuCache()
    {
        $apiAuth = $this->request->header('apiAuth','');
        if (empty($apiAuth))
        {
            return json([
                'code' => ErrorCode::PARAM_ERROR,
                'msg'  => '缺少致命参数apiAuth'
            ]);
        }

        $adminInfo = cache('Login:'.$apiAuth);
        if (empty($adminInfo))
        {
            return json([
                'code' => ErrorCode::INVALID_USER_INFO,
                'msg'  => '缺少致命参数apiAuth'
            ]);
        }

        $menuVersion = cache('SystemMenuVersion:','');
        if (!$menuVersion)
        {
            $menuVersion = 1;
            cache('SystemMenuVersion:',1,0);
        }

        $adminInfo = json_decode($adminInfo,true);

        cache('MenuVersion:'.$adminInfo['admin_id'],$menuVersion,0);

        return json(['code'=>200,'msg'=>'成功']);
    }

    /**
     * 测试异步任务
     * 接口地址 admin/login/ceshiTask
     */
    public function ceshiTask()
    {
        $res = app('advertisingTasks')->asyncSend(config('app.asyncTasks.advertising.send'),[
            'tasks_id'=>73]);
        dump($res);
    }
}