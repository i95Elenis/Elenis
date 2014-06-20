<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/9/13
 * Time   : 12:31 PM
 * File   : CreditCards.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Model_System_Config_CreditCards
{
    public function toOptionArray()
    {
        $options =  array();

        foreach (Mage::getSingleton('ebizmarts_sagepaymentspro/config')->getCcTypesSagePayments() as $code => $name) {
            $options[] = array(
                'value' => $code,
                'label' => $name
            );
        }

        return $options;
    }
}