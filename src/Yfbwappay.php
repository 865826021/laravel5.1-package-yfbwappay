<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/15
 * Time: 16:27
 */

namespace Yuxiaoyang\YfbwapPay;

use Yuxiaoyang\YfbwapPay\yfbpay;

class YfbwapPay {

    public $pay;
    public function __construct()
    {
        $this->pay = new yfbpay();
    }
    /*
     * 传递$params参数支付
     */
    public function pay($params)
    {
        //根据参数获取订单号,订单名称,订单说明,订单金额
        //$out_trade_no	= rand(1000000000,9999999999);		//请与贵网站订单系统中的唯一订单号匹配
        $out_trade_no	= $params["out_trade_no"];
        //$subject		= "易付宝PC在线支付";		//订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
        $subject		= $params["subject"];
        //$body			= "订单详细";		//订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
        $body			= $params["body"];
        //$total_fee	= "0.01";		//订单总金额，显示在支付宝收银台里的“应付总额”里
        $total_fee		= $params["total_fee"];
        //$returnUrl      = "http://www.***.com/yipcpay/payReturn.php";                 //页面跳转同步 通知页面路径
        $returnUrl      = $params["returnUrl"];

        error_reporting(E_ALL & ~E_NOTICE);

        $order= array();

        $order["order_sn"] = $out_trade_no;
        $order["goodsName"] = $out_trade_no."易付宝pc";
        $order['order_amount'] = $total_fee;
        $order["body"] = $out_trade_no."易付宝pc在线支付";

        $order["buyerMerchantLoginName"] = $_POST['buyerMerchantLoginName'];
        $order["buyerMerchantUserNo"] = $_POST['buyerMerchantUserNo'];

        $order["returnUrl"] = $returnUrl;

        $payment = array();
        $payment['yfbpay_account'] = "********";
        $payment['yfbpay_Pindex'] = "****";

        //建立请求
        $html_text = $this->pay->get_code($order, $payment);
        echo $html_text;
    }

    /*
    * 回调参数验签
    */
    public function payReturn($params)
    {
        error_reporting(E_ALL & ~E_NOTICE);
        $responseCode = $params['responseCode'];
        $signAlgorithm = $params['signAlgorithm'];//签名方式
        $keyIndex = $params['keyIndex'];
        $merchantOrderNos = $params['merchantOrderNos'];
        $signature = $params['signature'];


        //验证签名
        $result = $this->pay->respond();

        if($result)
        {
            //echo '支付成功';
            /*
            修改订单状态
            */
            //执行完成后的跳转操作
            echo 'SUCCESS';
            //header("location:http://www.***.com/index.php?pc=order");
        }
        else
        {
            echo '验签失败';
        }
    }

}