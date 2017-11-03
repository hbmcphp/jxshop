<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/4
 * Time: 21:28
 */

namespace Admin\Model;


class RoleRuleModel extends CommonModel
{
    protected $fields = array('id','role_id','rule_id');
    public function disfetch($role_id,$rules)
    {
        //删除当前角色的权限
        $this->where("role_id=$role_id")->delete();
        //最新权限入库
        foreach ($rules as $value){
            $list[] = array(
                'role_id'=>$role_id,
                'rule_id'=>$value,
            );
        }
        $this->addAll($list);
    }
    public function getRules($role_id)
    {
        return $this->where("role_id=$role_id")->select();
    }
}