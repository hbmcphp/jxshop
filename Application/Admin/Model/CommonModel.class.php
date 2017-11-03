<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 11:43
 */

namespace Admin\Model;


use Think\Model;

class CommonModel extends Model
{
    public function findOneById($id)
    {
        return $this->where('id='.$id)->find();
    }
}