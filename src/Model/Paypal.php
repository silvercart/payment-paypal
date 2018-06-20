<?php
namespace SilverCart\Paypal\Model;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_ExclusiveRelationEditor;
use SilverCart\Forms\FormFields\FieldGroup;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Order\Order;
use SilverCart\Model\Order\OrderStatus;
use SilverCart\Model\Payment\PaymentMethod;
use SilverCart\Paypal\Model\PaypalTranslation;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\GridField\GridField;

/**
 * Paypal payment modul
 *
 * @package SilverCart
 * @subpackage Paypal_Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>,
 *         Sascha Koehler <skoehler@pixeltricks.de>
 * @since 24.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class Paypal extends PaymentMethod {
    
    const SESSION_KEY = 'Silvercart.Paypal';
    const TOKEN_SESSION_KEY = self::SESSION_KEY . '.Token';
    const PAYERID_SESSION_KEY = self::SESSION_KEY . '.PayerID';
    const BEFORE_PAYMENT_PROVIDER_IS_PROCESSED_SESSION_KEY = self::SESSION_KEY . '.ProcessedBeforePaymentProvider';
    const AFTER_PAYMENT_PROVIDER_IS_PROCESSED_SESSION_KEY = self::SESSION_KEY . '.ProcessedAfterPaymentProvider';

    /**
     * db field definitions
     *
     * @var array
     */
    private static $db = [
        'paypalSharedSecret'          => 'Varchar(255)',
        'paypalCheckoutUrl_Dev'       => 'Varchar(255)',
        'paypalCheckoutUrl_Live'      => 'Varchar(255)',
        'paypalApiUsername_Dev'       => 'Varchar(255)',
        'paypalApiUsername_Live'      => 'Varchar(255)',
        'paypalApiPassword_Dev'       => 'Varchar(255)',
        'paypalApiPassword_Live'      => 'Varchar(255)',
        'paypalApiSignature_Dev'      => 'Varchar(255)',
        'paypalApiSignature_Live'     => 'Varchar(255)',
        'paypalNvpApiServerUrl_Dev'   => 'Varchar(255)',
        'paypalNvpApiServerUrl_Live'  => 'Varchar(255)',
        'paypalSoapApiServerUrl_Dev'  => 'Varchar(255)',
        'paypalSoapApiServerUrl_Live' => 'Varchar(255)',
        'paypalApiVersion_Dev'        => 'Varchar(255)',
        'paypalApiVersion_Live'       => 'Varchar(255)',
        'PaidOrderStatus'             => 'Int',
        'CanceledOrderStatus'         => 'Int',
        'PendingOrderStatus'          => 'Int',
        'RefundedOrderStatus'         => 'Int'
    ];
    
    /**
     * Casted attributes
     *
     * @var array
     */
    private static $casting = [
        'CheckoutUrl'      => 'Text',
        'ApiUsername'      => 'Text',
        'ApiPassword'      => 'Text',
        'ApiSignature'     => 'Text',
        'NvpApiServerUrl'  => 'Text',
        'SoapApiServerUrl' => 'Text',
        'ApiVersion'       => 'Text',
    ];

    /**
     * Default db values.
     *
     * @var array
     */
    private static $defaults = [
        'paypalCheckoutUrl_Dev'       => 'https://www.sandbox.paypal.com/cgi-bin/webscr?',
        'paypalCheckoutUrl_Live'      => 'https://www.paypal.com/cgi-bin/webscr?',
        'paypalNvpApiServerUrl_Dev'   => 'https://api-3t.sandbox.paypal.com/nvp',
        'paypalNvpApiServerUrl_Live'  => 'https://api-3t.paypal.com/nvp',
        'paypalSoapApiServerUrl_Dev'  => 'https://api-3t.sandbox.paypal.com/2.0',
        'paypalSoapApiServerUrl_Live' => 'https://api-3t.paypal.com/2.0',
        'paypalApiVersion_Dev'        => '2.3',
        'paypalApiVersion_Live'       => '2.3',
    ];
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    private static $has_many = [
        'PaypalTranslations' => PaypalTranslation::class,
    ];

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentPaypal';
    
    /**
     * contains module name for display in the admin backend
     *
     * @var string
     */
    protected $moduleName = 'Paypal';
    
    /**
     * contains name for the shared secret ID; Used by paypal at the IPN answer
     *
     * @var string
     */
    protected $sharedSecretVariableName = 'sh';
    
    /**
     * contains all strings of the paypal answer which declare the transaction status false
     *
     * @var array
     */
    public $failedPaypalStatus = [
        'Denied',
        'Expired',
        'Failed',
        'Voided',
    ];
    
    /**
     * contains all strings of the paypal answer which declare the transaction status true
     *
     * @var array
     */
    public $successPaypalStatus = [
        'Completed',
        'Processed',
        'Canceled-Reversal',
    ];
    
    /**
     * contains all strings of the paypal answer of a withdrawn payment
     *
     * @var array
     */
    public $refundedPaypalStatus = [
        'Refunded',
        'Reversed',
    ];
    
    /**
     * contains all strings of the paypal answer which declare the transaction status pending
     *
     * @var array
     */
    public $pendingPaypalStatus = [
        'Pending',
        'Created',
    ];

    /**
     * i18n for field labels
     *
     * @param boolean $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 24.04.2018
     */
    public function fieldLabels($includerelations = true) {
        $fields = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'paypalSharedSecret'                 => _t(self::class . '.SHARED_SECRET', 'shared secret for secure communication'),
                    'paypalCheckoutUrl'                  => _t(self::class . '.CHECKOUT_URL', 'URL to the paypal checkout'),
                    'paypalCheckoutUrl_Dev'              => _t(self::class . '.CHECKOUT_URL', 'URL to the paypal checkout'),
                    'paypalCheckoutUrl_Live'             => _t(self::class . '.CHECKOUT_URL', 'URL to the paypal checkout'),
                    'paypalApiUsername'                  => _t(self::class . '.API_USERNAME', 'API username'),
                    'paypalApiUsername_Dev'              => _t(self::class . '.API_USERNAME', 'API username'),
                    'paypalApiUsername_Live'             => _t(self::class . '.API_USERNAME', 'API username'),
                    'paypalApiPassword'                  => _t(self::class . '.API_PASSWORD', 'API password'),
                    'paypalApiPassword_Dev'              => _t(self::class . '.API_PASSWORD', 'API password'),
                    'paypalApiPassword_Live'             => _t(self::class . '.API_PASSWORD', 'API password'),
                    'paypalApiSignature'                 => _t(self::class . '.API_SIGNATURE', 'API signature'),
                    'paypalApiSignature_Dev'             => _t(self::class . '.API_SIGNATURE', 'API signature'),
                    'paypalApiSignature_Live'            => _t(self::class . '.API_SIGNATURE', 'API signature'),
                    'paypalApiVersion'                   => _t(self::class . '.API_VERSION', 'API version'),
                    'paypalApiVersion_Dev'               => _t(self::class . '.API_VERSION', 'API version'),
                    'paypalApiVersion_Live'              => _t(self::class . '.API_VERSION', 'API version'),
                    'paypalNvpApiServerUrl'              => _t(self::class . '.URL_API_NVP', 'URL to the paypal NVP API server'),
                    'paypalNvpApiServerUrl_Dev'          => _t(self::class . '.URL_API_NVP', 'URL to the paypal NVP API server'),
                    'paypalNvpApiServerUrl_Live'         => _t(self::class . '.URL_API_NVP', 'URL to the paypal NVP API server'),
                    'paypalSoapApiServerUrl'             => _t(self::class . '.URL_API_SOAP', 'URL to the paypal SOAP API server'),
                    'paypalSoapApiServerUrl_Dev'         => _t(self::class . '.URL_API_SOAP', 'URL to the paypal SOAP API server'),
                    'paypalSoapApiServerUrl_Live'        => _t(self::class . '.URL_API_SOAP', 'URL to the paypal SOAP API server'),
                    'PaidOrderStatus'                    => _t(self::class . '.ORDERSTATUS_PAID', 'orderstatus for notification "paid"'),
                    'CanceledOrderStatus'                => _t(self::class . '.ORDERSTATUS_CANCELED', 'orderstatus for notification "canceled"'),
                    'PendingOrderStatus'                 => _t(self::class . '.ORDERSTATUS_PENDING', 'orderstatus for notification "pending"'),
                    'RefundedOrderStatus'                => _t(self::class . '.ORDERSTATUS_REFUNDED', 'orderstatus for notification "refunded"'),
                    'TabOrderStatus'                     => _t(self::class . '.ATTRIBUTED_ORDERSTATUS', 'attributed order status'),
                    'TabApiDev'                          => _t(self::class . '.API_DEVELOPMENT_MODE', 'API development mode'),
                    'TabApiLive'                         => _t(self::class . '.API_LIVE_MODE', 'API live mode'),
                    'TabUrlsDev'                         => _t(self::class . '.URLS_DEV_MODE', 'URLs of dev mode'),
                    'TabUrlsLive'                        => _t(self::class . '.URLS_LIVE_MODE', 'URLs of live mode'),
                    'PaypalApiData'                      => _t(self::class . '.PaypalApiData', 'PayPal login data'),
                    'OrderConfirmationSubmitButtonTitle' => _t(self::class . '.ORDER_CONFIRMATION_SUBMIT_BUTTON_TITLE', 'Proceed to payment via PayPal'),
                    'PaypalTranslations'                 => PaypalTranslation::singleton()->plural_name(),
                    'StatusPaid'                         => _t(OrderStatus::class . '.PAID', 'Paid'),
                    'StatusPaypalRefunded'               => _t(self::class . '.StatusPaypalRefunding', 'PayPal refunding'),
                    'StatusPaypalPending'                => _t(self::class . '.StatusPaypalPending', 'PayPal pending'),
                    'StatusPaypalSuccess'                => _t(self::class . '.StatusPaypalSuccess', 'Payment approved by PayPal'),
                    'StatusPaypalError'                  => _t(self::class . '.StatusPaypalError', 'PayPal error'),
                    'StatusPaypalCanceled'               => _t(self::class . '.StatusPaypalCanceled', 'PayPal canceled'),
                    'AnErrorOccurredPaymentFailed'       => _t(self::class . '.AnErrorOccurredPaymentFailed', 'PayPal payment failed (error 10417)'),
                )
        );
        return $fields;
    }
    
    /**
     * Adds the fields for the PayPal API
     *
     * @param FieldList $fields FieldList to add fields to
     * @param bool      $forDev Add fields for dev or live mode?
     * 
     * @return void
     */
    protected function getFieldsForAPI($fields, $forDev = false) {
        $mode = 'Live';
        if ($forDev) {
            $mode = 'Dev';
        }
        $apiGroup = new FieldGroup('APIDevGroup', '', $fields);
        $apiGroup->push(new TextField('paypalApiUsername_' . $mode,  $this->fieldLabel('paypalApiUsername_' . $mode)));
        $apiGroup->push(new TextField('paypalApiPassword_' . $mode,  $this->fieldLabel('paypalApiPassword_' . $mode)));
        $apiGroup->push(new TextField('paypalApiSignature_' . $mode, $this->fieldLabel('paypalApiSignature_' . $mode)));
        $apiGroup->push(new TextField('paypalApiVersion_' . $mode,   $this->fieldLabel('paypalApiVersion_' . $mode)));
        
        $fieldlist = array(
                    $apiGroup,
                    new TextField('paypalCheckoutUrl_' . $mode,      $this->fieldLabel('paypalCheckoutUrl_' . $mode)),
                    new TextField('paypalNvpApiServerUrl_' . $mode,  $this->fieldLabel('paypalNvpApiServerUrl_' . $mode)),
                    new TextField('paypalSoapApiServerUrl_' . $mode, $this->fieldLabel('paypalSoapApiServerUrl_' . $mode)),
        );
        
        if (!$forDev) {
            $fieldlist[] = new TextField('paypalSharedSecret', $this->fieldLabel('paypalSharedSecret'));
        }
        
        $apiDataToggle = ToggleCompositeField::create(
                'PaypalAPI' . $mode,
                $this->fieldLabel('PaypalApiData') . ' "' . $this->fieldLabel('mode' . $mode) . '"',
                $fieldlist
        )->setHeadingLevel(4)->setStartClosed(true);
        
        $fields->addFieldToTab('Root.Basic', $apiDataToggle);
    }
    
    /**
     * Adds the fields for the PayPal order status
     *
     * @param FieldList $fields FieldList to add fields to
     * 
     * @return void
     */
    protected function getFieldsForOrderStatus($fields) {
        $orderStatus = OrderStatus::get();
        $fieldlist = array(
                $fields->dataFieldByName('orderStatus'),
                new DropdownField('PaidOrderStatus',        $this->fieldLabel('PaidOrderStatus'),       $orderStatus->map('ID', 'Title'), $this->PaidOrderStatus),
                new DropdownField('CanceledOrderStatus',    $this->fieldLabel('CanceledOrderStatus'),   $orderStatus->map('ID', 'Title'), $this->CanceledOrderStatus),
                new DropdownField('PendingOrderStatus',     $this->fieldLabel('PendingOrderStatus'),    $orderStatus->map('ID', 'Title'), $this->PendingOrderStatus),
                new DropdownField('RefundedOrderStatus',    $this->fieldLabel('RefundedOrderStatus'),   $orderStatus->map('ID', 'Title'), $this->RefundedOrderStatus)
        );
        
        $orderStatusDataToggle = ToggleCompositeField::create(
                'OrderStatus',
                $this->fieldLabel('TabOrderStatus'),
                $fieldlist
        )->setHeadingLevel(4)->setStartClosed(true);
        
        $fields->removeByName('orderStatus');
        
        $fields->addFieldToTab('Root.Basic', $orderStatusDataToggle);
    }

    /**
     * returns CMS fields
     *
     * @return \SilverStripe\Forms\FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFieldsForModules();

        $this->getFieldsForOrderStatus($fields);
        $this->getFieldsForAPI($fields, true);
        $this->getFieldsForAPI($fields);
        
        $translations = new GridField(
                'PaypalTranslations',
                $this->fieldLabel('PaypalTranslations'),
                $this->PaypalTranslations(),
                GridFieldConfig_ExclusiveRelationEditor::create()
        );
        $fields->addFieldToTab('Root.Translations', $translations);
        
        return $fields;
    }
    
    /**
     * Creates and relates required order status and logo images.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();
        
        $requiredStatus = [
            'paid'            => $this->fieldLabel('StatusPaid'),
            'paypal_refunded' => $this->fieldLabel('StatusPaypalRefunded'),
            'paypal_pending'  => $this->fieldLabel('StatusPaypalPending'),
            'paypal_success'  => $this->fieldLabel('StatusPaypalSuccess'),
            'paypal_error'    => $this->fieldLabel('StatusPaypalError'),
            'paypal_canceled' => $this->fieldLabel('StatusPaypalCanceled'),
        ];
        
        $paymentLogos = [
            'Paypal' => SILVERCART_PAYPAL_IMG_PATH . DIRECTORY_SEPARATOR . 'paypal-payments.png',
        ];

        parent::createRequiredOrderStatus($requiredStatus);
        parent::createLogoImageObjects($paymentLogos, self::class);

        $paypalPayments = Paypal::get()->filter('PaidOrderStatus', 0);
        if ($paypalPayments->exists()) {
            foreach ($paypalPayments as $paypalPayment) {
                $paypalPayment->PaidOrderStatus      = OrderStatus::get()->filter('Code', 'paid')->first()->ID;
                $paypalPayment->successPaypalStatus  = OrderStatus::get()->filter('Code', 'paypal_success')->first()->ID;
                $paypalPayment->failedPaypalStatus   = OrderStatus::get()->filter('Code', 'paypal_error')->first()->ID;
                $paypalPayment->refundedPaypalStatus = OrderStatus::get()->filter('Code', 'paypal_refunded')->first()->ID;
                $paypalPayment->pendingPaypalStatus  = OrderStatus::get()->filter('Code', 'paypal_pending')->first()->ID;
                $paypalPayment->write();
            }
        }
    }

    /***********************************************************************************************
     ***********************************************************************************************
     **                                                                                           ** 
     **                          Mutator methods for casted attributes                            ** 
     **                                                                                           ** 
     ***********************************************************************************************
     **********************************************************************************************/

    /**
     * Set the title for the submit button on the order confirmation step.
     *
     * @return string
     */
    public function getOrderConfirmationSubmitButtonTitle() {
        return $this->fieldLabel('OrderConfirmationSubmitButtonTitle');
    }
    
    /**
     * Returns the PayPal checkout URL.
     * 
     * @return string
     */
    public function getPaypalCheckoutUrl() {
        return $this->getCheckoutUrl();
    }
    
    /**
     * Returns the PayPal checkout URL.
     * 
     * @return string
     */
    public function getCheckoutUrl() {
        $paypalCheckoutUrl = '';
        if ($this->mode == 'Live') {
            $paypalCheckoutUrl = $this->paypalCheckoutUrl_Live . 'cmd=_express-checkout&token=' . $this->getPaypalToken();
        } else {
            $paypalCheckoutUrl = $this->paypalCheckoutUrl_Dev . 'cmd=_express-checkout&token=' . $this->getPaypalToken();
        }
        return $paypalCheckoutUrl;
    }
    
    /**
     * Returns the PayPal API username.
     * 
     * @return string
     */
    public function getApiUsername() {
        if ($this->mode == 'Live') {
            return $this->paypalApiUsername_Live;
        } else {
            return $this->paypalApiUsername_Dev;
        }
    }
    
    /**
     * Returns the PayPal API password.
     * 
     * @return string
     */
    public function getApiPassword() {
        if ($this->mode == 'Live') {
            return $this->paypalApiPassword_Live;
        } else {
            return $this->paypalApiPassword_Dev;
        }
    }
    
    /**
     * Returns the PayPal API signature.
     * 
     * @return string
     */
    public function getApiSignature() {
        if ($this->mode == 'Live') {
            return $this->paypalApiSignature_Live;
        } else {
            return $this->paypalApiSignature_Dev;
        }
    }
    
    /**
     * Returns the PayPal NVP API server URL.
     * 
     * @return string
     */
    public function getNvpApiServerUrl() {
        if ($this->mode == 'Live') {
            return $this->paypalNvpApiServerUrl_Live;
        } else {
            return $this->paypalNvpApiServerUrl_Dev;
        }
    }
    
    /**
     * Returns the PayPal SOAP API server URL.
     * 
     * @return string
     */
    public function getSoapApiServerUrl() {
        if ($this->mode == 'Live') {
            return $this->paypalSoapApiServerUrl_Live;
        } else {
            return $this->paypalSoapApiServerUrl_Dev;
        }
    }
    
    /**
     * Returns the PayPal SOAP API server URL.
     * 
     * @return string
     */
    public function getApiVersion() {
        if ($this->mode == 'Live') {
            return $this->paypalApiVersion_Live;
        } else {
            return $this->paypalApiVersion_Dev;
        }
    }
    
    /**
     * Returns the PayPal IPN target URL.
     * 
     * @return string
     */
    public function getIPNTargetURL() {
        $url = 'ssl://sandbox.paypal.com';
        if ($this->mode == 'Live') {
            $url = 'ssl://www.paypal.com';
        }
        return $url;
    }

    /**
     * accepts the variables and values sent via IPN and saves them to an
     * associative array.
     * 
     * @return array
     */
    public function getIPNRequestVariables() {
        $variables  = [];
        $ipnKeysMap = [
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
            'verify_sign'           => 'VERIFYSIGN',
        ];

        foreach ($ipnKeysMap as $ipnVariable => $checkoutVariable) {
            if (isset($_REQUEST[$ipnVariable]) &&
                $encoding = mb_detect_encoding($_REQUEST[$ipnVariable]) &&
                $encoding != 'UTF-8') {
                
                $variables[$checkoutVariable] = iconv($encoding, 'UTF-8', $_REQUEST[$ipnVariable]);
            } else {
                $variables[$checkoutVariable] = utf8_encode($_REQUEST[$ipnVariable]);
            }
        }

        $variables['ORDERTIME_CUSTOM'] = date('Y-m-d H:i:s', strtotime($variables['ORDERTIME_CUSTOM']));

        $this->Log('getIPNRequestVariables: Incoming Request Variables', var_export($_REQUEST, true));
        $this->Log('getIPNRequestVariables: Translated Request Variables', var_export($variables, true));

        return $variables;
    }

    /**
     * returns an associative array with data passed to the field "Custom".
     * 
     * @return array
     */
    public function getIPNCustomVariables() {
        $variables = [];

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
     * retireves paypal PayerID from the URL; IPN notification variable is different
     * from the checkout notification.
     * 
     * @return string
     */
    public function getPayerID() {
        $payerID = '';
        if (isset($_REQUEST['payer_id'])) {
            $payerID = $_REQUEST['payer_id'];
        } elseif (isset($_REQUEST['PayerID'])) {
            $payerID = $_REQUEST['PayerID'];
        } else {
            $payerID = Tools::Session()->get(self::PAYERID_SESSION_KEY);
        }
        return $payerID;
    }

    /**
     * returns the Paypal token saved to the session
     *
     * @return string
     */
    public function getPaypalToken() {
        return Tools::Session()->get(self::TOKEN_SESSION_KEY);
    }

    /***********************************************************************************************
     ***********************************************************************************************
     **                                                                                           ** 
     ** Payment processing section. SilverCart checkout will call these methods:                  ** 
     **                                                                                           ** 
     **     - canProcessBeforePaymentProvider                                                     ** 
     **     - canProcessAfterPaymentProvider                                                      ** 
     **     - canProcessBeforeOrder                                                               ** 
     **     - canProcessAfterOrder                                                                ** 
     **     - canPlaceOrder                                                                       ** 
     **     - processBeforePaymentProvider                                                        ** 
     **     - processAfterPaymentProvider                                                         ** 
     **     - processBeforeOrder                                                                  ** 
     **     - processAfterOrder                                                                   ** 
     **     - processNotification                                                                 ** 
     **     - processConfirmationText                                                             ** 
     **                                                                                           ** 
     ***********************************************************************************************
     **********************************************************************************************/
    
    /**
     * Returns whether the checkout is ready to call self::processBeforePaymentProvider().
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function canProcessBeforePaymentProvider(array $checkoutData) {
        return !$this->beforePaymentProviderIsProcessed();
    }
    
    /**
     * Returns whether the checkout is ready to call self::processAfterPaymentProvider().
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function canProcessAfterPaymentProvider(array $checkoutData) {
        $can     = false;
        $request = $this->getController()->getRequest();
        $token   = $request->getVar('token');
        $payerID = $request->getVar('PayerID');
        if (!is_null($token) &&
            !is_null($payerID)) {
            $can = true;
        }
        return $can && $this->beforePaymentProviderIsProcessed() && !$this->afterPaymentProviderIsProcessed();
    }
    
    /**
     * Is called by default checkout right before placing an order.
     * If this returns false, the order won't be placed and the checkout won't be finalized.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function canPlaceOrder(array $checkoutData) {
        return $this->beforePaymentProviderIsProcessed() && $this->afterPaymentProviderIsProcessed();
    }
    
    /**
     * Returns whether the checkout is ready to call self::processAfterOrder().
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function canProcessAfterOrder(Order $order, array $checkoutData) {
        return $this->canPlaceOrder($checkoutData) && $order instanceof Order;// && $order->exists();
    }
    
    /**
     * Is called by default checkout right before placing an order.
     * - fetches the Paypal token
     * - saves the Paypal token to the session
     * - redirects to Paypal checkout
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function processBeforePaymentProvider(array $checkoutData) {
        $token = $this->fetchPaypalToken($checkoutData);
       
        if (!$this->errorOccured) {
            $this->saveToken($token);
        }
        
        if (!($token === false &&
              $this->errorOccured)) {
            $skip = false;
            $this->extend('skipProcessBeforePaymentProvider', $skip);
            if ($skip) {
                return;
            }
            $this->getController()->redirect($this->CheckoutUrl);
            Tools::Session()->set(self::BEFORE_PAYMENT_PROVIDER_IS_PROCESSED_SESSION_KEY, true);
            Tools::saveSession();
        }
    }

    /**
     * Is called right after returning to the checkout after being redirected to PayPal.
     * PayPal sends the PayerID during this step which will be saved to the session.
     * 
     * @param array $checkoutData Checkout data
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.04.2014
     */
    public function processAfterPaymentProvider(array $checkoutData) {
        $request = $this->getController()->getRequest();
        $token   = $request->getVar('token');
        $payerID = $request->getVar('PayerID');
        if (is_null($token)) {
            $this->Log('processAfterPaymentProvider', 'ERROR: paypal token is not set.');
            $this->errorOccured = true;
        }
        if (is_null($payerID)) {
            $this->Log('processAfterPaymentProvider', 'ERROR: paypal payerID is not set.');
            $this->errorOccured = true;
        }
        if ($this->errorOccured) {
            $this->Log('processAfterPaymentProvider', ' - request data:');
            $this->Log('processAfterPaymentProvider', var_export($request->getVars(), true));
            $this->Log('processAfterPaymentProvider', '');
        } else {
            $this->savePayerID($payerID);
            Tools::Session()->set(self::AFTER_PAYMENT_PROVIDER_IS_PROCESSED_SESSION_KEY, true);
            Tools::saveSession();
        }
    }
    
    /**
     * Is called by default checkout right after placing an order.
     * 
     * @param \SilverCart\Model\Order\Order $order        Order
     * @param array                         $checkoutData Checkout data
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2018
     */
    protected function processAfterOrder(Order $order, array $checkoutData) {
        $this->doExpressCheckoutPayment($order);
        $this->clearSession();
    }
    
    /**
     * Is called when a payment provider sends a background notification to the shop.
     * 
     * @param HTTPRequest $request Request data
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function processNotification(HTTPRequest $request) {
        if ($this->validateSharedSecret($request) === false) {
            $this->Log('processNotification', '');
            $this->Log('processNotification', 'ERROR: validation of the shared secret failed.');
            $this->Log('processNotification', 'request details:');
            $this->Log('processNotification', var_export($_REQUEST, true));
            $this->Log('processNotification', '');
        }
        
        if ($this->isValidPaypalIPNCall($request)) {
            $this->Log('processNotification', 'valid Paypal notification.');
            $payerId         = $this->getPayerID();
            $ipnVariables    = $this->getIPNRequestVariables();
            $customVariables = $this->getIPNCustomVariables();

            $order = Order::get()->byID($customVariables['order_id']);
            if ($order instanceof Order &&
                $order->exists()) {
                if (in_array($ipnVariables['PAYMENTSTATUS'], $this->successPaypalStatus)) {
                    $order->setOrderStatus(OrderStatus::get()->byID($this->PaidOrderStatus));
                } elseif (in_array($ipnVariables['PAYMENTSTATUS'], $this->failedPaypalStatus)) {
                    $order->setOrderStatus(OrderStatus::get()->byID($this->CanceledOrderStatus));
                } elseif (in_array($ipnVariables['PAYMENTSTATUS'], $this->refundedPaypalStatus)) {
                    $order->setOrderStatus(OrderStatus::get()->byID($this->RefundedOrderStatus));
                } elseif (in_array($ipnVariables['PAYMENTSTATUS'], $this->pendingPaypalStatus)) {
                    $order->setOrderStatus(OrderStatus::get()->byID($this->PendingOrderStatus));
                }
            }

            $paypalOrder = PaypalOrder::get()->filter('orderId', $customVariables['order_id'])->first();
            if ($paypalOrder instanceof PaypalOrder &&
                $paypalOrder->exists()) {
                
                $paypalOrder->updateOrder(
                    $customVariables['order_id'],
                    $payerId,
                    $ipnVariables
                );
                $this->Log('processNotification', 'updated order status for #' . $customVariables['order_id']);
            } else {
                $this->Log('processNotification', 'ERROR: PaypalOrder #' . $customVariables['order_id'] . ' not found.');
            }
        } else {
            $this->Log('processNotification', 'ERROR: invalid IPN call.');
            $this->Log('processNotification', 'request data:');
            $this->Log('processNotification', var_export($_REQUEST, true));
            $this->Log('processNotification', '');
        }
    }

    /***********************************************************************************************
     ***********************************************************************************************
     **                                                                                           ** 
     **                                  Paypal handling methods                                  ** 
     **                                                                                           ** 
     ***********************************************************************************************
     **********************************************************************************************/
    
    /**
     * Clears the PayPal session data.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.04.2018
     */
    public function clearSession() {
        Tools::Session()->set(self::SESSION_KEY, null);
        Tools::saveSession();
    }

    /**
     * Returns whether self::processBeforePaymentProvider() is already processed.
     * 
     * @return bool
     */
    protected function beforePaymentProviderIsProcessed() {
        return Tools::Session()->get(self::BEFORE_PAYMENT_PROVIDER_IS_PROCESSED_SESSION_KEY);
    }
    
    /**
     * Returns whether self::processAfterPaymentProvider() is already processed.
     * 
     * @return bool
     */
    protected function afterPaymentProviderIsProcessed() {
        return Tools::Session()->get(self::AFTER_PAYMENT_PROVIDER_IS_PROCESSED_SESSION_KEY);
    }

    /**
     * Fetches a paypal token via API-call (SetExpressCheckout) which is used for
     * identification in further steps;
     * 
     * @param array $checkoutData Checkout data to inject
     *
     * @return string|boolean false
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.04.2018
     */
    public function fetchPaypalToken(array $checkoutData = []) {
        if (empty($checkoutData)) {
            $checkoutData = $this->getController()->getCheckout()->getData();
        }
        $parameters    = $this->initPaypalTokenParameters($checkoutData);
        $apiCallResult = $this->callPaypalAPI('SetExpressCheckout', $this->generateUrlParams($parameters));

        if (strtolower($apiCallResult['ACK']) != 'success' &&
            strtolower($apiCallResult['ACK']) != 'successwithwarning') {
            $this->Log('fetchPaypalToken', 'ERROR: fetching Paypal token failed.');
            $this->Log('fetchPaypalToken', ' - API call parameters:');
            $this->Log('fetchPaypalToken', var_export($parameters, true));
            $this->Log('fetchPaypalToken', ' - API call response:');
            $this->Log('fetchPaypalToken', var_export($apiCallResult, true));
            $this->Log('fetchPaypalToken', '');
            $this->errorOccured = true;
            $this->addError('Die Kommunikation mit Paypal konnte nicht initialisiert werden.');
            
            return false;
        } else {
            $this->Log('fetchPaypalToken', 'fetched paypal token ' . $apiCallResult['TOKEN']);
            $this->Log('fetchPaypalToken', ' - API call parameters:');
            $this->Log('fetchPaypalToken', var_export($parameters, true));
            $this->Log('fetchPaypalToken', ' - API call response:');
            $this->Log('fetchPaypalToken', var_export($apiCallResult, true));

            return $apiCallResult['TOKEN'];
        }
    }
    
    /**
     * Initializes the Paypal token parameters to send via API.
     * 
     * @param array $checkoutData Checkout data
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function initPaypalTokenParameters(array $checkoutData) {
        $shippingAddress = $this->getShippingAddress();
        $shoppingCart    = $this->getShoppingCart();
        $positionIndex   = 0;
        $shoppingCart->setShippingMethodID($checkoutData['ShippingMethod']);
        $shoppingCart->setPaymentMethodID($checkoutData['PaymentMethod']);
        
        if ($shippingAddress->IsPackstation) {
            $streetInfo = $shippingAddress->Packstation . ' ' . $shippingAddress->PostNumber;
        } else {
            $streetInfo = $shippingAddress->Street . ' ' . $shippingAddress->StreetNumber;
        }
        
        $parameters = [
            'ADDROVERRIDE'                          => '1',
            'VERSION'                               => '63',
            'PAYMENTREQUEST_0_AMT'                  => round((float) $shoppingCart->getAmountTotal()->getAmount(), 2),
            'PAYMENTREQUEST_0_ITEMAMT'              => round((float) $shoppingCart->getTaxableAmountGrossWithoutFees()->getAmount(), 2),
            'PAYMENTREQUEST_0_CURRENCYCODE'         => $shoppingCart->getAmountTotal()->getCurrency(),
            'PAYMENTREQUEST_0_SHIPPINGAMT'          => round((float) $shoppingCart->HandlingCostShipment()->getAmount(), 2),
            'PAYMENTREQUEST_0_HANDLINGAMT'          => round((float) $shoppingCart->HandlingCostPayment()->getAmount(), 2),
            'RETURNURL'                             => $this->getReturnLink(),
            'CANCELURL'                             => $this->getCancelLink(),
            'NOTIFYURL'                             => $this->getNotificationLink(),
            'CUSTOM'                                => '',
            'PAYMENTREQUEST_0_RETURNURL'            => $this->getReturnLink(),
            'PAYMENTREQUEST_0_CANCELURL'            => $this->getCancelLink(),
            'PAYMENTREQUEST_0_NOTIFYURL'            => $this->getNotificationLink(),
            'PAYMENTREQUEST_0_SHIPTONAME'           => $shippingAddress->FirstName . ' ' . $shippingAddress->Surname,
            'PAYMENTREQUEST_0_SHIPTOSTREET'         => $streetInfo,
            'PAYMENTREQUEST_0_SHIPTOCITY'           => $shippingAddress->City,
            'PAYMENTREQUEST_0_SHIPTOZIP'            => $shippingAddress->Postcode,
            'PAYMENTREQUEST_0_SHIPTOSTATE'          => $shippingAddress->State,
            'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'    => $shippingAddress->Country()->ISO2,
            'PAYMENTREQUEST_0_SHIPTOPHONENUM'       => $shippingAddress->Phone,
        ];
        $this->addPositionParameters($shoppingCart, $parameters, $positionIndex);
        $this->addChargesAndDiscountsForProductsParameters($shoppingCart, $parameters, $positionIndex);
        $this->addChargesAndDiscountsForTotalParameters($shoppingCart, $parameters, $positionIndex);
        $this->addTaxAmountParameters($shoppingCart, $parameters);
        return $parameters;
    }
    
    /**
     * Adds the shopping cart positions to the Paypal token parameters.
     * 
     * @param \SilverCart\Model\Order\ShoppingCart $shoppingCart   Shopping cart
     * @param array                                $parameters     Token parameters
     * @param int                                  &$positionIndex Current position index
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function addPositionParameters($shoppingCart, &$parameters, &$positionIndex) {
        foreach ($shoppingCart->getTaxableShoppingcartPositions() as $position) {
            /* @var $position \SilverCart\Model\Order\ShoppingCartPosition */
            if ($position->hasMethod('getTaxAmount')) {
                $taxAmount = round($position->getTaxAmount(), 2);
            } else {
                $taxAmount = round($position->Product()->getTaxAmount(), 2);
            }
            if ($position->hasMethod('getShortDescription')) {
                $description = substr($position->getShortDescription(), 0, 50);
            } else {
                $description = substr($position->Product()->getShortDescription(false), 0, 50);
            }
            $parameters['L_PAYMENTREQUEST_0_NAME' . $positionIndex]         = $position->Quantity.' x ' . strip_tags($position->getTitle());
            $parameters['L_PAYMENTREQUEST_0_DESC' . $positionIndex]         = $description;
            $parameters['L_PAYMENTREQUEST_0_AMT' . $positionIndex]          = round((float) $position->getPrice()->getAmount(), 2);
            $parameters['L_PAYMENTREQUEST_0_ITEMCATEGORY' . $positionIndex] = 'Physical';

            $positionIndex++;
        }
    }
    
    /**
     * Adds the charges and discounts for cart positions to the Paypal token parameters.
     * 
     * @param \SilverCart\Model\Order\ShoppingCart $shoppingCart   Shopping cart
     * @param array                                $parameters     Token parameters
     * @param int                                  &$positionIndex Current position index
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function addChargesAndDiscountsForProductsParameters($shoppingCart, &$parameters, &$positionIndex) {
        if ($shoppingCart->HasChargesAndDiscountsForProducts()) {
            $position = $shoppingCart->ChargesAndDiscountsForProducts();
            $parameters['L_PAYMENTREQUEST_0_NAME' . $positionIndex]         = $position->Name;
            $parameters['L_PAYMENTREQUEST_0_DESC' . $positionIndex]         = '';
            $parameters['L_PAYMENTREQUEST_0_AMT' . $positionIndex]          = round((float) $position->Price->getAmount(), 2);
            $parameters['L_PAYMENTREQUEST_0_ITEMCATEGORY' . $positionIndex] = 'Physical';
            $positionIndex++;
        }
    }
    
    /**
     * Adds the charges and discounts for the total amount to the Paypal token parameters.
     * 
     * @param \SilverCart\Model\Order\ShoppingCart $shoppingCart   Shopping cart
     * @param array                                $parameters     Token parameters
     * @param int                                  &$positionIndex Current position index
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function addChargesAndDiscountsForTotalParameters($shoppingCart, &$parameters, &$positionIndex) {
        if ($shoppingCart->HasChargesAndDiscountsForTotal()) {
            $position = $shoppingCart->ChargesAndDiscountsForTotal();
            $amount   = round((float) $position->Price->getAmount(), 2);
            $parameters['L_PAYMENTREQUEST_0_NAME' . $positionIndex]         = $position->Name;
            $parameters['L_PAYMENTREQUEST_0_DESC' . $positionIndex]         = '';
            $parameters['L_PAYMENTREQUEST_0_AMT' . $positionIndex]          = $amount;
            $parameters['L_PAYMENTREQUEST_0_ITEMCATEGORY' . $positionIndex] = 'Physical';
            $positionIndex++;
            // Charges and discounts for total are not possible as a single 
            // position for paypal.
            // To workaround this, the amount will be added to or reduced from
            // item amount.
            $parameters['PAYMENTREQUEST_0_ITEMAMT'] += $amount;
            $parameters['PAYMENTREQUEST_0_CUSTOM']   = '';
        }
    }
    
    /**
     * Adds the charges and discounts for the total amount to the Paypal token parameters.
     * 
     * @param \SilverCart\Model\Order\ShoppingCart $shoppingCart Shopping cart
     * @param array                                $parameters   Token parameters
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function addTaxAmountParameters($shoppingCart, &$parameters) {
        if (!Customer::currentUser()->showPricesGross()) {
            // Add taxes as a special position when the current order is displayed in net price mode.
            $taxTotalList = $shoppingCart->getTaxTotal();
            if ($taxTotalList instanceof ArrayList &&
                $taxTotalList->exists()) {
                $taxAmountTotal = 0;
                foreach ($taxTotalList as $tax) {
                    $taxAmountTotal += $tax->AmountRaw;
                }
                $parameters['PAYMENTREQUEST_0_TAXAMT'] = round((float) $taxAmountTotal, 2);
            }
        }
    }

    /**
     * Validation of the Paypal shared secret.
     * 
     * @param HTTPRequest $request Request data
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function validateSharedSecret(HTTPRequest $request) {
        $secretIsValid    = false;
        $sentSharedSecret = $request->getVar($this->sharedSecretVariableName);
        if (is_string($sentSharedSecret)) {
            $ownSharedSecret     = mb_convert_encoding($this->paypalSharedSecret, 'UTF-8');
            $encodedSharedSecret = mb_convert_encoding(urldecode($sentSharedSecret), 'UTF-8');
            
            if (mb_strstr($ownSharedSecret, $encodedSharedSecret) === $ownSharedSecret) {
                $secretIsValid = true;
            } else {
                $this->Log('validateSharedSecret', '');
                $this->Log('validateSharedSecret', 'ERROR: sent shared secret doesn\'t match with the own shared secret.');
                $this->Log('validateSharedSecret', ' - requested secret: ' . $sentSharedSecret);
                $this->Log('validateSharedSecret', ' - own secret:       ' . $ownSharedSecret);
                $this->Log('validateSharedSecret', '');
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
     * @param HTTPRequest $request Request data
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function isValidPaypalIPNCall(HTTPRequest $request) {
        $requestIsFromPaypal = false;
        $req                 = 'cmd=_notify-validate';
        $url                 = $this->getIPNTargetURL();
        
        // Combine request data to an URL encoded string
        foreach ($request->getVars() as $key => $value) {
            if (get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        // Create header string to send back to Paypal
        $header  = "POST /cgi-bin/webscr HTTP/1.1\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n";
        $header .= "Host: www.paypal.com\r\n"; 
        $header .= "Connection: close\r\n\r\n";
        
        $fp = fsockopen($url, 443, $errno, $errstr, 30);

        if (!$fp) {
            $this->Log('isValidPaypalIPNCall', '');
            $this->Log('isValidPaypalIPNCall', 'ERROR: couldn\'t connect to Paypal.');
            $this->Log('isValidPaypalIPNCall', ' - URL: ' . $url);
            $this->Log('isValidPaypalIPNCall', ' - code: ' . $errno);
            $this->Log('isValidPaypalIPNCall', ' - message: ' . $errstr);
            $this->Log('isValidPaypalIPNCall', '');
        } else {
            fputs($fp, $header . $req);

            while (!feof($fp)) {

                $res = fgets($fp, 1024);

                if (strcmp($res, "VERIFIED") == 0) {
                    // IPN call was verified and is valid
                    $requestIsFromPaypal = true;
                } else if (strcmp($res, "INVALID") == 0) {
                    // IPN call is NOT valid
                    $this->Log('isValidPaypalIPNCall', '');
                    $this->Log('isValidPaypalIPNCall', 'ERROR: payment notification was not sent by Paypal.');
                    $this->Log('isValidPaypalIPNCall', ' - Paypal response: ' . var_export($res, true));
                    $this->Log('isValidPaypalIPNCall', '');
                }
            }
            fclose($fp);
        }

        return $requestIsFromPaypal;
    }

    /**
     * returns payment and shipping information from paypal
     * 
     * @return void
     */
    public function getExpressCheckoutDetails() {

        $parameters = array(
            'TOKEN'   => $this->getPaypalToken(),
            'PAYERID' => $this->getPayerID()
        );

        $response = $this->callPaypalAPI('GetExpressCheckoutDetails', $this->generateUrlParams($parameters));

        $this->Log('getExpressCheckoutDetails','');
        $this->Log('getExpressCheckoutDetails','parameters:');
        $this->Log('getExpressCheckoutDetails', var_export($parameters, true));
        $this->Log('getExpressCheckoutDetails','response data:');
        $this->Log('getExpressCheckoutDetails', var_export($response, true));
        $this->Log('getExpressCheckoutDetails','');

        return $response;
    }

    /**
     * Finalizes the PayPal expres payment.
     * 
     * @param \SilverCart\Model\Order\Order $order        Order
     *
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.04.2018
     */
    public function doExpressCheckoutPayment(Order $order = null) {
        if (is_null($order)) {
            $order = $this->getOrder();
        }
        
        $parameters = $this->initPaypalExpressPaymentParameters($order);
        $response   = $this->callPaypalAPI('DoExpressCheckoutPayment', $this->generateUrlParams($parameters));

        if (isset($response['ORDERTIME'])) {
            ['T','Z'];[' ',''];
            $response['ORDERTIME_CUSTOM'] = str_replace(['T','Z'], [' ',''], $response['ORDERTIME']);
        } else {
            $response['ORDERTIME_CUSTOM'] = '';
        }

        $paypalOrder = new PaypalOrder();
        $paypalOrder->updateOrder($order->ID, $this->getPayerID(), $response);

        if (isset($response['PAYMENTSTATUS'])) {
            if (in_array($response['PAYMENTSTATUS'], $this->successPaypalStatus)) {
                $orderStatusID = $this->PaidOrderStatus;
            } elseif (in_array($response['PAYMENTSTATUS'], $this->failedPaypalStatus)) {
                $orderStatusID = $this->CanceledOrderStatus;
            } elseif (in_array($response['PAYMENTSTATUS'], $this->pendingPaypalStatus)) {
                $orderStatusID = $this->PendingOrderStatus;
            } elseif (in_array($response['PAYMENTSTATUS'], $this->refundedPaypalStatus)) {
                $orderStatusID = $this->RefundedOrderStatus;
            } else {
                $orderStatusID = $this->CanceledOrderStatus;
            }
        } else {
            $orderStatusID = $this->CanceledOrderStatus;
        }
        
        if (is_numeric($orderStatusID)) {
            $orderStatus = OrderStatus::get()->byID($orderStatusID);
            if ($orderStatus instanceof OrderStatus) {
                $order->setOrderStatus($orderStatus);
            }
        }

        $this->Log('doExpressCheckoutPayment', '');
        $this->Log('doExpressCheckoutPayment', 'done with status: ' . $response['ACK']);
        $this->Log('doExpressCheckoutPayment', ' - sent parameters:');
        $this->Log('doExpressCheckoutPayment', var_export($parameters, true));
        $this->Log('doExpressCheckoutPayment', ' - response:');
        $this->Log('doExpressCheckoutPayment', var_export($response, true));
        $this->Log('doExpressCheckoutPayment', '');

        if (strtolower($response['ACK']) != 'success') {
            $this->errorOccured = true;
            $this->addError($this->fieldLabel('AnErrorOccurredPaymentFailed'));
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Initializes the Paypal token parameters to send via API.
     * 
     * @param Order $order Order
     * 
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    protected function initPaypalExpressPaymentParameters(Order $order) {
        $cartAmountGross = round((float) $order->AmountTotal->getAmount(), 2);
        $itemAmountGross = round((float) $order->getPositionsPriceGross()->getAmount(), 2);
        $itemAmountNet   = round((float) $order->getPositionsPriceNet()->getAmount(), 2);
        $shippingAmount  = round((float) $order->HandlingCostShipmentAmount, 2);
        $handlingAmount  = round((float) $order->HandlingCostPaymentAmount, 2);
        $taxTotal        = $itemAmountGross - $itemAmountNet;
        $notifyUrl       = $this->getNotificationLink()
                            . '?' . $this->sharedSecretVariableName . '=' . urlencode($this->paypalSharedSecret) . '&';
        
        $this->Log('initPaypalExpressPaymentParameters', '');
        $this->Log('initPaypalExpressPaymentParameters', 'finalizing PayPal payment with these amounts:');
        $this->Log('initPaypalExpressPaymentParameters', ' - total (gross):     ' . $cartAmountGross);
        $this->Log('initPaypalExpressPaymentParameters', ' - positions (gross): ' . $itemAmountGross);
        $this->Log('initPaypalExpressPaymentParameters', ' - positions (net):   ' . $itemAmountNet);
        $this->Log('initPaypalExpressPaymentParameters', ' - shipping:          ' . $shippingAmount);
        $this->Log('initPaypalExpressPaymentParameters', ' - handling:          ' . $handlingAmount);
        $this->Log('initPaypalExpressPaymentParameters', ' - tax:               ' . $taxTotal);
        $this->Log('initPaypalExpressPaymentParameters', '');
        
        return [
            'TOKEN'         => $this->getPaypalToken(),
            'PAYERID'       => $this->getPayerID(),
            'PAYMENTACTION' => 'Sale',
            'AMT'           => $cartAmountGross,
            'ITEMAMT'       => $itemAmountNet,
            'SHIPPINGAMT'   => $shippingAmount,
            'HANDLINGAMT'   => $handlingAmount,
            'TAXAMT'        => $taxTotal,
            'DESC'          => 'Order Nr. ' . $order->OrderNumber,
            'CURRENCYCODE'  => $order->getPriceGross()->getCurrency(),
            'CUSTOM'        => 'order_id=' . $order->ID,
            'NOTIFYURL'     => Director::absoluteUrl($notifyUrl),
        ];
    }

    /**
     * Creates and returns a string ("key=value&key=value&...") from an
     * associative array.
     *
     * @param array $parameters an associative array
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>,
     *         Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.11.2015
     */
    public function generateUrlParams($parameters) {

        $paramString = '';

        foreach ($parameters as $key => $value) {
            $paramString .= '&' . urlencode($key) . '=' . urlencode($value);
        }

        return $paramString;
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function callPaypalAPI($methodName, $nvpStr) {
        $nvpreq = "METHOD=" . urlencode($methodName) .
                "&VERSION=" . urlencode($this->ApiVersion) .
                "&PWD=" . urlencode($this->ApiPassword) .
                "&USER=" . urlencode($this->ApiUsername) .
                "&SIGNATURE=" . urlencode($this->ApiSignature) .
                $nvpStr;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->NvpApiServerUrl);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        $response    = curl_exec($ch);
        $nvpResArray = $this->deformatNVP($response);

        if (curl_errno($ch)) {
            $this->Log('callPaypalAPI', 'ERROR: CURL request failed.');
            $this->Log('callPaypalAPI', ' - code: ' . curl_errno($ch));
            $this->Log('callPaypalAPI', ' - message: ' . curl_error($ch));
            $this->Log('callPaypalAPI', '');
            return false;
        }
        
        curl_close($ch);

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
     * @since 17.11.2010
     */
    protected function deformatNVP($nvpstr) {
        $intial   = 0;
        $nvpArray = [];

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
     * saves the paypal token to the session
     *
     * @param string $token Paypal token
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function saveToken($token) {
        Tools::Session()->set(self::TOKEN_SESSION_KEY, $token);
        Tools::saveSession();
    }

    /**
     * writes the PayerID to the session
     *
     * @param string $payerID Payer ID
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function savePayerID($payerID) {
        Tools::Session()->set(self::PAYERID_SESSION_KEY, $payerID);
        Tools::saveSession();
    }

}