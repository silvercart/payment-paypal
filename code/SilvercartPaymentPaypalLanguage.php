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
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 31.01.2012
     */
    public function singular_name() {
        if (_t('SilvercartPaymentPaypalLanguage.SINGULARNAME')) {
            return _t('SilvercartPaymentPaypalLanguage.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 31.01.2012
     */
    public function plural_name() {
        if (_t('SilvercartPaymentPaypalLanguage.PLURALNAME')) {
            return _t('SilvercartPaymentPaypalLanguage.PLURALNAME');
        } else {
            return parent::plural_name();
        }

    }
    
}

