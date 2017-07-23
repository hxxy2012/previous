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
  'order_id' => 
  array (
    'field' => 'order_id',
    'label' => '订单ID',
    'pk' => true,
    'add' => true,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '订单ID',
    'default' => '',
    'SO' => '=',
  ),
  'product_name' => 
  array (
    'field' => 'product_name',
    'label' => '商品名称',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '商品名称',
    'default' => '',
    'SO' => '=',
  ),
    'product_number' => 
  array (
    'field' => 'product_number',
    'label' => '商品数量',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '商品数量',
    'default' => '',
    'SO' => '=',
  ),
  'product_price' => 
  array (
    'field' => 'product_price',
    'label' => '商品总价',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '商品总价',
    'default' => '0.00',
    'SO' => '=',
  ), 
 'tuan_time' => 
  array (
    'field' => 'tuan_time',
    'label' => '成团有效时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '成团有效时间',
    'default' => '',
    'SO' => '=',
  ),
    'money_master' => 
  array (
    'field' => 'money_master',
    'label' => '团长佣金',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '团长佣金',
    'default' => '0.00',
    'SO' => '=',
  ),
  'money_master_paid' => 
  array (
    'field' => 'money_master_paid',
    'label' => '支付佣金',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '支付佣金',
    'default' => '0.00',
    'SO' => '=',
  ),
   'money_master_time' => 
  array (
    'field' => 'money_master_time',
    'label' => '佣金支付时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '佣金支付时间',
    'default' => '',
    'SO' => false,
  ),  
    'spend_number' => 
  array (
    'field' => 'spend_number',
    'label' => '自提单审核密码',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '自提单审核密码',
    'default' => '',
    'SO' => false,
  ),
     'spend_status' => 
  array (
    'field' => 'spend_status',
    'label' => '自提单审核状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '自提单审核状态',
    'default' => '',
    'SO' => false,
  ),
     'freight' => 
  array (
    'field' => 'freight',
    'label' => '运费',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '运费',
    'default' => '',
    'SO' => false,
  ),
      'group_id' => 
  array (
    'field' => 'group_id',
    'label' => '组团ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '组团ID',
    'default' => '',
    'SO' => false,
  ),
      'is_money_pre' => 
  array (
    'field' => 'is_money_pre',
    'label' => '是否全款',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '是否全款',
    'default' => '',
    'SO' => false,
  ),
  'uid' => 
  array (
    'field' => 'uid',
    'label' => '用户ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'int',
    'comment' => '用户ID',
    'default' => '',
    'SO' => '=',
  ),  
    'money_need_pay' => 
  array (
    'field' => 'money_need_pay',
    'label' => '预付款',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '预付款',
    'default' => '',
    'SO' => '=',
  ),    
  'money_paid' => 
  array (
    'field' => 'money_paid',
    'label' => '预付款',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => true,
    'list' => true,
    'type' => 'number',
    'comment' => '预付款',
    'default' => '',
    'SO' => '=',
  ),   
);