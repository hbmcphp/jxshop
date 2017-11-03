<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 0:47
 */

namespace Admin\Model;


class GoodsCateModel extends CommonModel
{
    public function insertExCate($ext_cate_id,$goods_id)
    {
        $ext_cate_id = array_unique($ext_cate_id);

        //遍历数据添加到数据表中
        foreach ($ext_cate_id as $key=>$value){
            if($value!=0){
                $list[]=array('goods_id'=>$goods_id,'cate_id'=>$value);
            }
        }
       $this->addAll($list);
    }
}