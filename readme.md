

### Laravel 5.1 易付宝WAP端支付 扩展使用教程

### 用法

```
composer require yuxiaoyang/yfbwappay
```

或者在你的 `composer.json` 的 require 部分中添加:
```json
 "yuxiaoyang/yfbwappay": "~1.0"
```

下载完毕之后,直接配置 `config/app.php` 的 `providers`:

```php
//Illuminate\Hashing\HashServiceProvider::class,

Yuxiaoyang\YfbwapPay\YfbwapPayProvider::class,
```
控制器中使用 `YfbwapPayController.php` :


```php

<?php


use \Yuxiaoyang\YfbwapPay\YfbwapPay;

class YfbwapPayController extends Controller
{
    public $yfbwappay;

    //易付宝PC支付
    public function yfbwappay()
    {
        //创建示例对象
        $this->yfbwappay = new yfbwappay();
        $params["out_trade_no"] = rand(1000000000,9999999999);
        $params["subject"] = "易付宝PC在线支付";
        $params["body"] = "订单详细";
        $params["total_fee"] = "0.01";
        $params["returnUrl"] = "http://www.***.com/yiwappayReturn";
        $data = $this->yfbwappay->pay($params);
        return $data;
    }

    //易付宝PC支付回调验签
    public function yfbwappayReturn(Request $request)
    {
        //创建示例对象
        $this->yfbwappay = new yfbwappay();
        $params['responseCode'] = Input::get('responseCode');
        $params['signAlgorithm'] = Input::get('signAlgorithm');//签名方式
        $params['keyIndex'] = Input::get('keyIndex');
        $params['merchantOrderNos'] = Input::get('merchantOrderNos');
        $params['signature'] = Input::get('signature');
        $data = $this->yfbwappay->payReturn($params);
        return $data;
    }

}
