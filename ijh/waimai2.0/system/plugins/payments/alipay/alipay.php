<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: alipay.php 5379 2014-05-30 10:17:21Z youyi $
 */

class Payment_Alipay 
{

    //支付宝网关地址（新）
    private $gateway = 'https://mapi.alipay.com/gateway.do?';
    private $mgateway = 'http://wappaygw.alipay.com/service/rest.htm?';
    private $appgatway = 'https://openapi.alipay.com/gateway.do';

    //消息验证地址
    private $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    
    private $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';

    private $cacert_url = '';

    private $transport = 'https';

    //支付接口标识
    private $code      = 'alipay';

    private $sign_type = 'MD5';
    //支付接口配置信息
    private $config = array();
    //订单信息
    private $order = array();
    //发送至支付宝的参数
    private $_parameter = array();   
    
    public function __construct($cfg)
    {
        //alipay_rsa_private, alipay_rsa_public
        $this->config = $cfg;
        $this->config['_input_charset'] = strtolower('utf-8');        
        $this->_parameter = array();
        $this->_parameter['_input_charset'] = $this->config['_input_charset'];
        $this->_parameter['service'] = $cfg['alipay_service'];
        $this->_parameter['payment_type'] = 1;
        /* 物流参数 统一暂定为其他快递*/
        $this->_parameter['logistics_type'] = 'EXPRESS';          //物流配送方式：POST(平邮)、EMS(EMS)、EXPRESS(其他快递)
        $this->_parameter['logistics_payment'] = 'BUYER_PAY';     //物流费用付款方式：SELLER_PAY(卖家支付)、BUYER_PAY(买家支付)、BUYER_PAY_AFTER_RECEIVE(货到付款)        
        $this->_parameter['partner'] = $cfg['alipay_partner'];
        $this->_parameter['seller_email'] = $cfg['alipay_account'];
        $this->_parameter['return_url'] = $cfg['return_url'];
        $this->_parameter['notify_url'] = $cfg['notify_url'];
        $this->_parameter['show_url'] = $cfg['show_url'];
        if(defined('IN_MOBILE')){
            $this->gateway = $this->mgateway;
            $this->_parameter['service'] = 'alipay.wap.auth.authAndExecute';
        }        
        if (!extension_loaded('openssl')){
            $this->transport = 'http';
        }
        $this->cacert_url = dirname(__FILE__).DIRECTORY_SEPARATOR.'cacert.pem';
    }

    protected function getAppConfig($type = 'APP')
    {
        $config = array(
            'alipay_public_key'=>$this->config['alipay_rsa_public'], 
            'merchant_private_key'=>$this->config['alipay_rsa_private'], 
            'charset'=>'UTF-8', 
            'app_id'=>$this->config['alipay_appid'],
            'gateway_url' => $this->appgatway,
            'notify_url' => $this->config['notify_url']
        );
        $config['MaxQueryRetry'] = 10; //扫码支付是需要密码轮循次数
        $config['QueryDuration'] =3; //扫码支付是需要密码轮循等待时间
        return $config;
    }

    public function codepay($params, &$msg='SUCCESS')
    {
        require_once "lib/AopSdk.php";
        $builderObj = new AlipayTradePayContentBuilder();
        $builderObj->setOutTradeNo($params['trade_no']);
        $builderObj->setTotalAmount($params['amount']);
        $builderObj->setAuthCode($params['auth_code']);
        $builderObj->setSubject($params['title']);
        $builderObj->setBody($params['body']);
        if(!empty($params['storeId'])){
            $builderObj->setStoreId($storeId);
        }
        if(!empty($params['operator_id'])){
            $builderObj->setOperatorId($params['operator_id']);
        }
        if(!empty($params['alipay_store_id'])){
            $builderObj->setAlipayStoreId($params['alipay_store_id']);
        }
        if(!empty($params['app_auth_token'])){
            $builderObj->setAppAuthToken($params['app_auth_token']);
        }
        $config = $this->getAppConfig();
        $payObj = new AlipayTradeService($config);

        $result = $payObj->barPay($builderObj);

        $response = $result->getResponse();
        $msg = $response->sub_msg;
        if($result->getTradeStatus() == 'SUCCESS'){
            $trade = array('code'=>'alipay','trade_no'=>$params['trade_no'],'trade_status'=>'SUCCESS', 'amount'=>$params['amount']);
            $trade['pay_trade_no']= $response->trade_no;
            $trade['pay_info'] = array(
                'code' => $response->code,
                'msg' => $response->msg,
                'buyer_logon_id' => $response->buyer_logon_id,
                'buyer_pay_amount' => $response->buyer_pay_amount,
                'buyer_user_id' => $response->buyer_user_id,
                'fund_bill_list' => json_encode($response->fund_bill_list),
                'gmt_payment' => $response->gmt_payment,
                'invoice_amount' => $response->invoice_amount,
                'open_id' => $response->open_id,
                'out_trade_no' => $response->out_trade_no,
                'point_amount' => $response->point_amount,
                'receipt_amount' => $response->receipt_amount,
                'total_amount' => $response->total_amount,
                'trade_no' => $response->trade_no,
                //common
                'result_code' => $result->getTradeStatus(),
                'total_fee' => $response->total_amount,
                'trade_state' => $response->msg,
            );
            return $trade;
        }
        else{
            K::M('system/logs')->log('codepay.alipay', array($result,$params));
        }
        return false;
    }

