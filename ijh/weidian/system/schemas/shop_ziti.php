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
  'addr_id' => 
  array (
    'field' => 'addr_id',
    'label' => '自提地址ID',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'shop_id' => 
  array (
    'field' => 'shop_id',
    'label' => '商户ID',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'title' => 
  array (
    'field' => 'title',
    'label' => '自提点名称',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '自提点名称便于买家理解和管理',
    'default' => '',
    'SO' => false,
  ),
  'province_id' => 
  array (
    'field' => 'province_id',
    'label' => '省份ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '省份ID',
    'default' => '',
    'SO' => false,
  ),
  'city_id' => 
  array (
    'field' => 'city_id',
    'label' => '城市ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '城市ID',
    'default' => '',
    'SO' => false,
  ),
  'area_id' => 
  array (
    'field' => 'area_id',
    'label' => '区县ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '区县ID',
    'default' => '',
    'SO' => false,
  ),
  'address_detail' => 
  array (
    'field' => 'address_detail',
    'label' => '自提点的具体地址',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '自提点的具体地址',
    'default' => '',
    'SO' => false,
  ),
  'phone' => 
  array (
    'field' => 'phone',
    'label' => '联系电话',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '联系电话，便于买家联系',
    'default' => '',
    'SO' => false,
  ),
  'fuwu_stime' => 
  array (
    'field' => 'fuwu_stime',
    'label' => '接待时间开始',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '接待时间开始',
    'default' => '',
    'SO' => false,
  ),
  'fuwu_ltime' => 
  array (
    'field' => 'fuwu_ltime',
    'label' => '接待时间结束',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '接待时间结束',
    'default' => '',
    'SO' => false,
  ),
  'photo1' => 
  array (
    'field' => 'photo1',
    'label' => '自提点照片1',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '自提点照片1',
    'default' => '',
    'SO' => false,
  ),
  'photo2' => 
  array (
    'field' => 'photo2',
    'label' => '自提点照片2',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '自提点照片2',
    'default' => '',
    'SO' => false,
  ),
  'photo3' => 
  array (
    'field' => 'photo3',
    'label' => '自提点照片3',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '自提点照片3',
    'default' => '',
    'SO' => false,
  ),
  'photo4' => 
  array (
    'field' => 'photo4',
    'label' => '自提点照片4',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '自提点照片4',
    'default' => '',
    'SO' => false,
  ),
  'description' => 
  array (
    'field' => 'description',
    'label' => '描述',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '可以描述自提点的活动或相关备注信息（最多200个字）',
    'default' => '',
    'SO' => false,
  ),
  'is_store' => 
  array (
    'field' => 'is_store',
    'label' => '是否同时作为线下门店接待',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '同时作为线下门店接待',
    'default' => '',
    'SO' => false,
  ),
  'orderby' => 
  array (
    'field' => 'orderby',
    'label' => '排序',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '排序',
    'default' => '50',
    'SO' => false,
  ),
  'dateline' => 
  array (
    'field' => 'dateline',
    'label' => '创建时间',
    'pk' => false,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '创建时间',
    'default' => '',
    'SO' => false,
  ),
  'closed' => 
  array (
    'field' => 'closed',
    'label' => '删除标识',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '是否删除标识，0否，1已删除',
    'default' => '',
    'SO' => false,
  ),
  'data_city_id' => 
  array (
    'field' => 'data_city_id',
    'label' => '城市ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '城市ID',
    'default' => '',
    'SO' => false,
  ),
);