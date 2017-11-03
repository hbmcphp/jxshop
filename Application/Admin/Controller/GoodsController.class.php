<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 16:13
 */

namespace Admin\Controller;


class GoodsController extends CommonController
{
    public function showAttr()
    {
        $type_id = intval(I('post.type_id'));
        if($type_id<=0){
            echo '参数错误';die;
        }
        $data = D('Attribute')->where('type_id='.$type_id)->select();
//        dump($data);die;
        foreach($data as $key=>$value){
            if($value['attr_input_type']==2){
                //处理属性列表录入 是为了方便后面使用下拉列表 因此explode转成数组
                $data[$key]['attr_value']=explode(',',$value['attr_value']);
            }
        }
        $this->assign('data',$data);
        $this->display();
    }

    public function add()
    {
        if(IS_GET){
            //获取所有的类型
            $type=D('Type')->select();
            $this->assign('type',$type);
            //获取所有分类
            $cate = D('Category')->getCateTree();
            $this->assign('cate',$cate);
            $this->display();
            exit();
        }
//        dump(I('post.attr'));die;
        $model = D('Goods');
//        dump($model);die();
        $data = $model->create();
        if(!$data){
            $this->error($model->getError());
        }
        $goods_id = $model->add($data);
        if(!$goods_id){
            $this->error($model->getError());
        }
        $this->success('添加成功');
    }

    public function index()
    {
        //获取分类信息
        $model = D('Category');
        $cate = $model->getCateTree();
        $this->assign('cate',$cate);

        $model = D('Goods');
        $data = $model->listData();
//        dump($data);die();
        $this->assign('data',$data);
        $this->display();
    }
    //回收站
    public function trash()
    {
        //获取分类信息
        $model = D('Category');
        $cate = $model->getCateTree();
        $this->assign('cate',$cate);

        $model = D('Goods');
        $data = $model->listData(0);
//        dump($data);die();
        $this->assign('data',$data);
        $this->display();
    }
    //还原
    public function recover()
    {
        $goods_id = intval(I('get.goods_id'));
        $model = D('Goods');
        $res = $model->setStatus($goods_id,1);
        if(!$res){
            $this->error('还原失败');
        }
        $this->success('还原成功');
    }
    //伪删除
    public function dels()
    {
        $goods_id = intval(I('get.goods_id'));
        if($goods_id<=0){
            $this->error('参数错误');
        }
        $model = D('Goods');
        $res = $model->setStatus($goods_id);
        if($res===false){
            $this->error('删除失败');
        }
        $this->success('删除成功');
    }
    //彻底删除
    public function remove()
    {
        $goods_id = intval(I('get.goods_id'));
        if($goods_id<=0){
            $this->error('参数错误');
        }
        $model = D('Goods');
        $res = $model->remove($goods_id);
        if($res===false){
            $this->error('删除失败');
        }
        $this->success('删除成功');
    }
    public function edit()
    {
        //判断是否是GET提交
        if(IS_GET)
        {
            $goods_id = intval(I('get.goods_id'));
            $model = D('Goods');
            $info = $model->findOneById($goods_id);
            if(!$info){
                $this->error('参数错误');
            }
            //分类显示
            $cate = D('Category')->getCateTree();
            $this->assign('cate',$cate);
            //扩展分类获取 GoodsCate 会自动转化为jx_Goods_Cate
            $ext_cate_ids = M('GoodsCate')->where("goods_id=$goods_id")->select();
//            dump($ext_cate_ids);
            //解决没有扩展分类的情况
            if(!$ext_cate_ids){
                $ext_cate_ids=array(
                    array('msg'=>'no data'),
                );
            }
            $this->assign('ext_cate_ids',$ext_cate_ids);
            //对商品描述进行反转义
            $info['goods_body'] = htmlspecialchars_decode($info['goods_body']);
            $this->assign('info',$info);
            //获取所有的类型
            $type = D('Type')->select();
            $this->assign('type',$type);
            //根据商品ID获得当前商品的属性及属性值
            $goodsAttrModel = M('GoodsAttr');
            $attr = $goodsAttrModel->alias('a')->field('a.*,b.attr_name,b.attr_type,b.attr_input_type
            ,b.attr_value')->join('left join jx_attribute b on a.attr_id=b.id')->where(
                'a.goods_id='.$goods_id)->select();
            foreach($attr as $key =>$value){
                if($value['attr_input_type']==2){
                    $attr[$key]['attr_value']=explode(',',$value['attr_value']);
                }
            }
//            dump($attr);die();
            foreach($attr as $key=>$value){
                $attr_list[$value['attr_id']][]=$value;
            }
//            dump($attr_list);die();
            $this->assign('attr',$attr_list);
            //获取商品对应的相册图片信息
            $goods_img_list = M('GoodsImg')->where('goods_id='.$goods_id)->select();
//            dump($goods_img_list);die;
            $this->assign('goods_img_list',$goods_img_list);
            $this->display();
        }else{
//            dump(I('post.attr'));die;
            $model = D('Goods');
            $data = $model->create();
//            dump($data);
            if(!$data){
                $this->error($model->getError());
            }
            $res = $model->update($data);
            if($res===false){
                $this->error($model->getError());
            }
            $this->success('修改成功',U('index'));
        }
    }
    //实现相册图片删除
    public function delImg()
    {
        //接收图片id
        $img_id = intval(I('post.img_id'));
        $ImgModel = M('GoodsImg');
        $info = $ImgModel->where('id='.$img_id)->find();
        if($img_id<=0){
            $this->ajaxReturn(array('status'=>0,'msg'=>'参数错误'));
        }
        unlink($info['goods_img']);
        unlink($info['goods_thumb']);
        //将数据在数据库中删除
        $ImgModel->where('id='.$img_id)->delete();
        $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
    }

