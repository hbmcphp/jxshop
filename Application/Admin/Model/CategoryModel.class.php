<?php
namespace Admin\Model;

class CategoryModel extends CommonModel
{
    //自定义字段
    protected $fields  = array('id','cname','parent_id','isrec');
    //自动验证
    protected $_validata = array(
        array('cname','require','分类名称必须重写'),
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

    public function getFloor()
    {
        //获取顶级分类
        $data = $this->where('parent_id=0')->select();
        foreach($data as $key=>$value){
            //获取二级分类信息
            $data[$key]['son'] = $this->where('parent_id='.$value['id'])->select();
            //获取推荐的二级分类
            $data[$key]['recson'] = $this->where('isrec=1 and parent_id='.$value['id'])->select();
            //每个推荐分类对应的信息 商品下标通过方法得到上商品
            foreach($data[$key]['recson'] as $k=>$v){
                $data[$key]['recson'][$k]['goods']=$this->getGoodsByCateId($v['id']);
            }
        }
//        dump($data);
        return $data;
    }

    public function getGoodsByCateId($cate_id,$limit=8)
    {
        $children = $this->getChildren($cate_id);
        $children[]=$cate_id;
        $children = implode(',',$children);
        $goods = D('Goods')->where("is_sale=1 and cate_id in ($children)")->limit($limit)->select();
        return $goods;
    }
}