<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Payment_Paypal
 */

/**
 * processes paypals reply
 *
 * @return void
 *
 * @package Silvercart
 * @subpackage Payment_Paypal
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 01.07.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
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
            'SilvercartPaymentPaypal',
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

                if ($orderObj) {
                    if (in_array($ipnVariables['PAYMENTSTATUS'], $paypalModule->successPaypalStatus)) {
                        $orderObj->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $paypalModule->PaidOrderStatus));
                    }
                    if (in_array($ipnVariables['PAYMENTSTATUS'], $paypalModule->failedPaypalStatus)) {
                        $orderObj->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $paypalModule->CanceledOrderStatus));
                    }
                    if (in_array($ipnVariables['PAYMENTSTATUS'], $paypalModule->refundedPaypalStatus)) {
                        $orderObj->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $paypalModule->RefundedOrderStatus));
                    }
                    if (in_array($ipnVariables['PAYMENTSTATUS'], $paypalModule->pendingPaypalStatus)) {
                        $orderObj->setOrderStatus(DataObject::get_by_id('SilvercartOrderStatus', $paypalModule->PendingOrderStatus));
                    }
                }

                //load the payment modul of the payment method
                //
                // Bestellmodul der Zahlungsart laden
                $paymentPaypalOrder = DataObject::get_one(
                    'SilvercartPaymentPaypalOrder',
                    sprintf(
                        "\"orderId\" = '%s'",
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
                    $paypalModule->Log('SilvercartPaymentPaypalNotification', 'Bestellstatus fuer Bestellung mit ID '.$customVariables['order_id'].'wurde aktualisiert.');
                } else {
                    $paypalModule->Log('SilvercartPaymentPaypalNotification', 'Das PaymentPaypalOrder Objekt konnte nicht geladen werden f√ºr die orderId '.$customVariables['order_id']);
                }
            } else {
                $paypalModule->Log('SilvercartPaymentPaypalNotification', 'Kein valider IPN-Call; Requestvariablen: '.var_export($_REQUEST, true));
            }
        }
    }
}
