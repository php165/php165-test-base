<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/8/31 0031
 * Time: 13:57
 */

namespace app\common\base;


use think\Model;

class ModelBase extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    /**
     * 添加数据
     * @param array $data
     * @return int  id值
     */
    public function add($data)
    {
        return $this->insert($data);
    }

    /**
     * 根据条件删除
     * @param array $where
     * @return bool  id值
     */
    public function deleteByWhere($where)
    {
        return $this->where($where)->delete();
    }

    /**
     * 根据条件修改
     * @param array $where
     * @param array $data
     * @return
     */
    public function updateByWhere($data, $where = [])
    {
        return $this->where($where)->update($data);
    }

    /**
     * 根据属性获取一行记录
     * @param array $where
     * @param string $field
     * @return array 返回一维数组，未找到记录则返回空数组
     */
    public function findByAttributes($where = array(), $field = "*",$order = "create_time asc")
    {
        return $this->field($field)->where($where)->order($order)->find() ?? [];
    }

    /**
     * 根据条件查询获得数据
     * @param array $where
     * @param string $field
     * @param string $order
     * @return array 返回二维数组，未找到记录则返回空数组
     */
    public function findAllByWhere($where = array(), $field = "*", $order = "create_time desc")
    {

        return $this->field($field)->where($where)->order($order)->select()->toArray();
    }

    /**
     * 查询全部数据有分页查询
     * @param array $where
     * @param string $field
     * @param string $page
     * @param string $limit
     * @param string $order
     * @return array 返回二维数组，未找到记录则返回空数组
     */
    public function selectAllData($where = array(), $field = '*', $page = 1, $limit = 10, $order = "create_time desc")
    {
        return $this->field($field)->where($where)->order($order)->page($page, $limit)->select()->toArray();
    }

    /**
     * 根据条件统计
     * @param array $where
     * @return bool  条数
     */
    public function countWhere($where)
    {
        return $this->where($where)->count();
    }
}