<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/8/31 0031
 * Time: 10:32
 *
 * @use [日志记录]
 */

namespace app\http\middleware;

use think\facade\Db;
use app\common\base\ErrorCode;

class AdminLog
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

        $adminId = json_decode($adminInfo,true)['admin_id'];
        $adminData = Db::name('admin')->field('admin_id,username')->where('admin_id','=',$adminId)->find();
        if (empty($adminData))
        {
            return json(
                [
                    'code' => ErrorCode::INVALID_USER_INFO,
                    'msg'  => '无效的管理员信息'
                ]
            );
        }

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

        //TODO 暂时按以往数据库进行查询
        $resultMenu = Db::name('admin_menu')->field(
            'menu_id,menu_name,type,status,param,method,remark,log'
        )->where(['url'=>$nowUrl])->find();
        if ($resultMenu && 1 === $resultMenu['log'])
        {
            $behavior = self::logLevelBehavior($resultMenu['method'],$resultMenu['menu_name'],$nowUrl);
            $url = $request->domain().$request->url();
            $param = $request->param();
            $ip = $request->ip();

            $logData = [
                'behavior'    => $behavior ,
                'menu_id'     => $resultMenu['menu_id'],
                'user_id'     => $adminId,
                'user_name'   => $adminData['username'],
                'method'      => $resultMenu['method'],
                'description' => $resultMenu['remark'],
                'url'         => $url,
                'ip'          => $ip,
                'create_time' => time(),
                'param'       => json_encode($param),
            ];

            Db::name('admin_log')->save($logData);
        }

        return $next($request);
    }

    /**
     * 后台操作日志等级
     * @param int $level    菜单等级：1=>get;2=>post;3=>put;4=>delete
     * @param string $menuName 原始菜单名称
     * @param string $url    具体请求方法
     * @return string $result  返回具体操作行为
     */
    private static function logLevelBehavior($level = 1,$menuName = '',$url = ''){
        $requestMethod = '';
        switch ($level)
        {
            case 1:
                $requestMethod = 'GET';
                break;
            case 2:
                $requestMethod = 'POST';
                break;
            case 3:
                $requestMethod = 'PUT';
                break;
            case 4:
                $requestMethod = 'DELETE';
                break;
            default:
                $requestMethod = '暂未开放';
                break;
        }

        if(!empty($requestMethod))
        {
            $result = '['.$requestMethod.']' . '-' . '[' . $menuName . ']' . '-' . $url;
        }

        return $result;
    }
}