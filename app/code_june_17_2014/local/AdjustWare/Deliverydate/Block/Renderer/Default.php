<?php
/**
 * Delivery Date
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Deliverydate
 * @version      10.1.5
 * @license:     5WLwzjinYV1BwwOYUOiHBcz0D7SjutGH8xWy5nN0br
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Deliverydate_Block_Renderer_Default extends Mage_Core_Block_Template 
implements Varien_Data_Form_Element_Renderer_Interface
{    
    public function render(Varien_Data_Form_Element_Abstract $element){
        return $element->getLabelHtml() . '<br />' . $element->getElementHtml();
    }
}