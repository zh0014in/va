<?php /* Smarty version Smarty-3.1.11, created on 2015-01-24 01:17:57
         compiled from "/home/student/public_html/prestashop/modules/editorial/editorial.tpl" */ ?>
<?php /*%%SmartyHeaderCode:146849575954c28245e35690-98714206%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '08d687ee40afb66ab045a2bf007a2ba861f2fc9f' => 
    array (
      0 => '/home/student/public_html/prestashop/modules/editorial/editorial.tpl',
      1 => 1372306200,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '146849575954c28245e35690-98714206',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'editorial' => 0,
    'homepage_logo' => 0,
    'image_path' => 0,
    'link' => 0,
    'image_width' => 0,
    'image_height' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_54c28245ee95f6_57493034',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54c28245ee95f6_57493034')) {function content_54c28245ee95f6_57493034($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_escape')) include '/home/student/public_html/prestashop/tools/smarty/plugins/modifier.escape.php';
?>

<!-- Module Editorial -->
<div id="editorial_block_center" class="editorial_block">
	<?php if ($_smarty_tpl->tpl_vars['editorial']->value->body_home_logo_link){?><a href="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['editorial']->value->body_home_logo_link, 'htmlall', 'UTF-8');?>
" title="<?php echo stripslashes(smarty_modifier_escape($_smarty_tpl->tpl_vars['editorial']->value->body_title, 'htmlall', 'UTF-8'));?>
"><?php }?>
	<?php if ($_smarty_tpl->tpl_vars['homepage_logo']->value){?><img src="<?php echo $_smarty_tpl->tpl_vars['link']->value->getMediaLink($_smarty_tpl->tpl_vars['image_path']->value);?>
" alt="<?php echo stripslashes(smarty_modifier_escape($_smarty_tpl->tpl_vars['editorial']->value->body_title, 'htmlall', 'UTF-8'));?>
" <?php if ($_smarty_tpl->tpl_vars['image_width']->value){?>width="<?php echo $_smarty_tpl->tpl_vars['image_width']->value;?>
"<?php }?> <?php if ($_smarty_tpl->tpl_vars['image_height']->value){?>height="<?php echo $_smarty_tpl->tpl_vars['image_height']->value;?>
" <?php }?>/><?php }?>
	<?php if ($_smarty_tpl->tpl_vars['editorial']->value->body_home_logo_link){?></a><?php }?>
	<?php if ($_smarty_tpl->tpl_vars['editorial']->value->body_logo_subheading){?><p id="editorial_image_legend"><?php echo stripslashes($_smarty_tpl->tpl_vars['editorial']->value->body_logo_subheading);?>
</p><?php }?>
	<?php if ($_smarty_tpl->tpl_vars['editorial']->value->body_title){?><h1><?php echo stripslashes($_smarty_tpl->tpl_vars['editorial']->value->body_title);?>
</h1><?php }?>
	<?php if ($_smarty_tpl->tpl_vars['editorial']->value->body_subheading){?><h2><?php echo stripslashes($_smarty_tpl->tpl_vars['editorial']->value->body_subheading);?>
</h2><?php }?>
	<?php if ($_smarty_tpl->tpl_vars['editorial']->value->body_paragraph){?><div class="rte"><?php echo stripslashes($_smarty_tpl->tpl_vars['editorial']->value->body_paragraph);?>
</div><?php }?>
</div>
<!-- /Module Editorial -->
<?php }} ?>