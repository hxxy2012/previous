<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */
if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Cashier_Account extends Ctl
{

    public function index()
    {
        $this->login();
    }

    // 商户登陆
    public function login()
    {
        if($data = $this->checksubmit('data')){
           $keep = $data['is_auto']? true : false;
            if(!$mobile = K::M('verify/check')->mobile($data['mobile'])){
                $this->msgbox->add('登录帐号不正确',  211);
            }else if(!$passwd = $data['passwd']){
                $this->msgbox->add('登录密码不正确',  212);
            } else if($biz = K::M('shop/auth')->login($mobile, $passwd,null,false,$keep)){
                $this->msgbox->set_data('forward', $this->mklink('cashier/index/index',null,null,$this->request['url']));
            }
        }else{
            $this->tmpl = 'biz/cashier/account/login.html';
        }

    }

    // 商户申请入驻
    public function signup()
    {
        $session =K::M('system/session')->start();
        if($data = $this->checksubmit('data')){
            if(!$data = $this->check_fields($data, 'mobile,passwd,title,phone,city_id,cate_id,addr,code')){
                $this->msgbox->add('非法的数据提交', 210);
            }else if(!$data['mobile']){
                $this->msgbox->add('手机号不能为空', 212);
            }else if(!$data['code']){
                $this->msgbox->add('验证码不能为空', 213);
            }else if($data['code'] != $session->get('code_'.$data['mobile'])){
                $this->msgbox->add('验证码不正确', 214);
            }else if(!$data['passwd']){
                $this->msgbox->add('登录密码不正确', 215);
            }else if($shop = K::M('shop/shop')->find(array('mobile'=>$data['mobile']))){
                $this->msgbox->add('该手机号码已存在', 216);
            }else{
                unset($data['code']);
                $data['passwd'] = md5($data['passwd']);
                if($shop_id = K::M('shop/shop')->create($data)){
                   $this->msgbox->add('申请入驻成功，请等待网站审核通知'); 
                }else{
                    $this->msgbox->add('申请入驻失败，系统错误',217);
                }
            }
        }else{
            $this->pagedata['citys'] = K::M('data/city')->items(null,array('pinyin'=>'asc'));
            $this->pagedata['cates'] = K::M('shop/cate')->tree();
            $this->tmpl = 'biz/cashier/account/signup.html';
        }
    }

    // 商户退出登录
    public function loginout()
    {
        K::M('shop/auth')->loginout();
        header('Location:/cashier/account/index');
    }

    // 商户找回密码
    public function forgot()
    {
        $session =K::M('system/session')->start();
        if($data = $this->checksubmit('data')){
            if(!$data['mobile']){
                $this->msgbox->add('手机号不能为空', 211);
            }else if(!$data['code']){
                $this->msgbox->add('验证码不能为空', 212);
            }else if($data['code'] != $session->get('code_'.$data['mobile'])){
                $this->msgbox->add('验证码不正确', 213);
            }else if(!$data['new_passwd']){
                $this->msgbox->add('新密码不能为空', 212);
            }else if(!$data['new_passwd2']){
                $this->msgbox->add('确认密码不能为空', 213);
            }else if($data['new_passwd'] != $data['new_passwd2']){
                $this->msgbox->add('两次新密码输入不一致', 214);
            }else if($data['passwd'] == $data['new_passwd']){
                $this->msgbox->add('新密码不能和旧密码一致', 216);
            }else if(!$shop = K::M('shop/shop')->shop($data['mobile'],'mobile')){
                $this->msgbox->add('该商家不存在');
            }else if(K::M('shop/shop')->update($shop['shop_id'],array('passwd'=>md5($data['new_passwd'])))){
                $this->msgbox->add('修改登录密码成功');
            }else{
                $this->msgbox->add('登录密码修改失败',217);
            }    
        }else{
            $this->tmpl = 'biz/account/forgot.html';
        }
    }

    // 商户未审核指定跳转页面
    public function noaudit() {
        $this->tmpl = "biz/noaudit.html";
    }
}
