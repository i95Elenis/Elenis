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

       /* $html.='<select class="messenger_sensitive has_sb" title="Delivery Timeframe" name="adj[delivery_timeframe][]" style="display: block;">
                <option value="11" "selected="selected">by 11am</option>
                <option value="14">by 2pm</option>
                <option value="17">by 5pm</option>
                </select>';
        * */
       
        $code= Mage::getModel('core/session')->getData('shipping_code'); 
        
        Mage::log('Shipping code from time: '.$code,'1','$time.log');
        if($code=='storepickupmodule')
            {
    
        $html = '<select name="'. $element->getName() . '" '.$element->serialize($element->getHtmlAttributes()).' style="width:40px">'."\n";
        for( $i = $this->_iTimeFrom; $i < $this->_iTimeTo; $i++ ) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $hours_24 = $i+1 .':00';
             $time_in_12_hour_format  = date('h a', strtotime($hours_24));
            $hours_24_1 = $i .':00';
             $hours_12 = date('h', strtotime($hours_24_1));
            $html.= '<option value="'.$hour.'" '. ( ($value_hrs == $i) ? 'selected="selected"' : '' ) .'>' . $hours_12 .'-' . $time_in_12_hour_format   . '</option>';
        }
        $html.= '</select>'."\n";
     $html.= $element->getAfterElementHtml();
        return $html;
          
        }
       
        if($code=='matrixrate'){
            $html = '<select name="'. $element->getName() . '" '.$element->serialize($element->getHtmlAttributes()).' style="width:40px">'."\n";
        $html.='<option value="9" "selected="selected">by 9am</option>
                <option value="11">by 11am</option>
                    <option value="14">by 2pm</option>
                <option value="17">by 5pm</option>';
        $html.= '</select>'."\n";
         return $html;
        }
        $html = '<select name="'. $element->getName() . '" '.$element->serialize($element->getHtmlAttributes()).' style="width:40px">'."\n";
        $html.='<option value="9" "selected="selected">by 9am</option>
                <option value="11">by 11am</option>
                    <option value="14">by 2pm</option>
                <option value="17">by 5pm</option>';
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