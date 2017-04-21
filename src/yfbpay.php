<?php

namespace Yuxiaoyang\YfbwapPay;
use Yuxiaoyang\YfbwapPay\RSA;
error_reporting(E_ALL & ~E_NOTICE);

/**
 * 类
 */
class yfbpay
{
	function __construct()
    {
        $this->yfbpay();
    }
    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function yfbpay()
    {
    }

    

    /**
     * 生成支付代码
     * @param   array    $order       订单信息
     * @param   array    $payment     支付方式信息
     */
    function get_code($order, $payment)
    {
    	//私钥
    	$prifile1="MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCmlcnbcT+nOWsRwCNMdJdCfGhW1WmYRfBJpUJGQRtuQsJRtRqe1XvTzkn7H2z9x4pRyKA7k9R63ZilYBMIUVgaR4zDGOQqb5O8RrWa/8o/obQH8cZ/be0vd0IXni7gDeVjtaU441tuXQdGpUC4BLuLGM4U8TvNLzPiZxlVi3eJ+wIDAQAB";
    	$prifile=__DIR__ . '/rsa_private_key.pem';
    	//公钥
    	$pubfile=__DIR__ . "/rsa_public_key.pem";
    	
    	$merchantNo=$payment['yfbpay_account'];//交易发起方的商户号
    	$merchantDomain=$_SERVER["HTTP_HOST"];//交易发起方商 户域名
    	$publicKeyIndex=$payment['yfbpay_Pindex'];//公钥索引
    	$signature="";//签名
    	$signAlgorithm="RSA";//签名算法
    	$inputCharset="UTF-8";//编码类型
    	$notifyUrl="http://api.test.suning.com/atinterface/receive.htm";//服务器异步通 知URL
    	$buyerMerchantLoginName=$order["buyerMerchantLoginName"];//买家快速付款登录名
    	$buyerMerchantUserNo=$order["buyerMerchantUserNo"];//买家快速付款会员编号
    	$returnUrl=$order["returnUrl"];//页面跳转同步 通知页面路径
    	$submitTime=date('YmdHis');//提交时间
    	$buyerIp=$_SERVER['REMOTE_ADDR'];//买方客户端IP防钓鱼验证
    	$orders= array();//订单数据集
    	$orders["salerMerchantNo"]=$merchantNo;//卖家商户号
    	$orders["orderType"]="01";//订单类型  01：即时到帐订单  02：担保支付订单
      $orders["outOrderNo"]=$order["order_sn"];//商品订单号
      /* 展示商户订单号 */
      $orders["merchantOrderNo"]=$order["order_sn"];//用于展示商户订单号
      //商品名称的取得
      $mch_name = $order["body"];
        	
    	$orders["goodsName"]=base64_encode($mch_name);//商品名称
    	$orders["goodsType"]="580092";//商品类型-实物商品类
    	$orders["body"]=base64_encode($mch_name);//商品描述
    	//$orders["receiveInfo"]=base64_encode($order['consignee']."|".$order['address']."|".$order['zipcode']."|".$order['tel']);//收货人信息
    	$total_fee = floatval($order['order_amount']) * 100;
    	$orders["orderAmount"]=$total_fee;//订单金额
    	$orders["currency"]="CNY";//币种
    	$orders["orderTime"]=date('YmdHis');//下单时间
      /* 数字签名,按照字母排序 */
      $sigstr="buyerIp=".$buyerIp."&buyerMerchantLoginName=".$buyerMerchantLoginName."&buyerMerchantUserNo=".$buyerMerchantUserNo."&inputCharset=UTF-8"."&merchantDomain=".$merchantDomain."&merchantNo=".$merchantNo."&notifyUrl=".$notifyUrl."&orders="."[".json_encode($orders)."]"."&publicKeyIndex=".$publicKeyIndex."&returnUrl=".$returnUrl."&submitTime=".$submitTime."&version=1.1";
      $sign = strtoupper(md5($sigstr));
      ///////////////////////////////用私钥加密//////////////////////// 
      if(!file_exists($prifile) && !file_exists($pubfile)){
        die('密钥或者公钥的文件路径不正确'); 
      }
          
      $m = new RSA($pubfile, $prifile);
	    $signature = $m->sign($sign);
        /* 交易参数 */
      $parameter = array(
         'version'                =>  "1.1",//版本号
         'merchantNo'             =>  $merchantNo,  //交易发起方的商户号
         'merchantDomain'         =>  $merchantDomain,//交易发起方商 户域名
    	   'publicKeyIndex'         =>  $publicKeyIndex,//公钥索引
    	   'signature'              =>  $signature,//签名
    	   'signAlgorithm'          =>  $signAlgorithm,//签名算法                            
    	   'inputCharset'           =>  $inputCharset,//编码类型
    	   'notifyUrl'              =>  $notifyUrl,//服务器异步通 知URL
    	   'buyerMerchantLoginName' =>  $buyerMerchantLoginName,
    	   'buyerMerchantUserNo'    =>  $buyerMerchantUserNo,
    	   'returnUrl'              =>  $returnUrl,//页面跳转同步 通知页面路径
    	   'submitTime'             =>  $submitTime,//提交时间
    	   'buyerIp'                =>  $buyerIp,//买方客户端IP防钓鱼验证
    	   'orders'                 =>  "[".json_encode($orders)."]"//订单数据集
        );

        $button  = '<br /><form style="text-align:center;" method="post" action="https://wpay.suning.com/epps-pwg/routeGateway/merchant/paymentOrder.htm" style="margin:0px;padding:0px" >';
		//https://wpaypre.cnsuning.com/epps-pwg/routeGateway/merchant/paymentOrder.htm
		//https://wpaysandbox.suning.com/epps-pwg/routeGateway/merchant/paymentOrder.htm
		//$button  .= 'sign==' .$sign;
        foreach ($parameter AS $key=>$val)
        {
            $button  .= "<input type='hidden' name='$key' value='$val' />";
        }
        $button  .= '<input type="submit"  value="' .$GLOBALS['_LANG']['pay_button']. '"  style="display:none;"/></form><br />';
        $button = $button."<script>document.forms[0].submit();</script>";

        return $button;
    }

