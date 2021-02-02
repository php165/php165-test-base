<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/9/1 0001
 * Time: 9:41
 */

namespace app\http\middleware;

use app\common\base\Controller;
use think\facade\Db;
use app\common\base\ErrorCode;

class AdminAuth
{
    public function handle($request, \Closure $next)
    {
        $apiAuth = $request->header('apiAuth');
        if (empty($apiAuth))
        {
            return json(
                [
                    'code' => ErrorCode::LOGIN_IN,
                    'msg'  => '已失效，请重新登陆'
                ]
            );
        }

        $adminInfo = cache('Login:'.$apiAuth);
        if (empty($adminInfo))
        {
            return json(
                [
                    'code' => ErrorCode::LOGIN_IN,
                    'msg'  => '已失效，请重新登陆'
                ]
            );
        }

//        $menuId = ''; //TODO 暂时处理 无用
        //获取菜单id
//        $menuId = $request->header('menuId');
//        if (empty($menuId))
//        {
//            return json(
//                [
//                    'code' => ErrorCode::INVALID_USER_INFO,
//                    'msg'  => '缺少致命参数menuId'
//                ]
//            );
//        }

        $adminId = json_decode($adminInfo,true)['admin_id'];

        //url处理
        $url = self::manageRouteUrl($request);

        //校验权限
        $result = $this->checkAccess($adminId,$url,$request);
        if ($result['code'] != 200)
        {
            return json(
                [
                    'code' => $result['code'],
                    'msg'  => $result['msg']
                ]
            );
        }

        return $next($request);
    }

    /**校验用户权限
     * @param int $admin_id   管理员ID
     * @param int $menu_id    目录id
     * @return bool
     */
    public function checkAccess($adminId , $url , $request = '')
    {
        $adminData = Db::name('admin')->field('admin_id,role_id,secret')->where('admin_id','=',$adminId)->find();
        if (empty($adminData))
        {
            return ['code'=>ErrorCode::INVALID_USER_INFO,'msg'=>'无效的管理员信息'];
        }

        //TODO 超级管理员root无需校验
        if (1 === $adminId)
        {
            return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'SUCCESS'];
        }

        $menuData = Db::name('admin_menu')->where(['url'=>$url])->field('menu_id,is_access,permission')->find();
        if (empty($menuData))
        {
            return ['code'=>ErrorCode::NOT_VISIT_ACCESS,'msg'=>'暂无权限访问'];
        }

        //确定当前目录是否二级验证
        if (1 === $menuData['permission'])
        {
            if (!isset($request->param()['permission_pwd']) || empty($request->param()['permission_pwd']))
            {
                return ['code'=>ErrorCode::ERROR_CODE,'msg'=>'请输入您二次验证的密码'];
            }

            if (empty($adminData['secret']))
            {
                return ['code'=>ErrorCode::ERROR_CODE,'msg'=>'请先设置您二次验证的私钥'];
            }

            if ($adminData['secret'] != $request->param()['permission_pwd'])
            {
                return ['code'=>ErrorCode::INVALID_SECRET_KEY,'msg'=>'密钥错误，请重新输入,如有问题，请联系后台管理人员'];
            }
        }


        if (0 === $menuData['is_access'])
        {
            return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'SUCCESS'];
        }

        $roleId = json_decode($adminData['role_id'],true);
        if (empty($roleId))
        {
            return ['code'=>ErrorCode::NOT_VISIT_ACCESS,'msg'=>'暂无权限访问'];
        }

        $isAccess = Db::name('admin_role_access')
            ->where([['role_id','in',$roleId],['menu_id','=',$menuData['menu_id']]])
            ->column('access_id');
        if (empty($isAccess))
        {
            return ['code'=>ErrorCode::NOT_VISIT_ACCESS,'msg'=>'暂无权限访问'];
        }


        return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'SUCCESS'];
    }

    public static function manageRouteUrl($request)
    {
        $controller = $request->controller();
        //首字母大写转成数组
        $array = preg_split("/(?=[A-Z])/",$controller);

        //数组转换成带下划线的字符串并去掉左边下划线
        $controller = ltrim(implode('',$array),'_');

        $action = $request->action();
        if ($action == 'index')
        {
            $nowUrl = app('http')->getName() . '/' . $controller;
        } else
        {
            $nowUrl = app('http')->getName() . '/' . $controller . '/' . $action;
        }

        return $nowUrl;
    }
}