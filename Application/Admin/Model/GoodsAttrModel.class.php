<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 11:04
 */

namespace Admin\Model;


class GoodsAttrModel extends CommonModel
{
    protected $fields = array('id', 'goods_id','attr_id','attr_values');

    public function insertAttr($attr,$goods_id)
    {
        foreach($attr as $key=>$value){
            foreach($value as $v){
               $attr_list[] = array(
                    'goods_id'=>$goods_id,
                    'attr_id'=>$key,
                    'attr_values'=>$v,
                );
            }
        }
        $this->addAll($attr_list);
    }

    public function getSigleAttr($goods_id)
    {
        $data = $this->alias('a')->join('left join jx_attribute b on a.attr_id=b.id')->field('
        a.*,b.attr_name,attr_type,attr_input_type,attr_value')
            ->where("a.goods_id = $goods_id and b.attr_type=2")->select();
//        dump($data);die;

//        转化为以属性为下标的三位数组 方便页面显示
        foreach($data as $key=>$value){
            $list[$value['attr_id']][]=$value;
        }
//        dump($list);
        return $list;
    }
}