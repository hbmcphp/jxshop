<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25
 * Time: 10:18
 */

namespace Admin\Controller;


class CategoryController extends CommonController
{
    public function add()
    {
        if(IS_GET){
            //获取格式化分类信息
            $model = D('Category');
            $cate = $model->getCateTree();
            $this->assign('cate',$cate);
            $this->display();
        }else{
            //数据入库
            $model = D('Category');
            //创建数据
            $data = $model->create();
            if(!$data){
                $this->error($model->getError());
            }
            $insertid = $model ->add($data);
            if(!$insertid){
                $this->error('数据添加失败');
            }
            $this->success('添加成功');
        }
    }

    public function index()
    {
        $model = D('Category');
        $list = $model->getCateTree();
        $this->assign('list',$list);
        $this->display();
    }


    public function dels()
    {
        $id = intval(I('get.id'));
        if($id<0){
            $this->error('参数不对');
        }
        $model = D('Category');
        $res = $model->dels($id);
        if($res===false){
            $this->error('删除失败');
        }
        $this->success('删除成功');
    }

    public function edit()
    {
        if(IS_GET){
            //显示要编辑的分类
            $id = intval(I('get.id'));
            $model = D('Category');
            $info = $model->findOneById($id);
            $this->assign('info',$info);
            //获取所有分类信息
            $cate = $model->getCateTree($id);
            $this->assign('cate',$cate);
            $this->display();
        }else{
            $model = D('Category');
            $data = $model->create();
//            $res = $model->update($data);
            $res =$model->save($data);
            if($res===false){
                $this->error($model->getError());
            }
            $this->success('修改成功');
        }
    }
}