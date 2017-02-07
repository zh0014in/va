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

class CountryCore extends ObjectModel
{
	public 		$id;

	/** @var integer Zone id which country belongs */
	public 		$id_zone;

	/** @var integer Currency id which country belongs */
	public 		$id_currency;

	/** @var string 2 letters iso code */
	public 		$iso_code;

	/** @var integer international call prefix */
	public 		$call_prefix;

	/** @var mixed Name */
	public 		$name;

	/** @var boolean Contain states */
	public		$contains_states;

	/** @var boolean Need identification number dni/nif/nie */
	public		$need_identification_number;

	/** @var boolean Need Zip Code */
	public		$need_zip_code;

	/** @var string Zip Code Format */
	public		$zip_code_format;

	/** @var boolean Display or not the tax incl./tax excl. mention in the front office */
	public $display_tax_label = true;

	/** @var boolean Status for delivery */
	public		$active = true;

	protected static $_idZones = array();

	protected 	$tables = array ('country', 'country_lang');

 	protected 	$fieldsRequired = array('id_zone', 'id_currency', 'iso_code', 'contains_states', 'need_identification_number', 'display_tax_label');
 	protected 	$fieldsSize = array('iso_code' => 3);
 	protected 	$fieldsValidate = array('id_zone' => 'isUnsignedId', 'id_currency' => 'isUnsignedId', 'call_prefix' => 'isInt', 'iso_code' => 'isLanguageIsoCode', 'active' => 'isBool', 'contains_states' => 'isBool', 'need_identification_number' => 'isBool', 'need_zip_code' => 'isBool', 'zip_code_format' => 'isZipCodeFormat', 'display_tax_label' => 'isBool');
 	protected 	$fieldsRequiredLang = array('name');
 	protected 	$fieldsSizeLang = array('name' => 64);
 	protected 	$fieldsValidateLang = array('name' => 'isGenericName');

	protected	$webserviceParameters = array(
		'objectsNodeName' => 'countries',
		'fields' => array(
			'id_zone' => array('sqlId' => 'id_zone', 'xlink_resource'=> 'zones'),
			'id_currency' => array('sqlId' => 'id_currency', 'xlink_resource'=> 'currencies'),
		),
	);

	protected 	$table = 'country';
	protected 	$identifier = 'id_country';

    protected static $cache_iso_by_id = array();

	public function getFields()
	{
		parent::validateFields();
		$fields['id_zone'] = (int)($this->id_zone);
		$fields['id_currency'] = (int)($this->id_currency);
		$fields['iso_code'] = pSQL(strtoupper($this->iso_code));
		$fields['call_prefix'] = (int)($this->call_prefix);
		$fields['active'] = (int)($this->active);
		$fields['contains_states'] = (int)($this->contains_states);
		$fields['need_identification_number'] = (int)($this->need_identification_number);
		$fields['need_zip_code'] = (int)($this->need_zip_code);
		$fields['zip_code_format'] = pSQL($this->zip_code_format);
		$fields['display_tax_label'] = (int)$this->display_tax_label;
		return $fields;
	}

