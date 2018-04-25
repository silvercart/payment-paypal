<?php

namespace SilverCart\Paypal\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

/**
 * additional information for orders via paypal
 *
 * @package SilverCart
 * @subpackage Paypal_Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 24.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PaypalOrder extends DataObject {

    /**
     * DB attributes
     * 
     * @var array
     */
    private static $db = [
        'orderId'           => 'Int',
        'payerId'           => 'Varchar(50)',
        'transactionId'     => 'Varchar(50)',
        'transactionType'   => 'Varchar(50)',
        'paymentType'       => 'Varchar(50)',
        'paymentStatus'     => 'Varchar(50)',
        'pendingReason'     => 'Varchar(255)',
        'reasonCode'        => 'Varchar(255)',
        'orderTime'         => 'Varchar(255)',
        'currencyCode'      => 'Varchar(10)',
        'amt'               => 'Float',
        'feeAmt'            => 'Float',
        'taxAmt'            => 'Float',
        'shippingAmt'       => 'Float',
        'shipToName'        => 'Varchar(255)',
        'shipToStreet'      => 'Varchar(255)',
        'shipToZip'         => 'Varchar(255)',
        'shipToCity'        => 'Varchar(255)',
        'shipToState'       => 'Varchar(255)',
        'shipToCountry'     => 'Varchar(255)',
        'shipToCountryCode' => 'Varchar(255)',
        'addressStatus'     => 'Varchar(255)',
        'payerStatus'       => 'Varchar(255)',
    ];

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentPaypalOrder';
    
    /**
     * register extensions
     *
     * @var array
     */
    private static $extensions = [
        Versioned::class . '.versioned',
    ];

    /**
     * update an order
     *
     * @param int   $orderId orders ID
     * @param int   $payerId Paypal PayerId
     * @param array $data    array with order data
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.04.2018
     */
    public function updateOrder($orderId, $payerId, $data) {
        $this->orderId = $orderId;
        $this->payerId = $payerId;
        $dbFields = $this->config()->get('db');
        foreach ($dbFields as $dbFieldName => $dbFieldType) {
            $dataKey = strtoupper($dbFieldName);
            if (array_key_exists($dataKey, $data)) {
                $this->setField($dbFieldName, $data[$dataKey]);
            } elseif (array_key_exists($dataKey . '_CUSTOM', $data)) {
                $this->setField($dbFieldName, $data[$dataKey . '_CUSTOM']);
            } elseif (array_key_exists($dataKey . 'NAME', $data)) {
                $this->setField($dbFieldName, $data[$dataKey . 'NAME']);
            }
        }
        if (array_key_exists('SHIPTOADDRESS', $data)) {
            $this->setField('shipToStreet', $data['SHIPTOADDRESS']);
        }
        $this->write();
    }
}