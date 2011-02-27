<?php

/**
 * German (Germany) language pack
 * @package modules: silvercart
 */
i18n::include_locale_file('silvercart', 'en_US');

if (array_key_exists('de_DE', $lang) && is_array($lang['de_DE'])) {
    $lang['de_DE'] = array_merge($lang['en_US'], $lang['de_DE']);
} else {
    $lang['de_DE'] = $lang['en_US'];
}

$lang['de_DE']['SilvercartHandlingCostPaypal']['PLURALNAME'] = array(
    'Gebühren',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartHandlingCostPaypal']['SINGULARNAME'] = array(
    'Gebühr',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartPaymentPaypal']['API_DEVELOPMENT_MODE'] = array(
    'API Entwicklungsmodus',
    null,
    'API Entwicklungsmodus'
);
$lang['de_DE']['SilvercartPaymentPaypal']['API_LIVE_MODE'] = 'API live mode';
$lang['de_DE']['SilvercartPaymentPaypal']['API_PASSWORD'] = array(
    'API Passwort',
    null,
    'API Passwort'
);
$lang['de_DE']['SilvercartPaymentPaypal']['API_SIGNATURE'] = 'API signature';
$lang['de_DE']['SilvercartPaymentPaypal']['API_USERNAME'] = array(
    'API Benutzername',
    null,
    'API Benutzername'
);
$lang['de_DE']['SilvercartPaymentPaypal']['API_VERSION'] = 'API version';
$lang['de_DE']['SilvercartPaymentPaypal']['ATTRIBUTED_ORDERSTATUS'] = array(
    'Zuordnung Bestellstatus',
    null,
    'Zuordnung Bestellstatus'
);
$lang['de_DE']['SilvercartPaymentPaypal']['CHECKOUT_URL'] = array(
    'URL zum Paypal Checkout',
    null,
    'URL zum Paypal Checkout'
);
$lang['de_DE']['SilvercartPaymentPaypal']['INFOTEXT_CHECKOUT'] = array(
    'Die Zahlung erfolgt per Paypal',
    null,
    'Die Zahlung erfolgt per Paypal'
);
$lang['de_DE']['SilvercartPaymentPaypal']['ORDERSTATUS_CANCELED'] = array(
    'Bestellstatus für Meldung "abgebrochen"',
    null,
    'Bestellstatus für Meldung "abgebrochen"'
);
$lang['de_DE']['SilvercartPaymentPaypal']['ORDERSTATUS_PAYED'] = array(
    'Bestellstatus für Meldung "bezahlt"',
    null,
    'Bestellstatus für Meldung "bezahlt"'
);
$lang['de_DE']['SilvercartPaymentPaypal']['ORDERSTATUS_PENDING'] = array(
    'Bestellstatus für Meldung "in der Schwebe"',
    null,
    'Bestellstatus für Meldung "in der Schwebe"'
);
$lang['de_DE']['SilvercartPaymentPaypal']['ORDERSTATUS_REFUNDED'] = array(
    'Bestellstatus für Meldung "zurückerstattet"',
    null,
    'Bestellstatus für Meldung "zurückerstattet"'
);
$lang['de_DE']['SilvercartPaymentPaypal']['PLURALNAME'] = array(
    'Bezahlarten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartPaymentPaypal']['SHARED_SECRET'] = array(
    'Shared Secret zur Absicherung der Kommunikation',
    null,
    'Shared Secret zur Absicherung der Kommunikation'
);
$lang['de_DE']['SilvercartPaymentPaypal']['SINGULARNAME'] = array(
    'Bezahlart',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartPaymentPaypal']['URLS_DEV_MODE'] = array(
    'URLs Entwicklungsmodus',
    null,
    'URLs Entwicklungsmodus'
);
$lang['de_DE']['SilvercartPaymentPaypal']['URLS_LIVE_MODE'] = array(
    'URLs Livemodus',
    null,
    'URLs Livemodus'
);
$lang['de_DE']['SilvercartPaymentPaypal']['URL_API_NVP'] = array(
    'URL zum Paypal NVP API Server',
    null,
    'URL zum Paypal NVP API Server'
);
$lang['de_DE']['SilvercartPaymentPaypal']['URL_API_SOAP'] = array(
    'URL zum Paypal SOAP API Server',
    null,
    'URL zum Paypal SOAP API Server'
);
$lang['de_DE']['SilvercartPaymentPaypalNotification']['PLURALNAME'] = array(
    'Zahlungsbenachrichtigungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartPaymentPaypalNotification']['SINGULARNAME'] = array(
    'Zahlungsbenachrichtigung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SilvercartPaymentPaypalOrder']['PLURALNAME'] = array(
    'Paypal Bestellungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SilvercartPaymentPaypalOrder']['SINGULARNAME'] = array(
    'Paypal Bestellung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