    public function qrcodepay($params)
    {
        require_once "lib/AopSdk.php";
        $builderObj = new AlipayTradePrecreateContentBuilder();
        $builderObj->setOutTradeNo($params['trade_no']);
        $builderObj->setTotalAmount($params['amount']);
        $builderObj->setSubject($params['title']);
        $builderObj->setBody($params['body']);
        if(!empty($params['storeId'])){
            $builderObj->setStoreId($storeId);
        }
        if(!empty($params['operator_id'])){
            $builderObj->setOperatorId($params['operator_id']);
        }
        if(!empty($params['alipay_store_id'])){
            $builderObj->setAlipayStoreId($params['alipay_store_id']);
        }
        if(!empty($params['app_auth_token'])){
            $builderObj->setAppAuthToken($params['app_auth_token']);
        }
        $config = $this->getAppConfig();
        $qrPay = new AlipayTradeService($config);
        $qrPayResult = $qrPay->qrPay($builderObj);
        if($qrPayResult->getTradeStatus() == 'SUCCESS'){
            $response = $qrPayResult->getResponse();
            return array('code'=>'alipay', 'trade_no'=>$params['trade_no'], 'qrcode'=>$response->qr_code, 'amount'=>$params['amount']);
        }
        return false;
    }

    public function build_url($params)
    {
        if(defined('IN_MOBILE') && !defined('IN_APP')){
        require_once "lib/AopSdk.php";
        $builderObj = new AlipayTradeWapPayContentBuilder();

	    $builderObj->setBody($params['body']);
	    $builderObj->setSubject($params['title']);
	    $builderObj->setOutTradeNo($params['trade_no']);
	    $builderObj->setTotalAmount($params['amount']);
	    $builderObj->setTimeExpress('1m');

        $config = $this->getAppConfig();
        $payResponse = new AlipayTradeService($config);
        $result=$payResponse->wapPay($builderObj, $this->config['return_url'], $this->config['notify_url']);


        var_dump($result);exit;


//            $params['service'] = 'alipay.wap.trade.create.direct';
//            $parameter = $this->build_mparameter($params);
//            $html_text = $this->http($this->mgateway, $parameter, 'POST');
//            $html_text = urldecode($html_text);
//            $para_response = $this->parse_response($html_text);
//            $params['service'] = 'alipay.wap.auth.authAndExecute';
//            $params['request_token'] = $para_response['request_token'];
//            $parameter = $this->build_mparameter($params);
//            $url = $this->gateway ."_input_charset=".$this->config['_input_charset']."&". $this->_build_query($parameter);
        }else{          
            $parameter = $this->build_parameter($params);
            $url = $this->gateway ."_input_charset=".$this->config['_input_charset']."&". $this->_build_query($parameter);
        }
        return $url;
    }

