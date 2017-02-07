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

class ManufacturerCore extends ObjectModel
{
	public 		$id;

	/** @var string Name */
	public 		$name;

	/** @var mixed A description */
	public 		$description;

	/** @var mixed A short description */
	public 		$short_description;

	/** @var int Address */
	public 		$id_address;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	/** @var string Friendly URL */
	public 		$link_rewrite;

	/** @var mixed Meta title */
	public 		$meta_title;

	/** @var mixed Meta keywords */
	public 		$meta_keywords;

	/** @var mixed Meta description */
	public 		$meta_description;

	/** @var boolean active */
	public		$active;

 	protected 	$fieldsRequired = array('name');
 	protected 	$fieldsSize = array('name' => 64);
 	protected 	$fieldsValidate = array('name' => 'isCatalogName');

	protected	$fieldsSizeLang = array('short_description' => 254, 'meta_title' => 128, 'meta_description' => 255, 'meta_description' => 255);
	protected	$fieldsValidateLang = array('description' => 'isCleanHtml', 'short_description' => 'isCleanHtml', 'meta_title' => 'isGenericName', 'meta_description' => 'isGenericName', 'meta_keywords' => 'isGenericName');

	protected 	$table = 'manufacturer';
	protected 	$identifier = 'id_manufacturer';

	protected	$webserviceParameters = array(
		'fields' => array(
			'active' => array(),
			'link_rewrite' => array('getter' => 'getLink', 'setter' => false),
		),
		'associations' => array(
			'addresses' => array('resource' => 'address', 'setter' => false, 'fields' => array(
				'id' => array('xlink_resource' => 'addresses'),
			)),
		),
	);

	public function __construct($id = null, $id_lang = null)
	{
		parent::__construct($id, $id_lang);

		/* Get the manufacturer's id_address */
		$this->id_address = $this->getManufacturerAddress();
		$this->link_rewrite = $this->getLink();
		$this->image_dir = _PS_MANU_IMG_DIR_;
	}

	public function getFields()
	{
		parent::validateFields();
		if (isset($this->id))
			$fields['id_manufacturer'] = (int)($this->id);
		$fields['name'] = pSQL($this->name);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		$fields['active'] = (int)($this->active);
		return $fields;
	}

	public function getTranslationsFieldsChild()
	{
		$fieldsArray = array('description', 'short_description', 'meta_title', 'meta_keywords', 'meta_description');
		$fields = array();
		$languages = Language::getLanguages(false);
		$defaultLanguage = _PS_LANG_DEFAULT_;
		foreach ($languages as $language)
		{
			$fields[$language['id_lang']]['id_lang'] = $language['id_lang'];
			$fields[$language['id_lang']][$this->identifier] = (int)($this->id);

			foreach ($fieldsArray as $field)
			{
				if (!Validate::isTableOrIdentifier($field))
					die(Tools::displayError());

				/* Check fields validity */
				if (isset($this->{$field}[$language['id_lang']]) && !empty($this->{$field}[$language['id_lang']]))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']], true);
				elseif (in_array($field, $this->fieldsRequiredLang))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage]);
				else
					$fields[$language['id_lang']][$field] = '';

			}

