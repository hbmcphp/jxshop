<?php
namespace Admin\Model;

class RuleModel extends CommonModel
{
    //自定义字段
    protected $fields  = array('id','rule_name','module_name','controller_name','action_name','parent_id','is_show');
    //自动验证
    protected $_validata = array(
        array('rule_name','require','权限名称必须填写'),
        array('module_name','require','模型名称必须填写'),
        array('controller_name','require','控制器名称必须填写'),
        array('action_name','require','方法名称必须填写'),
    );
    //获取某个分类的子分类
    public function getChildren($id)
    {
        $data = $this->select();
        //信息格式化
        $list = $this->getTree($data,0,$id,1,false);
        foreach($list as $value){
            $tree[] = $value['id'];
        }
        return $tree;
    }


    //获取格式化数据
    public function getCateTree($stop_id=0)
    {
        $data = $this->select();
        $list = $this->getTree($data,$stop_id);
        return $list;
    }
    //格式化分类信息
    public function getTree($data,$stop_id,$id=0,$lev=1,$iscache=true)
    {
        //每次当$this->getTree()执行完了  函数数据会消失  所以静态
        static $list = array();
        //根据参数 决定是否需要重置静态变量
        if(!$iscache) $list = array();
        foreach($data as $value){
            if($value['parent_id']==$id){
                //$stop_id 是从CategoryController中的要编辑时intval(I('get.id'))获取的参数
                //$stop_id等于当前遍历所有数据中存在的ID的时候 终止循环
                if($stop_id==$value['id']) continue;
                $value['lev']=$lev;
                $list[] = $value;
                $this->getTree($data,$stop_id,$value['id'],$lev+1);
            }
        }
        return $list;
    }



    public function dels($id)
    {
        //有子分类不允许删除
        $result = $this->where('parent_id='.$id)->find();
        if($result){
           return false;
        };
        return $this->where('id='.$id)->delete();
    }

    public function update($data)
    {
        // dump($tree);exit();
        // 需要过滤掉设置父分类为自己或者自己下的子分类
        // 1、根据要修改的分类的标识 获取到自己下的所有的子分类
//        $Tree = $this->getCateTree($data['id']);
//        //将自己添加到不能修改的数组中
//        $Tree[] = array('id'=>$data['id']);
//
//        // 2、根据提交的parent_id的值 判断如果等于当前修改的分类ID或者是当前分类下的所有子分类的ID不容许修改
//        foreach($Tree as $key=>$value){
//            if($data['parent_id']==$value['id']){
//                $this->error='不能设置子分类为父分类';
//            }
//        }
//        return $this->save($data);
    }


}