    public function build_form($params)
    {      
        //待请求参数数组
        //if(defined('IN_MOBILE')){
        if(defined('IS_MOBILE') && IS_MOBILE){
            $parameter = array(
                "service"       => 'alipay.wap.create.direct.pay.by.user',
                "partner"       => $this->config['alipay_partner'],
                "seller_id"  => $this->config['alipay_account'],
                "payment_type"  => '1',
                "notify_url"    => $this->config['notify_url'],
                "return_url"    =>  $this->config['return_url'],
                "_input_charset" => $this->config['_input_charset'],
                "out_trade_no"  => $params['trade_no'],
                "subject"   => $params['title'],
                "total_fee" => $params['amount'],
                "body"  => $params['body']
            );

            $html = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gateway."_input_charset=".trim(strtolower($this->config['_input_charset']))."' method='".$method."'>";
            while (list ($key, $val) = each ($parameter)) {
                $html.= "<input type='hidden' name='".$key."' value='".$val."'/>";
            }
            //submit按钮控件请不要含有name属性
            $html .= "<input type='submit' value='立即支付'></form>";        
            $html .= "<script>document.forms['alipaysubmit'].submit();</script>";

        }else{
            $parameter = $this->build_parameter($params);        
            $html = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gateway."_input_charset=".trim(strtolower($this->config['_input_charset']))."' method='".$method."'>";
            while (list ($key, $val) = each ($parameter)) {
                $html.= "<input type='hidden' name='".$key."' value='".$val."'/>";
            }
            //submit按钮控件请不要含有name属性
            $html .= "<input type='submit' value='立即支付'></form>";        
            $html .= "<script>document.forms['alipaysubmit'].submit();</script>";
        }
        return $html;
    }

    public function build_app($params)
    {
        $parameter = array(
            'partner'       => $this->_parameter['partner'],
            'seller_id'     => $this->_parameter['seller_email'],
            'out_trade_no'  => $params['trade_no'],
            'subject'       => $params['title'],
            'body'          => $params['body'],
            'total_fee'     => sprintf("%01.2f", $params['amount']),
            'notify_url'    => $this->_parameter['notify_url'],
            //'show_url'      => $this->_parameter['show_url'],
            'service'       => 'mobile.securitypay.pay',
            'payment_type'  => '1',            
            '_input_charset'=> 'utf-8',
        );
        $signstr = $this->_build_query($parameter, false, true);
        $parameter['sign'] = urlencode($this->rsaSign($signstr));
        $parameter['signstr'] = $signstr;
        $parameter['sign_type'] = 'RSA';
        return $parameter;
    }

    public function return_verify()
    {
        //K::M('system/logs')->log('___paypal_return_get_test', array( __FILE__.__LINE__,$_GET, $_POST));
        if(defined('IN_MOBILE')){
            if(empty($_GET)) {//判断GET来的数组是否为空
                return false;
            }else {
                $notify = $_GET;
                require_once "lib/AopSdk.php";
                $config = $this->getAppConfig();
                $alipaySevice = new AlipayTradeService($config);
                $result = $alipaySevice->wap_check($notify);

                if ($result) {
                    //通知没有状态,不用判断.

                    $out_trade_no = $_GET['out_trade_no'];
                    //支付宝交易号
                    $trade_no = $_GET['trade_no'];
                    //交易状态
                    $trade_status = $_GET['trade_status'];
                    $log = "alipay, trade_no: {$out_trade_no}, status: {$trade_status} ";
                    $this->_logs($log);

                    $this->_return_data['TRADENO'] = $notify['trade_no']; //交易号
                    $this->_return_data['IDCARD'] = $notify['buyer_id']; //买家帐号

                    return array('code' => 'alipay', 'trade_no' => $notify['out_trade_no'], 'pay_trade_no' => $notify['trade_no'], 'trade_status' => $notify['trade_status'],
                        'amount' => $notify['total_fee'], 'extra_param' => $this->_decode_params($notify['extra_common_param']));

                }//sign check ends

                return false;
            }// else end
        }else{
//            K::M('system/logs')->log('___paypal_return_test', array( __FILE__.__LINE__));
            //WAIT_BUYER_PAY,WAIT_SELLER_SEND_GOODS,WAIT_BUYER_CONFIRM_GOODS,TRADE_FINISHED,TRADE_SUCCESS,TRADE_CLOSED
            $_allow_status = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_FINISHED', 'TRADE_SUCCESS');
            if(empty($_GET)){   //判断GET来的数组是否为空
                return false;
            }else if(!in_array($_GET['trade_status'],$_allow_status)){
                return false;
            }else{
                //判断veryfy_result是否为ture，生成的签名结果mysign与获得的签名结果sign是否一致
                //$veryfy_result的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
                //mysign与sign不等，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关            
                $notify = $this->_filter_params($_GET);
                $verify_sign = $this->verify_sign($notify, $_GET['sign_type'], $_GET['sign']);
                $verify_sign_str = (string)$verify_sign;
                $verify_result = $this->verify_notify($_GET["notify_id"]);
                //写日志记录
                $log  = "verify_result:{$verify_result}\n\n";
                $log .= "return_url_log:sign={$_GET[sign]}&verify_sign={$verify_sign_str}&".$this->_build_query($notify);
                $this->_logs($log);
                if (preg_match("/true$/i",$verify_result) && $verify_sign){
                    return array('code'=>'alipay','trade_no'=>$notify['out_trade_no'], 'pay_trade_no'=>$notify['trade_no'], 'trade_status'=>$_GET['trade_status'], 'amount'=>$notify['total_fee'], 'extra_param'=>$this->_decode_params($notify['extra_common_param']));
                }
                return false;
            }
            return true;
        }
    }