			$fields[$language['id_lang']]['description'] = (isset($this->description[$language['id_lang']])) ? pSQL($this->description[$language['id_lang']], true) : '';
			$fields[$language['id_lang']]['short_description'] = (isset($this->short_description[$language['id_lang']])) ? pSQL($this->short_description[$language['id_lang']], true) : '';
		}
		return $fields;
	}

	public function delete()
	{
		if ($this->id_address)
		{
			$address = new Address((int)$this->id_address);
			if (!Validate::isLoadedObject($address) || !$address->delete())
				return false;
		}

		if (Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product` SET `id_manufacturer` = 0 WHERE `id_manufacturer` = '.(int)$this->id) && parent::delete())
			return $this->deleteImage();

		return false;
	}

	/**
	 * Delete several objects from database
	 *
	 * return boolean Deletion result
	 */
	public function deleteSelection($selection)
	{
		if (!is_array($selection) || !Validate::isTableOrIdentifier($this->identifier) || !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());
		$result = true;
		foreach ($selection as $id)
		{
			$this->id = (int)$id;
			$this->id_address = self::getManufacturerAddress();
			$result &= $this->delete();
		}
		return $result;
	}

	protected function getManufacturerAddress()
	{
		if (!(int)$this->id)
			return false;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_address` FROM '._DB_PREFIX_.'address WHERE `id_manufacturer` = '.(int)$this->id);
	}

	/**
	  * Return manufacturers
	  *
	  * @param boolean $getNbProducts [optional] return products numbers for each
	  * @return array Manufacturers
	  */
	public static function getManufacturers($getNbProducts = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_group = false)
	{
		if (!$id_lang)
			$id_lang = (int)_PS_LANG_DEFAULT_;
		$sql = 'SELECT m.*, ml.`description`';
		$sql.= ' FROM `'._DB_PREFIX_.'manufacturer` m
		LEFT JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.(int)$id_lang.')
		'.($active ? ' WHERE m.`active` = 1' : '');
		$sql .= ' ORDER BY m.`name` ASC'.($p ? ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n : '');
		$manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
		if ($manufacturers === false)
			return false;
		if ($getNbProducts)
		{
			$sqlGroups = '';
			if (!$all_group)
			{
				$groups = FrontController::getCurrentCustomerGroups();
				$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
			}
			foreach ($manufacturers as $key => $manufacturer)
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` as m ON (m.`id_manufacturer`= p.`id_manufacturer`)
				WHERE m.`id_manufacturer` = '.(int)$manufacturer['id_manufacturer'].
				($active ? ' AND p.`active` = 1 ' : '').
				($all_group ? '' : ' AND p.`id_product` IN (
					SELECT cp.`id_product`
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
					WHERE cg.`id_group` '.$sqlGroups.')'));

				$manufacturers[$key]['nb_products'] = count($result);
			}
		}
		$rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS');
		$count_manufacturers = count($manufacturers);
		for ($i = 0; $i < $count_manufacturers; $i++)
			if ($rewrite_settings)
				$manufacturers[$i]['link_rewrite'] = Tools::link_rewrite($manufacturers[$i]['name'], false);
			else
				$manufacturers[$i]['link_rewrite'] = 0;
		return $manufacturers;
	}

	/**
	 * @deprecated
	 */
	public static function getManufacturersWithoutAddress()
	{
		Tools::displayAsDeprecated();

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT m.* FROM `'._DB_PREFIX_.'manufacturer` m
		LEFT JOIN `'._DB_PREFIX_.'address` a ON (a.`id_manufacturer` = m.`id_manufacturer` AND a.`deleted` = 0)
		WHERE a.`id_manufacturer` IS NULL');
	}

	/**
	  * Return name from id
	  *
	  * @param integer $id_manufacturer Manufacturer ID
	  * @return string name
	  */
	static protected $cacheName = array();
	public static function getNameById($id_manufacturer)
	{
		if (!isset(self::$cacheName[$id_manufacturer]))
			self::$cacheName[$id_manufacturer] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `name` FROM `'._DB_PREFIX_.'manufacturer` WHERE `id_manufacturer` = '.(int)($id_manufacturer).' AND `active` = 1');
		return self::$cacheName[$id_manufacturer];
	}

	public static function getIdByName($name)
	{
		$result = Db::getInstance()->getRow('
		SELECT `id_manufacturer`
		FROM `'._DB_PREFIX_.'manufacturer`
		WHERE `name` = \''.pSQL($name).'\'');
		if (isset($result['id_manufacturer']))
			return (int)($result['id_manufacturer']);
		return false;
	}

	public function getLink()
	{
		return Tools::link_rewrite($this->name, false);
	}

	public static function getProducts($id_manufacturer, $id_lang, $p, $n, $orderBy = null, $orderWay = null, $getTotal = false, $active = true, $active_category = true)
	{
		if ($p < 1) $p = 1;
	 	if (empty($orderBy) ||$orderBy == 'position') $orderBy = 'name';
	 	if (empty($orderWay)) $orderWay = 'ASC';

		if (!Validate::isOrderBy($orderBy) OR !Validate::isOrderWay($orderWay))
			die (Tools::displayError());

		$groups = FrontController::getCurrentCustomerGroups();
		$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		/* Return only the number of products */
		if ($getTotal)
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'product` p
			WHERE p.id_manufacturer = '.(int)($id_manufacturer)
			.($active ? ' AND p.`active` = 1' : '').'
			AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)'.
				($active_category ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
				WHERE cg.`id_group` '.$sqlGroups.')');

		$sql = '
		SELECT p.*, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
		pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`, m.`name` manufacturer_name, tl.`name` tax_name,
		t.`rate`, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 new
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
		                                           AND tr.`id_country` = '.(int)Country::getDefaultCountryId().'
	                                           	   AND tr.`id_state` = 0)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
		WHERE p.`id_manufacturer` = '.(int)($id_manufacturer).($active ? ' AND p.`active` = 1' : '').'
		AND p.`id_product` IN (
					SELECT cp.`id_product`
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)'.
					($active_category ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
					WHERE cg.`id_group` '.$sqlGroups.'
				)
		ORDER BY '.(($orderBy == 'id_product') ? 'p.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).'
		LIMIT '.(((int)($p) - 1) * (int)($n)).','.(int)($n);

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
		if (!$result)
			return false;
		if ($orderBy == 'price')
			Tools::orderbyPrice($result, $orderWay);
		return Product::getProductsProperties($id_lang, $result);
	}

	public function getProductsLite($id_lang)
	{
		return Db::getInstance()->ExecuteS('
		SELECT p.`id_product`,  pl.`name`
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)($id_lang).')
		WHERE p.`id_manufacturer` = '.(int)($this->id));
	}
	/*
	* Specify if a manufacturer already in base
	*
	* @param $id_manufacturer Manufacturer id
	* @return boolean
	*/
	public static function manufacturerExists($id_manufacturer)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_manufacturer`
		FROM '._DB_PREFIX_.'manufacturer m
		WHERE m.`id_manufacturer` = '.(int)$id_manufacturer);

		return isset($row['id_manufacturer']);
	}

	public function getAddresses($id_lang)
	{
		return Db::getInstance()->ExecuteS('
		SELECT a.*, cl.name `country`, s.name `state`
		FROM `'._DB_PREFIX_.'address` a
		LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`id_state`)
		WHERE `id_manufacturer` = '.(int)$this->id.' AND a.`deleted` = 0');
	}

	public function getWsAddresses()
	{
		return Db::getInstance()->ExecuteS('
		SELECT a.id_address id
		FROM `'._DB_PREFIX_.'address` a
		WHERE `id_manufacturer` = '.(int)$this->id.' AND a.`deleted` = 0');
	}

	public function setWsAddresses($id_addresses)
	{
		$ids = array();
		foreach ($id_addresses as $id)
			$ids[] = (int)$id['id'];
		$result1 = (Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'address`
			SET id_manufacturer = 0
			WHERE id_manufacturer = '.(int)$this->id.'
			AND deleted = 0') !== false);
		$result2 = true;
		if (count($ids))
			$result2 = (Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'address`
			SET id_customer = 0, id_supplier = 0, id_manufacturer = '.(int)$this->id.'
			WHERE id_address IN('.implode(',', $ids).')
			AND deleted = 0') !== false);
		return ($result1 && $result2);
	}
}