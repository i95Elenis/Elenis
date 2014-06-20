<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 5/9/13
 * Time   : 10:18 AM
 * File   : Hint.php
 * Module : Ebizmarts_SagePaymentsPro
 */
class Ebizmarts_SagePaymentsPro_Block_Adminhtml_System_Config_Fieldset_Hint     extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_template = 'ebizmarts/sagepaymentspro/system/config/fieldset/hint.phtml';

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return (string) Mage::getConfig()->getNode('modules/Ebizmarts_SagePaymentsPro/version');
    }

    /**
     * @return string
     */
    public function getPxParams() {
        $v = (string)Mage::getConfig()->getNode('modules/Ebizmarts_SagePaymentsPro/version');
        $ext = "SagePaymentsPro;{$v}";

        $modulesArray = (array)Mage::getConfig()->getNode('modules')->children();
        $aux = (array_key_exists('Enterprise_Enterprise', $modulesArray))? 'EE' : 'CE' ;
        $mageVersion = Mage::getVersion();
        $mage = "Magento {$aux};{$mageVersion}";

        $hash = md5($ext . '_' . $mage . '_' . $ext);

        return "ext=$ext&mage={$mage}&ctrl={$hash}";

    }

    /**
     * @return mixed
     */
    public function verify()
    {
        return Mage::helper('ebizmarts_sagepaymentspro')->verify();
    }

}