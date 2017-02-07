<?php /*%%SmartyHeaderCode:56757563954c282459dd1c4-82816017%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '47d3e0d6d0b8644483a3b108f49e2fc17d904ed9' => 
    array (
      0 => '/home/student/public_html/prestashop/modules/blockcategories/blockcategories.tpl',
      1 => 1372306200,
      2 => 'file',
    ),
    '47299dbe4f92c31b8cefe602edc74527d6b559eb' => 
    array (
      0 => '/home/student/public_html/prestashop/modules/blockcategories/category-tree-branch.tpl',
      1 => 1372306200,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '56757563954c282459dd1c4-82816017',
  'version' => 'Smarty-3.1.11',
  'unifunc' => 'content_54c313fb0fc479_58355885',
  'has_nocache_code' => false,
  'cache_lifetime' => 31536000,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54c313fb0fc479_58355885')) {function content_54c313fb0fc479_58355885($_smarty_tpl) {?>
<!-- Block categories module -->
<div id="categories_block_left" class="block">
	<h4>Categories</h4>
	<div class="block_content">
		<ul class="tree dhtml">
									
<li >
	<a href="http://localhost/prestashop/category.php?id_category=2" class="selected" title="Now that you can buy movies from the iTunes Store and sync them to your iPod, the whole world is your theater.">iPods</a>
	</li>

												
<li >
	<a href="http://localhost/prestashop/category.php?id_category=3"  title="Wonderful accessories for your iPod">Accessories</a>
	</li>

												
<li class="last">
	<a href="http://localhost/prestashop/category.php?id_category=4"  title="The latest Intel processor, a bigger hard drive, plenty of memory, and even more new features all fit inside just one liberating inch. The new Mac laptops have the performance, power, and connectivity of a desktop computer. Without the desk part.">Laptops</a>
	</li>

							</ul>
		
		<script type="text/javascript">
		// <![CDATA[
			// we hide the tree only if JavaScript is activated
			$('div#categories_block_left ul.dhtml').hide();
		// ]]>
		</script>
	</div>
</div>
<!-- /Block categories module -->
<?php }} ?>