<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/4
 * Time: 23:26
 */

namespace Admin\Controller;


use Think\Controller;

class LoginController extends Controller
{
    public function login()
    {
        if(IS_GET){
            $this->display();
        }else{
            $captcha = I('post.captcha');
            $verify = new \Think\Verify();
            $res = $verify->check($captcha);
            if(!$res){
                $this->error('验证码错误');
            }
            $username = I('post.username');
//            dump($username);die;
            $password = I('password');
            $model = D('Admin');
            $res = $model->login($username,$password);
            if(!$res){
                $this->error($model->getError());
            }
            $this->success('登录成功',U('Index/index'));
        }
    }

    public function verify()
    {
        $arr = array('length'=>3);
        $verify = new \Think\Verify($arr);
        $verify->entry();
    }
}