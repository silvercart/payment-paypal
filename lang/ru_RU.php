<?php
/**
 * Copyright 2012 pixeltricks GmbH
 *
 * This file is part of SilvercartPrepaymentPayment.
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
 * along with SilvercartPrepaymentPayment.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Russian language pack
 *
 * @package SilvercartPaymentPaypal
 * @subpackage i18n
 * @ignore
 */

i18n::include_locale_file('silvercart_payment_paypal', 'en_US');

global $lang;

if (array_key_exists('ru_RU', $lang) && is_array($lang['ru_RU'])) {
    $lang['ru_RU'] = array_merge($lang['en_US'], $lang['ru_RU']);
} else {
    $lang['ru_RU'] = $lang['en_US'];
}

$lang['ru_RU']['SilvercartHandlingCostPaypal']['PLURALNAME'] = 'пошлины ';
$lang['ru_RU']['SilvercartHandlingCostPaypal']['SINGULARNAME'] = 'пошлина';
$lang['ru_RU']['SilvercartOrderStatus']['PAYPAL_CANCELED'] = '';
$lang['ru_RU']['SilvercartOrderStatus']['PAYPAL_ERROR'] = '';
$lang['ru_RU']['SilvercartOrderStatus']['PAYPAL_REFUNDING'] = '';
$lang['ru_RU']['SilvercartOrderStatus']['PAYPAL_PENDING'] = '';
$lang['ru_RU']['SilvercartOrderStatus']['PAYPAL_SUCCESS'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['API_DEVELOPMENT_MODE'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['API_LIVE_MODE'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['API_PASSWORD'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['API_SIGNATURE'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['API_USERNAME'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['API_VERSION'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['ATTRIBUTED_ORDERSTATUS'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['CHECKOUT_URL'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['ENTERDATAATPAYPAL'] = 'способ оплаты – PayPal';
$lang['ru_RU']['SilvercartPaymentPaypal']['INFOTEXT_CHECKOUT'] = 'оплата производится методом PayPal';
$lang['ru_RU']['SilvercartPaymentPaypal']['ORDERSTATUS_CANCELED'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['ORDERSTATUS_PAYED'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['ORDERSTATUS_PENDING'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['ORDERSTATUS_REFUNDED'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['PLURALNAME'] = 'Способы оплаты';
$lang['ru_RU']['SilvercartPaymentPaypal']['SHARED_SECRET'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['SINGULARNAME'] = 'способ оплаты';
$lang['ru_RU']['SilvercartPaymentPaypal']['ORDER_CONFIRMATION_SUBMIT_BUTTON_TITLE'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['URLS_DEV_MODE'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['URLS_LIVE_MODE'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['URL_API_NVP'] = '';
$lang['ru_RU']['SilvercartPaymentPaypal']['URL_API_SOAP'] = '';
$lang['ru_RU']['SilvercartPaymentPaypalLanguage']['SINGULARNAME'] = _t('Silvercart.TRANSLATION');
$lang['ru_RU']['SilvercartPaymentPaypalLanguage']['PLURALNAME'] = _t('Silvercart.TRANSLATIONS');
$lang['ru_RU']['SilvercartPaymentPaypalNotification']['PLURALNAME'] = '';
$lang['ru_RU']['SilvercartPaymentPaypalNotification']['SINGULARNAME'] = '';
$lang['ru_RU']['SilvercartPaymentPaypalOrder']['PLURALNAME'] = '';
$lang['ru_RU']['SilvercartPaymentPaypalOrder']['SINGULARNAME'] = '';
