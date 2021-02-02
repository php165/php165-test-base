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
// | DateTime: 2020/9/3 13:13
// +----------------------------------------------------------------------
// | Desc: 
// +----------------------------------------------------------------------

namespace app\common\traits;


/**
 * Trait Tree
 * @dec:使用方法如下
    $treelist = $this->construct('menu_id','parent_id','Children')->load($result)->DeepTree()//所有分类树结构
    var_dump($treelist);//查看结果
    $subtree = $this->construct('menu_id','parent_id','Children')->load($result)->DeepTree(1);//获取id为1下面的子树
    var_dump($subtree);
 * @author: Alan <alanstars@qq.com>
 * @package app\common\traits
 */
trait Tree
{
    private $OriginalList;
    public $pKey;//主键字段名
    public $parentKey;//上级id字段名
    public $childrenKey;//用来存储子分类的数组key名

    public function construct($pk="id",$parentKey="pid",$childrenKey="children"){
        if(!empty($pk) && !empty($parentKey) && !empty($childrenKey)){
            $this->pKey=$pk;
            $this->parentKey=$parentKey;
            $this->childrenKey=$childrenKey;
            return $this;
        }else{
            return false;
        }

    }

    //载入初始数组
    function load($data){
        if(is_array($data)){
            $this->OriginalList=$data;
            return $this;
        }else{
            return false;
        }
    }

    /**
     * 生成嵌套格式的树形数组
     * array(..."children"=>array(..."children"=>array(...)))
     */
    function DeepTree($root=0){
        if(!$this->OriginalList){
            return FALSE;
        }
        $OriginalList=$this->OriginalList;
        $tree=array();//最终数组
        $refer=array();//存储主键与数组单元的引用关系
        //遍历
        foreach($OriginalList as $k=>$v){
            if(!isset($v[$this->pKey]) || !isset($v[$this->parentKey]) || isset($v[$this->childrenKey])){
                unset($OriginalList[$k]);
                continue;
            }
            $refer[$v[$this->pKey]]=&$OriginalList[$k];//为每个数组成员建立引用关系
        }
        //遍历2
        foreach($OriginalList as $k=>$v){
            if($v[$this->parentKey]==$root){//根分类直接添加引用到tree中
                $tree[]=&$OriginalList[$k];
            }else{
                if(isset($refer[$v[$this->parentKey]])){
                    $parent=&$refer[$v[$this->parentKey]];//获取父分类的引用
                    $parent[$this->childrenKey][]=&$OriginalList[$k];//在父分类的children中再添加一个引用成员
                }
            }
        }
        return $tree;
    }

}