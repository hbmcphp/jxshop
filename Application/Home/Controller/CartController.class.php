<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/14
 * Time: 10:26
 */

namespace Home\Controller;


class CartController extends CommonController
{
    public function addCart()
    {
        $goods_id = intval(I('post.goods_id'));
        $goods_count = intval(I('post.goods_count'));
        $attr = I('post.attr');
        $model = D('Cart');
        $res = $model->addCart($goods_id,$goods_count,$attr);
        if(!$res){
            $this->error($model->getError());
        }
        $this->success('写入成功');
    }

    public function test()
    {
        dump(cookie());
    }
    //购物车显示
    public function index()
    {
        $model =D('Cart');
        //获取具体商品信息
        $data = $model->getList();
        //计算购物车总金额
        $total = $model->getTotle($data);

        $this->assign('data',$data);
//        dump($data);die;
        $this->assign('total',$total);
        $this->display();
    }

    public function dels()
    {
        $goods_id =intval(I('get.goods_id'));
        $goods_attr_ids =I('get.goods_attr_ids');
        D('Cart')->dels($goods_id,$goods_attr_ids);
//        dump($goods_attr_ids);die;
        $this->success('删除成功');
    }

    //更新购物车数量
    public function updateCount()
    {
        $goods_id =intval(I('post.goods_id'));
        $goods_count =intval(I('post.goods_count'));
        $goods_attr_ids =I('post.goods_attr_ids');
        D('Cart')->updateCount($goods_id,$goods_attr_ids,$goods_count);
    }
}