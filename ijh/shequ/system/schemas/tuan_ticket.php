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
  'ticket_id' => 
  array (
    'field' => 'ticket_id',
    'label' => '入场券ID',
    'pk' => true,
    'add' => false,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '入场券ID',
    'default' => '',
    'SO' => '=',
  ),
  'uid' => 
  array (
    'field' => 'uid',
    'label' => '用户UID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '用户UID',
    'default' => '',
    'SO' => '=',
  ),
  'shop_id' => 
  array (
    'field' => 'shop_id',
    'label' => '商户ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '商户ID',
    'default' => '',
    'SO' => '=',
  ),
  'tuan_id' => 
  array (
    'field' => 'tuan_id',
    'label' => '团购ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '团购ID',
    'default' => '',
    'SO' => '=',
  ),
  'order_id' => 
  array (
    'field' => 'order_id',
    'label' => '订单ID',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '订单ID',
    'default' => '',
    'SO' => '=',
  ),
  'number' => 
  array (
    'field' => 'number',
    'label' => '密码',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => false,
    'type' => 'text',
    'comment' => '密码',
    'default' => '',
    'SO' => false,
  ),
  'count' => 
  array (
    'field' => 'count',
    'label' => '份数',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '份数',
    'default' => '',
    'SO' => false,
  ),
  'ltime' => 
  array (
    'field' => 'ltime',
    'label' => '过期时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '过期时间',
    'default' => '',
    'SO' => 'betweendate',
  ),
  'use_time' => 
  array (
    'field' => 'use_time',
    'label' => '使用时间',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '使用时间',
    'default' => '',
    'SO' => 'betweendate',
  ),
  'status' => 
  array (
    'field' => 'status',
    'label' => '状态',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '0:未使用，1:已使用,-1:已退款',
    'default' => '',
    'SO' => '=',
  ),
  'dateline' => 
  array (
    'field' => 'dateline',
    'label' => '创建时间',
    'pk' => false,
    'add' => false,
    'edit' => false,
    'html' => false,
    'empty' => false,
    'show' => true,
    'list' => true,
    'type' => 'text',
    'comment' => '创建时间',
    'default' => '',
    'SO' => 'betweendate',
  ),
);