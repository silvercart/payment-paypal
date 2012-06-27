<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilvercartPaypalPayment.
 *
 * SilvercartPaypalPayment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilvercartPaypalPayment is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilvercartPaypalPayment.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Payment
 */

/**
 * Paypal payment modul
 *
 * @package Silvercart
 * @subpackage Payment
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 09.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentPaypal extends SilvercartPaymentMethod {

    /**
     * db field definitions
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 12.11.2010
     */
    public static $db = array(
        'paypalSharedSecret' => 'VarChar(255)',
        'paypalCheckoutUrl_Dev' => 'VarChar(255)',
        'paypalCheckoutUrl_Live' => 'VarChar(255)',
        'paypalApiUsername_Dev' => 'VarChar(255)',
        'paypalApiUsername_Live' => 'VarChar(255)',
        'paypalApiPassword_Dev' => 'VarChar(255)',
        'paypalApiPassword_Live' => 'VarChar(255)',
        'paypalApiSignature_Dev' => 'VarChar(255)',
        'paypalApiSignature_Live' => 'VarChar(255)',
        'paypalNvpApiServerUrl_Dev' => 'VarChar(255)',
        'paypalNvpApiServerUrl_Live' => 'VarChar(255)',
        'paypalSoapApiServerUrl_Dev' => 'VarChar(255)',
        'paypalSoapApiServerUrl_Live' => 'VarChar(255)',
        'paypalApiVersion_Dev' => 'VarChar(255)',
        'paypalApiVersion_Live' => 'VarChar(255)',
        'PaidOrderStatus' => 'Int',
        'CanceledOrderStatus' => 'Int',
        'PendingOrderStatus' => 'Int',
        'RefundedOrderStatus' => 'Int'
    );
    
    public static $casting = array(
        'paypalInfotextCheckout' => 'VarChar(255)'
    );
    
    /**
     * label definitions for class attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 12.11.2010
     */
    public static $field_labels = array(
        'paypalSharedSecret' => 'Shared Secret zur Absicherung der Kommunikation',
        'paypalCheckoutUrl_Dev' => 'URL zum Paypal Checkout',
        'paypalCheckoutUrl_Live' => 'URL zum Paypal Checkout',
        'paypalApiUsername_Dev' => 'API Benutzername',
        'paypalApiUsername_Live' => 'API Benutzername',
        'paypalApiPassword_Dev' => 'API Passwort',
        'paypalApiPassword_Live' => 'API Passwort',
        'paypalApiSignature_Dev' => 'API Signatur',
        'paypalApiSignature_Live' => 'API Signatur',
        'paypalApiVersion_Dev' => 'API Version',
        'paypalApiVersion_Live' => 'API Version',
        'paypalNvpApiServerUrl_Dev' => 'URL zum Paypal NVP API Server',
        'paypalNvpApiServerUrl_Live' => 'URL zum Paypal NVP API Server',
        'paypalSoapApiServerUrl_Dev' => 'URL zum Paypal SOAP API Server',
        'paypalSoapApiServerUrl_Live' => 'URL zum Paypal SOAP API Server',
        'PaidOrderStatus' => 'Bestellstatus für Meldung "bezahlt"',
        'CanceledOrderStatus' => 'Bestellstatus für Meldung "abgebrochen"',
        'PendingOrderStatus' => 'Bestellstatus für Meldung "in der Schwebe"',
        'RefundedOrderStatus' => 'Bestellstatus für Meldung "zurückerstattet"'
    );



    /**
     * define 1:1 relations
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public static $has_one = array(
        'SilvercartHandlingCost' => 'SilvercartHandlingCostPaypal'
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 31.01.2012
     */
    public static $has_many = array(
        'SilvercartPaymentPaypalLanguages' => 'SilvercartPaymentPaypalLanguage'
    );
    
    /**
     * contains module name for display in the admin backend
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 12.11.2010
     */
    protected $moduleName = 'Paypal';
    /**
     * contains name for the shared secret ID; Used by paypal at the IPN answer
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    protected $sharedSecretVariableName = 'sh';
    /**
     * contains all strings of the paypal answer which declare the transaction status false
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    public $failedPaypalStatus = array(
        'Denied',
        'Expired',
        'Failed',
        'Voided'
    );
    /**
     * contains all strings of the paypal answer which declare the transaction status true
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    public $successPaypalStatus = array(
        'Completed',
        'Processed',
        'Canceled-Reversal'
    );
    /**
     * contains all strings of the paypal answer of a withdrawn payment
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    public $refundedPaypalStatus = array(
        'Refunded',
        'Reversed'
    );
    /**
     * contains all strings of the paypal answer which declare the transaction status pending
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    public $pendingPaypalStatus = array(
        'Pending',
        'Created'
    );
    
    /**
     * getter for the multilingual attribute paypalInfotextCheckout
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 31.01.2012
     */
    public function getpaypalInfotextCheckout() {
        $text = '';
        if ($this->getLanguage()) {
            $text = $this->getLanguage()->paypalInfotextCheckout;
        }
        return $text;
    }

    /**
     * i18n for field labels
     *
     * @param boolean $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 27.02.2011
     * @copyright 2010 pixeltricks GmbH
     */
    public function fieldLabels($includerelations = true) {
        $fields = parent::fieldLabels($includerelations);
        $fields['paypalSharedSecret'] = _t('SilvercartPaymentPaypal.SHARED_SECRET', 'shared secret for secure communication', null, 'Shared Secret zur Absicherung der Kommunikation');
        $fields['paypalCheckoutUrl_Dev'] = _t('SilvercartPaymentPaypal.CHECKOUT_URL', 'URL to the paypal checkout', null, 'URL zum Paypal Checkout');
        $fields['paypalCheckoutUrl_Live'] = _t('SilvercartPaymentPaypal.CHECKOUT_URL');
        $fields['paypalApiUsername_Dev'] = _t('SilvercartPaymentPaypal.API_USERNAME', 'API username', null, 'API Benutzername');
        $fields['paypalApiUsername_Live'] = _t('SilvercartPaymentPaypal.API_USERNAME');
        $fields['paypalApiPassword_Dev'] = _t('SilvercartPaymentPaypal.API_PASSWORD', 'API password', null, 'API Passwort');
        $fields['paypalApiPassword_Live'] = _t('SilvercartPaymentPaypal.API_PASSWORD');
        $fields['paypalApiSignature_Dev'] = _t('SilvercartPaymentPaypal.API_SIGNATURE', 'API signature');
        $fields['paypalApiSignature_Live'] = _t('SilvercartPaymentPaypal.API_SIGNATURE');
        $fields['paypalApiVersion_Dev'] = _t('SilvercartPaymentPaypal.API_VERSION', 'API version');
        $fields['paypalApiVersion_Live'] = _t('SilvercartPaymentPaypal.API_VERSION');
        $fields['paypalNvpApiServerUrl_Dev'] = _t('SilvercartPaymentPaypal.URL_API_NVP', 'URL to the paypal NVP API server', null, 'URL zum Paypal NVP API Server');
        $fields['paypalNvpApiServerUrl_Live'] = _t('SilvercartPaymentPaypal.URL_API_NVP');
        $fields['paypalSoapApiServerUrl_Dev'] = _t('SilvercartPaymentPaypal.URL_API_SOAP', 'URL to the paypal SOAP API server', null, 'URL zum Paypal SOAP API Server');
        $fields['paypalSoapApiServerUrl_Live'] = _t('SilvercartPaymentPaypal.URL_API_SOAP');
        $fields['paypalInfotextCheckout'] = _t('SilvercartPaymentPaypal.INFOTEXT_CHECKOUT', 'payment via paypal', null, 'Die Zahlung erfolgt per Paypal');
        $fields['PaidOrderStatus'] = _t('SilvercartPaymentPaypal.ORDERSTATUS_PAYED', 'orderstatus for notification "payed"', null, 'Bestellstatus für Meldung "bezahlt"');
        $fields['CanceledOrderStatus'] = _t('SilvercartPaymentPaypal.ORDERSTATUS_CANCELED', 'orderstatus for notification "canceled"', null, 'Bestellstatus für Meldung "abgebrochen"');
        $fields['PendingOrderStatus'] = _t('SilvercartPaymentPaypal.ORDERSTATUS_PENDING', 'orderstatus for notification "pending"', null, 'Bestellstatus für Meldung "in der Schwebe"');
        $fields['RefundedOrderStatus'] = _t('SilvercartPaymentPaypal.ORDERSTATUS_REFUNDED', 'orderstatus for notification "refunded"', null, 'Bestellstatus für Meldung "zurückerstattet"');
        return $fields;
    }

    /**
     * returns CMS fields
     *
     * @param mixed $params optional
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 12.11.2010
     */
    public function getCMSFields($params = null) {
        $fields = parent::getCMSFieldsForModules($params);
        $fieldLabels = self::$field_labels;

        $tabApi = new Tab('PaypalAPI');
        $tabUrls = new Tab('PaypalURLs');
        $tabOrderStatus = new Tab('OrderStatus', _t('SilvercartPaymentPaypal.ATTRIBUTED_ORDERSTATUS', 'attributed order status', null, 'Zuordnung Bestellstatus'));

        $fields->fieldByName('Sections')->push($tabApi);
        $fields->fieldByName('Sections')->push($tabUrls);
        $fields->fieldByName('Sections')->push($tabOrderStatus);

        // basic settings -------------------------------------------------
        $fields->addFieldToTab(
                'Sections.Basic',
                new TextField('paypalSharedSecret', _t('SilvercartPaymentPaypal.SHARED_SECRET'))
        );

        // API Tabset ---------------------------------------------------------
        $tabApiTabset = new TabSet('APIOptions');
        $tabApiTabDev = new Tab(_t('SilvercartPaymentPaypal.API_DEVELOPMENT_MODE', 'API development mode', null, 'API Entwicklungsmodus'));
        $tabApiTabLive = new Tab(_t('SilvercartPaymentPaypal.API_LIVE_MODE', 'API live mode'));

        // API Tabs -----------------------------------------------------------
        $tabApiTabset->push($tabApiTabDev);
        $tabApiTabset->push($tabApiTabLive);

        $tabApi->push($tabApiTabset);

        // URL Tabset ---------------------------------------------------------
        $tabUrlTabset = new TabSet('URLOptions');
        $tabUrlTabDev = new Tab(_t('SilvercartPaymentPaypal.URLS_DEV_MODE', 'URLs of dev mode', null, 'URLs Entwicklungsmodus'));
        $tabUrlTabLive = new Tab(_t('SilvercartPaymentPaypal.URLS_LIVE_MODE', 'URLs of live mode', null, 'URLs Livemodus'));

        // URL Tabs -----------------------------------------------------------
        $tabUrlTabset->push($tabUrlTabDev);
        $tabUrlTabset->push($tabUrlTabLive);

        $tabUrls->push($tabUrlTabset);

        // API Tab Dev fields -------------------------------------------------
        $tabApiTabDev->setChildren(
                new FieldSet(
                        new TextField('paypalApiUsername_Dev', _t('SilvercartPaymentPaypal.API_USERNAME')),
                        new TextField('paypalApiPassword_Dev', _t('SilvercartPaymentPaypal.API_PASSWORD')),
                        new TextField('paypalApiSignature_Dev', _t('SilvercartPaymentPaypal.API_SIGNATURE')),
                        new TextField('paypalApiVersion_Dev', _t('SilvercartPaymentPaypal.API_VERSION'))
                )
        );

        // API Tab Live fields ------------------------------------------------
        $tabApiTabLive->setChildren(
                new FieldSet(
                        new TextField('paypalApiUsername_Live', _t('SilvercartPaymentPaypal.API_USERNAME')),
                        new TextField('paypalApiPassword_Live', _t('SilvercartPaymentPaypal.API_PASSWORD')),
                        new TextField('paypalApiSignature_Live', _t('SilvercartPaymentPaypal.API_SIGNATURE')),
                        new TextField('paypalApiVersion_Live', _t('SilvercartPaymentPaypal.API_VERSION'))
                )
        );

        // URL Tab Dev fields -------------------------------------------------
        $tabUrlTabDev->setChildren(
                new FieldSet(
                        new TextField('paypalCheckoutUrl_Dev', _t('SilvercartPaymentPaypal.CHECKOUT_URL')),
                        new TextField('paypalNvpApiServerUrl_Dev', _t('SilvercartPaymentPaypal.URL_API_NVP')),
                        new TextField('paypalSoapApiServerUrl_Dev', _t('SilvercartPaymentPaypal.URL_API_SOAP'))
                )
        );

        // URL Tab Live fields ------------------------------------------------
        $tabUrlTabLive->setChildren(
                new FieldSet(
                        new TextField('paypalCheckoutUrl_Live', _t('SilvercartPaymentPaypal.CHECKOUT_URL')),
                        new TextField('paypalNvpApiServerUrl_Live', _t('SilvercartPaymentPaypal.URL_API_NVP')),
                        new TextField('paypalSoapApiServerUrl_Live', _t('SilvercartPaymentPaypal.URL_API_SOAP'))
                )
        );

        // Bestellstatus Tab fields -------------------------------------------
        $OrderStatus = DataObject::get('SilvercartOrderStatus');
        $tabOrderStatus->setChildren(
                new FieldSet(
                        new DropdownField('PaidOrderStatus', _t('SilvercartPaymentPaypal.ORDERSTATUS_PAYED'), $OrderStatus->map('ID', 'Title'), $this->PaidOrderStatus),
                        new DropdownField('CanceledOrderStatus', _t('SilvercartPaymentPaypal.ORDERSTATUS_CANCELED'), $OrderStatus->map('ID', 'Title'), $this->CanceledOrderStatus),
                        new DropdownField('PendingOrderStatus', _t('SilvercartPaymentPaypal.ORDERSTATUS_PENDING'), $OrderStatus->map('ID', 'Title'), $this->PendingOrderStatus),
                        new DropdownField('RefundedOrderStatus', _t('SilvercartPaymentPaypal.ORDERSTATUS_REFUNDED'), $OrderStatus->map('ID', 'Title'), $this->RefundedOrderStatus)
                )
        );
        $fields->addFieldToTab('Sections.Translations', new ComplexTableField($this, 'SilvercartPaymentPaypalLanguages', 'SilvercartPaymentPaypalLanguage'));
        return $fields;
    }

    /**
     * Set the title for the submit button on the order confirmation step.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 07.04.2011
     */
    public function getOrderConfirmationSubmitButtonTitle() {
        return _t('SilvercartPaymentPaypal.ORDER_CONFIRMATION_SUBMIT_BUTTON_TITLE');
    }

    // ------------------------------------------------------------------------
    // processing methods
    // ------------------------------------------------------------------------

    /**
     * hook to be called after order creation
     *
     * @param Order $orderObj object to be processed
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function processPaymentAfterOrder($orderObj = array()) {
        return $this->doExpressCheckoutPayment();
    }

    /**
     * hook to be called before order creation
     *
     * saves the paypal token to the session; after that redirects to paypal checkout
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.11.2010
     */
    public function processPaymentBeforeOrder() {
       
        $token = $this->fetchPaypalToken();
       
        if (!$this->errorOccured) {
            $this->saveToken($token);
        }

        $this->controller->addCompletedStep($this->controller->getCurrentStep());
        $this->controller->addCompletedStep($this->controller->getNextStep());
        $this->controller->setCurrentStep($this->controller->getNextStep());

        if ($this->mode == 'Live') {
            Director::redirect($this->paypalCheckoutUrl_Live . 'cmd=_express-checkout&token=' . $token);
        } else {
            Director::redirect($this->paypalCheckoutUrl_Dev . 'cmd=_express-checkout&token=' . $token);
        }
    }

    /**
     * hook to be called after jumpback from payment provider; called before
     * order creation
     *
     * paypal sends the PayerID during this step which will be saved to the session
     * after that redirect to the next step of the checkout
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    public function processReturnJumpFromPaymentProvider() {
        if (!isset($_REQUEST['token'])) {
            $this->Log('processReturnJumpFromPaymentProvider', var_export($_REQUEST, true));
            $this->errorOccured = true;
            $this->addError('In der Kommunikation mit Paypal ist ein Fehler aufgetreten.');
        }
        if (!$this->errorOccured &&
                !isset($_REQUEST['PayerID'])) {

            $this->Log('processReturnJumpFromPaymentProvider', var_export($_REQUEST, true));
            $this->errorOccured = true;
            $this->addError('In der Kommunikation mit Paypal ist ein Fehler aufgetreten.');
        }

        if (!$this->errorOccured) {
            $this->savePayerid($_REQUEST['PayerID']);
            $this->controller->NextStep();
        }
    }

    // -----------------------------------------------------------------------
    // methods which concern the paypal modul only
    // -----------------------------------------------------------------------

    /**
     * returns payment and shipping information from paypal
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getExpressCheckoutDetails() {

        $parameters = array(
            'TOKEN' => $_SESSION['Paypal_Token'],
            'PAYERID' => $this->getPayerId()
        );

        $response = $this->hash_call('GetExpressCheckoutDetails', $this->generateUrlParams($parameters));

        $this->Log('getExpressCheckoutDetails: Got Response', var_export($response, true));
        $this->Log('getExpressCheckoutDetails: With Parameters', var_export($parameters, true));

        return $response;
    }

    /**
     * payment confirmation
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function doExpressCheckoutPayment() {
        // Rundungsdifferenzen beseitigen
        $cartAmountGross    = round((float) $this->order->AmountTotal->getAmount(), 2);
        $itemAmountNet      = round((float) $this->order->getPriceNet()->getAmount(), 2);
        $shippingAmt        = round((float) $this->order->HandlingCostShipmentAmount, 2);
        $handlingAmt        = round((float) $this->order->HandlingCostPaymentAmount, 2);
        $taxTotal           = 0.0;
        foreach ($this->order->getTaxRatesWithoutFees(true, true) as $taxRate) {
            $taxTotal += $taxRate->Amount->getAmount();
        }
        $taxTotal = round($taxTotal, 2);

        $this->Log(
                'doExpressCheckoutPayment: Amounts',
                '  warenkorb_summe_brutto: ' . $cartAmountGross .
                ', itemamt: ' . $itemAmountNet .
                ', shippingamt: ' . $shippingAmt .
                ', handlingamt: ' . $handlingAmt .
                ', taxamt: ' . $taxTotal
        );

        // required fields
        // Pflichtparameter:
        $parameters = array(
            'TOKEN'             => $this->getPaypalToken(),
            'PAYERID'           => $this->getPayerId(),
            'PAYMENTACTION'     => 'Sale',
            'AMT'               => $cartAmountGross, // total amount + shipping + tax
            //information for the total amount
            //
            // Informationen zum Gesamtbetrag:
            'ITEMAMT'           => $itemAmountNet, // net amounts of all positions
            'SHIPPINGAMT'       => $shippingAmt, // shipping costs
            'HANDLINGAMT'       => $handlingAmt, // packaging costs an processing fee
            'TAXAMT'            => $taxTotal, // sum of all taxes
            'DESC'              => 'Order Nr. ' . $this->order->OrderNumber,
            'CURRENCYCODE'      => $this->order->getPriceGross()->getCurrency(),
            'CUSTOM'            => 'order_id=' . $this->order->ID
        );

        $notifyUrl  =  $this->controller->PageByIdentifierCode('SilvercartPaymentNotification')->Link() . 'process/' . $this->moduleName;
        $notifyUrl .= '?' . $this->sharedSecretVariableName . '=' . urlencode($this->paypalSharedSecret) . '&';
        $notifyUrl  = Director::absoluteUrl($notifyUrl);
        $parameters['NOTIFYURL'] = $notifyUrl;
        $response                = $this->hash_call('DoExpressCheckoutPayment', $this->generateUrlParams($parameters));

        // prepare respone for DB save
        if (isset($response['ORDERTIME'])) {
            $orderTime = str_replace(
                array(
                    'T',
                    'Z'
                ),
                array(
                    ' ',
                    ''
                ),
                $response['ORDERTIME']
            );
            $response['ORDERTIME_CUSTOM'] = $orderTime;
        } else {
            $response['ORDERTIME_CUSTOM'] = '';
        }

        // create paypal order
        $paypalOrder = new SilvercartPaymentPaypalOrder();
        $paypalOrder->updateOrder(
            $this->order->ID,
            $this->getPayerId(),
            $response
        );

        if (isset($response['PAYMENTSTATUS'])) {
            // adjust order status to reply
            if (in_array($response['PAYMENTSTATUS'], $this->successPaypalStatus)) {
                $this->order->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $this->PaidOrderStatus));
            } else if (in_array($response['PAYMENTSTATUS'], $this->failedPaypalStatus)) {
                $this->order->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $this->CanceledOrderStatus));
            } else if (in_array($response['PAYMENTSTATUS'], $this->pendingPaypalStatus)) {
                $this->order->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $this->PendingOrderStatus));
            } else if (in_array($response['PAYMENTSTATUS'], $this->refundedPaypalStatus)) {
                $this->order->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $this->RefundedOrderStatus));
            } else {
                $this->order->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $this->CanceledOrderStatus));
            }
        } else {
            $this->order->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $this->CanceledOrderStatus));
        }

        $this->Log('doExpressCheckoutPayment: Got Response', var_export($response, true));
        $this->Log('doExpressCheckoutPayment: With Parameters', var_export($parameters, true));

        // return status
        if (strtolower($response['ACK']) != 'success') {
            $this->errorOccured = true;
            $this->addError('Leider konnte uns Paypal keine positive Rückmeldung zu der Zahlungsfähigkeit Ihrer gewählten Bezahlart geben (Paypal Fehler 10417). Aus diesem Grund haben wir Ihre Bestellung storniert.');
            return false;
        } else {
            return true;
        }
    }

    /**
     * make shure that the shared secred was passed correctly
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 23.11.2010
     */
    public function validateSharedSecret() {

        $secretIsValid = false;

        if (isset($_REQUEST[$this->sharedSecretVariableName])) {
            $ownSharedSecret = mb_convert_encoding($this->paypalSharedSecret, 'UTF-8');
            $sentSharedSecret = mb_convert_encoding(urldecode($_REQUEST[$this->sharedSecretVariableName]), 'UTF-8');

            if (mb_strstr($ownSharedSecret, $sentSharedSecret) === $ownSharedSecret) {
                $secretIsValid = true;
            } else {
                $this->Log('validateSharedSecret', 'Gesendetes Secret: ' . $sentSharedSecret . ', Eigenes Secret: ' . $ownSharedSecret);
            }
        }

        return $secretIsValid;
    }

    /**
     * called via IPN script; processes the request confirmation and adjusts the
     * order status;
     * paypal calls this IPN script and sends all data relevant for the request.
     * To check if the paypal answer is really from paypal, the answer plus an
     * additional parameter will be sent back to paypal. paypal answers "VERIFIED"
     * or "INVALID".
     * If the answer is "VERIFIED" we check if the order status must be adjusted.
     *
     * Wird vom IPN-Script aufgerufen und kuemmert sich um die Bestaetigung der
     * gesendeten Anfrage und ggfs. die Anpassung des Status der Bestellung.
     * 
     * Paypal ruft das IPN-Script auf und sendet alle fuer die Zahlung
     * relevanten Daten. Um zu ueberpruefen, ob das IPN-Script tatsaechlich von
     * Paypal aufgerufen wurde, senden wir alle erhaltenen Parameter plus einen
     * Zusatzparameter an Paypal zurueck und erhalten als Antwort entweder
     * "VERIFIED" oder "INVALID".
     * Ist die Antwort "VERIFIED", pruefen wir, ob der Bestellstatus angepasst
     * werden muss.
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function isValidPaypalIPNCall() {
        $requestIsFromPaypal = false;
        $req = 'cmd=_notify-validate';
        $header = '';

        // Alle gesendeten Variablen muessen korrekt zusammengefasst werden.
        foreach ($_REQUEST as $key => $value) {
            if (get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        // Zusammenfasste Variablen an Paypal zuruecksenden.
        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        if ($this->mode == 'Live') {
            $url = 'ssl://www.paypal.com';
        } else {
            $url = 'ssl://sandbox.paypal.com';
        }
        $fp = fsockopen($url, 443, $errno, $errstr, 30);

        if (!$fp) {
            // Socket konnte nicht geoeffnet werden, Abbruch.
            $this->Log('isValidPaypalIPNCall', 'Bestaetigung konnte nicht an Paypal zurueckgesendet werden. URL: ' . $url . ', Errno: ' . $errno . ', Errstr: ' . $errstr);
            $requestIsFromPaypal = false;
        } else {
            // Socket ist offen, zusammengefasste Variablen senden und Antwort
            // entgegennehmen.
            fputs($fp, $header . $req);

            while (!feof($fp)) {

                $res = fgets($fp, 1024);

                if (strcmp($res, "VERIFIED") == 0) {
                    // Erfolgreiche Bestaetigung von Paypal: Zahlung kann untersucht
                    // werden.
                    $requestIsFromPaypal = true;
                } else if (strcmp($res, "INVALID") == 0) {
                    // Die Zahlungsbestaetigung kam nicht von Paypal
                    $this->Log('isValidPaypalIPNCall', 'Zahlungsbestaetigung kam nicht von Paypal! Abbruch');
                    $this->Log('isValidPaypalIPNCall', 'Antwort von Paypal: ' . var_export($res, true));
                    $requestIsFromPaypal = false;
                }
            }
            fclose($fp);
        }

        return $requestIsFromPaypal;
    }

    /**
     * retireves paypal PayerID from the URL; IPN notification variable is different
     * from the checkout notification
     *
     * Holt sich die Paypal PayerID aus der URL. IPN-Benachrichtigungsvariable
     * unterscheidet sich von Checkout-Benachrichtung.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getPayerId() {

        $payerId = '';

        if (isset($_REQUEST['payer_id'])) {
            $payerId = $_REQUEST['payer_id'];
        } elseif (isset($_REQUEST['PayerID'])) {
            $payerId = $_REQUEST['PayerID'];
        } elseif (isset($_SESSION['paypal_module_payer_id'])) {
            $payerId = $_SESSION['paypal_module_payer_id'];
        }

        return $payerId;
    }

    /**
     * accepts the variables and values sent via IPN and saves them to an
     * assiciative array
     *
     * Nimmt die per IPN gesendeten Variablen und Werte entgegen und ueber-
     * traegt sie in ein assoziatives Array.
     * 
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getIpnRequestVariables() {
        $variables = array();
        $ipnKeysMap = array(
            'txn_id'                => 'TRANSACTIONID',
            'txn_type'              => 'TRANSACTIONTYPE',
            'payment_type'          => 'PAYMENTTYPE',
            'payment_status'        => 'PAYMENTSTATUS',
            'payment_date'          => 'ORDERTIME_CUSTOM',
            'pending_reason'        => 'PENDINGREASON',
            'reason_code'           => 'REASONCODE',
            'mc_currency'           => 'CURRENCYCODE',
            'mc_fee'                => 'FEEAMT',
            'mc_gross'              => 'AMT',
            'tax'                   => 'TAXAMT',
            'shipping'              => 'SHIPPINGAMT',
            'address_city'          => 'SHIPTOCITY',
            'address_country'       => 'SHIPTOCOUNTRYNAME',
            'address_country_code'  => 'SHIPTOCOUNTRYCODE',
            'address_name'          => 'SHIPTONAME',
            'address_state'         => 'SHIPTOSTATE',
            'address_status'        => 'ADDRESSSTATUS',
            'address_street'        => 'SHIPTOADDRESS',
            'address_zip'           => 'SHIPTOZIP',
            'first_name'            => 'FIRSTNAME',
            'last_name'             => 'LASTNAME',
            'payer_email'           => 'PAYEREMAIL',
            'payer_status'          => 'PAYERSTATUS',
            'verify_sign'           => 'VERIFYSIGN'
        );

        // Empfangene Werte in das richtige Charset konvertieren
        foreach ($ipnKeysMap as $ipnVariable => $checkoutVariable) {
            if (isset($_REQUEST[$ipnVariable])) {
                if ($encoding = mb_detect_encoding($_REQUEST[$ipnVariable])) {
                    if ($encoding != 'UTF-8') {
                        $variables[$checkoutVariable] = iconv($encoding, 'UTF-8', $_REQUEST[$ipnVariable]);
                    } else {
                        $variables[$checkoutVariable] = utf8_encode($_REQUEST[$ipnVariable]);
                    }
                }
            }
        }

        // Empfangene Werte aufbereiten
        $variables['ORDERTIME_CUSTOM'] = date('Y-m-d H:i:s', strtotime($variables['ORDERTIME_CUSTOM']));

        $this->Log('getIpnRequestVariables: Incoming Request Variables', var_export($_REQUEST, true));
        $this->Log('getIpnRequestVariables: Translated Request Variables', var_export($variables, true));

        return $variables;
    }

    /**
     * returns an associative array with data passed to the field "Custom"
     *
     * Liefert die im Feld "Custom" uebergebenen Key-Value-Paare als
     * assoziatives Array zurueck.
     * 
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function getIpnCustomVariables() {
        $variables = array();

        if (isset($_REQUEST['custom'])) {
            if (strpos($_REQUEST['custom'], ',') !== false) {
                $pairStr = explode(',', $_REQUEST['custom']);
            } else {
                $pairStr = $_REQUEST['custom'];
            }

            $pairArr = explode('=', $pairStr);
            $variables[$pairArr[0]] = $pairArr[1];
        }

        return $variables;
    }

    /**
     * uptates the orders shipping address
     * 
     * @param int   $ordersId     order's ID
     * @param array $ipnVariables paypals request variables
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function updateOrderDeliveryAddress($ordersId, $ipnVariables) {
    }

    /**
     * returns the PaypalTokens saved to the session
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    protected function getPaypalToken() {
        $token = '';

        if (isset($_SESSION['paypal_module_token'])) {
            $token = $_SESSION['paypal_module_token'];
        }

        return $token;
    }

    /**
     * Returns the step configuration.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 06.04.2011
     */
    public function getStepConfiguration() {
        return array(
            'silvercart_payment_paypal/templates/checkout/' => array(
                'prefix' => 'SilvercartPaymentPaypalCheckoutFormStep'
            )
        );
    }
    
    /**
     * Creates and relates required order status and logo images.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@standardized.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 27.04.2011
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();
        
        $requiredStatus = array(
            'payed'             => _t('SilvercartOrderStatus.PAYED'),
            'paypal_refunded'   => _t('SilvercartOrderStatus.PAYPAL_REFUNDING'),
            'paypal_pending'    => _t('SilvercartOrderStatus.PAYPAL_PENDING'),
            'paypal_success'    => _t('SilvercartOrderStatus.PAYPAL_SUCCESS'),
            'paypal_error'      => _t('SilvercartOrderStatus.PAYPAL_ERROR'),
            'paypal_canceled'   => _t('SilvercartOrderStatus.PAYPAL_CANCELED')
        );
        
        $paymentLogos = array(
            'Paypal'  => '/silvercart_payment_paypal/images/horizontal_solution_PPeCheck.png',
        );

        foreach ($requiredStatus as $code => $title) {
            if (!DataObject::get_one('SilvercartOrderStatus', sprintf("`Code`='%s'", $code))) {
                $silvercartOrderStatus = new SilvercartOrderStatus();
                $silvercartOrderStatus->Title = $title;
                $silvercartOrderStatus->Code = $code;
                $silvercartOrderStatus->write();
            }
        }
        
        $uploadsFolder = DataObject::get_one('Folder', "`Name`='Uploads'");
        if (!$uploadsFolder) {
            $uploadsFolder = new Folder();
            $uploadsFolder->Name = 'Uploads';
            $uploadsFolder->Title = 'Uploads';
            $uploadsFolder->Filename = 'assets/Uploads/';
            $uploadsFolder->write();
        }
        
        // check if images exist
        $paypalModule = DataObject::get_one('SilvercartPaymentMethod', "`SilvercartPaymentMethod`.`ClassName` = 'SilvercartPaymentPaypal'");
        foreach ($paymentLogos as $title => $logo) {
            if ($paypalModule->PaymentLogos()->Count() == 0 && $paypalModule->showPaymentLogos) {
                $paymentLogo = new SilvercartImage();
                $paymentLogo->Title = $title;
                $storedLogo = DataObject::get_one('Image', sprintf("`Name`='%s'", basename($logo)));
                if ($storedLogo) {
                    $paymentLogo->ImageID = $storedLogo->ID;
                } else {
                    file_put_contents(Director::baseFolder() . '/' . $uploadsFolder->Filename . basename($logo), file_get_contents(Director::baseFolder() . $logo));
                    $image = new Image();
                    $image->setFilename($uploadsFolder->Filename . basename($logo));
                    $image->setName(basename($logo));
                    $image->Title = basename($logo, '.png');
                    $image->ParentID = $uploadsFolder->ID;
                    $image->write();
                    $paymentLogo->ImageID = $image->ID;
                }
                $paymentLogo->write();
                $paypalModule->PaymentLogos()->add($paymentLogo);
            }
        }

        $paypalPayments = DataObject::get('SilvercartPaymentPaypal', "`PaidOrderStatus`=0");
        if ($paypalPayments) {
            foreach ($paypalPayments as $paypalPayment) {
                $paypalPayment->PaidOrderStatus       = DataObject::get_one('SilvercartOrderStatus', "`Code`='payed'")->ID;
                $paypalPayment->successPaypalStatus   = DataObject::get_one('SilvercartOrderStatus', "`Code`='paypal_success'")->ID;
                $paypalPayment->failedPaypalStatus    = DataObject::get_one('SilvercartOrderStatus', "`Code`='paypal_error'")->ID;
                $paypalPayment->refundedPaypalStatus  = DataObject::get_one('SilvercartOrderStatus', "`Code`='paypal_refunded'")->ID;
                $paypalPayment->pendingPaypalStatus   = DataObject::get_one('SilvercartOrderStatus', "`Code`='paypal_pending'")->ID;
                $paypalPayment->write();
            }
        }
    }

    /**
     * Fetches a paypal token via API-call(SetExpressCheckout) which is used for
     * identification in further steps;
     *
     * Holt sich ueber einen API-Aufruf bei Paypal ein Token, das fuer die
     * restlichen Schritte als Identifikation verwendet wird.
     * Name der Paypal API Methode: SetExpressCheckout
     *
     * @return string|boolean false
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    protected function fetchPaypalToken() {
        $checkoutData = $this->controller->getCombinedStepData();
        
        if (isset($checkoutData['ShippingMethod'])) {
            $this->shoppingCart->setShippingMethodID($checkoutData['ShippingMethod']);
        }
        if (isset($checkoutData['PaymentMethod'])) {
            $this->shoppingCart->setPaymentMethodID($checkoutData['PaymentMethod']);
        }
        
        $notifyUrl  =  Director::absoluteUrl($this->controller->PageByIdentifierCode('SilvercartPaymentNotification')->Link().'process/'.$this->moduleName);
        $parameters = array(
            'ADDROVERRIDE'                          => '1',
            'VERSION'                               => '63',
            'PAYMENTREQUEST_0_AMT'                  => round((float) $this->shoppingCart->getAmountTotal()->getAmount(), 2),
            'PAYMENTREQUEST_0_ITEMAMT'              => round((float) $this->shoppingCart->getTaxableAmountGrossWithoutFees()->getAmount(), 2),
            'PAYMENTREQUEST_0_CURRENCYCODE'         => $this->shoppingCart->getAmountTotal()->getCurrency(),
            'PAYMENTREQUEST_0_SHIPPINGAMT'          => round((float) $this->shoppingCart->HandlingCostShipment()->getAmount(), 2),
            'PAYMENTREQUEST_0_HANDLINGAMT'          => round((float) $this->shoppingCart->HandlingCostPayment()->getAmount(), 2),
            'RETURNURL'                             => $this->getReturnLink(),
            'CANCELURL'                             => $this->getCancelLink(),
            'NOTIFYURL'                             => $notifyUrl,
            'CUSTOM'                                => '',
            'PAYMENTREQUEST_0_RETURNURL'            => $this->getReturnLink(),
            'PAYMENTREQUEST_0_CANCELURL'            => $this->getCancelLink(),
            'PAYMENTREQUEST_0_NOTIFYURL'            => $notifyUrl,
            'PAYMENTREQUEST_0_SHIPTONAME'           => $this->shippingAddress->FirstName.' '.$this->shippingAddress->Surname,
            'PAYMENTREQUEST_0_SHIPTOSTREET'         => $this->shippingAddress->Street.' '.$this->shippingAddress->StreetNumber,
            'PAYMENTREQUEST_0_SHIPTOCITY'           => $this->shippingAddress->City,
            'PAYMENTREQUEST_0_SHIPTOZIP'            => $this->shippingAddress->Postcode,
            'PAYMENTREQUEST_0_SHIPTOSTATE'          => $this->shippingAddress->State,
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'    => $this->shippingAddress->Country->ISO2,
            'PAYMENTREQUEST_0_SHIPTOPHONENUM'       => $this->shippingAddress->PhoneAreaCode.' '.$this->shippingAddress->Phone,
        );

        $itemCount          = 0;
        $taxAmtTotal        = 0.0;

        foreach ($this->shoppingCart->SilvercartShoppingCartPositions() as $shoppingCartPosition) {
            $positionTaxAmt         = round($shoppingCartPosition->SilvercartProduct()->getTaxAmount(), 2);
            $positionTaxAmtTotal    = $positionTaxAmt * $shoppingCartPosition->Quantity;
            $taxAmtTotal           += round($positionTaxAmtTotal, 2);

            $parameters['L_PAYMENTREQUEST_0_NAME'.$itemCount]           = $shoppingCartPosition->Quantity.' x '.$shoppingCartPosition->SilvercartProduct()->Title;
            $parameters['L_PAYMENTREQUEST_0_DESC'.$itemCount]           = substr($shoppingCartPosition->SilvercartProduct()->ShortDescription, 0, 50);
            $parameters['L_PAYMENTREQUEST_0_AMT'.$itemCount]            = round((float) $shoppingCartPosition->getPrice()->getAmount(), 2);
            $parameters['L_PAYMENTREQUEST_0_ITEMCATEGORY'.$itemCount]   = 'Physical';

            $itemCount++;
        }

        // Charges and discounts for products
        if ($this->shoppingCart->HasChargesAndDiscountsForProducts()) {
            foreach ($this->shoppingCart->ChargesAndDiscountsForProducts() as $shoppingCartPosition) {
                $parameters['L_PAYMENTREQUEST_0_NAME'.$itemCount]           = $shoppingCartPosition->Name;
                $parameters['L_PAYMENTREQUEST_0_DESC'.$itemCount]           = '';
                $parameters['L_PAYMENTREQUEST_0_AMT'.$itemCount]            = round((float) $shoppingCartPosition->Price->getAmount(), 2);
                $parameters['L_PAYMENTREQUEST_0_ITEMCATEGORY'.$itemCount]   = 'Physical';

                $itemCount++;
            }
        }

        // define optional parameters
        // Optionale Parameter definieren
        if ($this->mode == 'Live') {
            if (!empty($this->paypalBackLinkGiropaySucess_Live)) {
                $parameters['GIROPAYSUCCESSURL'] = $this->paypalBackLinkGiropaySucess_Live;
            }
            if (!empty($this->paypalBackLinkGiropayCancel_Live)) {
                $parameters['GIROPAYCANCELURL'] = $this->paypalBackLinkGiropayCancel_Live;
            }
            if (!empty($this->paypalBackLinkBanktransfer_Live)) {
                $parameters['BANKTXNPENDINGURL'] = $this->paypalBackLinkBanktransfer_Live;
            }
        } else {
            if (!empty($this->paypalBackLinkGiropaySucess_Dev)) {
                $parameters['GIROPAYSUCCESSURL'] = $this->paypalBackLinkGiropaySucess_Dev;
            }
            if (!empty($this->paypalBackLinkGiropayCancel_Dev)) {
                $parameters['GIROPAYCANCELURL'] = $this->paypalBackLinkGiropayCancel_Dev;
            }
            if (!empty($this->paypalBackLinkBanktransfer_Dev)) {
                $parameters['BANKTXNPENDINGURL'] = $this->paypalBackLinkBanktransfer_Dev;
            }
        }

        $apiCallResult = $this->hash_call('SetExpressCheckout', $this->generateUrlParams($parameters));

        // an error has occured
        // Es ist ein Fehler aufgetreten
        if (strtolower($apiCallResult['ACK']) != 'success' &&
            strtolower($apiCallResult['ACK']) != 'successwithwarning') {
            $this->Log('fetchPaypalToken', var_export($apiCallResult, true));
            $this->Log('fetchPaypalToken', var_export($parameters, true));
            $this->errorOccured = true;
            $this->addError('Die Kommunikation mit Paypal konnte nicht initialisiert werden.');
            
            return false;
        } else {

            $this->Log('fetchPaypalToken: Got Response', var_export($apiCallResult, true));
            $this->Log('fetchPaypalToken: With Parameters', var_export($parameters, true));

            return $apiCallResult['TOKEN'];
        }
    }

    /**
     * processes a method call via paypals NVP-API
     *
     * Fuehrt einen Methodenaufruf ueber die NVP-API von Paypal durch.
     *
     * @param string $methodName method to be called
     * @param string $nvpStr     the string to be sent to the NVP server
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    protected function hash_call($methodName, $nvpStr) {
        //setting the curl parameters.
        $ch = curl_init();

        if ($this->mode == 'Live') {
            curl_setopt($ch, CURLOPT_URL, $this->paypalNvpApiServerUrl_Live);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->paypalNvpApiServerUrl_Dev);
        }

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //curl_setopt($ch, CURLOPT_PROXY,"localhost:80");

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        //NVPRequest for submitting to server
        if ($this->mode == 'Live') {
            $nvpreq = "METHOD=" . urlencode($methodName) .
                    "&VERSION=" . urlencode($this->paypalApiVersion_Live) .
                    "&PWD=" . urlencode($this->paypalApiPassword_Live) .
                    "&USER=" . urlencode($this->paypalApiUsername_Live) .
                    "&SIGNATURE=" . urlencode($this->paypalApiSignature_Live) .
                    $nvpStr;
        } else {
            $nvpreq = "METHOD=" . urlencode($methodName) .
                    "&VERSION=" . urlencode($this->paypalApiVersion_Dev) .
                    "&PWD=" . urlencode($this->paypalApiPassword_Dev) .
                    "&USER=" . urlencode($this->paypalApiUsername_Dev) .
                    "&SIGNATURE=" . urlencode($this->paypalApiSignature_Dev) .
                    $nvpStr;
        }

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        //getting response from server
        $response = curl_exec($ch);

        //convrting NVPResponse to an Associative Array
        $nvpResArray = $this->deformatNVP($response);
        $nvpReqArray = $this->deformatNVP($nvpreq);
        $_SESSION['nvpReqArray'] = $nvpReqArray;

        if (curl_errno($ch)) {
            // moving to display page to display curl errors
            $_SESSION['curl_error_no'] = curl_errno($ch);
            $_SESSION['curl_error_msg'] = curl_error($ch);
            $this->Log('hash_call', 'curl_errno: ' . curl_errno($ch) . ', curl_error_msg: ' . curl_error($ch));
            return false;
        } else {
            //closing the curl
            curl_close($ch);
        }

        return $nvpResArray;
    }

    /**
     * This method will take a NVPString and convert it to an Associative Array and it will decode the response.
     * It is usefull to search for a particular key and displaying arrays.
     *
     * @param string $nvpstr NVPString
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    protected function deformatNVP($nvpstr) {
        $intial = 0;
        $nvpArray = array();

        while (strlen($nvpstr)) {
            //postion of Key
            $keypos = strpos($nvpstr, '=');
            //position of value
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

            /* getting the Key and Value values and storing in a Associative Array */
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        return $nvpArray;
    }

    /**
     * Creates and returns a string ("key=value&key=value&...") from an
     * associative array
     *
     * Erzeugt aus einem assoziativen Array einen String im Format
     * "key=value&key=value&..." und gibt diesen zurueck.
     *
     * @param array $parameters an associative array
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    protected function generateUrlParams($parameters) {

        $paramString = '';

        foreach ($parameters as $key => $value) {
            $paramString .= '&' . urlencode($key) . '=' . urlencode($value);
        }

        return $paramString;
    }

    /**
     * adds the session call sign and ID to the URL
     *
     * Haengt die Sessionkennung und ID an eine URL an.
     * 
     * @param string $url Die URL
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 17.11.2010
     */
    protected function addSessionToUrl($url) {

        if (strpos($url, '?') === false) {
            $url .= '?';
        }

        $url .= session_name() . '=' . session_id() . '&';

        return $url;
    }

    /**
     * saves the paypaltoken to the session
     *
     * Speichert das Paypal-Token in der Session.
     *
     * @param string $token Das Paypal-Token
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 18.11.2010
     */
    protected function saveToken($token) {
        $_SESSION['paypal_module_token'] = $token;
    }

    /**
     * writes the PayerID to the session
     *
     * Speichert die PayerId in der Session.
     *
     * @param string $payerId Die PayerId
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 19.11.2010
     */
    protected function savePayerid($payerId) {
        $_SESSION['paypal_module_payer_id'] = $payerId;
    }

}
