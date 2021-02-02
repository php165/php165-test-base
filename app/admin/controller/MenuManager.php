<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\common\base\AdminController;
use app\common\base\ErrorCode;
use app\common\traits\Tree;
use think\App;
use think\Request;
use think\facade\Validate;

class MenuManager extends AdminController
{
    use Tree;
    protected $menu;
    protected $middleware = ['auth','log'];

    public function __construct(App $app)
    {
        parent::__construct($app);

        //菜单模型
        $this->menu = app('adminMenu');

    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $param = $this->param;
        $result = $this->menu->findAllByWhere([], 'menu_id,menu_name,parent_id,type,list_order,url,param,menu_icon,component,router,status,log,permission,method,is_access,remark', 'parent_id asc,list_order asc');
        $lists = $this->construct('menu_id', 'parent_id', 'Children')->load($result)->DeepTree();
        return $this->returnJson(200, '获取成功', $lists);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        echo "MenuManager create";

    }

    public function find()
    {
        echo "MenuManager find";

    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //TODO bear 字段permission_pwd  unset
        if (isset($this->param['permission_pwd']))
        {
            unset($this->param['permission_pwd']);
        }

        //校验url最后一位是否有/结尾，如果有，自动去掉
        if (isset($this->param['url']) && (substr($this->param['url'], -1, 1) == '/')) {
            $this->param['url'] = substr($this->param['url'], 0, strlen($this->param['url']) - 1);
        }
        $result = $this->menu->add($this->param);
        if ($result) {
            //TODO 菜单缓存 bear
            $this->setMenuVserionCache();

            return $this->returnJson(200, '添加菜单成功');
        } else {
            return $this->returnJson(400, '添加菜单失败');
        }

    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
        echo "MenuManager read";

    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
        echo "MenuManager edit";

    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update()
    {
        //
        $param = $this->param;

        //TODO bear 字段permission_pwd  unset
        if (isset($param['permission_pwd']))
        {
            unset($param['permission_pwd']);
        }
        //校验url最后一位是否有/结尾，如果有，自动去掉
        if (isset($this->param['url']) && (substr($this->param['url'], -1, 1) == '/')) {
            $this->param['url'] = substr($this->param['url'], 0, strlen($this->param['url']) - 1);
        }
        $result = $this->menu->updateByWhere($param);
        if ($result) {
            //TODO 菜单缓存 bear
            $this->setMenuVserionCache();

            return $this->returnJson(200, '更新成功');
        } else {
            return $this->returnJson(ErrorCode::NO_DATA_CHANGE_CODE, '暂无变化');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete()
    {
        $param = $this->param;
        //校验参数
        $rule = [
            'menu_id' => 'require',
        ];
        $msg = [
            'menu_id' => '菜单',
        ];
        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($param)) {
            return $this->returnJson(ErrorCode::PARAM_ERROR, $validate->getError());
        }
        $res = app('adminRoleAccess')->deleteMenuRole($param);
        if ($res['code'] == ErrorCode::SUCCESS_CODE)
        {
            //TODO 菜单缓存 bear
            $this->setMenuVserionCache();
        }

        return $this->returnJson($res['code']);
    }


}
