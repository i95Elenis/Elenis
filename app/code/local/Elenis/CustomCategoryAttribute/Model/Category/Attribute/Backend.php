<?php

/**
 *
 *
 * @category       
 * @package        Elenis_CustomCategoryAttribute
 * @Description    
 * @author         
 * @copyright      
 * @license        
 */
class Elenis_CustomCategoryAttribute_Model_Category_Attribute_Backend extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     *  Options for getting all the sku in dropdown
     * @return array $options
     */
    public function getAllOptions() {
        $allProducts = Mage::getModel('catalog/product')->getCollection();
        $allProducts->addAttributeToSelect('*');
        $listSku=array();
        //echo "<pre>";print_r($allProducts->getData());exit;
        if (is_null($this->_options)) {
           
              $listSku[]=  array(
                    'label' => Mage::helper('Elenis_CustomCategoryAttribute')->__('Select Sku'),
                    'value' => ''
                );
           
            
            foreach ($allProducts->getData() as $productData) {
                            
                   $listSku[]= array('value' => $productData['entity_id'], 'label' => $productData['sku']);
               
            }
            $this->_options = $listSku;
            
        }
       // echo "<pre>";print_r($this->_options);exit;
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

}