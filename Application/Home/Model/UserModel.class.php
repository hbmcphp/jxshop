<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/13
 * Time: 20:59
 */

namespace Home\Model;

use Think\Model;


class UserModel extends Model
{
    //字段入库
    protected $fields = array('id','username','password','salt','tel','status','active_code','email','active_code','openid');

    //实现用户的信息入库
    public function regist($username,$password,$tel)
    {
        //检查用户名是否可用
        $info = $this->where("username = '$username'")->find();
        if($info){
            $this->error='用户名重复';
            return false;
        }
        //检查电话号码
        $infotel = $this->where('tel ='.$tel)->find();
        if($infotel){
            $this->error='电话重复';
            return false;
        }

        //生成盐
        $salt=rand(100000,999999);
        //生成双重MD5之后的密码
        $db_password= md5(md5($password).$salt);
        $data=array(
            'username'=>$username,
            'password' =>$db_password,
            'salt'=>$salt,
            'tel'=>$tel,
            'status'=>'1',
        );
        $user_id = $this->add($data);
        $data['user_id']=$user_id;
        return $data;
    }
    //实现邮箱注册
    public function registbyemail($username,$password,$email)
    {
        //检查用户名是否可用
        $info = $this->where("username = '$username'")->find();
        if($info){
            $this->error='用户名重复';
            return false;
        }
        //检查电话号码
        $infoemail = $this->where("email = '$email'")->find();
        if($infoemail){
            $this->error='邮箱重复';
            return false;
        }

        //生成盐
        $salt=rand(100000,999999);
        //生成双重MD5之后的密码
        $db_password= md5(md5($password).$salt);
        $data=array(
            'username'=>$username,
            'password' =>$db_password,
            'salt'=>$salt,
            'email'=>$email,
            'status'=>'0',
            'active_code'=>uniqid(),
        );
        $user_id = $this->add($data);
        $data['user_id']=$user_id;
        return $data;
    }

    public function login($username,$password)
    {
        $info = $this->where("username='$username'")->find();
        if(!$info){
            $this->error = '用户名不存在';
            return false;
        }

        if($info['status']==0){
            $this->error = '用户没有激活';
            return false;
        }
        //双重MD5密码比对
        $pwd = md5(md5($password).$info['salt']);
        if($pwd!=$info['password']){
            $this->error = '密码不对';
            return false;
        }
        //保存用户登录状态
        session('user',$info);
        session('user_id',$info['id']);
        //实现购物车中cookie购物车数据转移
        D('Cart')->cookie2db();
        return true;
    }

    public function qqLogin($user,$openid)
    {
        $data = $this->where("openid='$openid'")->find();
        if(!$data){
            $salt = rand(10000,99999);
            $username = 'qquser'.rand(1000,9999).'_'.$user['nickname'];
            $db_password = md5(md5('123456').$salt);
            $data = array(
                'username'=>$username,
                'password'=>$db_password,
                'salt'=>$salt,
                'status'=>1,
                'openid'=>$openid,
            );
            //数据入库 同时设置id
            $data['id']=$this->add($data);
            //保存session数据 同时cookied数据进购物车
        }
        session('user_id',$data['id']);
        session('user',$data);
        D('Cart')->cookie2db();
    }
}