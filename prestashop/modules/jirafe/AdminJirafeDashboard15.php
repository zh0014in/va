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

include_once('jirafe.php');

class AdminJirafeDashboard extends AdminTab
{
    public function __construct()
    {
        parent::__construct();
    }

    public function display()
    {
        $jirafe = new Jirafe();
        $ps = $jirafe->getPrestashopClient();

        $apiUrl = (JIRAFE_DEBUG) ? 'https://test-api.jirafe.com/v1' : 'https://api.jirafe.com/v1';
        $token = $ps->get('token');
        $appId = $ps->get('app_id');
        $locale = $ps->getLanguage();
        $title = $this->l('Dashboard');
        $errMsg = $this->l("We're unable to connect with the Jirafe service for the moment. Please wait a few minutes and refresh this page later.");
        echo <<<EOF
<div>
    <h1>{$title}</h1>
    <hr style="background-color: #812143;color: #812143;" />
    <br />
</div>

<!-- Jirafe Dashboard Begin -->
<div id="jirafe"></div>
<script type="text/javascript">
(function(jQuery) {
    var $ = jQuery;
     $('#jirafe').jirafe({
        api_url:    '{$apiUrl}',
        api_token:  '{$token}',
        app_id:     '{$appId}',
        locale:     '{$locale}',
        version:    'presta-v0.1.0'
     });
})(jirafe.jQuery);
setTimeout(function() {
    if ($('mod-jirafe') == undefined){
        $('messages').insert ("<ul class=\"messages\"><li class=\"error-msg\">{$errMsg}</li></ul>");
    }
}, 2000);
</script>
<!-- Jirafe Dashboard End -->
EOF;

    }
}
