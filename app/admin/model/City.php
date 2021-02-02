<?php
// +----------------------------------------------------------------------
// | CoolCms [ DEVELOPMENT IS SO SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.coolcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Myj <815081410@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\base\ModelBase;

class City extends ModelBase
{
    protected $pk = 'area_id';

    /**
     * 获取城市三级联动
     */
    public function getCityInfo($data)
    {
        $province = $this->findAllByWhere(['parent_id'=>0],'city_id,city_name');
        if(!empty($province))
        {
            $city_ids = array_column($province,'city_id');
            $city = $this->findAllByWhere(['city_id'=>$city_ids],'city_id,city_name');
            if(!empty($city))
            {
                $district_ids = array_column($city,'city_id');
                $district = $this->findAllByWhere(['city_id'=>$district_ids],'city_id,city_name');
                if(!empty($district))
                {
                    //重组数据

                }
            }
        }
    }

    /**
     * 获取区域地址列表
     * @param int $parentId 父栏目ID
     * @param int $rank 获取几层栏目
     * @return bool|null
     */
    public function getAreaList($parentId = 0, $rank = 3)
    {
        $data = $this->getTree($parentId, $rank);
        return $data;
    }


    /**
     * 按父ID查找菜单子项
     * @param int $parentId 父菜单ID
     * @param boolean $withSelf 是否包括他自己
     * @return mixed
     */
    public function getSonLists($parentId, $withSelf = false)
    {
        //父节点ID
        $parentId = intval($parentId);
        $result = $this->where(['parent_id' => $parentId])->field('area_id,city_id,parent_id,city_name')->order('area_id asc')->select();
        return $result;
    }

    /**
     * 取得树形结构的菜单
     * @param $myId
     * @param string $parent
     * @param int $Level
     * @return bool|null
     */
    public function getTree($myId, $rank = 3, $parent = 0, $Level = 1)
    {
        $data = $this->getSonLists($myId);

        $Level++;

        if (count($data) > 0) {
            $ret = NULL;
            foreach ($data as $key => $v) {
                $array = [
                    "area_id" => $v['area_id'],
                    "city_id" => $v['city_id'],
                    "city_name" => $v['city_name'],
                    "parentId" => $parent,
                ];
                $ret[$key] = $array;

                if ($Level <= $rank) {
                    $child = $this->getTree($v['city_id'], $rank, $v['city_id'], $Level);
                    if ($child) {
                        $ret[$key]['is_son'] = true;
                        $ret[$key]['items'] = $child;
                    } else {
                        $ret[$key]['is_son'] = false;
                    }
                }
            }
            return $ret;
        }

        return false;
    }

    public function getThree($where)
    {
        $result = $this->field('city_id,city_name,parent_id,area_id')->where($where)->find();
        if ($result) {
            $data = $this->field('city_id,city_name,parent_id,area_id')->where(['city_id' => $result['parent_id']])->find();
            return $row = [
                'province' => [
                    'area_id' => $data['area_id'],
                    'city_name' => $data['city_name'],
                ],
                'city' => [
                    'area_id' => $result['area_id'],
                    'city_name' => $result['city_name'],
                ],
            ];
        } else {
            return false;
        }
    }
}