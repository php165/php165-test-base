<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/9/8 0008
 * Time: 14:30
 *
 * @use [日志管理]
 */

namespace app\admin\controller;

use app\common\base\AdminController;
use think\facade\Validate;
use think\App;
use app\common\base\ErrorCode;

class Log extends AdminController
{
    protected $log;
    protected $middleware = ['auth'];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->log = app('adminLog');
    }

    public function index()
    {
        $param = $this->request->get();

        $where = [];
        if (isset($param['behavior']) && $param['behavior'])
        {
            $where[] = ['behavior','like','%'.trim($param['behavior']).'%'];
        }

        if (isset($param['user_name']) && $param['user_name'])
        {
            $where[] = ['user_name','like','%'.trim($param['user_name']).'%'];
        }

        if (isset($param['method']) && $param['method'])
        {
            $where[] = ['method','=',$param['method']];
        }

        if (isset($param['start_time']) && isset($param['end_time']))
        {
            $where[] = ['create_time','between',[$param['start_time'],$param['end_time']]];
        } else if (isset($param['start_time']))
        {
            $where[] = ['create_time','>=',$param['start_time']];
        } else if (isset($param['end_time']))
        {
            $where[] = ['create_time','<=',$param['end_time']];
        }

        $where[] = ['is_del','=',0];

        $page  = isset($param['page']) ? $param['page'] : 1;
        $limit = isset($param['limit']) ? $param['limit'] : 10;

        //逻辑处理
        $result = $this->log->getLogList($where,$page,$limit);

        return $this->returnJson($result['code'],$result['msg'],$result['data']);
    }

    /**
     * [查看]
     *
     * @return \think\response\Json
     */
//    public function read()
//    {
//        $param = $this->request->get();
//
//        if (!isset($param['log_id']) || empty($param['log_id']))
//        {
//            return $this->returnJson(ErrorCode::PARAM_ERROR,'缺少致命参数');
//        }
//
//        $result = $this->log->findByAttributes(['log_id'=>$param['log_id']],'log_id,behavior,user_name,user_id,create_time');
//        if ($result)
//        {
//            return $this->returnJson(ErrorCode::SUCCESS_CODE,'SUCCESS',$result);
//        }
//
//        return $this->returnJson(ErrorCode::ERROR_CODE,'无效的信息');
//    }

    /**
     * [删除]
     *
     * @return \think\response\Json
     */
    public function delete()
    {
        $param = $this->request->delete();

        if (!isset($param['log_id']) || empty($param['log_id']))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'缺少致命参数');
        }

        $result = $this->log->updateByWhere(['is_del'=>1,'delete_time'=>time()],['log_id'=>$param['log_id']]);
        if ($result)
        {
            return $this->returnJson(ErrorCode::SUCCESS_CODE,'删除成功');
        }

        return $this->returnJson(ErrorCode::ERROR_CODE,'删除失败');
    }
}