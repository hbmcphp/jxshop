<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 10:55
 */

namespace Admin\Controller;


class TypeController extends CommonController
{
    //角色添加
    public function add()
    {
        if(IS_GET){
            $this->display();
        }else{
            $model = D('Type');
            $data = $model->create();
            if(!$data){
                $this->error($model->getError());
            }
            $model->add($data);
            $this->success('写入成功');
        }
    }
    //列表显示
    public function index()
    {
            $model = D('Type');
            $data = $model -> listDate();
//            dump($data);
            $this->assign('data',$data);
            $this->display();
    }

    public function dels()
    {
        $type_id = intval(I('get.type_id'));
        if($type_id<=1){
            $this->error('参数错误');
        }
        $res = D('Type')->remove($type_id);
        if($res===false){
            $this->error('删除失败');
        }
        $this->success('删除成功');
    }

    public function edit()
    {
        $model = D('Type');
        if(IS_GET){
            $type_id = intval(I('get.type_id'));
            $info = $model->findOneById($type_id);
            $this->assign('info',$info);
            $this->display();
        }else{
            $data = $model->create();
//            dump($data);
            if(!$data){
                $this->error($model->getError());
            }
            //不能用$role_id 自己写错了
            if($data['id']<=0){
                $this->error('参数错误');
            }
            $model->save($data);
            $this->success('修改成功',U('index'));
        }
    }

    public function disfetch()
    {
        if(IS_GET){
            //获取角色权限
            $role_id = intval(I('get.role_id'));
            if($role_id<=1){
                $this->error('参数错误');
            }
            $hasRules = D('RoleRule')->getRules($role_id);
            //二维数组转一维数组 方便IN标签显示
            foreach($hasRules as $value){
                $hasRulesids[]= $value['rule_id'];
            }
            $this->assign('hasRules',$hasRulesids);
            //获取所有的权限信息
            $RuleModel = D('Rule');
            $rule = $RuleModel ->getCateTree();
            $this->assign('rule',$rule);
            $this->display();
        }else {
            $role_id = intval(I('post.role_id'));
            if ($role_id <= 1) {
                $this->error('参数错误');
            }
            $rules = I('post.rule');
//            dump($rules);die;
            D('RoleRule')->disfetch($role_id, $rules);
            //修改角色权限的时候 需要将当前角色下对应管理员所有文件信息全部删除
            $user_info = M('AdminRole')->where('role_id='.$role_id)-select();
            foreach($user_info as $value){
                S('user_'.$value['admin_id'],null);
            }
            $this->success('操作成功', U('index'));
        }
    }

    //删除超级管理员下所有用户的权限信息
    public function flushAdmin()
    {
        $user = M('AdminRole')->where('role_id=1')->select();
        foreach($user as $value){
            S('user_'.$value['admin_id'],null);
        }
        echo 'ok';
    }
}