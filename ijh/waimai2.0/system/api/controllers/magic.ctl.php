<?php

/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Magic extends Ctl
{

    public function sendsms($params)
    {
        if(!$params['mobile']){
            $this->msgbox->add('电话号码有误', 211);
        }else if(!$mobile = K::M('verify/check')->mobile($params['mobile'])){
            $this->msgbox->add('电话号码有误', 212);
        }else{
            $code = rand(100000, 999999);
            $session = K::M('system/session')->start();
            $session->set('code_' . $mobile, $code, 900); /* 15分钟缓存 */
//            $this->cache->set('sms_code_'.$mobile, $code, 900);//session 不起作用,无法收到验证码的时候,开启这个.
            $smsdata = array('code' => $code);
            if($img_code = $params['img_code']){ /* 有发送图形验证码 */
                if(!K::M('magic/verify')->check($img_code)){
                    $this->msgbox->add('图形验证码错误', 213);
                }else if(K::M('sms/sms')->send($mobile, 'login', $smsdata)){
                    if(__DEBUG){
                        $this->msgbox->add('success');
                        $this->msgbox->set_data('data', array('code' => $code, 'sms_code' => 0));
                    }else{
                        $this->msgbox->add('success');
                        $this->msgbox->set_data('data', array('code' => '******', 'sms_code' => 0));
                    }
                }
            }else if(($log_count = K::M('sms/log')->count(array('mobile' => $mobile, 'dateline' => '>:' . (__TIME - 600)))) > 2){ /* 大于5次需要验证码 */
                $this->msgbox->set_data('data', array('code' => '******', 'sms_code' => 1));
            }else if(K::M('sms/sms')->send($mobile, 'login', $smsdata)){
                if(__DEBUG){
                    $this->msgbox->add('success');
                    $this->msgbox->set_data('data', array('code' => $code, 'sms_code' => 0));
                }else{
                    $this->msgbox->add('success');
                    $this->msgbox->set_data('data', array('code' => '******', 'sms_code' => 0));
                }
            }
        }
    }

    public function verify($params)
    {
        K::M('magic/verify')->output();
    }

}