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
// | DateTime: 2020/9/2 10:21
// +----------------------------------------------------------------------
// | Desc: 
// +----------------------------------------------------------------------

namespace app\admin\model;


use app\common\base\ModelBase;

class AdminMenu extends ModelBase
{
    protected $pk = 'menu_id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * 添加菜单数据
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @param array $data
     * @return int|string
     */
    public function add($data)
    {
        return $this->insertGetId($data);
    }

    /**
     * 编辑修改菜单
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @param array $data
     * @param string|null $sequence
     * @return bool
     */
    public function save(array $data = [], string $sequence = null): bool
    {
        return $this->where($data)->update($data);
    }

    public function insertGetId(array $data)
    {
        return parent::insertGetId($data); // TODO: Change the autogenerated stub
    }
}