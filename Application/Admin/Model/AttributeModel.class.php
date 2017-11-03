<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/3
 * Time: 20:37
 */

namespace Admin\Model;


class AttributeModel extends CommonModel
{
    protected $fields =array('id','attr_name','type_id','attr_type','attr_input_type','attr_value');
    protected $_validate = array(
        array('attr_name','require','属性名称必须填写'),
        array('attr_type','require','类型名称必须填写'),
        array('attr_type','1,2','属性类型只能为单选或者唯一',1,in),
        array('attr_input_type','1,2','属性录入方法只能为手工或者列表',1,in)
    );

    public function listData()
    {
        $pagesize =3;
        $count = $this->count();
        $page = new \Think\Page($count,$pagesize);
        $show = $page->show();
        $p = intval(I('get.p'));
        //分页连贯操作
        $list = $this->page($p,$pagesize)->select();
        //主键ID作为索引  id等于下表中的type_id
        $type = D('type')->select();
        foreach($type as $value){
            $typeinfo[$value['id']]=$value;
        }
        //循环属性数据 替换type_id
        //$value['type_id'] 对应 Type中的ID字段 也就是根据Type表中的id为下标
        //而上一步中的正是把ID作为下标来循环的  可以得到对应的type_name
        //$value 值没有保存 因此要用list下的key值
        foreach($list as $key=>$value){
            $list[$key]['type_id']=$typeinfo[$value['type_id']]['type_name'];
        }
        return array('pageStr'=>$show,'list'=>$list);
    }

    public function remove($attr_id)
    {
        return $this->where('id='.$attr_id)->delete();
    }
}