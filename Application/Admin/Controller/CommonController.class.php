<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 10:16
 */

namespace Admin\Controller;


use Think\Controller;

class CommonController extends Controller
{
    //标识是否进行权限认证
    public $is_check_rule = true;
    //保存用户的信息 包括基本信息 角色ID 权限信息
    public $user = array();

//    public function  __construct()
//    {
//        parent::__construct();
//        $admin = cookie('admin');
//        if(!$admin){
//            $this->error('没有登录',U('Login/login'));
//        }
        
//        //获取用户对应文件的权限信息
//        $this->user = S('user_'.$admin['id']);
//        //当没有获得用户权限信息时
//        if(!$this->user) {
////            echo 'mysql';测试
//            //将用户信息保存在属性中
//            $this->user = $admin;
//            //根据用户ID  来获取角色ID
//            $role_info = M('AdminRole')->where('admin_id=' . $admin['id'])->find();
//            //将角色ID 存储到用户信息中
//            $this->user['role_id'] = $role_info['role_id'];
//
//            $RoleModel = D('Rule');
//            if ($role_info['role_id'] == 1) {
//                //超级管理员
//                $this->is_check_rule = false;//不验证权限
//                $rule_list = $RoleModel->select();
//            } else {
//                $rules = D('RoleRule')->getRules($role_info['role_id']);
//                //权限ID 转化了一维数组
//                foreach ($rules as $value) {
//                    $rules_ids[] = $value['rule_id'];
//                }
//                //转化为字符串 用in得到对应的rule权限信息
//                $rules_ids = implode(',', $rules_ids);
//                $rule_list = $RoleModel->where("id in ($rules_ids)")->select();
//            }
//            //对应二维数组的权限转化为一维数组保存到user中
//            foreach ($rule_list as $value) {
//                $this->user['rules'][] = strtolower($value['module_name'] . '/' . $value['controller_name']
//                    . '/' . $value['action_name']);
//                //是否菜单显示
//                if ($value['is_show'] == 1) {
//                    $this->user['menus'][] = $value;
//                }
//            }
//            //结果写入文件中
//            S('user_'.$admin['id'],$this->user);
//        }
//
////        dump($this->user);
//        //超级管理不进行判断
//        if($this->user['role_id']==1){
//            $this->is_check_rule=false;
//        }
//        if($this->is_check_rule){
//            //增加默认具备的访问权限
//            $this->user['rules'][] = 'admin/index/index';
//            $this->user['rules'][] = 'admin/index/top';
//            $this->user['rules'][] = 'admin/index/menu';
//            $this->user['rules'][] = 'admin/index/main';
//            //当前普通用户的的URL地址
//            $action = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
//            if(in_array($action,$this->user['rules'])){
//                if(IS_AJAX){
//                    $this->ajaxRetrun(array('status'=>0,'msg'=>'没有权限'));
//                }else{
//                    echo '没有权限';exit();
////                    $this->error('没有权限');
//                }
//            }
//        }
//    }

}