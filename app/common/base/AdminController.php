<?php
// +----------------------------------------------------------------------
// | CoolCms [ DEVELOPMENT IS SO SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://www.coolcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Alan <alanstars@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2020/9/2 10:37
// +----------------------------------------------------------------------
// | Desc: 
// +----------------------------------------------------------------------

namespace app\common\base;


use think\App;

class AdminController extends Controller
{
    public function __construct(App $app)
    {
        parent::__construct($app);

        $menuVersion = cache('SystemMenuVersion:','');
        if (!$menuVersion)
        {
            $menuVersion = 1;
            cache('SystemMenuVersion:',1,0);
        }

        $apiAuth = $this->request->header('apiAuth','');
        //校验当前用户是否登陆
        $userInfo = self::checkUserIsInvalid($apiAuth);
        if (ErrorCode::SUCCESS_CODE != $userInfo['code'])
        {
            echo json_encode(['code'=>$userInfo['code'],'msg'=>$userInfo['msg']]);
            exit();
        }

        $adminMenuVersion = cache('MenuVersion:' . $userInfo['data']['admin_id']);
        if (!$adminMenuVersion)
        {
            $adminMenuVersion = 0;
        }

        //校验当前用户菜单版本是否需要更新
        if ($menuVersion != $adminMenuVersion)
        {
            echo json_encode(['code'=>ErrorCode::MENU_IS_NOT_NEW,'msg'=>'菜单目录已发生变化，点击加载更新']);
            exit();
        }

        return true;
    }

    public static function checkUserIsInvalid($apiAuth)
    {
        if (empty($apiAuth))
        {
            return [
                'code' => ErrorCode::LOGIN_IN,
                'msg'  => '页面加载失败，请重新登陆'
            ];
        }

        $adminInfo = cache('Login:'.$apiAuth);
        if (empty($adminInfo))
        {
            return [
                'code' => ErrorCode::LOGIN_IN,
                'msg'  => '页面加载失败，请重新登陆'
            ];
        }

        return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'SUCCESS','data'=>json_decode($adminInfo,true)];
    }

    /**
     * [处理该角色下所有管理员的缓存]
     *
     * @param integer $roleId [角色id]
     * @return bool
     */
    public function setRoleAdminMenuCache($roleId)
    {
        $adminData = app('admin')
            ->whereOr(
                [
                    ['role_id','like','%['.$roleId.',%'],
                    ['role_id','like','%['.$roleId.']%'],
                    ['role_id','like','%,'.$roleId.']%'],
                    ['role_id','like','%,'.$roleId.',%']
                ]
            )->field('admin_id,mobile,username,truename')->select()->toArray();
        if ($adminData)
        {
            foreach ($adminData as $adminVal)
            {
                cache('MenuVersion:' . $adminVal['admin_id'],0,0);
            }
        }

        return true;
    }
}