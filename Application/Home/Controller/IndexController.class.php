<?php
namespace Home\Controller;

class IndexController extends CommonController {
    public function index(){

        $this->assign('is_show',1);
        //获取商品热卖信息
        $goodsModel = D('Admin/Goods');
        $hot = $goodsModel->getRecGoods('is_hot');
        //获取商品推荐信息
        $rec = $goodsModel->getRecGoods('is_rec');
        //获取商品新品信息
        $new = $goodsModel->getRecGoods('is_new');
//        dump($new);die;
        //获取商品促销信息
        $crazy = $goodsModel->getCrazyGoods();
        //获取楼层
        $floor = D('Admin/Category')->getFloor();

        $this->assign('hot',$hot);
        $this->assign('rec',$rec);
        $this->assign('new',$new);
        $this->assign('crazy',$crazy);
        $this->assign('floor',$floor);

        $this->display();
    }

    public function testUrl()
    {

    }
}