	/**
	  * Check then return multilingual fields for database interaction
	  *
	  * @return array Multilingual fields
	  */
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name'));
	}

	/**
	  * Return available countries
	  *
	  * @param integer $id_lang Language ID
	  * @param boolean $active return only active countries (and states, if asked)
	  * @param boolean $contain_states return only countries including states
	  * @param boolean $list_states return only countries
	  * @return array Countries and corresponding zones
	  */
	public static function getCountries($id_lang, $active = false, $contain_states = false, $list_states = true)
	{
	 	if (!Validate::isBool($active))
	 		die(Tools::displayError());

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT cl.*, c.*, cl.`name` country, z.`name` zone
		FROM `'._DB_PREFIX_.'country` c
		LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'zone` z ON z.`id_zone` = c.`id_zone`
		WHERE 1
		'.($active ? 'AND c.active = 1' : '').'
		'.($contain_states ? 'AND c.`contains_states` = '.(int)$contain_states : '').'
		ORDER BY cl.name ASC');
		$countries = array();
		foreach ($result as $country)
			$countries[$country['id_country']] = $country;
			
		if ($list_states)
		{
			$states = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT s.*
			FROM `'._DB_PREFIX_.'state` s
			'.($active ? ' WHERE s.active = 1' : '').'
			ORDER BY s.`name` ASC');
			foreach ($states as $state)
				if (isset($countries[$state['id_country']])) /* Does not keep the state if its country has been disabled and not selected */
					$countries[$state['id_country']]['states'][] = $state;
		}

		return $countries;
	}

	/**
	  * Get a country ID with its iso code
	  *
	  * @param string $iso_code Country iso code
	  * @return integer Country ID
	  */
	public static function getByIso($iso_code)
	{
		if (!Validate::isLanguageIsoCode($iso_code))
			die(Tools::displayError());
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `id_country`
		FROM `'._DB_PREFIX_.'country`
		WHERE `iso_code` = \''.pSQL(strtoupper($iso_code)).'\'');

		return $result['id_country'];
	}

	public static function getIdZone($id_country)
	{
		if (!Validate::isUnsignedId($id_country))
			die(Tools::displayError());

		if (isset(self::$_idZones[$id_country]))
			return self::$_idZones[$id_country];

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `id_zone`
		FROM `'._DB_PREFIX_.'country`
		WHERE `id_country` = '.(int)($id_country));

		self::$_idZones[$id_country] = $result['id_zone'];
		return $result['id_zone'];
	}

	/**
	* Get a country name with its ID
	*
	* @param integer $id_lang Language ID
	* @param integer $id_country Country ID
	* @return string Country name
	*/
	public static function getNameById($id_lang, $id_country)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `name`
		FROM `'._DB_PREFIX_.'country_lang`
		WHERE `id_lang` = '.(int)($id_lang).'
		AND `id_country` = '.(int)($id_country));

		return $result['name'];
	}

	/**
	* Get a country iso with its ID
	*
	* @param integer $id_country Country ID
	* @return string Country iso
	*/
	public static function getIsoById($id_country)
	{
        if (!isset(Country::$cache_iso_by_id[$id_country]))
        {
            Country::$cache_iso_by_id[$id_country] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
            SELECT `iso_code`
            FROM `'._DB_PREFIX_.'country`
            WHERE `id_country` = '.(int)($id_country));
        }

		return Country::$cache_iso_by_id[$id_country];
	}

	/**
	* Get a country id with its name
	*
	* @param integer $id_lang Language ID
	* @param string $country Country Name
	* @return intval Country id
	*/
	public static function getIdByName($id_lang, $country)
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_country`
		FROM `'._DB_PREFIX_.'country_lang`
		WHERE `name` LIKE \''.pSQL($country).'\''.
		($id_lang ? ' AND `id_lang` = '.(int)$id_lang : ''));
	}


	/**
	 * @param $id_country
	 * @deprecated
	 */
	public static function getNeedIdentifcationNumber($id_country)
	{
		Tools::displayAsDeprecated();
		return self::isNeedDniByCountryId($id_country);
	}

	public static function getNeedZipCode($id_country)
	{
		if (!(int)$id_country)
			return false;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `need_zip_code`
		FROM `'._DB_PREFIX_.'country`
		WHERE `id_country` = '.(int)$id_country);
	}

	public static function getZipCodeFormat($id_country)
	{
		if (!(int)$id_country)
			return false;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `zip_code_format`
		FROM `'._DB_PREFIX_.'country`
		WHERE `id_country` = '.(int)$id_country);
	}

	public static function displayCallPrefix($prefix)
	{
		return ((int)($prefix) ? '+'.$prefix : '-');
	}

	/**
	 * Returns the default country Id
	 *
	 * @return integer default country id
	 */
	public static function getDefaultCountryId()
	{
		static $id_country_default = null;

		if ($id_country_default)
			return $id_country_default;
		
		if (_PS_GEOLOCATION_ENABLED_)
		{
			global $cookie;
			
			if ($cookie && isset($cookie->iso_code_country) && Validate::isLanguageIsoCode($cookie->iso_code_country))
				$id_country_default = (int)Country::getByIso($cookie->iso_code_country);
		}
		else
			$id_country_default = _PS_COUNTRY_DEFAULT_;
			
		return (int)$id_country_default;
	}


    public static function getCountriesByZoneId($id_zone, $id_lang)
    {
        if (empty($id_zone) || empty($id_lang))
            die(Tools::displayError());

        return Db::getInstance()->ExecuteS('
        SELECT DISTINCT c.*, cl.*
        FROM `'._DB_PREFIX_.'country` c
        LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_country` = c.`id_country`)
        LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
        WHERE (c.`id_zone` = '.(int)$id_zone.' OR s.`id_zone` = '.(int)$id_zone.')
        AND `id_lang` = '.(int)$id_lang);
    }

	public function isNeedDni()
	{
		return (bool)self::isNeedDniByCountryId($this->id);
	}

	public static function isNeedDniByCountryId($id_country)
	{
		return (bool)Db::getInstance()->getValue('
			SELECT `need_identification_number`
			FROM `'._DB_PREFIX_.'country`
			WHERE `id_country` = '.(int)$id_country);
	}

	public static function containsStates($id_country)
	{
		return (bool)Db::getInstance()->getValue('
			SELECT `contains_states`
			FROM `'._DB_PREFIX_.'country`
			WHERE `id_country` = '.(int)$id_country);
	}
	
	/**
	 * Replace letters of zip code format And check this format on the zip code
	 * @param $zip_code
	 * @return (bool)
	 */
	public function checkZipCode($zip_code)
	{
		$zip_regexp = '/^'.$this->zip_code_format.'$/ui';
		$zip_regexp = str_replace(' ', '( |)', $zip_regexp);
		$zip_regexp = str_replace('-', '(-|)', $zip_regexp);
		$zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
		$zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
		$zip_regexp = str_replace('C', $this->iso_code, $zip_regexp);

		return (bool)preg_match($zip_regexp, $zip_code);
	 }
}