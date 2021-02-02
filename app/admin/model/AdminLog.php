<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/10/9 0009
 * Time: 15:19
 */

namespace app\admin\model;


use app\common\base\ModelBase;
use app\common\base\ErrorCode;

class AdminLog extends ModelBase
{
    protected $pk = 'log_id';

    /**
     * [列表]
     * @param array $where [查询条件]
     * @param int $page [页码]
     * @param int $limit [页码展示数量]
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getLogList($where , $page = 1 , $limit = 10)
    {
        $field = 'log_id,behavior,user_id,user_name,create_time';
        $logData = $this->field($field)->where($where)->page($page,$limit)->order('create_time desc')->select()->toArray();

        $count = $this->where($where)->count();

        return ['code'=>ErrorCode::SUCCESS_CODE,'msg'=>'请求成功','data'=>['count'=>$count,'data'=>$logData]];
    }
}