<?php /* Smarty version Smarty-3.1.8, created on 2016-08-16 14:11:18
         compiled from "admin:product/product/create.html" */ ?>
<?php /*%%SmartyHeaderCode:120135985357b2ae86331d51-55528398%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9ca60f67d4774abb25e7a2c9bc285a8330190d2f' => 
    array (
      0 => 'admin:product/product/create.html',
      1 => 1470380625,
      2 => 'admin',
    ),
  ),
  'nocache_hash' => '120135985357b2ae86331d51-55528398',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'pager' => 0,
    'MOD' => 0,
    'shop_id' => 0,
    'detail' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_57b2ae863af0c3_95464610',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b2ae863af0c3_95464610')) {function content_57b2ae863af0c3_95464610($_smarty_tpl) {?><?php if (!is_callable('smarty_function_link')) include '/data/htdocs/blhoney_com/public_html/system/plugins/smarty/function.link.php';
if (!is_callable('smarty_function_widget')) include '/data/htdocs/blhoney_com/public_html/system/plugins/smarty/function.widget.php';
?><?php echo $_smarty_tpl->getSubTemplate ("admin:common/header.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="page-title">
    <table width="100%" align="center" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="30" align="right"><img src="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/images/main-h5-ico.gif" alt="" /></td>
            <th><?php echo $_smarty_tpl->tpl_vars['MOD']->value['title'];?>
</th>
            <td align="right"><?php echo smarty_function_link(array('ctl'=>"product/product:index",'priv'=>"hidden",'class'=>"button"),$_smarty_tpl);?>
</td>
            <td width="15"></td>
        </tr>
    </table>
</div>
<div class="page-data"><form action="?product/product-create.html" mini-form="product-form" method="post" ENCTYPE="multipart/form-data">
        <table width="100%" border="0" cellspacing="0" class="table-data form">
            <input type="hidden" name="shop_id" value="<?php echo $_smarty_tpl->tpl_vars['shop_id']->value;?>
" />
            <tr>
                <th><span class="red">*</span>分类：</th>
                <td>
                    <select name="data[cate_id]" class="w-100">
                        <?php echo smarty_function_widget(array('id'=>"product/cate",'shop_id'=>$_smarty_tpl->tpl_vars['shop_id']->value,'value'=>$_smarty_tpl->tpl_vars['detail']->value['cate_id'],'type'=>"类型"),$_smarty_tpl);?>

                    </select>
                </td>
            </tr>
            <tr><th><span class="red">*</span>标题：</th><td><input type="text" name="data[title]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['detail']->value['title'])===null||$tmp==='' ? '' : $tmp);?>
" class="input w-200"/><span class="tip-comment">标题</span></td></tr>
            <tr><th><span class="red">*</span>图片：</th><td><input type="text" name="data[photo]" value="<?php echo $_smarty_tpl->tpl_vars['detail']->value['photo'];?>
" <?php if ($_smarty_tpl->tpl_vars['detail']->value['photo']){?>photo="<?php echo $_smarty_tpl->tpl_vars['pager']->value['img'];?>
/<?php echo $_smarty_tpl->tpl_vars['detail']->value['photo'];?>
"<?php }?> class="input w-200" />&nbsp;&nbsp;&nbsp;<input type="file" name="data[photo]" class="input w-100" /><span class="tip-comment">图片</span></td></tr>
            <tr><th><span class="red">*</span>价格：</th><td><input type="text" name="data[price]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['detail']->value['price'])===null||$tmp==='' ? '0.00' : $tmp);?>
" class="input w-100"/><span class="tip-comment">价格</span></td></tr>
            <tr><th><span class="red">*</span>打包费：</th><td><input type="text" name="data[package_price]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['detail']->value['package_price'])===null||$tmp==='' ? '0.00' : $tmp);?>
" class="input w-100"/><span class="tip-comment">打包费,0:免打包费</span></td></tr>
            <tr><th><span class="red">*</span>销量：</th><td><input type="text" name="data[sales]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['detail']->value['sales'])===null||$tmp==='' ? '' : $tmp);?>
" class="input w-100"/><span class="tip-comment">销量</span></td></tr>
            <tr>
                <th>类型：</th>
                <td>
                    <ul class="group-list">
                        <li><label><input type="radio" name="data[sale_type]" value="0" checked="checked">普通</label></li>
                        <li><label><input type="radio" name="data[sale_type]" value="1">限量</label></li>
                        <div class="clear-both"></div>
                    </ul>
                </td>
            </tr>
            <tr id="tr_sale_sku1" class="hide">
                <th>限购数：</th><td><input type="text" name="data[sale_sku]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['detail']->value['sale_sku'])===null||$tmp==='' ? '' : $tmp);?>
" class="input w-100"/></td></tr>
            <tr id="tr_sale_sku2" class="hide">
                <th>已购数：</th><td><input type="text" name="data[sale_count]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['detail']->value['sale_count'])===null||$tmp==='' ? '' : $tmp);?>
" class="input w-100"/></td>
            </tr>
            <tr><th><span class="red">*</span>描述：</th><td><textarea name="data[intro]" class="textarea"><?php echo $_smarty_tpl->tpl_vars['detail']->value['intro'];?>
</textarea><br /></td></tr>
<tr><th><span class="red">*</span>排序：</th><td><input type="text" name="data[orderby]" value="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['detail']->value['orderby'])===null||$tmp==='' ? '50' : $tmp);?>
" class="input w-100"/><span class="tip-comment">排序</span></td></tr>
    <tr><th class="clear-th-bottom"></th><td class="clear-td-bottom" colspan="10"><input type="submit" class="bt-big" value="提交数据" /></td></tr>
</table>
</form></div>
<script type="text/javascript">
        $("[name='data[sale_type]']").click(function () {
            if ($(this).val() == 1) {
                $("#tr_sale_sku1").show();
                $("#tr_sale_sku2").show();
            } else {
                $("#tr_sale_sku2").hide();
                $("#tr_sale_sku1").hide();
            }
        });
        $("[name='data[sale_type]']:checked").trigger("click");
</script>
<?php echo $_smarty_tpl->getSubTemplate ("admin:common/footer.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>