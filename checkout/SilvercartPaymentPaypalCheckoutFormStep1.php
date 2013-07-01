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
 * CheckoutProcessPaymentBeforeOrder
 *
 * @package Silvercart
 * @subpackage Forms_Checkout
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 01.07.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartPaymentPaypalCheckoutFormStep1 extends SilvercartCheckoutFormStepPaymentInit {

    /**
     * Here we set some preferences.
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 07.04.2011
     */
    public function preferences() {
        parent::preferences();

        $this->preferences['stepTitle']     = _t('SilvercartPaymentPaypal.ENTERDATAATPAYPAL');
        $this->preferences['stepIsVisible'] = true;
    }

    /**
     * Process the current step
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>,
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.02.2013
     */
    public function process() {
        if (parent::process()) {
            $member         = Member::currentUser();
            $checkoutData   = $this->controller->getCombinedStepData();

            $this->paymentMethodObj->setCancelLink(Director::absoluteURL($this->controller->Link()) . 'GotoStep/4');
            $this->paymentMethodObj->setReturnLink(Director::absoluteURL($this->controller->Link()));

            $this->paymentMethodObj->processPaymentBeforeOrder();
        }
    }
}