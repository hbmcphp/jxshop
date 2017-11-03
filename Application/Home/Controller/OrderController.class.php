<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/14
 * Time: 19:16
 */

namespace Home\Controller;


class OrderController extends  CommonController
{
    //显示出结算的页面
    public function check()
    {
        //判断是否登录
        $this->checkLogin();
        $model = D('Cart');
        //获取购物车具体信息
        $data = $model->getList();
        //计算当前购物车总金额
        $total = $model->getTotle($data);

        $this->assign('data',$data);
        $this->assign('total',$total);

        $this->display();
    }

    //实现用户下单操作
    public function order()
    {
        $model = D('Order');
        $res = $model->order();
        if(!$res){
            $this->error($model->getError());
        }

        require_once '../pay/config.php';
        require_once '../pay/pagepay/service/AlipayTradeService.php';
        require_once '../pay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $res['id'];

        //订单名称，必填
        $subject = 'test';

        //付款金额，必填
        $total_amount = 100;

        //商品描述，可空
        $body = 'test-desc';

        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        //输出表单
        var_dump($response);
    }

    //实现用户的继续支付
    public function pay()
    {
        $order_id = intval(I('get.order_id'));
        $model = D('Order');
        $res = $model->where('id='.$order_id)->find();
        if(!$res){
            $this->error('参数错误');
        }
        if($res['pay_status']==1){
            $this->error('已经支付了该订单');
        }

//        require_once '../pay/config.php';
//        require_once '../pay/pagepay/service/AlipayTradeService.php';
//        require_once '../pay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';
        Vendor('pay.config');
        Vendor('pay.pagepay.service.AlipayTradeService');
        Vendor('pay.pagepay.buildermodel.AlipayTradePagePayContentBuilder');


        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $res['id'];

        //订单名称，必填
        $subject = 'test';

        //付款金额，必填
        $total_amount = 100;

        //商品描述，可空
        $body = 'test-desc';

        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        //输出表单
        var_dump($response);
    }

    //实现用户付款之后回跳
    public function returnUrl()
    {
        require_once("../pay/config.php");
        require_once '../pay/pagepay/service/AlipayTradeService.php';

        $arr=$_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);

        if($result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);

            //支付宝交易号
            $trade_no = htmlspecialchars($_GET['trade_no']);
            //根据自己的业务逻辑进行代码修改
            //设置订单为已支付状态
            D('Order')->where('id='.$out_trade_no)->setField('pay_status',1);

            echo "验证成功<br />支付宝交易号：".$trade_no;

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "验证失败";
        }
    }
}