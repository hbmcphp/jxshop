<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/16
 * Time: 21:55
 */

namespace Admin\Controller;


class OrderController extends CommonController
{
    public function index()
    {
        $data = M('Order')->select();
        $this->assign('data',$data);
        $this->display();
    }

    public function send()
    {

        if(IS_GET){
            $order_id = I('get.order_id');
//            dump($order_id);die;
            $info = M('Order')->alias('a')->field('a.*,b.username')->join('left join jx_user b
            on a.user_id = b.id')->where('a.id='.$order_id)->find();

            $this->assign('info',$info);
            $this->display();
        }else{
            $order_id = I('post.id');
//            dump($order_id);die;
            $info = M('Order')->where('id='.$order_id)->find();
            if(!$info || $info['pay_status']!=1){
                $this->error('参数不对');
            }
            $data = array(
                'com'=>I('com'),
                'no'=>I('no'),
                'order_status'=>2
            );
            M('Order')->where('id='.$order_id)->save($data);
            $this->success('发货成功');

        }
    }
}