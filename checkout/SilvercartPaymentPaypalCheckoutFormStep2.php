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
 * CheckoutReturnFromPaymentProviderPage
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 01.07.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartPaymentPaypalCheckoutFormStep2 extends SilvercartCheckoutFormStepPaymentInit {

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
            $this->paymentMethodObj->processReturnJumpFromPaymentProvider();
        }
    }
}