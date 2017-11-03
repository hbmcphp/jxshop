<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/14
 * Time: 20:34
 */

namespace Home\Model;


use Think\Model;

class OrderModel extends Model
{
    //1 获取商品信息
    public function order()
    {
        $cartModel = D('Cart');
        $data = $cartModel->getList();
//        dump($data);die;
        if(!$data){
            $this->error = '购物车没有商品';
            return false;
        }
        //2 获取商品库存
        foreach($data as $value){
            $status = $cartModel->checkGoodsNumber($value['goods_id'],
                $value['good_count'],$value['goods_attr_ids']);
            if(!$status){
                $this->error = '库存不够';
                return false;
            }
        }
        //3 订单表中写入数据
        $total = $cartModel->getTotle($data);
        $order = array(
          'user_id'=>session('user_id'),
          'addtime'=>time(),
          'total_price'=>$total['price'],
           'name'=>I('post.name'),
           'address'=>I('post.address'),
           'tel'=>I('post.tel'),
        );
        $order_id = $this->add($order);
        $order['id'] = $order_id;
//        dump($order_id);exit();
        //4 商品信息详情
        foreach($data as $value){
            $goods_order[] = array(
                'order_id'=>$order_id,
                'goods_id'=>$value['goods_id'],
                'goods_attr_ids'=>$value['goods_attr_ids'],
                'price'=>$value['goods']['shop_price'],
                'goods_count'=>$value['goods_count']
            );
        }
        M('OrderGoods')->addAll($goods_order);
        //5 减少商品对应的库存
        foreach($data as $value){
            //减少总库存
            M('Goods')->where('id='.$value['goods_id'])->setDec('goods_number',$value['goods_count']);
            //减少单选属性组合的库存
            if($value['goods_attr_ids']){
                $where = 'goods_id='.$value['goods_id'].' and goods_attr_ids='."'"
                    .$value['goods_attr_ids']."'";
                M('GoodsNumber')->where($where)->setDec('goods_number',$value['goods_count']);
            }
        }
        //6 清空购物车数据
        $user_id =session('user_id');
        $cartModel->where('user_id='.$user_id)->delete();
        return $order;
    }

}