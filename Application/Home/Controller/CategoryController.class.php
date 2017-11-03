<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 19:57
 */

namespace Home\Controller;


class CategoryController extends CommonController
{
    public function index()
    {
        //控制是否展开分类
        $this->display();
    }
}