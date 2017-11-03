<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 21:57
 */

namespace Admin\Model;


use Think\Crypt\Driver\Think;

class GoodsModel extends CommonModel
{
    //自定义自动验证
    protected $fields=array('id','goods_name','goods_sn','cate_id','market_price',
        'shop_price','goods_img','goods_thumb','goods_body','is_hot','is_rec','is_new',
        'addtime','isdel','is_sale','type_id','goods_number','cx_price','start','end');
    //对分类进行验证
    protected $_validate = array(
        array('goods_name','require','商品名必须填写',1),
        array('cate_id','checkCategory','分类必须填写',1,'callback'),
        array('market_price','currency','市场价格格式不对'),
        array('shop_price','currency','本店价格格式不对'),
    );
    //分类验证
    public function checkCategory($cate_id)
    {
        $cate_id = intval($cate_id);
        if($cate_id>0){
            return true;
        }
        return false;
    }

    //实现商品信息修改
    public function update($data)
    {
        //商品促销时间
        if($data['cx_price']>0){
            $data['start']=strtotime($data['start']);
            $data['end']=strtotime($data['end']);
        }else{
            $data['cx_price']=0.00;
            $data['start']=0.00;
            $data['end']=0.00;
        }

        $goods_id = $data['id'];
        //解决货号问题
        $goods_sn = $data['goods_sn'];
        if(!$goods_sn){
            $data['goods_sn'] = 'JX'.uniqid();
        }else{
            $res = $this->where("goods_sn = '$goods_sn'AND id != $goods_id")->find();
            if($res){
                $this->error = '货号错误';
                return  false;
            }
        }
        //删除之前的分类 解决扩展分类问题
        $extCateModel = D('GoodsCate');
        $extCateModel->where("goods_id = $goods_id")->delete();
        $ext_cate_id = I('post.ext_cate_id');
        $extCateModel->insertExCate($ext_cate_id,$goods_id);
        //图片上传
        $res = $this->uploadImg();
        if($res){
            $data['goods_img']=$res['goods_img'];
            $data['goods_thumb']=$res['goods_thumb'];
        }
        //实现数据入库
        $goodsAttrModel = M('GoodsAttr');
        //删除之前的分类
        $goodsAttrModel->where('goods_id='.$goods_id)->delete();
        $attr = I('post.attr');
//        foreach ($attr as $key=>$value){
//            foreach($value as $v){
//                $attr_list[]=array(
//                    'goods_id'=>$goods_id,
//                    'attr_id'=>$key,
//                    'attr_values'=>$v,
//                );
//            }
//        }
//        M('GoodsAtrt')->addAll($attr_list);
        D('GoodsAttr')->insertAttr($attr,$goods_id);
        //追加商品相册图片
        //释放商品上传图片 并且批量上传
        unset($_FILES['goods_img']);
        $upload = new \Think\Upload();
        $info = $upload->upload();
//        dump($info);die;
        foreach($info as $key=>$value){
            //上传后图片地址
            $goods_img = 'Uploads/'.$value['savepath'].$value['savename'];
            //实现缩略图制作
            $img =new\Think\Image();
            $img->open($goods_img);

            //制作缩略图
            $goods_thumb = 'Uploads/'.$value['savepath'].'thumb_'.$value['savename'];
            //将图片裁剪为450x450并保存为$goods_thumb
            $img->thumb(100,100)->save($goods_thumb);
            $list[] = array(
                'goods_id'=>$goods_id,
                'goods_img'=>$goods_img,
                'goods_thumb'=>$goods_thumb,
            );
        }

        if($list){
            M('GoodsImg')->addAll($list);
        }
        return $this->save($data);
    }

