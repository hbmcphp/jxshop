<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/13
 * Time: 20:02
 */

namespace Home\Controller;


use Think\Think;

class UserController extends CommonController
{
    public function oauth()
    {
        require_once("qq/API/qqConnectAPI.php");
        $qc = new \QC();
        $qc->qq_login();

    }

    public function callback()
    {
        require_once("qq/API/qqConnectAPI.php");
        $qc = new \QC();
        //access_token值
        $access_token = $qc->qq_callback();
        // openid值 判断用户是否是一个用户
        $openid = $qc->get_openid();
        //再次实例化防止网络问题和用户基本信息改变
        $qc = new \QC($access_token,$openid);
        $user = $qc->get_user_info();
        //调用方法实现登录
        $model = D('User');
        $model->qqLogin($user,$openid);
        $this->success('登录成功',U('Index/index'));
        echo <<<EOF
<script type='text/javascript'>
window.opener.location.href='/';
window.close();
</script>
EOF;

    }

    public function sendcode()
    {
        $tel =I('post.tel');
        if(!$tel){
            $this->ajaxReturn(array('status'=>0,'msg'=>'手机号码错误'));
        }
        //根据手机号码验证短信
        $code = rand(1000,9999);
        $res = sendTemplateSMS($tel,array($code,'60'),"1");
        if(!$res){
            $this->ajaxReturn(array('status'=>0,'msg'=>'验证码发送失败'));
        }
        //session保存 过期时间是根据发送时间和有效时间来记录的
        $data = array(
            'code'=>$code,
            'time'=>time(),//当前时间
            'limit'=>3600,//过期时间
        );
        session('telcode',$data);
        $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
    }

    public function test()
    {
       sendemail('374667053@qq.com','测试','测试邮件方法');
    }

    //注册
    public function regist()
    {
        if(IS_GET){
            $this->display();
        }else{
            $username = I('post.username');
            $password = I('post.password');
            $checkcode = I('post.checkcode');
            $tel = I('post.tel');
            $telcode = I('post.telcode');
//            验证码检测
            $obj = new \Think\Verify();
            if(!$obj->check($checkcode)){
                $this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误'));
            }
            if(!$telcode){
                $this->ajaxReturn(array('status'=>0,'msg'=>'手机验证码未输入'));
            }
            $data = session('telcode');

            if(!$data){
                $this->ajaxReturn(array('status'=>0,'msg'=>'未发送手机验证码'));
            }

            if($data['time']+$data['limit']<time()){
                $this->ajaxReturn(array('status'=>0,'msg'=>'验证码过期'));
            }
            if($telcode!=$data['code']){
                $this->ajaxReturn(array('status'=>0,'msg'=>'手机验证码错误'));
            }
            $model = D('User');
            $res = $model->regist($username,$password,$tel);
            if(!$res){
                $this->ajaxReturn(array('status'=>0,'msg'=>$model->getError()));
            }
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
        }
}

    //邮箱注册
    public function registbyemail()
    {
        //echo 11;die;
        if(IS_GET){
            //echo 111;die;
            $this->display();
        }else{
            $username = I('post.username');
            $password = I('post.password');
            $email = I('post.email');
            $model = D('User');
            $res = $model->registbyemail($username,$password,$email);
            if(!$res){
//
                $this->ajaxReturn(array('status'=>0,'msg'=>$model->getError()));
            }
            $link = 'http://jxshop.com'.U('active').'?user_id='.$res['user_id'].'&active_code='.
                $res['active_code'];

            sendemail($email,'商城用户激活邮件',$link);
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            //echo '0';
        }
    }

    public function active()
    {
        $user_id = I('get.user_id');
        $active_code = I('get.active_code');
        $model = D('User');
        $user_info = $model->where('id='.$user_id)->find();

        if(!$user_info){
            $this->error('参数错误');
        }

        if($user_info['status']==1){
            $this->error('用户已经激活');
        }

        if($active_code!=$user_info['active_code']){
            $this->error('激活码错误');
        }
        $model->where('id='.$user_id)->setField('status',1);
        $this->success('激活成功',U('login'));
    }

    public function login()
    {
        if(IS_GET){
            $this->display();
        }else{
            $username = I('post.username');
            $password = I('post.password');
            $checkcode = I('post.checkcode');
//            验证码检测
//            $obj = new \Think\Verify();
//            if(!$obj->check($checkcode)){
//                $this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误'));
//            }
            $model = D('User');

            $res = $model->login($username,$password);
            if(!$res){
                $this->ajaxReturn(array('status'=>0,'msg'=>$model->getError()));
            }
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
        }
    }

    public function logout()
    {
        session('user',null);
        session('user_id',null);
        $this->redirect('/');
    }

    public function code()
    {
        $obj = new \Think\Verify();
        $obj->entry();
    }
}