    /**
     * 响应操作
     */
    function respond_1()
    {
    	 //私钥
    	$prifile=__DIR__ . '/rsa_private_key.pem';
    	//公钥
    	$pubfile=__DIR__ . "/rsa_public_key.pem";
    	
        /*取返回参数*/
        $pay_result     = $_GET['responseCode'];//响应码
        $signAlgorithm         = $_GET['signAlgorithm'];//签名方式
        $signature       = $_GET['signature'];
        $keyIndex      = $_GET['keyIndex'];
        $merchantOrderNos   = $_GET['merchantOrderNos'];
		
        //$order_sn   = $bill_date . str_pad(intval($sp_billno), 5, '0', STR_PAD_LEFT);
        //$log_id = preg_replace('/0*([0-9]*)/', '\1', $sp_billno); //取得支付的log_id
         $log_id = get_order_id_by_snyfb($merchantOrderNos);
        /* 如果pay_result大于0则表示支付失败 */
        if ($pay_result <>"0000")
        {
            return false;
        }else
        {
        	/* 检查数字签名是否正确 */
	        if(!file_exists($prifile) && !file_exists($pubfile)){
	          die('密钥或者公钥的文件路径不正确'); 
	        }
	       
	        $sigstr="keyIndex=".$keyIndex."&merchantOrderNos=".$merchantOrderNos."&responseCode=".$pay_result;
            $sign = strtoupper(md5($sigstr));
            $signature = str_replace(array('-','_'),array('+','/'),$signature);
            $m = new RSA($pubfile, $prifile);
            if(!$m->verify($sign,$signature)){
               return false;
            }
            /* 改变订单状态 */
            order_paid($log_id);
            return true;
        }
    }
	
	function respond()
    {
    	 //私钥
    	$prifile='';
    	//公钥
    	$pubfile=__DIR__ . "/hui_rsa_public_key.pem";
    	
        /*取返回参数*/
        $pay_result     = $_GET['responseCode'];//响应码
        $signAlgorithm         = $_GET['signAlgorithm'];//签名方式
        $signature       = $_GET['signature'];
        $keyIndex      = $_GET['keyIndex'];
        $merchantOrderNos   = $_GET['merchantOrderNos'];

        if ($pay_result <>"0000")
        {
            return false;
        }else
        {
        	/* 检查数字签名是否正确 */
	        if(!file_exists($prifile) && !file_exists($pubfile)){
	          die('密钥或者公钥的文件路径不正确'); 
	        }
	       
	        $sigstr="keyIndex=".$keyIndex."&merchantOrderNos=".$merchantOrderNos."&responseCode=".$pay_result;
            $sign = strtoupper(md5($sigstr));
            $signature = str_replace(array('-','_'),array('+','/'),$signature);
            $m = new RSA($pubfile, $prifile);
            if(!$m->verify($sign,$signature)){
               return false;
            }
            return true;
        }
    }
	
}

?>