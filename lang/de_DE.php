<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * German (Germany) language pack
 *
 * @package Silvercart
 * @subpackage i18n
 * @ignore
 */

i18n::include_locale_file('silvercart_payment_payal', 'en_US');

global $lang;

if (array_key_exists('de_DE', $lang) && is_array($lang['de_DE'])) {
    $lang['de_DE'] = array_merge($lang['en_US'], $lang['de_DE']);
} else {
    $lang['de_DE'] = $lang['en_US'];
}

$lang['de_DE']['SilvercartHandlingCostPaypal']['PLURALNAME']    = 'Gebühren';
$lang['de_DE']['SilvercartHandlingCostPaypal']['SINGULARNAME']  = 'Gebühr';

$lang['de_DE']['SilvercartOrderStatus']['PAYPAL_CANCELED']  = 'Paypal abgebrochen';
$lang['de_DE']['SilvercartOrderStatus']['PAYPAL_ERROR']     = 'Paypal Fehler';
$lang['de_DE']['SilvercartOrderStatus']['PAYPAL_REFUNDING'] = 'Paypal Rückerstattung';
$lang['de_DE']['SilvercartOrderStatus']['PAYPAL_PENDING']   = 'Paypal in Bearbeitung';
$lang['de_DE']['SilvercartOrderStatus']['PAYPAL_SUCCESS']   = 'Zahlung ist durch Paypal genehmigt';

$lang['de_DE']['SilvercartPaymentPaypal']['API_DEVELOPMENT_MODE']   = 'API Entwicklungsmodus';
$lang['de_DE']['SilvercartPaymentPaypal']['API_LIVE_MODE']          = 'API Live Modus';
$lang['de_DE']['SilvercartPaymentPaypal']['API_PASSWORD']           = 'API Passwort';
$lang['de_DE']['SilvercartPaymentPaypal']['API_SIGNATURE']          = 'API Signatur';
$lang['de_DE']['SilvercartPaymentPaypal']['API_USERNAME']           = 'API Benutzername';
$lang['de_DE']['SilvercartPaymentPaypal']['API_VERSION']            = 'API Version';
$lang['de_DE']['SilvercartPaymentPaypal']['ATTRIBUTED_ORDERSTATUS'] = 'Zuordnung Bestellstatus';
$lang['de_DE']['SilvercartPaymentPaypal']['CHECKOUT_URL']           = 'URL zum Paypal Checkout';
$lang['de_DE']['SilvercartPaymentPaypal']['ENTERDATAATPAYPAL']      = 'Zahlung bei Paypal';
$lang['de_DE']['SilvercartPaymentPaypal']['INFOTEXT_CHECKOUT']      = 'Die Zahlung erfolgt per Paypal';
$lang['de_DE']['SilvercartPaymentPaypal']['ORDERSTATUS_CANCELED']   = 'Bestellstatus für Meldung "abgebrochen"';
$lang['de_DE']['SilvercartPaymentPaypal']['ORDERSTATUS_PAYED']      = 'Bestellstatus für Meldung "bezahlt"';
$lang['de_DE']['SilvercartPaymentPaypal']['ORDERSTATUS_PENDING']    = 'Bestellstatus für Meldung "in der Schwebe"';
$lang['de_DE']['SilvercartPaymentPaypal']['ORDERSTATUS_REFUNDED']   = 'Bestellstatus für Meldung "zurückerstattet"';
$lang['de_DE']['SilvercartPaymentPaypal']['PLURALNAME']             = 'Bezahlarten';
$lang['de_DE']['SilvercartPaymentPaypal']['SHARED_SECRET']          = 'Shared Secret zur Absicherung der Kommunikation';
$lang['de_DE']['SilvercartPaymentPaypal']['SINGULARNAME']           = 'Bezahlart';
$lang['de_DE']['SilvercartPaymentPaypal']['ORDER_CONFIRMATION_SUBMIT_BUTTON_TITLE'] = 'Weiter zur Bezahlung bei Paypal';
$lang['de_DE']['SilvercartPaymentPaypal']['URLS_DEV_MODE']          = 'URLs Entwicklungsmodus';
$lang['de_DE']['SilvercartPaymentPaypal']['URLS_LIVE_MODE']         = 'URLs Livemodus';
$lang['de_DE']['SilvercartPaymentPaypal']['URL_API_NVP']            = 'URL zum Paypal NVP API Server';
$lang['de_DE']['SilvercartPaymentPaypal']['URL_API_SOAP']           = 'URL zum Paypal SOAP API Server';

$lang['de_DE']['SilvercartPaymentPaypalNotification']['PLURALNAME']     = 'Zahlungsbenachrichtigungen';
$lang['de_DE']['SilvercartPaymentPaypalNotification']['SINGULARNAME']   = 'Zahlungsbenachrichtigung';

$lang['de_DE']['SilvercartPaymentPaypalOrder']['PLURALNAME']    = 'Paypal Bestellungen';
$lang['de_DE']['SilvercartPaymentPaypalOrder']['SINGULARNAME']  = 'Paypal Bestellung';
