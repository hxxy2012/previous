<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

//Import::C('weixin');
class Ctl_Weixin_Api extends Ctl_Weixin
{

	public function __construct($system)
	{
		parent::__construct($system);
		if(!in_array($this->system->request['act'], array('openapi', 'wxloginpage', 'wxcallback'))){
			$appid = $this->system->request['act'];
			$this->system->request['act'] = 'index';
			$this->system->request['args'] = array($appid);
		}
	}

    public function index($appid)
    {

        $wx_config = K::M('system/config')->get('wechat');
        Import::L('weixin/crypt/wxBizMsgCrypt.php');
        Import::L('weixin/wechat.class.php');
        // 第三方收到公众号平台发送的消息
        $Crypt = new wxBizMsgCrypt($wx_config['open_mp_token'], $wx_config['open_mp_aeskey'], $wx_config['open_mp_appid']);
        $wechat = new WeixinWechat($Crypt);
        $data = $wechat->get_data();
        if ($weixin = K::M('weixin/weixin')->detail_by_appid($appid)) {
            if($data['ToUserName'] && ($data['ToUserName'] != $weixin['wx_ghid'])){
                $weixin['wx_ghid'] = $data['ToUserName'];
                K::M('weixin/weixin')->update($weixin['shop_id'], array('wx_ghid'=>$data['ToUserName']));
            }
        }
        if(!empty($data['FromUserName'])){
            $openid = $data ['FromUserName'];
        }
        K::M('weixin/log')->log($weixin['wx_ghid'], $data, $GLOBALS ['HTTP_RAW_POST_DATA']);
        $this->reply($data, $weixin, $wechat);
        exit();

    }


    //获取token前ticket票据
    public function openapi()
    {
        $xmldata = file_get_contents('php://input');
        $wx_config = K::M('system/config')->get('wechat');
        Import::L('weixin/crypt/wxBizMsgCrypt.php');
        // 第三方收到公众号平台发送的消息
        $Crypt = new wxBizMsgCrypt($wx_config['open_mp_token'], $wx_config['open_mp_aeskey'], $wx_config['open_mp_appid']);
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $msg_sign = $_GET['msg_signature'];
        $errCode = $Crypt->decryptMsg($msg_sign, $timeStamp, $nonce, $xmldata, $msg);
        K::M('system/logs')->log('wxopen.api.ticket', array($xmldata, $_POST, $_REQUEST, $msg, $wx_config));
        if ($errCode == 0) {
            $xmlobj = simplexml_load_string($msg);
            foreach($xmlobj as $k=>$v){
                $data[$k] = strval($v);
            }
            $this->cache->set('wxopen.api.ticket', $data);
            exit('SUCCESS');
        } else {
            exit('FAIL');
        }        
        
    }

    public function wxloginpage()
    {
        $component = K::M('weixin/wechat')->component_client();
        $redirect_url = $this->mklink('weixin/api/wxcallback', null, null, 'www');
        if($url = $component->component_login_page($redirect_url)){
            header("Location:{$url}");
            exit;
        }
        $this->msgbox->add('获取微信第三方授权票据失败', 211);
    }

    public function wxcallback()
    {
        if(!$code = $this->GP('auth_code')){
            $this->msgbox->add('授权失败或用户拒绝授权', 211);
        }else{
            $component = K::M('weixin/wechat')->component_client();
            if($ret = $component->authorizer_info_by_code($code)){
                $auth_info = $ret['authorizer_info'];
                if($auth_info['authorizer_appid']){
                    $data = array('wx_appid'=>$auth_info['authorizer_appid'], 'access_token'=>$auth_info['authorizer_access_token'], 'refresh_token'=>$auth_info['authorizer_refresh_token']);
                    $data['expires_in'] = __TIME + $auth_info['expires_in'];
                    if($ret = $component->authorizer_info($auth_info['authorizer_appid'])){
                        if($wx_info = $ret['authorizer_info']){
                            $data['wx_name'] = $wx_info['alias'];
                            $data['wx_uname'] = $wx_info['user_name'];
                            $data['nick_name'] = $wx_info['nick_name'];
                            $data['qrcode_url'] = $wx_info['qrcode_url'];
                            $data['head_img'] = $wx_info['head_img'];
                        }
                        K::M('weixin/weixin')->create($data);
                        $this->msgbox->set_data('forward', $this->mklink('biz/weixin/index'));
                    }
                    echo 'File:',__FILE__,',Line:',__LINE__;exit;
                    
                }
            }
            echo 'File:',__FILE__,',Line:',__LINE__;exit;
            
        }
    }



    /**
     * 通过微信事件来定位处理的插件
     * event可能的值：
     * subscribe : 关注公众号
     * unsubscribe : 取消关注公众号
     * scan : 扫描带参数二维码事件
     * click : 自定义菜单事件
     */
    protected function reply($data, $weixin, $wechat)
    {
        $key = $data ['Content'];
        $keywordArr = array ();
        if ($data ['MsgType'] == 'event') {
            $event = strtolower($data ['Event']);
            if('subscribe' == $event){ //关注
                if($welcome = K::M('weixin/welcome')->find(array('shop_id'=>$weixin['shop_id']))){
                    if($reply_id = $welcome['reply_id']){
                        $wechat->replyId($reply_id);
                    }else if($content = $welcome['content']){
                        $wechat->replyText($content);
                    }
                }
            }else if('scan' == $event){ //二难码                
                if($openid = $data['FromUserName']){                    
                    if($scene_id = (int)$data['EventKey']){

                    }
                }

            }else if('click' == $event){ //菜单
                if(preg_match('/^MENU\:(\d+)$/i', $data['EventKey'], $m)){
                    if($menu = K::M('weixin/menu')->detail($m[1])){
                        if($reply_id = (int)$menu['reply_id']){
                            $wechat->replyId($reply_id);
                        }else if($content = $menu['content']){
                            $wechat->replyText($content);
                        }
                    }
                }else if(!empty($data['EventKey'])){
                    $key = $data['Content'] = $data['EventKey'];
                }
            }
        }
        if($key = trim($key)){           
            if($keywordArr = K::M('weixin/keyword')->detail_by_keyword($key, $weixin['shop_id'])){
                if($reply_id = (int)$keywordArr['reply_id']){
                    $wechat->replyId($reply_id);
                }else if($keywordArr['plugin']){ //判断是插件，那个插件，
                    $plugin_arr = explode(':',$keywordArr['plugin']);
                    $plugin = $plugin_arr[0];
                    $plugin_id = $plugin_arr[1];
                    if($news = K::M('weixin/'.$plugin)->format_reply($plugin_id)){
                        $wechat->replyNews($news);
                    }                   
                }else{
                    $wechat->replyText($keywordArr['content']);
                }
            }
        }
    }



}