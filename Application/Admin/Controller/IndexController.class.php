<?php
namespace Admin\Controller;
use Think\Controller;

class IndexController extends CommonController
{
    public function index()
    {
        $this->display();
    }
    public function top()
    {
        $this->display();
    }
    public function menu()
    {
//        dump($this->user['menus']);
        //权限信息给导航菜单显示
        $this->assign('menus',$this->user['menus']);
        $this->display();
    }
    public function main()
    {
        $this->display();
    }
}