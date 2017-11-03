<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 11:04
 */

namespace Admin\Model;


class RoleModel extends CommonModel
{
    protected $fields = array('id','role_name');
    protected $_validate = array(
        array('role_name','require','角色名称必须填写'),
        array('role_name','','角色名重复',1,'unique'),
    );

    public function listDate()
    {
        $pagesize =3;
        $count = $this->count();
        $page = new \Think\Page($count,$pagesize);
        $show = $page->show();
        $p = intval(I('get.p'));
        //分页连贯操作
        $list = $this->page($p,$pagesize)->select();
        return array('pageStr'=>$show,'list'=>$list);
    }

    public function remove($role_id)
    {
        return $this->where("id=$role_id")->delete();
    }
}