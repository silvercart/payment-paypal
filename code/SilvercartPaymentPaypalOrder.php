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
 * additional information for orders via paypal
 *
 * @package Silvercart
 * @subpackage Payment
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 24.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentPaypalOrder extends DataObject {

    /**
     * attribute definition
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public static $db = array(
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
        'payerStatus'       => 'Varchar(255)'
    );

    /**
     * register extensions for $this
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 25.11.2010
     */
    static $extensions = array(
        "Versioned('Live')",
    );

    /**
     * update an order
     *
     * @param int   $orderId orders ID
     * @param int   $payerId Paypal PayerId
     * @param array $data    array with order data
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function updateOrder($orderId, $payerId, $data) {
        $this->setField('orderId', $orderId);
        $this->setField('payerId', $payerId);

        if (isset($data['TRANSACTIONID'])) {
            $this->setField('transactionId', $data['TRANSACTIONID']);
        }
        if (isset($data['TRANSACTIONTYPE'])) {
            $this->setField('transactionType', $data['TRANSACTIONTYPE']);
        }
        if (isset($data['PAYMENTTYPE'])) {
            $this->setField('paymentType', $data['PAYMENTTYPE']);
        }
        if (isset($data['PAYMENTSTATUS'])) {
            $this->setField('paymentStatus', $data['PAYMENTSTATUS']);
        }
        if (isset($data['PENDINGREASON'])) {
            $this->setField('pendingReason', $data['PENDINGREASON']);
        }
        if (isset($data['REASONCODE'])) {
            $this->setField('reasonCode', $data['REASONCODE']);
        }
        if (isset($data['ORDERTIME_CUSTOM'])) {
            $this->setField('orderTime', $data['ORDERTIME_CUSTOM']);
        }
        if (isset($data['CURRENCYCODE'])) {
            $this->setField('currencyCode', $data['CURRENCYCODE']);
        }
        if (isset($data['AMT'])) {
            $this->setField('amt', $data['AMT']);
        }
        if (isset($data['FEEAMT'])) {
           $this->setField('feeAmt', $data['FEEAMT']);
        }
        if (isset($data['TAXAMT'])) {
           $this->setField('taxAmt', $data['TAXAMT']);
        }
        if (isset($data['SHIPPINGAMT'])) {
           $this->setField('shippingAmt', $data['SHIPPINGAMT']);
        }
        if (isset($data['SHIPTONAME'])) {
           $this->setField('shipToName', $data['SHIPTONAME']);
        }
        if (isset($data['SHIPTOADDRESS'])) {
           $this->setField('shipToStreet', $data['SHIPTOADDRESS']);
        }
        if (isset($data['SHIPTOZIP'])) {
            $this->setField('shipToZip', $data['SHIPTOZIP']);
        }
        if (isset($data['SHIPTOCITY'])) {
            $this->setField('shipToCity', $data['SHIPTOCITY']);
        }
        if (isset($data['SHIPTOSTATE'])) {
            $this->setField('shipToState', $data['SHIPTOSTATE']);
        }
        if (isset($data['SHIPTOCOUNTRYNAME'])) {
            $this->setField('shipToCountry', $data['SHIPTOCOUNTRYNAME']);
        }
        if (isset($data['SHIPTOCOUNTRYCODE'])) {
            $this->setField('shipToCountryCode', $data['SHIPTOCOUNTRYCODE']);
        }
        if (isset($data['ADDRESSSTATUS'])) {
            $this->setField('addressStatus', $data['ADDRESSSTATUS']);
        }
        if (isset($data['PAYERSTATUS'])) {
            $this->setField('payerStatus', $data['PAYERSTATUS']);
        }
        $this->write();
    }
}
