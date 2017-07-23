<?php

/****************** 版权声明 ******************************
 *
 *----------------合肥生活宝网络科技有限公司-----------------
 *----------------      www.baocms.com    -----------------
 *QQ:800026911  
 *电话:0551-63641901  
 *EMAIL：youge@baocms.com
 * 
 ***************  未经许可不得用于商业用途  ****************/

class  HouseworkAction extends CommonAction{
    
    public function index(){
        
        $Housework = D('Housework');
        import('ORG.Util.Page'); // 导入分页类
        $map = array();
        if ($svc_id = (int) $this->_param('svc_id')) {
            $map['svc_id'] = $svc_id;
            $this->assign('svc_id', $svc_id);
        }
        $houselooks = D('Houseworklook')->where(array('shop_id'=>$this->shop_id))->select();
        $Housework_ids = array();
        foreach($houselooks as $v){
            $Housework_ids[] = $v['housework_id'];
        }
        $status = (int) $this->_param('status');
        switch ($status){
            case 1:
                break;
            case 2:
                $map['housework_id'] = array('IN',$Housework_ids);
                break;
            case 3:
                $map['housework_id'] = array('NOT IN',$Housework_ids);
                break;
        }
        $count = $Housework->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Housework->where($map)->order(array('housework_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        $workids = array();
        foreach($list as $k=>$val){
            $workids[$val['housework_id']] = $val['housework_id'];
            if(empty($val['num'])){
                $list[$k]['num'] = $this->_CONFIG['housework']['num'];
            }
            if(empty($val['gold'])){
                $list[$k]['gold'] =  $this->_CONFIG['housework']['gold'];
            }
        }
        $this->assign('looks',D('Houseworklook')->checkLook($this->shop_id,$workids));
        $this->assign('workTypes',$Housework->getCfg());
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }
    
    
    public function  look($housework_id){
        if(!$housework_id=(int)$housework_id){
            $this->baoError('参数错误');
        }
        if(!$detail=D('Housework')->find($housework_id)){
            $this->baoError('参数错误');
        }
        if(empty($detail['num'])){
            $detail['num'] = $this->_CONFIG['housework']['num'];
        }
        if(empty($detail['gold'])){
            $detail['gold'] =  $this->_CONFIG['housework']['gold'];
        }
        if($detail['num'] <= $detail['buy_num']){
            $this->baoError('该信息已经超过最大查看数了！');
        }
        if(D('Houseworklook')->checkIsLook($this->shop_id,$housework_id)){
            $this->baoError('您已经购买过该信息！');
        }
        if(!empty($detail['gold'])){
            if($this->member['gold'] < $detail['gold']){
                $this->baoErrorJump('账户金块余额不足',U('gold/index'));
            }
            D('Users')->addGold($this->uid,-$detail['gold'],'查看服务'.$detail['tel']);
        }
        D('Houseworklook')->add(array(
            'housework_id'=>$housework_id,
            'shop_id'     =>  $this->shop_id,
            'create_time'  => NOW_TIME,
            'create_ip'     => get_client_ip(),
        ));
        D('Housework')->updateCount($housework_id,'buy_num');
        $this->baoSuccess('恭喜您购买查看该服务成功！',U('housework/index',array('status'=>2)));
    }
    
    
}