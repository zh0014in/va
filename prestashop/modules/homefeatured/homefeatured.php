<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class HomeFeatured extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'homefeatured';
		$this->tab = 'front_office_features';
		$this->version = '0.9';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();
		
		$this->displayName = $this->l('Featured Products on the homepage');
		$this->description = $this->l('Displays Featured Products in the middle of your homepage.');
		
		$this->defaultNumberProducts = 8;
	}

	public function install()
	{
		return parent::install() && Configuration::updateValue('HOME_FEATURED_NBR', 8) && $this->registerHook('home');
	}
	
	public function uninstall()
	{
		return Configuration::deleteByName('HOME_FEATURED_NBR') && parent::uninstall();
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitHomeFeatured'))
		{
			$nbr = (int)Tools::getValue('nbr');
			if ($nbr <= 0)
				$errors[] = $this->l('Invalid number of products');
			else
				Configuration::updateValue('HOME_FEATURED_NBR', (int)$nbr);
			if (isset($errors) && count($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		return '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<p>'.$this->l('In order to add products to your homepage, just add them to the "home" category.').'</p><br />
				<label>'.$this->l('Number of products displayed').'</label>
				<div class="margin-form">
					<input type="text" size="5" name="nbr" value="'.Tools::safeOutput(Tools::getValue('nbr', (int)Configuration::get('HOME_FEATURED_NBR'))).'" />
					<p class="clear">'.sprintf($this->l('The number of products displayed on homepage (default: "%s").'), (int)$this->defaultNumberProducts).'</p>
					
				</div>
				<center><input type="submit" name="submitHomeFeatured" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}

	public function hookHome($params)
	{
		global $smarty;

		$category = new Category(1, (int)Configuration::get('PS_LANG_DEFAULT'));
		$nb = (int)Configuration::get('HOME_FEATURED_NBR');

		$smarty->assign(array(
		'products' => $category->getProducts((int)$params['cookie']->id_lang, 1, ($nb ? $nb : (int)$this->defaultNumberProducts)),
		'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
		'homeSize' => Image::getSize('home')));

		return $this->display(__FILE__, 'homefeatured.tpl');
	}
}
