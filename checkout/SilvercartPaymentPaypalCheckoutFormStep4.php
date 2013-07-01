<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 */

/**
 * CheckoutProcessPaymentAfterOrder
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 01.07.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartPaymentPaypalCheckoutFormStep4 extends SilvercartCheckoutFormStepPaymentInit {

    /**
     * Process the current step
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.04.2011
     */
    public function process() {
        if (parent::process()) {
            $paymentSuccessful  = false;
            $checkoutData       = $this->controller->getCombinedStepData();
            $orderObj           = DataObject::get_by_id(
                'SilvercartOrder',
                $checkoutData['orderId']
            );

            if ($this->paymentMethodObj &&
                $orderObj) {
                $this->paymentMethodObj->setOrder($orderObj);
                $paymentSuccessful = $this->paymentMethodObj->processPaymentAfterOrder();
            }

            if ($paymentSuccessful) {
                $this->controller->addCompletedStep();
                $this->controller->NextStep();
            } else {
                return $this->renderError();
            }
        }
    }
}