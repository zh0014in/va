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
 *  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_') || !defined('_CAN_LOAD_FILES_'))
    exit;

// Set to true if you want to debug the Jirafe API in the test sandbox
define ('JIRAFE_DEBUG', false);

// plugin version
define ('JIRAFE_MODULE_VERSION', '0.1');

if (version_compare(_PS_VERSION_, '1.5') >= 0) {
    require_once(dirname(__FILE__).'/jirafe15.php');
} elseif (version_compare(_PS_VERSION_, '1.4') >= 0) {
    require_once(dirname(__FILE__).'/jirafe14.php');
}
