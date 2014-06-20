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
class AdjustWare_Deliverydate_Block_Renderer_Time extends Mage_Core_Block_Template 
implements Varien_Data_Form_Element_Renderer_Interface
{    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_iTimeFrom  = (int)Mage::getStoreConfig('checkout/adjdeliverydate/time_enable_from');
        $this->_iTimeTo    = (int)Mage::getStoreConfig('checkout/adjdeliverydate/time_enable_to');
        
        if ($this->_iTimeTo AND $this->_iTimeTo >= $this->_iTimeFrom)
        {
            if ($this->_iTimeFrom < 0 OR $this->_iTimeFrom > 24)
            {
                $this->_iTimeFrom = 0;
            }
            
            if ($this->_iTimeTo > 24)
            {
                $this->_iTimeTo = 24;
            }
        }
        else 
        {
            return false;
        }
        
        return $element->getLabelHtml() . '<br />' . $this->_getElementHtml($element);
    }
    
    protected $_iTimeFrom;
    protected $_iTimeTo;
    
    private function _getElementHtml($element)
    {
        $element->addClass('select');

        $value_hrs = 0;
        $value_min = 0;
        $value_sec = 0;

        if( $value = $element->getValue() ) 
        {
            if (!is_array($value))
            {
                $values = explode(',', $value);
#                d($value, 1);
            }
            else 
            {
                $values = $value;
            }
            
//            if( is_array($values) && count($values) == 3 ) {
            if( is_array($values)) {
                $value_hrs = $values[0];
                $value_min = $values[1];
#                $value_sec = $values[2];
            }
        }

        $html = '<select name="'. $element->getName() . '" '.$element->serialize($element->getHtmlAttributes()).' style="width:40px">'."\n";
        for( $i = $this->_iTimeFrom; $i < $this->_iTimeTo; $i++ ) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html.= '<option value="'.$hour.'" '. ( ($value_hrs == $i) ? 'selected="selected"' : '' ) .'>' . $hour . '</option>';
        }
        $html.= '</select>'."\n";

        $html.= '&nbsp;:&nbsp;<select name="'. $element->getName() . '" '.$element->serialize($element->getHtmlAttributes()).' style="width:40px">'."\n";
        for( $i=0;$i<60;$i++ ) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html.= '<option value="'.$hour.'" '. ( ($value_min == $i) ? 'selected="selected"' : '' ) .'>' . $hour . '</option>';
        }
        $html.= '</select>'."\n";
/*
        $html.= '&nbsp;:&nbsp;<select name="'. $element->getName() . '" '.$element->serialize($element->getHtmlAttributes()).' style="width:40px">'."\n";
        for( $i=0;$i<60;$i++ ) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html.= '<option value="'.$hour.'" '. ( ($value_sec == $i) ? 'selected="selected"' : '' ) .'>' . $hour . '</option>';
        }
        $html.= '</select>'."\n";
        
        */
        
        $html.= $element->getAfterElementHtml();
        return $html;
    }
    
    
}