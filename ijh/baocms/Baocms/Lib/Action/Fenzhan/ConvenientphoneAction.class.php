<?php

/*
 * 软件为合肥生活宝网络公司出品，未经授权许可不得使用！
 * 作者：baocms团队
 * 官网：www.baocms.com
 * 邮件: youge@baocms.com  QQ 800026911
 */

class ConvenientphoneAction extends CommonAction {

    private $create_fields = array('name', 'phone', 'expiry_date', 'orderby');
    private $edit_fields = array('name', 'phone', 'expiry_date', 'orderby');

    public function index() {
        $Convenientphone = D('Convenientphone');
        import('ORG.Util.Page'); // 导入分页类
        $map = array();
        
        $community = D('Community')->where(array('city_id'=>$this->_fenzhan['city_id']))->select();
        $communityids = array();
        foreach($community as $k=>$v){
            $communityids[]=$v['community_id'];
        }
        $communityids = implode(',',$communityids);
        
        $map['community_id'] = array('in',$communityids);
        
        $keyword = $this->_param('keyword', 'htmlspecialchars');
        if ($keyword) {
            $map['name|phone'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }
        $count = $Convenientphone->where($map)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        $list = $Convenientphone->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('keyword', $keyword);
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板
    }

    public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Convenientphone');
            if ($phone_id = $obj->add($data)) {
                $obj->cleanCache();
                $this->baoSuccess('添加成功', U('convenientphone/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->assign('areas', D('Area')->fetchAll());
            $this->assign('communitys', D('Community')->fetchAll());
            $this->display();
        }
    }

    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['name'] = htmlspecialchars($data['name']);
        if($data['community_id']){
            $community = D('Community')->find($data['community_id']);
            if($community['city_id'] != $this->_fenzhan['city_id']){
                $this->baoError('数据不属于当前城市');
            }    
        }
        if (empty($data['name'])) {
            $this->baoError('名称不能为空');
        }$data['phone'] = htmlspecialchars($data['phone']);
        if (empty($data['phone'])) {
            $this->baoError('便民电话不能为空');
        } if (!isMobile($data['phone']) && !isPhone($data['phone'])) {
            $this->baoError('便民电话格式不正确');
        }$data['expiry_date'] = htmlspecialchars($data['expiry_date']);
        if (!empty($data['expiry_date']) && !isDate($data['expiry_date'])) {
            $this->baoError('过期日期格式不正确');
        }$data['orderby'] = (int) $data['orderby'];
        return $data;
    }

    public function edit($phone_id = 0) {
        if ($phone_id = (int) $phone_id) {
            $obj = D('Convenientphone');
            if (!$detail = $obj->find($phone_id)) {
                $this->baoError('请选择要编辑的便民电话管理');
            }
            $community = D('Community')->find($detail['community_id']);
            if($community['city_id'] != $this->_fenzhan['city_id']){
                $this->error('数据不属于当前城市');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['phone_id'] = $phone_id;
                if (false !== $obj->save($data)) {
                    $obj->cleanCache();
                    $this->baoSuccess('操作成功', U('convenientphone/index'));
                }
                $this->baoError('操作失败');
            } else {
                $this->assign('areas', D('Area')->fetchAll());
                $this->assign('communitys', D('Community')->fetchAll());
                $this->assign('detail', $detail);
                $this->display();
            }
        } else {
            $this->baoError('请选择要编辑的便民电话管理');
        }
    }

    private function editCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->edit_fields);
        if($data['community_id']){
            $community = D('Community')->find($data['community_id']);
            if($community['city_id'] != $this->_fenzhan['city_id']){
                $this->baoError('数据不属于当前城市');
            }
        }
        $data['name'] = htmlspecialchars($data['name']);
        if (empty($data['name'])) {
            $this->baoError('名称不能为空');
        }$data['phone'] = htmlspecialchars($data['phone']);
        if (empty($data['phone'])) {
            $this->baoError('便民电话不能为空');
        } if (!isMobile($data['phone']) && !isPhone($data['phone'])) {
            $this->baoError('便民电话格式不正确');
        }$data['expiry_date'] = htmlspecialchars($data['expiry_date']);
        if (!empty($data['expiry_date']) && !isDate($data['expiry_date'])) {
            $this->baoError('过期日期格式不正确');
        }$data['orderby'] = (int) $data['orderby'];
        return $data;
    }

    public function delete($phone_id = 0) {
        if (is_numeric($phone_id) && ($phone_id = (int) $phone_id)) {
            $obj = D('Convenientphone');
            if (!$detail = $obj->find($phone_id)) {
                $this->baoError('请选择要编辑的便民电话管理');
            }
            $community = D('Community')->find($detail['community_id']);
            if($community['city_id'] != $this->_fenzhan['city_id']){
                $this->error('数据不属于当前城市');
            }
            $obj->delete($phone_id);
            $obj->cleanCache();
            $this->baoSuccess('删除成功！', U('convenientphone/index'));
        } else {
            $phone_id = $this->_post('phone_id', false);
            if (is_array($phone_id)) {
                $obj = D('Convenientphone');
                foreach ($phone_id as $id) {
                    $obj->delete($id);
                }
                $obj->cleanCache();
                $this->baoSuccess('删除成功！', U('convenientphone/index'));
            }
            $this->baoError('请选择要删除的便民电话管理');
        }
    }

    public function set($phone_id = 0) {
        if ($phone_id = (int) $phone_id) {
            $obj = D('Convenientphonemaps');
            if (!$detail = $obj->find($phone_id)) {
                $this->baoError('请选择要编辑的便民电话管理');
            }
            $community = D('Community')->find($detail['community_id']);
            if($community['city_id'] != $this->_fenzhan['city_id']){
                $this->error('数据不属于当前城市');
            }
            if ($this->isPost()) {
                $community_id = (int) $this->_param('community_id');
                $data = array();
                $data['phone_id'] = $phone_id;
                $data['community_id'] = $community_id;
                if (!$res = $obj->where(array('phone_id' => $phone_id, 'community_id' => $community_id))->select()) {
                    $obj->add($data);
                    $this->baoSuccess('操作成功', U('convenientphone/set',array('phone_id'=>$phone_id)));
                } else {
                    $this->baoError('该便民电话已存在');
                }
            } else {
                $this->assign('areas', D('Area')->fetchAll());
                $this->assign('communitys', D('Community')->fetchAll());
                import('ORG.Util.Page'); // 导入分页类
                $count = $obj->where(array('phone_id' => $phone_id))->count(); // 查询满足要求的总记录数 
                $Page = new Page($count, 25); // 实例化分页类 传入总记录数和每页显示的记录数
                $show = $Page->show(); // 分页显示输出
                $list = $obj->where(array('phone_id' => $phone_id))->limit($Page->firstRow . ',' . $Page->listRows)->select();
                $this->assign('list', $list); // 赋值数据集
                $this->assign('page', $show); // 赋值分页输出
                $this->assign('phone_id', $phone_id);
                $this->display();
            }
        } else {
            $this->baoError('请选择要设置的便民电话');
        }
    }

    public function cancel($phone_id, $community_id) {
        $phone_id = (int) $phone_id;
        $obj = D('Convenientphonemaps');
        if (!$detail = $obj->find($phone_id)) {
            $this->baoError('请选择要编辑的便民电话管理');
        }
        $community = D('Community')->find($detail['community_id']);
        if($community['city_id'] != $this->_fenzhan['city_id']){
            $this->error('数据不属于当前城市');
        }
        $community_id = (int) $community_id;
        if (empty($phone_id) || empty($community_id)) {
            $this->baoError('不匹配');
        }
        $obj = D('Convenientphonemaps');
        $obj->where(array('phone_id' => $phone_id, 'community_id' => $community_id))->delete();
        $obj->cleanCache();
        $this->baoSuccess('取消成功！', U('convenientphone/set', array('phone_id' => $phone_id)));
    }

}
