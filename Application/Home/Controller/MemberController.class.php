<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/15
 * Time: 12:17
 */

namespace Home\Controller;


class MemberController extends CommonController
{
    public function __construct()
    {
        parent::__construct();

        $this->checkLogin();

    }

    public function order()
    {
        $user_id = session('user_id');
        $data = D('Order')->where('user_id='.$user_id)->select();
        $this->assign('data',$data);
        $this->display();
    }
    public function pay()
    {

    }

    public function express()
    {
        $order_id = I('get.order_id');
//        dump($order_id);die;
        $info = D('Order')->where('id='.$order_id)->find();
//        dump($info);die;
        //判断如果存在结果 或者是为未发货状态
        if(!$info || $info['order_status']!=2){
            $this->error('参数错误');
        }
        $url= 'http://v.juhe.cn/exp/index?key=1732d4c080f904fd7e05f8fcac57cf67&com='.$info['com'].'&no='.$info['no'];
        $res = file_get_contents($url);
        $res = json_decode($res,true);
        dump($res);
        $this->assign('data',$res);
        $this->display();

    }
}