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

class CarrierCompare extends Module
{
	public $template_directory = '';
	public $smarty;

	const VIRTUAL_CUSTOMER = 'SE_VIRTUAL_ID_CUSTOMER';
	const VIRTUAL_ADDRESS = 'SE_VIRTUAL_ID_ADDRESS';

	public function __construct()
	{
		$this->name = 'carriercompare';
		$this->tab = 'shipping_logistics';
		$this->version = '1.3';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Shipping Estimate');
		$this->description = $this->l('Compares carrier choices before checkout.');
		$this->template_directory = dirname(__FILE__).'/template/';
		$this->initRetroCompatibilityVar();
	}

	// Retro-compatibiliy 1.4/1.5
	private function initRetroCompatibilityVar()
	{
		if (class_exists('Context'))
			$smarty = Context::getContext()->smarty;
		else
			global $smarty;

		$this->smarty = $smarty;
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('shoppingCart') OR !$this->registerHook('header'))
			return false;

		if (!$this->createCustomer())
			return false;

		return true;
	}

	/**
	 * Create virtual customer to associate address for country selection
	 *
	 * @return bool
	 */
	private function createCustomer()
	{
		$customer = new Customer();
		$customer->email = 'shipping_estimation@prestashop_virtual.com';
		$customer->lastname = 'Shipping';
		$customer->firstname = 'Estimation';
		$customer->deleted = 1; // Keep it Hidden
		$customer->passwd = Tools::encrypt(Tools::passwdGen());

		if ($customer->add())
		{
			Configuration::updateValue(CarrierCompare::VIRTUAL_CUSTOMER, $customer->id);
			return true;
		}
		return false;
	}

	/**
	 * Create virtual address to associate id_zone for a country selection
	 *
	 * @param $id_country
	 * @param $zipcode
	 * @return bool
	 */
	private function addAddress($id_country, $zipcode)
	{
		$customer = new Customer((int)Configuration::get(CarrierCompare::VIRTUAL_CUSTOMER));

		$address = new Address();
		$address->id_country = $id_country;
		$address->alias = 'Shipping Estimation';
		$address->lastname = $customer->lastname;
		$address->firstname = $customer->firstname;
		$address->address1 = 'test';
		$address->city = 'test';
		$address->postcode = $zipcode;
		$address->id_customer = $customer->id;

		if ($address->add())
		{
			Configuration::updateValue(CarrierCompare::VIRTUAL_ADDRESS, $address->id);
			return true;
		}
		return false;
	}

	public function getContent()
	{
		if (!empty($_POST))
			$this->postProcess();

		$this->smarty->assign('refresh_method', Configuration::get('SE_RERESH_METHOD'));
		return $this->smarty->fetch($this->template_directory .'configuration.tpl');
	}

	public function postProcess()
	{
		$errors = array();

		if (Tools::isSubmit('setGlobalConfiguration'))
		{
			$method = (int)Tools::getValue('refresh_method');
			Configuration::updateValue('SE_RERESH_METHOD', $method);
		}

		$this->smarty->assign(array(
			'display_error' => count($errors) ? $errors : false));
	}

	public function hookHeader($params)
	{
		if (!$this->isModuleAvailable())
			return '';
		Tools::addCSS(($this->_path).'style.css', 'all');
		Tools::addJS(($this->_path).'carriercompare.js');
	}

	/*
	 ** Hook Shopping Cart Process
	 */
	public function hookShoppingCart($params)
	{
		global $cookie, $smarty, $currency, $defaultCountry;

		if (!$this->isModuleAvailable())
			return '';

		$protocol = (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS'])
			&& strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';

		$endURL = __PS_BASE_URI__.'modules/carriercompare/';

		if (method_exists('Tools', 'getShopDomainSsl'))
			$moduleURL = $protocol.Tools::getShopDomainSsl().$endURL;
		else
			$moduleURL = $protocol.$_SERVER['HTTP_HOST'].$endURL;

		$refresh_method = Configuration::get('SE_RERESH_METHOD');

		$this->smarty->assign(array(
			'countries' => Country::getCountries((int)$cookie->id_lang, true, false, false),
			'id_carrier' => ($params['cart']->id_carrier ? $params['cart']->id_carrier : Configuration::get('PS_CARRIER_DEFAULT')),
			'id_country' => (isset($cookie->id_country) ? $cookie->id_country : Configuration::get('PS_COUNTRY_DEFAULT')),
			'id_state' => (isset($cookie->id_state) ? $cookie->id_state : 0),
			'zipcode' => ((isset($cookie->postcode) && $cookie->postcode != 'undefined') ? $cookie->postcode : ''),
			'currencySign' => $currency->sign,
			'currencyRate' => $currency->conversion_rate,
			'currencyFormat' => $currency->format,
			'currencyBlank' => $currency->blank,
			'new_base_dir' => $moduleURL,
			'refresh_method' => ($refresh_method === false) ? 0 : $refresh_method
		));
												
		return $this->smarty->fetch($this->template_directory.'carriercompare.tpl');
	}

	/*
	** Get states by Country id, called by the ajax process
	*/
	public function getStatesByIdCountry($id_country)
	{
		return State::getStatesByIdCountry((int)$id_country);
	}

	/*
	** Get carriers by country id, called by the ajax process
	*/
	public function getCarriersListByIdZone($id_country, $id_state = 0, $zipcode = 0)
	{
		global $cart, $smarty, $cookie;

		// cookie saving/updating
		$cookie->id_country = $id_country;
		if ($id_state != 0)
			$cookie->id_state = $id_state;
		if ($zipcode !== 0)
			$cookie->postcode = $zipcode;

		$id_zone = 0;
		if ($id_state != 0)
			$id_zone = State::getIdZone($id_state);
		if (!$id_zone)
			$id_zone = Country::getIdZone($id_country);

		// Need to set the infos for carrier module !
		$cookie->id_country = $id_country;
		$cookie->id_state = $id_state;
		$cookie->postcode = $zipcode;

		$carriers = array();
		if ($this->addAddress($id_country, $zipcode))
		{
			// Back up the current id_address_delivery
			$current_id_address_delivery = $cart->id_address_delivery;

			// Get the new one created
			$cart->id_address_delivery = Configuration::get(CarrierCompare::VIRTUAL_ADDRESS);
			$cart->id_customer = Configuration::get(CarrierCompare::VIRTUAL_CUSTOMER);

			// Get carriers with good id_zone
			$carriers = Carrier::getCarriersForOrder((int)$id_zone);

			// Delete Address and restore id_address_delivery
			$address = new Address((int)Configuration::get(CarrierCompare::VIRTUAL_ADDRESS));
			$address->delete();
			$cart->id_address_delivery = $current_id_address_delivery;
		}

		return (count($carriers) ? $carriers : array());
	}

	public function saveSelection($id_country, $id_state, $zipcode, $id_carrier)
	{
		global $cart, $cookie, $defaultCountry;

		$errors = array();

		if (!Validate::isInt($id_state))
			$errors[] = $this->l('Invalid State ID');
		if ($id_state != 0 && !Validate::isLoadedObject(new State($id_state)))
			$errors[] = $this->l('Please select a state');
		if (!Validate::isInt($id_country) || !Validate::isLoadedObject(new Country($id_country)))
			$errors[] = $this->l('Please select a country');
		if (!$this->checkZipcode($zipcode, $id_country))
			$errors[] = $this->l('Depending on your country selection, please use a valid zip/postal code');
		if (!Validate::isInt($id_carrier) || !Validate::isLoadedObject(new Carrier($id_carrier)))
			$errors[] = $this->l('Please select a carrier');

		if (sizeof($errors))
			return $errors;

		$ids_carrier = array();
		foreach (self::getCarriersListByIdZone($id_country, $id_state, $zipcode) as $carrier)
			$ids_carrier[] = $carrier['id_carrier'];
		if (!in_array($id_carrier, $ids_carrier))
			$errors[] = $this->l('The carrier ID isn\'t available for your selection');

		if (sizeof($errors))
			return $errors;

		$cookie->id_country = $id_country;
		$cookie->id_state = $id_state;
		$cookie->postcode = $zipcode;
		$cart->id_carrier = $id_carrier;
		$cookie->iso_code_country = Country::getIsoById($id_country);		
		if (!$cart->update())
			return array($this->l('Cannot update the cart'));			
		return array();
	}

	/*
	** Check the validity of the zipcode format depending of the country
	*/
	private function checkZipcode($zipcode, $id_country)
	{
		$zipcodeFormat = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `zip_code_format`
				FROM `'._DB_PREFIX_.'country`
				WHERE need_zip_code = 1 AND `id_country` = '.(int)$id_country);

		if (!$zipcodeFormat)
			return true;

		$regxMask = str_replace(
			array('N', 'C', 'L'),
			array(
				'[0-9]',
				Country::getIsoById((int)$id_country),
				'[a-zA-Z]'),
			$zipcodeFormat);
		if (preg_match('/'.$regxMask.'/', $zipcode))
			return true;
		return false;
	}

	/**
	 * This module is shown on front office, in only some conditions
	 * @return bool
	 */
	private function isModuleAvailable()
	{
		global $cookie;

		$fileName = basename($_SERVER['SCRIPT_FILENAME']);
		/**
		 * This module is only available on standard order process because
		 * on One Page Checkout the carrier list is already available.
		 */
		if (!in_array($fileName, array('order.php', 'cart.php')))
			return false;
		/**
		 * If visitor is logged, the module isn't available on Front office,
		 * we use the account informations for carrier selection and taxes.
		 */
		if ($cookie->id_customer)
			return false;
		return true;
	}
	
	public function getZipcodes($id_country)
	{
		return (bool)Country::getNeedZipCode((int)$id_country);
	}
}