    public function notify_verify()
    {
        //K::M('system/logs')->log('___paypal_return_post_test', array( __FILE__.__LINE__,$_GET, $_POST));
        if(!defined('IN_APP') && defined('IN_MOBILE')){

            if(empty($_POST)) {//判断GET来的数组是否为空
                return false;
            }else {
                $notify = $_POST;
                require_once "lib/AopSdk.php";
                $config = $this->getAppConfig();
                $alipaySevice = new AlipayTradeService($config);
                $result = $alipaySevice->wap_check($notify);
                if ($result) {
                    $_allow_status = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_FINISHED', 'TRADE_SUCCESS');

                    $out_trade_no = $_POST['out_trade_no'];
                    //支付宝交易号
                    $trade_no = $_POST['trade_no'];
                    //交易状态
                    $trade_status = $_POST['trade_status'];
                    $log = "alipay, trade_no: {$out_trade_no}, status: {$trade_status} ";
                    $this->_logs($log);

                    if (!in_array($_POST['trade_status'], $_allow_status)) {
                        return false;
                    } else {
                        $this->_return_data['TRADENO'] = $notify['trade_no']; //交易号
                        $this->_return_data['IDCARD'] = $notify['buyer_id']; //买家帐号

                        return array('code' => 'alipay', 'trade_no' => $notify['out_trade_no'], 'pay_trade_no' => $notify['trade_no'], 'trade_status' => $notify['trade_status'],
                            'amount' => $notify['total_fee'], 'extra_param' => $this->_decode_params($notify['extra_common_param']));
                    }
                }//sign check ends
                return false;
            }// else end
        }else{
            //WAIT_SELLER_SEND_GOODS → WAIT_BUYER_CONFIRM_GOODS
            //WAIT_BUYER_PAY,WAIT_SELLER_SEND_GOODS,WAIT_BUYER_CONFIRM_GOODS,TRADE_FINISHED,TRADE_SUCCESS,TRADE_CLOSED
            //K::M('system/logs')->log('___paypal_return_post_test', array( __FILE__.__LINE__));
            $_allow_status = array('WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_FINISHED', 'TRADE_SUCCESS');
            if(empty($_POST)){//判断POST来的数组是否为空
                return false;
            }else if(!in_array($_POST['trade_status'], $_allow_status)){
                return false;
            }else{
                //判断veryfy_result是否为ture，生成的签名结果mysign与获得的签名结果sign是否一致
                //$veryfy_result的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
                //mysign与sign不等，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关            
                $notify = $this->_filter_params($_POST);
                $verify_sign = $this->verify_sign($notify, $_POST['sign'], $_POST['sign_type']);
                $verify_sign_str = (string)$verify_sign;
                $verify_result = $this->verify_notify($_POST["notify_id"]); 
                //写日志记录
                $log  = "verify_result:{$verify_result}\n\n";
                $log .= "notify_url_log:sign={$_POST[sign]}&verify_sign={$verify_sign_str}&".$this->_build_query($notify);
                $this->_logs($log);
                if (preg_match("/true$/i",$verify_result) && $verify_sign){
                    return array('code'=>'alipay','trade_no'=>$notify['out_trade_no'], 'pay_trade_no'=>$notify['trade_no'], 'trade_status'=>$_POST['trade_status'], 'amount'=>$notify['total_fee'], 'extra_param'=>$this->_decode_params($notify['extra_common_param']));
                }
                return false;
            }
        }
    }

