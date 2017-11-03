<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 21:22
 */

namespace Home\Controller;


class GoodsController extends CommonController
{
    public function index()
    {
        $goods_id = intval(I('get.goods_id'));
        if(!$goods_id){
            $this->redirect('Index/index');
        }
        $goodsmodel = D('Goods');
        $goods = $goodsmodel->where ("is_sale=1 and id=".$goods_id)->find();
        if(!$goods){
            $this->redirect('Index/index');
        }
//        dump($goods);
        //促销的时候显示 价格显示促销价格
        if($goods['cx_price']>0 && $goods['start']<time() &&$goods['end']>time()){
            $goods['shop_price']=$goods['cx_price'];
        }

        $goods['goods_body']=htmlspecialchars_decode($goods['goods_body']);
        $this->assign('goods',$goods);

        $pic = M('GoodsImg')->where('goods_id='.$goods_id)->select();
//        dump($pic);
        $this->assign('pic',$pic);

        //获取商品对应的商品属性信息
        $attr = M('GoodsAttr')->alias('a')->field('a.*,b.attr_name,attr_type')->join('left join jx_attribute b on a.attr_id = b.id')
            ->where('goods_id='.$goods_id)->select();
//        dump($attr);die;
        foreach($attr as $value){
            if($value['attr_type']==1){
                $uniqid[]=$value;
            }else{
                $sigle[$value['attr_id']][]=$value;
            }
        }
        $this->assign('uniqid',$uniqid);
        $this->assign('sigle',$sigle);
        dump($sigle);
        $this->display();
    }

    //实现评论
    public function comment()
    {
        //增加对用户的判断
        $this->checkLogin();
        $model = D('Comment');
        $data = $model->create();
        if(!$data){
            $this->error('参数不对');
        }
        $model->add($data);
        $this->success('写入成功');
    }
}