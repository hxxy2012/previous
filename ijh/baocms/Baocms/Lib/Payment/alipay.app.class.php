<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class alipay {

    //消息验证地址
    private $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    private $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
    public function getCode($logs, $cfg=array()) {

        //$setting = $cfg['setting'];
        //$log = D('Paymentlogs')->find($logs['logs_id']);
        $parameter = array(
            'partner'       => $cfg['alipay_app_partner'],
            'seller_id'     => $cfg['alipay_app_seller'],
            'out_trade_no'  => $logs['logs_id'],
            'subject'       => $logs['subject'],
            'body'          => $logs['subject'],
            'total_fee'     => sprintf("%01.2f", $logs['logs_amount']),
            'notify_url'    => __HOST__ . U( 'appv2/payment/respond', array('code' => 'alipay')),
            'service'       => 'mobile.securitypay.pay',
            'payment_type'  => '1',
            '_input_charset'=> 'utf-8',
        );
        $signstr = $this->_build_query($parameter, false, true);
        $parameter['sign'] = urlencode($this->rsaSign($signstr, $cfg));
        $parameter['signstr'] = $signstr;
        $parameter['sign_type'] = 'RSA';
        baolog('alipay.app.getcode', array($parameter, $signstr, $cfg));
        return $parameter;
    }


    //notify_url
    public function respond() {

        //WAIT_SELLER_SEND_GOODS → WAIT_BUYER_CONFIRM_GOODS
        //WAIT_BUYER_PAY,WAIT_SELLER_SEND_GOODS,WAIT_BUYER_CONFIRM_GOODS,TRADE_FINISHED,TRADE_SUCCESS,TRADE_CLOSED
        $_allow_status = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_FINISHED', 'TRADE_SUCCESS');
        if(empty($_POST)){//判断POST来的数组是否为空
            return false;
        }else if(!in_array($_POST['trade_status'], $_allow_status)){
            return false;
        }else{
            $notify = $this->_filter_params($_POST);
            ksort($notify);
            reset($notify);
            $sign_data = array();
            foreach($notify as $k=>$v){
                $sign_data[] = $k.'='.$v;
            }
            $signstr = implode('&', $sign_data);
            $setting = D('Payment')->getPayment('alipay');
            //$signstr = $this->_build_query($notify, false, false);
            $verify_sign = $this->rsaVerify($signstr, $_POST['sign'], $setting);
            $verify_sign_str = (string)$verify_sign;
            $verify_result = $this->verify_notify($_POST["notify_id"], $setting['alipay_app_partner']);
            $log  = "verify_result:{$verify_result}\n";
            $log .= "return_url_log:sign={$_POST['sign']}&verify_sign={$verify_sign_str}&".$signstr;
            baolog('alipay.app.respond', $log);
            if (preg_match("/true$/i",$verify_result) && $verify_sign){
                //todo 到这里已经支付成功，需要处理实际的业务
                //array('code'=>'alipay','trade_no'=>$notify['out_trade_no'], 'pay_trade_no'=>$notify['trade_no'], 'trade_status'=>$_GET['trade_status'], 'amount'=>$notify['total_fee']);
                if(D('Payment')->logsPaid($notify['out_trade_no'])){
                    return true;
                }
            }
            return false;
        }
    }

// false, true
    protected function _build_query($params, $urlencode=true, $quotation=false)
    {
        $query_string = "";
        while (list ($key, $val) = each ($params)) {
            if($quotation){
                $val = '"'.$val.'"';
            }
            $query_string .= $key."=".$val."&";
        }
        $query_string = substr($query_string, 0, count($query_string)-2);
        if(get_magic_quotes_gpc()){$query_string = stripslashes($query_string);}
        return $query_string;
    }


    //生成签名
    private function rsaSign($prestr, $setting)
    {
        //alipay_app_private,, alipay_app_public, ,alipay_app_seller,alipay_app_partner

        $rsa_private_key= $setting['alipay_app_private'];
        $rsa_private_key = str_replace("\\\/", "\/", $rsa_private_key);
        if(strpos($rsa_private_key, '-----') === false){
            $key_len = strlen($rsa_private_key);
            $pem_key = "-----BEGIN RSA PRIVATE KEY-----\n";
            for($i=0; $i<$key_len; $i=$i+64){
                $pem_key .= substr($rsa_private_key, $i, 64)."\n";
            }
            $pem_key .= '-----END RSA PRIVATE KEY-----';
            $rsa_private_key = $pem_key;
        }
        $pkeyid = openssl_get_privatekey($rsa_private_key);
        openssl_sign($prestr, $sign, $pkeyid);
        openssl_free_key($pkeyid);
        $sign = base64_encode($sign);
        return $sign;
    }

    //验证签名
    private function rsaVerify($prestr, $sign, $setting)
    {
        $rsa_public_key= $setting['alipay_app_public'];
        $rsa_public_key = str_replace("\\\/", "\/", $rsa_public_key);
        $rsa_public_key='-----BEGIN PUBLIC KEY-----'.PHP_EOL.wordwrap($rsa_public_key, 64, "\n", true) .PHP_EOL.'-----END PUBLIC KEY-----';
        // //$rsa_public_key = file_get_contents(dirname(__FILE__).'/rsa_alipay_public.pem');
        // if(strpos($rsa_public_key, '-----BEGIN RSA PUBLIC KEY-----') === false){
        //     $key_len = strlen($rsa_public_key);
        //     $pem_key = "-----BEGIN RSA PUBLIC KEY-----\n";
        //     for($i=0; $i<$key_len; $i=$i+64){
        //         $pem_key .= substr($rsa_public_key, $i, 64)."\n";
        //     }
        //     $pem_key .= '-----END RSA PUBLIC KEY-----';
        //     $rsa_public_key = $pem_key;
        // }

        $pkeyid = openssl_get_publickey($rsa_public_key);
        if(!$pkeyid){
            baolog('alipay.rsa.verify.error', array($prestr, $sign, $result, $pkeyid, $error, $rsa_public_key, $setting));
            return false;
        }else{
            $result = (bool)openssl_verify($prestr, base64_decode($sign), $pkeyid);
            $error = openssl_error_string();
            baolog('alipay.rsa.verify', array($prestr, $sign, $result, $pkeyid, $error, $rsa_public_key, $setting));
            openssl_free_key($pkeyid);
            return (bool)$result;
        }

    }

    private function _filter_params($params)
    {
        $para = array();
        while (list ($key, $val) = each ($params)) {
            if($key == "sign" || $key == "sign_type" || $val == "") continue;
            else $para[$key] = $params[$key];
        }
        $this->_return_data['TRADENO'] = $para['trade_no']; //交易号
        $this->_return_data['IDCARD'] = $para['buyer_id']; //买家帐号
        return $para;
    }


    public function verify_notify($notify_id, $partner)
    {
        //获取远程服务器ATN结果，验证是否是支付宝服务器发来的请求

        $veryfy_url = $this->https_verify_url. "partner=" .$partner. "&notify_id=".$notify_id;
        baolog('alipay.verify.notify', $veryfy_url);
        return file_get_contents($veryfy_url);
        return $this->http($veryfy_url, null, 'GET');
    }

    public function http($url, $params=array(), $mothed='POST')
    {
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果          
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($ci, CURLOPT_CAINFO,$this->cacert_url);//证书地址
        if(strtoupper($mothed) == 'POST'){// post传输数据
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($params)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
            }
        }else if(!empty($params)){ // get传输数据
            $url .= $this->build_query($params);
        }
        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        $res = curl_exec($ci);
        $code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        curl_close($ci);
        return $res;
    }

}