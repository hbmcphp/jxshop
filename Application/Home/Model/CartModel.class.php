<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/14
 * Time: 11:28
 */

namespace Home\Model;


use Think\Model;

class CartModel extends Model
{
    protected $fields = array('id','user_id','goods_id','goods_attr_ids','goods_count');

    public function addCart($goods_id,$goods_count,$attr)
    {
        //根据属性ID值 来从小到大排序
        sort($attr);
        //转字符串方便入库
        $goods_attr_ids = $attr?implode(',',$attr):'';
        //检查库存
        $res = $this->checkGoodsNumber($goods_id,$goods_count,$goods_attr_ids);
        if(!$res){
            $this->error = '库存不足';
            return false;
        }
        //获取用户ID
        $user_id = session('user_id');
        if($user_id){
            $map = array(
                'user_id'=>$user_id,
                'goods_id'=>$goods_id,
                'goods_attr_ids'=>$goods_attr_ids
            );
            $info = $this->where($map)->find();
            if($info){
                //存在数据  需要加上新的数量
                $this->where($map)->setField('goods_count',$goods_count+$info['goods_count']);
            }else{
                $map['goods_count']=$goods_count;
                $this->add($map);
            }
        }else{
            $cart = unserialize(cookie('cart'));
            $key = $goods_id.'-'.$goods_attr_ids;
            if(array_key_exists($key,$cart)){
                $cart[$key]+=$goods_count;
            }else{
                $cart[$key]=$goods_count;
            }
            cookie('cart',serialize($cart));
        }
        return true;
    }

    public function checkGoodsNumber($goods_id,$goods_count,$goods_attr_ids)
    {
        //检查总库存
        $goods = D('Admin/Goods')->where("id=$goods_id")->find();
        if($goods['goods_number']<$goods_count){
            return false;
        }
        //检查对应库存的库存量
        if($goods_attr_ids){
            $where ="goods_id=$goods_id and goods_attr_ids='$goods_attr_ids'";
            $number = M('GoodsNumber')->where($where)->find();
            if(!$number||$number['goods_number']<$goods_count){
                return false;
            }
        }
        return true;
    }

    //cookie数据转移
    public function cookie2db()
    {
        $cart = unserialize(cookie('cart'));
        $user_id = session('user_id');
        if(!$user_id){
            return false;
        }
        foreach($cart as $key=>$value){
            $tmp = explode('-',$key);
            $map = array(
                'user_id'=>$user_id,
                'goods_id'=>$tmp[0],
                'goods_attr_ids'=>$tmp[1],
            );
            $info = $this->where($map)->find();
            if($info){
                $this->where($map)->setField('goods_count',$value+$info['goods_count']);
            }else{
                $map['goods_count']=$value;
                $this->add($map);
            }
        }
        cookie('cart',null);
    }
    //获取具体商品信息
    public function getList()
    {
        $user_id = session('user_id');
        if($user_id){
            //用户登录情况
            $data = $this->where('user_id='.$user_id)->select();
        }else{
            //cookie情况
            $cart = unserialize(cookie('cart'));
            foreach($cart as $key =>$value){
                $tmp = explode('-',$key);
                $data[]=array(
                    'goods_id'=>$tmp[0],
                    'goods_attr_ids'=>$tmp[1],
                    'goods_count'=>$value
                );
            }
        }
        //根据商品ID找到具体的商品
        $goodsModel = D('Admin/Goods');
        foreach($data as $key =>$value){
            $goods = $goodsModel->where('id='.$value['goods_id'])->find();
            //根据商品是否处于促销价格设置
            if($goods['cx_price']>0 && $goods['start']<time() && $goods['end']>time()){
                $goods['shop_price'] = $goods['cx_price'];
            }
            $data[$key]['goods']=$goods;
            //根据ID获取商品的属性
            if($value['goods_attr_ids']){
                $attr = M('GoodsAttr')->alias('a')->join('left join jx_attribute b on a.attr_id
                =b.id')->field('a.attr_values,b.attr_name')->where("a.id in ({$value['goods_attr_ids']})")
                ->select();
                $data[$key]['attr']=$attr;
            }
        }
        return $data;
    }

    //实现购物车总金额计算
    public function getTotle($data)
    {
        $count = $price =0;
        foreach($data as $key =>$value){
            $count+=$value['goods_count'];
            $price+=$value['goods_count']*$value['goods']['shop_price'];
        }
        return array('count'=>$count,'price'=>$price);
    }

    public function dels($goods_id,$goods_attr_ids)
    {
        $goods_attr_ids = $goods_attr_ids?$goods_attr_ids:'';


        $user_id =session('user_id');
        if($user_id){
            $where = "user_id=$user_id and goods_id=$goods_id and goods_attr_ids='$goods_attr_ids'";
//            dump($where);die;
            $this->where($where)->delete();
        }else{
            $cart = unserialize(cookie('cart'));
            $key = $goods_id.'-'.$goods_attr_ids;
            //根据goods_id删除了当前商品
            unset($cart[$key]);
            //更新最新数据
            cookie('cart',serialize($cart));
        }
    }

    public function updateCount($goods_id,$goods_attr_ids,$goods_count)
    {
        //当$goods_count小于等于0  不进行更新
        if($goods_count<=0){
            return false;
        }
        $goods_attr_ids = $goods_attr_ids?$goods_attr_ids:'';

        $user_id =session('user_id');
        if($user_id){
            $where = "user_id=$user_id and goods_id=$goods_id and goods_attr_ids='$goods_attr_ids'";
//            dump($where);die;
            $this->where($where)->setField('goods_count',$goods_count);
        }else{
            $cart = unserialize(cookie('cart'));
            $key = $goods_id.'-'.$goods_attr_ids;
            $cart[$key]=$goods_count;
            //更新最新数据
            cookie('cart',serialize($cart));
        }
    }
}