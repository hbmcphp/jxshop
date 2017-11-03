<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/15
 * Time: 12:51
 */

namespace Home\Model;


use Think\Model;

class CommentModel extends Model
{
    protected $fields = array('id','user_id','goods_id','addtime','content','star','good_number');

    public function _before_insert(&$data)
    {
        $data['addtime']=time();

        $data['user_id']=session('user_id');
    }
}