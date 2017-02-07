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

class StockMvtCore extends ObjectModel
{
	public $id;

	public $id_product;
	public $id_product_attribute = null;
	public $id_order = null;
	public $id_employee = null;
	public $quantity;
	public $id_stock_mvt_reason;
	
	public $date_add;
	public $date_upd;
	
	protected $table = 'stock_mvt';
	protected $identifier = 'id_stock_mvt';
	
 	protected $fieldsRequired = array('id_product', 'id_stock_mvt_reason', 'quantity');
 	protected $fieldsValidate = array(
		'id_product' => 'isUnsignedId', 'id_product_attribute' => 'isUnsignedId', 
		'id_order' => 'isUnsignedId','id_employee' => 'isUnsignedId',
 		'quantity' => 'isInt', 'id_stock_mvt_reason' => 'isUnsignedId'
	);

	protected $webserviceParameters = array(
		'objectsNodeName' => 'stock_movements',
		'objectNodeName' => 'stock_movement',
		'fields' => array(
			'id_product' => array('xlink_resource'=> 'products'),
			'id_product_attribute' => array('xlink_resource'=> 'product_option_values'),
			'id_order' => array('xlink_resource'=> 'orders'),
			'id_employee' => array('xlink_resource'=> 'employees'),
			'id_stock_mvt_reason' => array('xlink_resource'=> 'stock_movement_reasons'),
		),
	);
	
	public function getFields()
	{
		parent::validateFields();
		$fields['id_product'] = (int)$this->id_product;
		$fields['id_product_attribute'] = (int)$this->id_product_attribute;
		$fields['id_order'] = (int)$this->id_order;
		$fields['id_employee'] = (int)$this->id_employee;
		$fields['id_stock_mvt_reason'] = (int)$this->id_stock_mvt_reason;
		$fields['quantity'] = (int)$this->quantity;
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}
	
	public function add($autodate = true, $nullValues = false, $update_quantity = true)
	{
		if (!parent::add($autodate, $nullValues))
			return false;
		
		if (!$update_quantity)
			return true;

		if ($this->id_product_attribute)
		{
			$product = new Product((int)$this->id_product, false, _PS_LANG_DEFAULT_);
			return (Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'product_attribute` 
				SET `quantity` = quantity+'.(int)$this->quantity.'
				WHERE `id_product` = '.(int)$product->id.' 
				AND `id_product_attribute` = '.(int)$this->id_product_attribute) 
			&& $product->updateQuantityProductWithAttributeQuantity());
		}
		else
			return Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'product` 
				SET `quantity` = quantity+'.(int)$this->quantity.' 
				WHERE `id_product` = '.(int)$this->id_product);
	}
	
	public static function addMissingMvt($id_employee)
	{
		$products_attributes = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_product`, coalesce(pa.`id_product_attribute`, 0) as id_product_attribute, (coalesce(pa.quantity,p.quantity) - SUM(coalesce(sm.`quantity`, 0))) as quantity
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product` = p.`id_product`)
		LEFT JOIN `'._DB_PREFIX_.'stock_mvt` sm ON (sm.`id_product` = p.`id_product` and sm.id_product_attribute = coalesce (pa.id_product_attribute,0))
		WHERE p.`active` = 1
		GROUP BY p.`id_product`, pa.`id_product_attribute`
		HAVING quantity != 0', false);

		while ($product = Db::getInstance()->nextRow($products_attributes))
		{
			if (!isset($product) || !is_array($product) || !isset($product['quantity'])  || !$product['quantity'])
				continue;
			$mvt = new StockMvt();
			foreach ($product as $k => $row)
				$mvt->{$k} = $row;
			$mvt->id_employee = (int)$id_employee;
			$mvt->id_stock_mvt_reason = _STOCK_MOVEMENT_MISSING_REASON_;
			$mvt->add(true, false, false);
		}
	}
}