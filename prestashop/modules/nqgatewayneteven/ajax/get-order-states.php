<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../init.php');
include(dirname(__FILE__).'/../classes/Gateway.php');

if (Tools::getValue('token') != Tools::encrypt(Configuration::get('PS_SHOP_NAME')))
	die(Tools::displayError());

$action = Tools::getValue('action');
$id = Tools::getValue('id');
$field = Tools::getValue('field');
$order_states = explode(':', Gateway::getConfig($field));

if (empty($order_states[0]))
	$order_states = array();

if ($action != 'display')
{
	$position = 0;
	if ($action == 'add')
		array_push($order_states, $id);
	elseif ($action == 'up')
	{
		foreach ($order_states as $key => $id_order_state )
			if ($id_order_state == $id)
			{
				$position = (int)$id_order_state;
				break;
			}

		if ($position != 0)
		{
			$temp = $order_states[$position];
			$order_states[$position] = $order_states[$position-1];
			$order_states[$position-1] = $temp;
		}
	}
	elseif ($action == 'del')
	{
		foreach ($order_states as $key => $id_order_state)
			if ($id_order_state == $id)
			{
				unset($order_states[$key]);
				break;
			}

	}
	else
	{
		foreach ($order_states as $key => $id_order_state)
			if ($id_order_state == $id)
			{
				$position = $key;
				break;
			}

		if ($position != count($order_states)-1)
		{
			$temp = $order_states[$position];
			$order_states[$position] = $order_states[$position+1];
			$order_states[$position+1] = $temp;
		}
	}

	Gateway::updateConfig($field, implode(':', $order_states));
}

//affichage des state en tableau.
if (count($order_states) > 0)
{
	$result = Db::getInstance()->ExecuteS('
					SELECT `id_order_state`, `name`
					FROM `'._DB_PREFIX_.'order_state_lang`
					WHERE `id_lang` = '.(int)$cookie->id_lang.'
					AND `id_order_state` IN ('.implode(',', pSQL($order_states)).' )
				');

	$order_state_names = array();
	foreach ($result as $key => $value)
		$order_state_names[$value['id_order_state']] = $value['name'];

	if (count($order_state_names) > 0)
		foreach ($order_state_names as $id_order_state => $order_state_name)
		{
			echo '
			<li id="state_'.(int)$id_order_state.'" data="'.(int)$id_order_state.'" class="order_state_line">
				<span class="delete_'.($field == 'ORDER_STATE_BEFORE' ? 'before' : 'after').'"  style="cursor:pointer;"><img src="../../img/admin/delete.gif" alt="X" /></span>
				<span>'.$order_state_name.'</span>
				<span class="up_'.($field == 'ORDER_STATE_BEFORE' ? 'before' : 'after').'" style="cursor:pointer;">▲</span>
				<span class="down_'.($field == 'ORDER_STATE_BEFORE' ? 'before' : 'after').'" style="cursor:pointer;">▼</span>
			</li>';
		}

}