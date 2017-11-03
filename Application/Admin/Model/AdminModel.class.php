<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/3
 * Time: 20:37
 */

namespace Admin\Model;


class AdminModel extends CommonModel
{
    protected $fields = array('id','username','password');
    protected $_validate = array(
        array('username','require','用户名必须填写'),
        array('username','','角色名重复',1,'unique'),
        array('password','require','密码必须填写')
    );
    protected $_auto = array(
        array('password','md5',3,'function')
    );

    //钩子函数来实现中间表入库
    protected function _after_insert($data)
    {
        $admin_role = array(
            'admin_id'=>$data['id'],
            'role_id'=>I('post.role_id')
        );
        M('AdminRole')->add($admin_role);
//        dump($data);
    }

    public function listDate()
    {
        $pagesize =3;
        $count = $this->count();
        $page = new \Think\Page($count,$pagesize);
        $show = $page->show();
        $p = intval(I('get.p'));
        //分页连贯操作
        $list = $this->alias('a')->field('a.*,c.role_name')->join('left join jx_admin_role  b
        on a.id=b.admin_id')->join('left join jx_role as c on b.role_id = c.id')->page($p,$pagesize)
        ->select();
        return array('pageStr'=>$show,'list'=>$list);
    }

    public function remove($admin_id)
    {
        //1开启事物
        $this->startTrans();
        //2删除用户信息
        $userStatus = $this->where("id=$admin_id")->delete();
        if(!$userStatus){
            $this->rollback();
            return false;
        }
        //3删除角色信息
        $roleStatus = M('AdminRole')->where("admin_id = $admin_id")->delete();
        if(!$roleStatus){
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    public function findOne($admin_id)
    {
        return $this->alias('a')->field('a.*,b.role_id')->join('left join jx_admin_role b on
        a.id=b.admin_id')->where("a.id=$admin_id")->find();
    }

    public function update($data)
    {
        $role_id = intval(I('post.role_id'));
        //修改用户信息
        $this->save($data);
        //修改角色信息
        M('AdminRole')->where('admin_id='.$data['id'])->save(array('role_id'=>$role_id));
    }

    public function login($username,$password)
    {
        $userinfo = $this->where("username='$username'")->find();
        if(!$userinfo){
            $this->error='用户名不存在';
            return false;
        }
        if($userinfo['password']!=md5($password)){
            $this->error='密码错误';
            return false;
        }
        cookie('admin',$userinfo);
        return true;
    }
}