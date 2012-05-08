<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilvercartPaymentPaypal.
 *
 * SilvercartPaypalPayment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilvercartPrepaymentPayment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilvercartPaymentPaypal.  If not, see <http://www.gnu.org/licenses/>.
 *
 * English (GB) language pack
 *
 * @package Silvercart
 * @subpackage i18n
 * @ignore
 */

i18n::include_locale_file('silvercart_payment_payal', 'en_US');

global $lang;

if (array_key_exists('en_GB', $lang) && is_array($lang['en_GB'])) {
    $lang['en_GB'] = array_merge($lang['en_US'], $lang['en_GB']);
} else {
    $lang['en_GB'] = $lang['en_US'];
}

$lang['en_GB']['SilvercartHandlingCostPaypal']['PLURALNAME']    = 'Fees';
$lang['en_GB']['SilvercartHandlingCostPaypal']['SINGULARNAME']  = 'Fee';

$lang['en_GB']['SilvercartOrderStatus']['PAYPAL_CANCELED']  = 'Paypal canceled';
$lang['en_GB']['SilvercartOrderStatus']['PAYPAL_ERROR']     = 'Paypal error';
$lang['en_GB']['SilvercartOrderStatus']['PAYPAL_REFUNDING'] = 'Paypal refunding';
$lang['en_GB']['SilvercartOrderStatus']['PAYPAL_PENDING']   = 'Paypal pending';
$lang['en_GB']['SilvercartOrderStatus']['PAYPAL_SUCCESS']   = 'Payment approved by Paypal';

$lang['en_GB']['SilvercartPaymentPaypal']['API_DEVELOPMENT_MODE']   = 'API development mode';
$lang['en_GB']['SilvercartPaymentPaypal']['API_LIVE_MODE']          = 'API live mode';
$lang['en_GB']['SilvercartPaymentPaypal']['API_PASSWORD']           = 'API password';
$lang['en_GB']['SilvercartPaymentPaypal']['API_SIGNATURE']          = 'API signature';
$lang['en_GB']['SilvercartPaymentPaypal']['API_USERNAME']           = 'API username';
$lang['en_GB']['SilvercartPaymentPaypal']['API_VERSION']            = 'API version';
$lang['en_GB']['SilvercartPaymentPaypal']['ATTRIBUTED_ORDERSTATUS'] = 'attributed order status';
$lang['en_GB']['SilvercartPaymentPaypal']['CHECKOUT_URL']           = 'URL to the paypal checkout';
$lang['en_GB']['SilvercartPaymentPaypal']['ENTERDATAATPAYPAL']      = 'Payment at Paypal';
$lang['en_GB']['SilvercartPaymentPaypal']['INFOTEXT_CHECKOUT']      = 'payment via paypal';
$lang['en_GB']['SilvercartPaymentPaypal']['ORDERSTATUS_CANCELED']   = 'orderstatus for notification "canceled"';
$lang['en_GB']['SilvercartPaymentPaypal']['ORDERSTATUS_PAYED']      = 'orderstatus for notification "payed"';
$lang['en_GB']['SilvercartPaymentPaypal']['ORDERSTATUS_PENDING']    = 'orderstatus for notification "pending"';
$lang['en_GB']['SilvercartPaymentPaypal']['ORDERSTATUS_REFUNDED']   = 'orderstatus for notification "refunded"';
$lang['en_GB']['SilvercartPaymentPaypal']['PLURALNAME']             = 'payment methods';
$lang['en_GB']['SilvercartPaymentPaypal']['SHARED_SECRET']          = 'shared secret for secure communication';
$lang['en_GB']['SilvercartPaymentPaypal']['SINGULARNAME']           = 'payment method';
$lang['en_GB']['SilvercartPaymentPaypal']['ORDER_CONFIRMATION_SUBMIT_BUTTON_TITLE'] = 'Proceed to payment via Paypal';
$lang['en_GB']['SilvercartPaymentPaypal']['URLS_DEV_MODE']          = 'URLs of dev mode';
$lang['en_GB']['SilvercartPaymentPaypal']['URLS_LIVE_MODE']         = 'URLs of live mode';
$lang['en_GB']['SilvercartPaymentPaypal']['URL_API_NVP']            = 'URL to the paypal NVP API server';
$lang['en_GB']['SilvercartPaymentPaypal']['URL_API_SOAP']           = 'URL to the paypal SOAP API server';

$lang['en_GB']['SilvercartPaymentPaypalLanguage']['SINGULARNAME'] = 'Translation of the payment method PayPal';
$lang['en_GB']['SilvercartPaymentPaypalLanguage']['PLURALNAME'] = 'Translations of the payment method PayPal';

$lang['en_GB']['SilvercartPaymentPaypalNotification']['PLURALNAME']     = 'Silvercart Payment Paypal Notifications';
$lang['en_GB']['SilvercartPaymentPaypalNotification']['SINGULARNAME']   = 'Silvercart Payment Paypal Notification';

$lang['en_GB']['SilvercartPaymentPaypalOrder']['PLURALNAME']    = 'Silvercart Payment Paypal Orders';
$lang['en_GB']['SilvercartPaymentPaypalOrder']['SINGULARNAME']  = 'Silvercart Payment Paypal Order';