    //使用钩子
    public function _before_insert(&$data)
    {
        //商品促销时间
        if($data['cx_price']>0){
            $data['start']=strtotime($data['start']);
            $data['end']=strtotime($data['end']);
        }else{
            $data['cx_price']=0.00;
            $data['start']=0.00;
            $data['end']=0.00;
        }

        $data['addtime']=time();
        //处理货号
        if(!$data['goods_sn']){
            $data['goods_sn'] = 'JX'.uniqid();
        }else{
            $info = $this->where('goods_sn='.$data['goods_sn'])->find();
            if($info){
                $this->error='货号重复';
                return false;
            }
        }
        $res = $this->uploadImg();
        if($res){
            $data['goods_img']=$res['goods_img'];
            $data['goods_thumb']=$res['goods_thumb'];
        }

    }
    //使用钩子
    public function _after_insert($data)
    {
        //获取商品ID 和扩展分类ID
        $goods_id = $data['id'];
        $ext_cate_id = I('post.ext_cate_id');
        D('GoodsCate')->insertExCate($ext_cate_id,$goods_id);

        //属性入库
        $attr = I('post.attr');
//        foreach($attr as $key => $value){
//            foreach($value as $v ){
//                $attr_list[]=array(
//                    'goods_id'=>$goods_id,
//                    'attr_id'=>$key,
//                    'attr_values'=>$v,
//                );
//            }
//        }
//        dump($attr_list);die();
//        M('GoodsAttr')->addAll($attr_list);
        D('GoodsAttr')->insertAttr($attr,$goods_id);
//        dump($_FILES);die;
        //释放商品上传图片 并且批量上传
        unset($_FILES['goods_img']);
        $upload = new \Think\Upload();
        $info = $upload->upload();
//        dump($info);die;
        foreach($info as $key=>$value){
            //上传后图片地址
            $goods_img = 'Uploads/'.$value['savepath'].$value['savename'];
            //实现缩略图制作
            $img =new\Think\Image();
            $img->open($goods_img);

            //制作缩略图
            $goods_thumb = 'Uploads/'.$value['savepath'].'thumb_'.$value['savename'];
            //将图片裁剪为450x450并保存为$goods_thumb
            $img->thumb(100,100)->save($goods_thumb);
            $list[] = array(
                'goods_id'=>$goods_id,
                'goods_img'=>$goods_img,
                'goods_thumb'=>$goods_thumb,
            );
        }

        if($list){
            M('GoodsImg')->addAll($list);
        }
    }
    //商品列表显示
    public function listData($isdel=1)
    {
        //1定义每页数据
        $pagesize=3;
        //2获取总记录数
        $where="isdel=".$isdel;
        //接收分类ID
        $cate_id = intval(I('get.cate_id'));
        if($cate_id){
            //根据获取的分类ID和下面的子分类ID来获取数据
            $model = D('Category');
            $tree = $model->getChildren($cate_id);
            //添加自己的分类ID
            $tree[] = $cate_id;
            //dump($tree);
            //转化为字符串格式 方便拼接where
            $children = implode(',',$tree);
//            dump($children);
            //获取扩展分类id 因为一个商品可能存在多个扩展id 那么在找寻多个扩展分类下的goods_id时
            //会得到重复的goods_id  因此使用group分组
            $ext_goods_ids = M('GoodsCate')->group('goods_id')->where("cate_id in ($children)")
                ->select();
            //存在扩展ID时
            if($ext_goods_ids){
                foreach($ext_goods_ids as $value){
                    $goods_ids[]= $value['goods_id'];
                }
                $goods_ids = implode(',',$goods_ids);
                //dump($goods_ids);
            }
            //组合where字句
            if(!$goods_ids){
                $where.=" AND cate_id in ($children)";
            }else{
                $where.=" AND cate_id in ($children) OR id in ($goods_ids)";
            }
        }
        //接收提交的推荐状态
        $intro_type = I('get.intro_type');
        if($intro_type){
            //限制只能使用三个推荐作为条件
            if($intro_type=='is_new'||$intro_type=='is_hot'||$intro_type=='is_rec'){
                $where .=" AND $intro_type = 1";
            }
        }
        //接收上下架状态
        $is_sale = intval(I('get.is_sale'));
        if($is_sale==1){
            $where.=" AND is_sale=1";
        }elseif($is_sale==2){
            $where.=" AND is_sale=0";
        }
        //接收关键词
        $keyword = I('get.keyword');
        if($keyword){
            $where.=" AND goods_name like '%$keyword%'";
        }

        $count = $this->where($where)->count();
        //3计算分页导航
        $page = new \Think\Page($count,$pagesize);
        $show = $page->show();
        //4获取当前页面
        $p = intval(I('get.p'));
        //5获取具体数据
        $data = $this->where($where)->page($p,$pagesize)->select();
//        dump($data);die();
        return array('pageStr'=>$show,'data'=>$data);
    }

    public function setStatus($goods_id,$isdel=0)
    {
        return $this->where("id=$goods_id")->setfield('isdel',$isdel);
    }


    public function uploadImg()
    {
        //判断图片上传
        if(!isset($_FILES['goods_img'])||$_FILES['goods_img']['error']!=0){
            return false;
        }

        //实现图片上传
        $upload = new \Think\Upload();
        $info = $upload->uploadOne($_FILES['goods_img']);
        if(!$info){
            $this->error = $upload->getError();
        }
        //上传后图片地址
        $goods_img = 'Uploads/'.$info['savepath'].$info['savename'];
        //实现缩略图制作
        $img =new \Think\Image();
        $img->open($goods_img);

        //制作缩略图
        $goods_thumb = 'Uploads/'.$info['savepath'].'thumb_'.$info['savename'];
        //将图片裁剪为450x450并保存为$goods_thumb
        $img->thumb(450,450)->save($goods_thumb);
        return array('goods_img'=>$goods_img,'goods_thumb'=>$goods_thumb);
    }

    public function remove($goods_id)
    {
        //删除商品的图片
        $goods_info = $this->findOneById($goods_id);
        if(!$goods_info){
            return false;
        }
        unlink($goods_info['goods_img']);
        unlink($goods_info['goods_thumb']);
        //删除商品的扩展分类
        D('GoodsCate')->where("goods_id=$goods_id")->delete();
        //删除商品的基本信息
        $this->where("id=$goods_id")->delete();
        return true;
    }
    //根据参数返回热卖、推荐、新品的商品信息
    public function getRecGoods($type)
    {
        return $this->where("is_sale=1 and $type=1")->limit(5)->select();
    }
    //获取促销的商品
    public function getCrazyGoods()
    {
        //满足条件 1上架商品 2时间在促销开始和结束之间 3促销价格>0
        $where = 'is_sale=1 and cx_price>0 and start<' .time().' and end>'.time();
        return $this->where($where)->limit(5)->select();
    }
}