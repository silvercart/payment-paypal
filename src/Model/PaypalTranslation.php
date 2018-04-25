<?php

namespace SilverCart\Paypal\Model;

use SilverCart\Model\Payment\PaymentMethodTranslation;
use SilverCart\Paypal\Model\Paypal;

/**
 * Translations for the multilingual attributes of Paypal.
 *
 * @package SilverCart
 * @subpackage Paypal_Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 24.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class PaypalTranslation extends PaymentMethodTranslation {
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    private static $has_one = [
        'Paypal' => Paypal::class,
    ];

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartPaymentPaypalTranslation';
    
}

