<?php /* Smarty version Smarty-3.1.11, created on 2015-01-24 11:39:39
         compiled from "/home/student/public_html/prestashop/themes/prestashop/breadcrumb.tpl" */ ?>
<?php /*%%SmartyHeaderCode:159930592454c313fb4a4670-74742510%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '084c9a51f817ad6218c4f03bdb1a6623157ad349' => 
    array (
      0 => '/home/student/public_html/prestashop/themes/prestashop/breadcrumb.tpl',
      1 => 1372306200,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '159930592454c313fb4a4670-74742510',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'base_dir' => 0,
    'path' => 0,
    'navigationPipe' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_54c313fb535cd7_88279852',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54c313fb535cd7_88279852')) {function content_54c313fb535cd7_88279852($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/student/public_html/prestashop/tools/smarty/plugins/modifier.escape.php';
?>

<!-- Breadcrumb -->
<?php if (isset(Smarty::$_smarty_vars['capture']['path'])){?><?php $_smarty_tpl->tpl_vars['path'] = new Smarty_variable(Smarty::$_smarty_vars['capture']['path'], null, 0);?><?php }?>
<div class="breadcrumb">
	<a href="<?php echo $_smarty_tpl->tpl_vars['base_dir']->value;?>
" title="<?php echo smartyTranslate(array('s'=>'return to'),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
"><?php echo smartyTranslate(array('s'=>'Home'),$_smarty_tpl);?>
</a><?php if (isset($_smarty_tpl->tpl_vars['path']->value)&&$_smarty_tpl->tpl_vars['path']->value){?><span class="navigation-pipe"><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['navigationPipe']->value, 'html', 'UTF-8');?>
</span><?php if (!strpos($_smarty_tpl->tpl_vars['path']->value,'span')){?><span class="navigation_page"><?php echo $_smarty_tpl->tpl_vars['path']->value;?>
</span><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['path']->value;?>
<?php }?><?php }?>
</div>
<!-- /Breadcrumb --><?php }} ?>