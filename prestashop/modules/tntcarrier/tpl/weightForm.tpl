{*
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
*}

<br/>
<fieldset style="width:400px">
  <legend><img src="../img/admin/delivery.gif" />{l s='Shipping information'}</legend>
  <form action="{$var.currentIndex}&view{$var.table}&token={$var.token}" method="post" style="margin-top:10px;">
{l s='Your package must be over 0.1 KG and' mod='tntcarrier'} {$weight} {l s='KG' mod='tntcarrier'}<br/><br/>
{foreach from=$productWeight item=product}
{$product.name} {l s='Weight (KG)' mod='tntcarrier'}: <input type="text" name="product_weight_{$product.id}"/><br/>
{/foreach}
<br/>
<input type="submit" value="{l s='Modify' mod='tntcarrier'}" class="button" />
  </form>
</fieldset>
