<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class CloudgoodsAction extends CommonAction {

    private $create_fields = array('title', 'shop_id', 'win_user_id', 'photo', 'city_id', 'area_id', 'price', 'join','max', 'settlement_price', 'intro', 'type', 'thumb', 'details');
    private $edit_fields = array('title', 'shop_id', 'win_user_id', 'photo', 'city_id', 'area_id', 'price', 'join','max', 'settlement_price', 'intro', 'type', 'thumb', 'details');

    public function _initialize() {
        parent::_initialize();
        $this->types = D('Cloudgoods')->getType();
        $this->assign('types', $this->types);
    }

    public function index() {
        $goods = D('Cloudgoods');
        import('ORG.Util.Page'); // 导入分页类
        $map = array('closed' => 0);
        if ($keyword = $this->_param('keyword', 'htmlspecialchars')) {
            $map['title|intro'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        
        $map['shop_id'] = array('in',D('Shop')->getCityShop($this->_fenzhan['city_id']));
        
        if ($shop_id = (int) $this->_param('shop_id')) {
            $map['shop_id'] = $shop_id;
            $shop = D('Shop')->find($shop_id);
            $this->assign('shop_name', $shop['shop_name']);
            $this->assign('shop_id', $shop_id);
        }
        if ($type = (int) $this->_param('type')) {
            $map['type'] = $type;
            $this->assign('type', $type);
        }
        if ($audit = (int) $this->_param('audit')) {
            $map['audit'] = ($audit === 1 ? 1 : 0);
            $this->assign('audit', $audit);
        }
        $count = $goods->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $goods->where($map)->order(array('goods_id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $val) {
            if ($val['shop_id']) {
                $shop_ids[$val['shop_id']] = $val['shop_id'];
            }
        }
        if ($shop_ids) {
            $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        }
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

	
	public function look($goods_id = 0) {
		if ($goods_id = (int) $goods_id) {
            $obj = D('Cloudgoods');
			$m = D('Users');
			if (!$detail = $obj->find($goods_id)) {
                $this->error('请选择要编辑的商品');
            }else if($detail['city_id'] != $this->_fenzhan['city_id']){
                $this->baoError('商品不属于当前城市');
            }else if(!$udetail = $m->find($detail['win_user_id'])){
				$this->error('该用户不存在！');
			}else{
				$detail['nickname'] = $udetail['nickname'];
				$detail['mobile'] = $udetail['mobile'];
			}
			if($this->isPost()){
				$goods_id = (int) $goods_id;
				$obj = D('Cloudgoods');
				$data['goods_id']=$goods_id;
				$data[adress] = $_POST[data]['adress'];
				if($obj->save($data)){
					$this->baoSuccess('操作成功', U('cloudgoods/index'));
				}else{
					$this->baoError('操作失败！');
				}
			}
		}
        $this->assign('detail', $detail); 		
		$this->display();
	}
	
	
    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $thumb = $this->_param('thumb', false);
            foreach ($thumb as $k => $val) {
                if (empty($val)) {
                    unset($thumb[$k]);
                }
                if (!isImage($val)) {
                    unset($thumb[$k]);
                }
            }
            $data['thumb'] = serialize($thumb);
            $obj = D('Cloudgoods');
            if ($goods_id = $obj->add($data)) {
                $this->baoSuccess('添加成功', U('cloudgoods/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('产品名称不能为空');
        }
        $data['shop_id'] = (int) $data['shop_id'];
        if (!empty($data['shop_id'])) {
            $shop = D('Shop')->find($data['shop_id']);
            if (empty($shop)) {
                $this->baoError('请选择正确的商家');
            }
            if($shop['city_id'] != $this->_fenzhan['city_id']){
                $this->baoError('商家不属于当前城市');
            }
            $data['city_id'] = $shop['city_id'];
            $data['area_id'] = $shop['area_id'];
        } else {
            $data['city_id'] = $this->_CONFIG['site']['city_id'];
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        }
        $data['type'] = (int) $data['type'];
        $data['price'] = (int) ($data['price']);
        $data['max'] = (int) ($data['max']);
        if (empty($data['price'])) {
            $this->baoError('总需人次不能为空');
        }
        if (empty($data['max'])) {
            $this->baoError('单人最大购买数不能为空');
        }
        if ($data['type'] == 2) {
            if ($data['price'] % 5 != 0) {
                $this->baoError('总需人次必须为5的倍数');
            }
            if ($data['max'] % 5 != 0) {
                $this->baoError('单人最大购买数必须为5的倍数');
            }
        }
        if ($data['type'] == 3) {
            if ($data['price'] % 10 != 0) {
                $this->baoError('总需人次必须为10的倍数');
            }
            if ($data['max'] % 10 != 0) {
                $this->baoError('单人最大购买数必须为10的倍数');
            }
        }

        $data['settlement_price'] = (int) ($data['settlement_price'] * 100);
        if (!empty($data['settlement_price'])) {
            if ($data['settlement_price'] >= $data['price'] * 100) {
                $this->baoError('结算价格必须小于总需人次');
            }
        }
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('商品详情不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('商品详情含有敏感词：' . $words);
        }
        $data['create_time'] = NOW_TIME;
        $data['create_ip'] = get_client_ip();
        $data['audit'] = 1;
        return $data;
    }

    public function plan($goods_id) {
        $obj = D('Cloudgoods');
        $goods_id = (int) $goods_id;
        if (empty($goods_id)) {
            $this->error('请选择要参拍的商品');
        }
        if (!$detail = $obj->find($goods_id)) {
            $this->error('请选择要参拍的商品');
        }
        if($detail['city_id'] != $this->_fenzhan['city_id']){
            $this->baoError('商品不属于当前城市');
        }
        if ($this->isPost()) {
            $data = $this->editCheck();
            $thumb = $this->_param('thumb', false);
            foreach ($thumb as $k => $val) {
                if (empty($val)) {
                    unset($thumb[$k]);
                }
                if (!isImage($val)) {
                    unset($thumb[$k]);
                }
            }
            $data['thumb'] = serialize($thumb);
            $data['create_time'] = NOW_TIME;
            $data['create_ip'] = get_client_ip();
            $data['audit'] = 1;
            if ($obj->add($data)) {
                $this->baoSuccess('操作成功', U('cloudgoods/index'));
            }
            $this->baoError('操作失败');
        } else {
            $thumb = unserialize($detail['thumb']);
            $this->assign('thumb', $thumb);
            $this->assign('shop', D('Shop')->find($detail['shop_id']));
            $this->assign('detail', $detail);
            $this->display();
        }
    }

    public function edit($goods_id = 0) {
        if ($goods_id = (int) $goods_id) {
            $obj = D('Cloudgoods');
            if (!$detail = $obj->find($goods_id)) {
                $this->error('请选择要编辑的商品');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $thumb = $this->_param('thumb', false);
                foreach ($thumb as $k => $val) {
                    if (empty($val)) {
                        unset($thumb[$k]);
                    }
                    if (!isImage($val)) {
                        unset($thumb[$k]);
                    }
                }
                $data['thumb'] = serialize($thumb);
                $data['goods_id'] = $goods_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('cloudgoods/index'));
                }
                $this->baoError('操作失败');
            } else {
                $thumb = unserialize($detail['thumb']);
                $this->assign('thumb', $thumb);
                $this->assign('shop', D('Shop')->find($detail['shop_id']));
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->error('请选择要编辑的商品');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        $data['title'] = htmlspecialchars($data['title']);
        if (empty($data['title'])) {
            $this->baoError('产品名称不能为空');
        }
        $data['shop_id'] = (int) $data['shop_id'];
        if (!empty($data['shop_id'])) {
            $shop = D('Shop')->find($data['shop_id']);
            if (empty($shop)) {
                $this->baoError('请选择正确的商家');
            }
            if($shop['city_id'] != $this->_fenzhan['city_id']){
                $this->baoError('商家不属于当前城市');
            }
            $data['city_id'] = $shop['city_id'];
            $data['area_id'] = $shop['area_id'];
        } else {
            $data['city_id'] = $this->_CONFIG['site']['city_id'];
        }
        $data['photo'] = htmlspecialchars($data['photo']);
        if (empty($data['photo'])) {
            $this->baoError('请上传缩略图');
        }
        if (!isImage($data['photo'])) {
            $this->baoError('缩略图格式不正确');
        }
        $data['type'] = (int) $data['type'];
        $data['price'] = (int) ($data['price']);
        $data['max'] = (int) ($data['max']);
        if (empty($data['price'])) {
            $this->baoError('总需人次不能为空');
        }
        if (empty($data['max'])) {
            $this->baoError('单人最大购买数不能为空');
        }
        if ($data['type'] == 2) {
            if ($data['price'] % 5 != 0) {
                $this->baoError('总需人次必须为5的倍数');
            }
            if ($data['max'] % 5 != 0) {
                $this->baoError('单人最大购买数必须为5的倍数');
            }
        }
        if ($data['type'] == 3) {
            if ($data['price'] % 10 != 0) {
                $this->baoError('总需人次必须为10的倍数');
            }
            if ($data['max'] % 10 != 0) {
                $this->baoError('单人最大购买数必须为10的倍数');
            }
        }

        $data['settlement_price'] = (int) ($data['settlement_price'] * 100);
        if (!empty($data['settlement_price'])) {
            if ($data['settlement_price'] >= $data['price'] * 100) {
                $this->baoError('结算价格必须小于总需人次');
            }
        }
        $data['details'] = SecurityEditorHtml($data['details']);
        if (empty($data['details'])) {
            $this->baoError('商品详情不能为空');
        }
        if ($words = D('Sensitive')->checkWords($data['details'])) {
            $this->baoError('商品详情含有敏感词：' . $words);
        }
        return $data;
    }

    public function delete($goods_id = 0) {
        if (is_numeric($goods_id) && ($goods_id = (int) $goods_id)) {
            $obj = D('Cloudgoods');
            $goods = $obj->find($goods_id);
            if($goods['city_id'] != $this->_fenzhan['city_id']){
                $this->baoError('商品不属于当前城市');
            }
            $obj->save(array('goods_id' => $goods_id, 'closed' => 1));
            $this->baoSuccess('删除成功！', U('cloudgoods/index'));
        } else {
            $goods_id = $this->_post('goods_id', false);
            if (is_array($goods_id)) {
                $obj = D('Cloudgoods');
                foreach ($goods_id as $id) {
                    $obj->save(array('goods_id' => $id, 'closed' => 1));
                }
                $this->baoSuccess('删除成功！', U('cloudgoods/index'));
            }
            $this->baoError('请选择要删除的商品');
        }
    }

    public function audit($goods_id = 0) {
        if (is_numeric($goods_id) && ($goods_id = (int) $goods_id)) {
            $obj = D('Cloudgoods');
            $goods = $obj->find($goods_id);
            if($goods['city_id'] != $this->_fenzhan['city_id']){
                $this->baoError('商品不属于当前城市');
            }
            $r = $obj->where('goods_id =' . $goods_id)->find();
            if (empty($r['settlement_price'])) {
                $this->baoError('不设置结算价格无法审核通过！');
            }
            $obj->save(array('goods_id' => $goods_id, 'audit' => 1));
            $this->baoSuccess('审核成功！', U('cloudgoods/index'));
        } else {
            $goods_id = $this->_post('goods_id', false);
            if (is_array($goods_id)) {
                $obj = D('Cloudgoods');
                $error = 0;
                foreach ($goods_id as $id) {
                    $r = $obj->where('goods_id =' . $id)->find();
                    if (empty($r['settlement_price'])) {
                        $error++;
                        //$this->baoError('遇到了结算价格没有设置的，该条无法审核通过！');
                    }
                    $obj->save(array('goods_id' => $id, 'audit' => 1));
                }
                $this->baoSuccess('审核成功！' . $error . '条失败', U('cloudgoods/index'));
            }
            $this->baoError('请选择要审核的商品');
        }
    }

}
