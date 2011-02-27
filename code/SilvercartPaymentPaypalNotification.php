<?php
/*
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
 */

/**
 * processes paypals reply
 *
 * @return void 
 *
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 23.11.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartPaymentPaypalNotification extends DataObject {
    
    /**
     * contains the modul's name
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 26.11.2010
     */
    protected $moduleName = 'Paypal';

    /**
     * This method will be called by the distributoing script and receives the
     * paypal status message
     *
     * Diese Methode wird vom Verteilerscript aufgerufen und nimmt die Status-
     * meldungen von Paypal entgegen.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 26.11.2010
     */
    public function process() {

        // load payment module
        //
        // Zahlungsmodul laden
        $paypalModule = DataObject::get_one(
            'SilvercartPayment',
            sprintf(
                "`Name` = '%s'",
                $this->moduleName
            )
        );

        if ($paypalModule) {
            // security level 1:
            //
            // Sicherheitsstufe 1:
            // ----------------------------------------------------------------------------
            // checks the shared secret
            //
            // Ueberpruefen, ob das Shared Secret korrekt uebergeben wurde.
            if ($paypalModule->validateSharedSecret() === false) {
                $paypalModule->Log('SilvercartPaymentPaypalNotification', 'Falsches Shared Secret gesendet! Abbruch.');
                $paypalModule->Log('SilvercartPaymentPaypalNotification', var_export($_REQUEST, true));
                exit();
            }

            // security level 2:
            // Sicherheitsstufe 2:
            // ----------------------------------------------------------------------------
            // send confirmation to paypal and receive paypal's reply; This way we make shure that
            // the answer came ffrom paypal
            //
            // Bestaetigung an Paypal schicken und Antwort entgegennehmen. So wird sicher-
            // gestellt, dass die Nachricht tatsaechlich von Paypal kam.
            if ($paypalModule->isValidPaypalIPNCall()) {
                $payerId            = $paypalModule->getPayerId();
                $ipnVariables       = $paypalModule->getIpnRequestVariables();
                $customVariables    = $paypalModule->getIpnCustomVariables();

                $paypalModule->Log('SilvercartPaymentPaypalNotification', 'Postback-Pruefung: Zahlungsbestaetigung kam von Paypal.');

                //set the order's status to payed if the payment is received
                //the delivery address will be set/adjusted
                //
                // Wenn die Bestellung bezahlt ist, dann den Status in der Stamm-
                // bestelltabelle umstellen auf "Bezahlt".
                // Ausserdem wird die Lieferadresse angepasst, wenn die entsprechenden
                // Daten geliefert wurden
                $orderObj = DataObject::get_by_id(
                    'SilvercartOrder',
                    $customVariables['order_id']
                );

                if (in_array($ipnVariables['PAYMENTSTATUS'], $paypalModule->successPaypalStatus)) {
                    $orderObj->setOrderStatus(DataObject::get_by_id('OrderStatus', $paypalModule->PaidOrderStatus));
                }
                if (in_array($ipnVariables['PAYMENTSTATUS'], $paypalModule->failedPaypalStatus)) {
                    $orderObj->setOrderStatus(DataObject::get_by_id('OrderStatus', $paypalModule->CanceledOrderStatus));
                }
                if (in_array($ipnVariables['PAYMENTSTATUS'], $paypalModule->refundedPaypalStatus)) {
                    $orderObj->setOrderStatus(DataObject::get_by_id('OrderStatus', $paypalModule->RefundedOrderStatus));
                }
                if (in_array($ipnVariables['PAYMENTSTATUS'], $paypalModule->pendingPaypalStatus)) {
                    $orderObj->setOrderStatus(DataObject::get_by_id('OrderStatus', $paypalModule->PendingOrderStatus));
                }                                                                                                                                                                                                                                                                                                       

                //load the payment modul of the payment method
                //
                // Bestellmodul der Zahlungsart laden
                $paymentPaypalOrder = DataObject::get_one(
                    'SilvercartPaymentPaypalOrder',
                    sprintf(
                        "\"orderId\" = '%d'",
                        $customVariables['order_id']
                    )
                );

                if ($paymentPaypalOrder) {
                    //save paypal's data
                    //
                    // Von Paypal gelieferte Daten speichern
                    $paymentPaypalOrder->updateOrder(
                        $customVariables['order_id'],
                        $payerId,
                        $ipnVariables
                    );
                } else {
                    $paypalModule->Log('SilvercartPaymentPaypalNotification', 'Das PaymentPaypalOrder Objekt konnte nicht geladen werden für die orderId '.$customVariables['order_id']);
                }
            } else {
                $paypalModule->Log('SilvercartPaymentPaypalNotification', 'Kein valider IPN-Call; Requestvariablen: '.var_export($_REQUEST, true));
            }
        }
    }
}
