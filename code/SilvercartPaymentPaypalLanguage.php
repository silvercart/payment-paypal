<?php
/**
 * Copyright 2014 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Payment_Paypal
 */

/**
 * carries multilingual attributes for SilvercartPaymentPaypal
 *
 * @package Silvercart
 * @subpackage Payment_Paypal
 * @author Roland Lehmann <rlehmann@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 01.07.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartPaymentPaypalLanguage extends SilvercartPaymentMethodLanguage {
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartPaymentPaypal' => 'SilvercartPaymentPaypal'
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2014
     */
    public function singular_name() {
        SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.04.2014
     */
    public function plural_name() {
        SilvercartTools::plural_name_for($this);
    }
    
}

