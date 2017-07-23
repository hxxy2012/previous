<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id$
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array (
  'shop_id' => 
  array (
    'field' => 'shop_id',
    'label' => '商家',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '商户ID',
    'default' => '',
    'SO' => false,
  ),
  'id_name' => 
  array (
    'field' => 'id_name',
    'label' => '店主姓名',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '店主姓名',
    'default' => '',
    'SO' => 'like',
  ),
  'id_number' => 
  array (
    'field' => 'id_number',
    'label' => '店主身份证号',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '店主身份证号',
    'default' => '',
    'SO' => '=',
  ),
  'id_photo' => 
  array (
    'field' => 'id_photo',
    'label' => '店主身份证图',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'photo',
    'comment' => '店主身份证图',
    'default' => '',
    'SO' => false,
  ),
  'shop_photo' => 
  array (
    'field' => 'shop_photo',
    'label' => '商铺实景图',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'photo',
    'comment' => '商铺实景图',
    'default' => '',
    'SO' => false,
  ),
  'verify_dianzhu' => 
  array (
    'field' => 'verify_dianzhu',
    'label' => '认证状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '店主验证: 0:待审核，1:审核通过, 2:审核失败',
    'default' => '',
    'SO' => false,
  ),
  'yz_number' => 
  array (
    'field' => 'yz_number',
    'label' => '营业执照号',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '营业执照号',
    'default' => '',
    'SO' => '=',
  ),
  'yz_photo' => 
  array (
    'field' => 'yz_photo',
    'label' => '营业执照图',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'photo',
    'comment' => '营业执照图',
    'default' => '',
    'SO' => false,
  ),
  'verify_yyzz' => 
  array (
    'field' => 'verify_yyzz',
    'label' => '营业执照验证状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '营业执照验证: 0:待审核，1:审核通过, 2:审核失败',
    'default' => '',
    'SO' => false,
  ),
  'cy_number' => 
  array (
    'field' => 'cy_number',
    'label' => '餐饮执照号',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '餐饮执照号',
    'default' => '',
    'SO' => '=',
  ),
  'company_name' => 
  array (
    'field' => 'company_name',
    'label' => '公司名称',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '公司名称',
    'default' => '',
    'SO' => '=',
  ),
  'cy_photo' => 
  array (
    'field' => 'cy_photo',
    'label' => '餐饮执照图',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'photo',
    'comment' => '餐饮执照图',
    'default' => '',
    'SO' => false,
  ),
  'verify_cy' => 
  array (
    'field' => 'verify_cy',
    'label' => '餐饮验证状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '餐饮验证: 0:待审核，1:审核通过, 2:审核失败',
    'default' => '',
    'SO' => false,
  ),
  'refuse' => 
  array (
    'field' => 'refuse',
    'label' => '拒绝原因',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'textarea',
    'comment' => '拒绝原因',
    'default' => '',
    'SO' => false,
  ),
  'verify' => 
  array (
    'field' => 'verify',
    'label' => '审核状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '审核状态   0:待审核，1:审核通过, 2:审核失败',
    'default' => '',
    'SO' => false,
  ),
  'verify_time' => 
  array (
    'field' => 'verify_time',
    'label' => '审核时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'unixtime',
    'comment' => '审核时间',
    'default' => '',
    'SO' => 'betweendate',
  ),
  'updatetime' => 
  array (
    'field' => 'updatetime',
    'label' => '申请时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'unixtime',
    'comment' => '申请时间',
    'default' => '',
    'SO' => false,
  ),
);