    public function notify_success($success=true)
    {
        if($success){
            echo "success";exit;
        }else{
            echo "fail";exit;
        }
    }

    public function verify_notify($notify_id)
    {
        //获取远程服务器ATN结果，验证是否是支付宝服务器发来的请求
        if($this->transport == "https") {
            $veryfy_url = $this->https_verify_url. "partner=" .$this->config['alipay_partner']. "&notify_id=".$notify_id;
        } else {
            $veryfy_url = $this->http_verify_url. "partner=".$this->config['alipay_partner']."&notify_id=".$notify_id;
        }
        return $this->http($veryfy_url, null, 'GET');
    }

    public function sendship($log, $trade)
    {
        $parameter = array(
            "service" => $this->_parameter['service'],
            "partner" => $this->_parameter['partner'],
            "trade_no"  => $log['pay_trade_no'],
            "logistics_name"    => 'JHKJ', //快递公司
            "invoice_no"    => 'KT'.date('Ymd').rand(1000, 9999), //快递单号
            "transport_type"    => 'EXPRESS',//物流发货时的运输类型，三个值可选：POST（平邮）、EXPRESS（快递）、EMS（EMS）
            "_input_charset"    => $this->_parameter['_input_charset']
        );

        
        $parameter = $this->_filter_params($parameter);
        $parameter = $this->_sort_params($parameter);
        $sign = $this->create_sign($parameter);
        $parameter['sign'] = $sign;
        $parameter['sign_type'] = strtoupper($this->sign_type);
        $url = $this->gateway."_input_charset=".$this->_parameter['_input_charset'];
        //远程获取数据
        $xml = $this->http($url, $parameter, 'POST');
        if($obj = simplexml_load_string($xml)){
            if($success = $obj->is_success){
                return strtoupper($success) == 'T' ? true : false;
            }
        }
        return false;
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

    /**
     * 生成要请求给支付宝的参数数组
     * @param $params 请求前的参数数组
     * @return 要请求的参数数组
     */
    public function build_parameter($params)
    {
        $parameter = $this->_parameter;
        $parameter['out_trade_no'] = $params['trade_no'];
        $parameter['subject'] = $params['title'];
        $parameter['body'] = $params['body'];
        if($params['show_url']){
            $parameter['show_url'] = $params['show_url'];
        }
        if($params['extra_param']){
            $parameter['extra_common_param'] = $this->_encode_params($params['extra_param']);
        }

        if ($params['service'] == 'create_direct_pay_by_user' || $params['service'] == 'alipay.wap.create.direct.pay.by.user1'){
            $parameter['total_fee'] = sprintf("%01.2f", $params['amount']);
        } else {
            $parameter['price'] = sprintf("%01.2f", $params['amount']);
            $parameter['quantity']= 1;      //商品数量
            $parameter['logistics_fee'] = "0.00";//物流配送费用
            if($params['contact']){
                $parameter['receive_name'] = $params['contact'];
            }
            if($params['addr']){
                $parameter['receive_address'] = $params['addr'];
            }
            if($params['mobile']){
                $parameter['receive_phone'] = $params['mobile'];
            }            
        }

        $parameter = $this->_filter_params($parameter);
        $parameter = $this->_sort_params($parameter);
        $sign = $this->create_sign($parameter);
        $parameter['sign'] = $sign;
        $parameter['sign_type'] = strtoupper($this->sign_type);
        return $parameter;
    }

    protected function build_mparameter($params)
    {
        if($params['service'] == 'alipay.wap.create.direct.pay.by.user'){
            $parameter = array(
                "service"       => 'alipay.wap.create.direct.pay.by.user',
                "partner"       => $this->_parameter['partner'],
                "seller_id"  => $this->_parameter['alipay_account'],
                "payment_type"  => '1',
                "notify_url"    =>  __HOST__ . U('mobile/payment/respond', array('code' => 'alipay')),
                "return_url"    =>  __HOST__ . U('mobile/payment/respond', array('code' => 'alipay')),
                "_input_charset"    => $this->input_charset,
                "out_trade_no"  => $logs['logs_id'],
                "subject"   => $logs['subject'],
                "total_fee" => $logs['logs_amount'],
                "body"  => $logs['subject']
            );
        }else{        
            $parameter = array(
                "service" => $params['service'], //"alipay.wap.auth.authAndExecute",
                "partner" => $this->_parameter['partner'],
                "sec_id" => $this->sign_type,
                "format"    => 'xml',
                "v" => '2.0',
                "req_id"    => $params['trade_no'],
                //"req_data"    => $req_data,
                "_input_charset"    => 'utf-8'
            );
        }
        $parameter['req_data'] = $this->_build_mreq_data($params);
        $parameter = $this->_filter_params($parameter);
        $parameter = $this->_sort_params($parameter);
        $sign = $this->create_sign($parameter);
        $parameter['sign'] = $sign;
        //$parameter['sign_type'] = strtoupper($this->sign_type);
        return $parameter;
    }


    protected function _build_mreq_data($params)
    {
        //请求业务参数详细
        if($params['service'] == 'alipay.wap.auth.authAndExecute'){
            return '<auth_and_execute_req><request_token>' . $params['request_token']. '</request_token></auth_and_execute_req>';
        }else{
            return '<direct_trade_create_req><notify_url>' . $this->_parameter['notify_url'] . '</notify_url><call_back_url>' . $this->_parameter['return_url'] . '</call_back_url><seller_account_name>' . $this->_parameter['seller_email'] . '</seller_account_name><out_trade_no>' . $params['trade_no'] . '</out_trade_no><subject>' . $params['title'] . '</subject><total_fee>' . sprintf("%01.2f", $params['amount']) . '</total_fee><merchant_url>'.$params['show_url'].'</merchant_url></direct_trade_create_req>';
        }
    }

    protected function _build_query($params, $urlencode=true, $quotation=false)
    {
        $query_string = "";
        while (list ($key, $val) = each ($params)) {
            if($quotation){
                $val = '"'.$val.'"';
            }
            if($urlencode){
                $query_string .= $key."=".urlencode($val)."&";
            }else{
                $query_string .= $key."=".$val."&";  
            }
        }
        $query_string = substr($query_string, 0, count($query_string)-2);
        if(get_magic_quotes_gpc()){$query_string = stripslashes($query_string);}
        return $query_string;
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


    //对数组排序 用作生成签名
    private function _sort_params($params)
    {
        ksort($params);
        reset($params);
        return $params;
    }

    /**
     *  生成签名结果
     *  $array  要签名的数组
     *  return  签名结果字符串
     */
    private function create_sign($params, $sign_type='MD5')
    {   
        $params = $this->_sort_params($params);                
        if(strtoupper($sign_type) == 'RSA'){
            $prestr = $this->_build_query($params, false, true); 
            return $this->rsaSign($prestr);           
        }else{
            $prestr = $this->_build_query($params, false);  //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
            $prestr = $prestr.$this->config['alipay_key']; //把拼接后的字符串再与安全校验码直接连接起来
            $mysgin = md5($prestr); //把最终的字符串签名，获得签名结果
        }
        return $mysgin;
    }

    public function verify_sign($params, $sign, $sign_type='MD5')
    {
        $params = $this->_sort_params($params);        
        if(strtoupper($sign_type) == 'RSA'){
            $prestr = $this->_build_query($params, false);  //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
            return $this->rsaVerify($prestr, $sign);
        }else{
            $prestr = $this->_build_query($params, false);  //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
            $prestr = $prestr.$this->config['alipay_key']; //把拼接后的字符串再与安全校验码直接连接起来
            $mysgin = md5($prestr); //把最终的字符串签名，获得签名结果
            return $sign == $mysgin;
        }
    }

    private function rsaSign($prestr)
    {
        $rsa_private_key= $this->config['alipay_rsa_private'];
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

    private function rsaVerify1($prestr, $sign)
    {
        $sign = base64_decode($sign);
        $rsa_public_key= $this->config['alipay_rsa_public'];;
        if(strpos($rsa_public_key, '-----BEGIN PUBLIC KEY-----') === false){
            $key_len = strlen($rsa_public_key);
            $pem_key = "-----BEGIN PUBLIC KEY-----\n";
            for($i=0; $i<$key_len; $i=$i+64){
                $pem_key .= substr($rsa_public_key, $i, 64)."\n";
            }
            $pem_key .= '-----END PUBLIC KEY-----';
            $rsa_public_key = $pem_key;
        }

        $pkeyid = openssl_get_publickey($rsa_public_key);
        $result = openssl_verify($prestr, $sign, $pkeyid);
        //$line = 'File:'.__FILE__.',Line:'.__LINE__;
        //K::M('system/logs')->log('alipay.line', $line);
        //K::M('system/logs')->log('alipay', array('pkeyid'=>$pkeyid, 'result'=>$result, 'prestr'=>$prestr, 'sign'=>base64_encode($sign), 'rsa_public_key'=>$rsa_public_key, 'rsa_private_key'=>$rsa_private_key));
        openssl_free_key($pkeyid);    
        return (bool)$result;
    }


    private function rsaVerify($prestr, $sign)
    {
        $sign = base64_decode($sign);
        $rsa_public_key= $this->config['alipay_rsa_public'];
        $rsa_public_key = str_replace("\\\/", "\/", $rsa_public_key);
        if(strpos($rsa_public_key, '-----BEGIN PUBLIC KEY-----') === false){
            $key_len = strlen($rsa_public_key);
            $pem_key = "-----BEGIN PUBLIC KEY-----\n";
            for($i=0; $i<$key_len; $i=$i+64){
                $pem_key .= substr($rsa_public_key, $i, 64)."\n";
            }
            $pem_key .= '-----END PUBLIC KEY-----';
            $rsa_public_key = $pem_key;
        }

        $pkeyid = openssl_get_publickey($rsa_public_key);
        $result = openssl_verify($prestr, $sign, $pkeyid);
        
        //K::M('system/logs')->log('alipay.line', $line);
        //K::M('system/logs')->log('alipay', array('pkeyid'=>$pkeyid, 'result'=>$result, 'prestr'=>$prestr, 'sign'=>base64_encode($sign), 'rsa_public_key'=>$rsa_public_key, 'rsa_private_key'=>$rsa_private_key));
        openssl_free_key($pkeyid);
        return (bool)$result;
    }


    protected function parse_response($str_text) 
    {
        //以“&”字符切割字符串
        $para_split = explode('&',$str_text);
        //把切割后的字符串数组变成变量与数值组合的数组
        foreach ($para_split as $item) {
            //获得第一个=字符的位置
            $nPos = strpos($item,'=');
            //获得字符串长度
            $nLen = strlen($item);
            //获得变量名
            $key = substr($item,0,$nPos);
            //获得数值
            $value = substr($item,$nPos+1,$nLen-$nPos-1);
            //放入数组中
            $para_text[$key] = $value;
        }
        
        if( ! empty ($para_text['res_data'])) {
            //解析加密部分字符串         
            //token从res_data中解析出来（也就是说res_data中已经包含token的内容）
            $doc = new DOMDocument();
            $doc->loadXML($para_text['res_data']);
            $para_text['request_token'] = $doc->getElementsByTagName( "request_token" )->item(0)->nodeValue;
        }       
        return $para_text;
    }


    protected function _encode_params($param)
    {
        //$param = serialize($param);
        $hex = '';
        foreach((array)$param as $k=>$v){
            $_K = $_V = '';
            $k = strval($k);
            $v = strval($v);
            for($i=0; $i<strlen($k); $i++)  
                $_K .= dechex(ord($k[$i]));
            for($i=0; $i<strlen($v); $i++)  
                $_V .= dechex(ord($v[$i]));
            $hex .= strtoupper($_K).'O'.strtoupper($_V).'I';
        }
        return trim($hex,'I');  
    }

    protected function _decode_params($hex)
    {
        $param = ''; 
        foreach(explode('I',$hex) as $h){
            list($_K,$_V) = explode('O',$h);
            $k = $v = '';
            for($i=0; $i<strlen($_K)-1; $i+=2)
                $k .= chr(hexdec($_K[$i].$_K[$i+1]));
            for($i=0; $i<strlen($_V)-1; $i+=2)
                $v .= chr(hexdec($_V[$i].$_V[$i+1]));
            $param[$k] = $v;
        }
        return  $param;         
    }

    protected function _logs($log)
    {
        $key = 'payment-alipay-'.date('Ymd');
        K::M('system/logs')->log($key, $log);
    }
}