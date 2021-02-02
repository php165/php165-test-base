<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/9/8 0008
 * Time: 14:30
 *
 * @use [场所管理]
 */

namespace app\admin\controller;

use app\common\base\AdminController;
use think\App;
use app\common\base\ErrorCode;

class City extends AdminController
{
    protected $city;

    protected $middleware = ['auth','log'];
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->city = app('city');
    }

    /**
     * 地区列表
     * 接口地址 admin/city
     */
    public function index()
    {

        $param = $this->param;
        if (!isset($param['parentId'])) {
            $param['parentId'] = 0;
        }
        if (!isset($param['rank'])) {
            $param['rank'] = 3;
        }
        //获取缓存数据
        $lists = cache('area_lists' . $param['parentId'] . '_' . $param['rank']);
        if (!$lists) {
            $lists = $this->city->getAreaList($param['parentId'], $param['rank']);
            if (!$lists) {
                return $this->returnJson(ErrorCode::PARAM_ERROR, '该父级城市ID不存在');
            }
            cache('area_lists' . $param['parentId'] . '_' . $param['rank'], $lists);
        }
        return $this->returnJson(ErrorCode::SUCCESS_CODE, '获取城市列表成功', $lists);
    }

    /**
     * [查看]
     *
     * @return \think\response\Json
     */
    public function read()
    {
        echo 'City read';
    }

    /**
     * [添加]
     *
     * @return \think\response\Json
     */
    public function save()
    {
        echo 'City save';
    }

    /**
     * [删除]
     *
     * @return \think\response\Json
     */
    public function delete()
    {
        echo 'City delete';
    }

    /**
     * [编辑]
     *
     * @return \think\response\Json
     */
    public function update()
    {
        echo 'City update';
    }
}