    //商品库存设置
    public function setNumber()
    {
        if(IS_GET){
            $goods_id = intval(I('get.goods_id'));
//            dump($goods_id);die();
            //通过商品ID 获取单选属性值和属性信息
            $GoodsAttrModel = D('GoodsAttr');
//            dump($GoodsAttrMoedl);die;
            $attr = $GoodsAttrModel->getSigleAttr($goods_id);
            if(!$attr){
                //没单选存库信息获取 一维数组
                $info = D('Goods')->where('id='.$goods_id)->find();
                $this->assign('info',$info);
                $this->display('nosigle');
//                dump($info);die;
                //加退出 不然还要显示其他内容
                exit();
            }
//            dump($attr);die;
            //单选存库信息获取 二维数组
            $info = M('GoodsNumber')->where('goods_id='.$goods_id)->select();
            if(!$info){
                $info = array('goods_number'=>0);
            }
//            dump($info);
            $this->assign('info',$info);
            $this->assign('attr',$attr);
            $this->display();
        }else{
            //接收过来的arrt 是以属性为下标的一组组数据
            $attr = I('post.attr');
//            dump($attr);die;
            $goods_number = I('post.goods_number');
            $goods_id = I('post.goods_id');
//            dump($goods_id);die;
            if(!$attr){
                D('Goods')->where('id='.$goods_id)->setField('goods_number',$goods_number);
                exit;
            }

            //以$goods_number作为循环  主要以他的下标当成循环 主要以他的下标当成循环
            // 每次循环提交都是作为同一行不同属性的数据（想想第一行数据的提交）
            foreach($goods_number as $key=>$value){
                $tmp = array();
                foreach($attr as $k=>$v){
                    $tmp[] = $v[$key];
                }
//                dump($tmp);die;
                //去重 3 4 和 4 3 这里是商品属性表的ID  代表着商品的属性值 为了防止遍历出现特殊情况
                //才去使用sort
                sort($tmp);
                //转格式方便入库
                $goods_attr_ids = implode(',',$tmp);
                if(in_array($goods_attr_ids,$has)){
                    unset($goods_number[$key]);
                    //删除当前数量 跳出这层属性值循环
                    continue;
                }
                $has[]=$goods_attr_ids;
                $list[] = array(
                'goods_id'=>$goods_id,
                'goods_attr_ids'=>$goods_attr_ids,
                'goods_number'=>$value,
                );
            }
//            dump($list);die();
            //入库
            M('GoodsNumber')->where('goods_id='.$goods_id)->delete();
            M('GoodsNumber')->addAll($list);
            //计算总库存
            $goods_count = array_sum($goods_number);
            D('Goods')->where('id='.$goods_id)->setField('goods_number',$goods_count);
        }
    }
}