<?php

class Webshopapps_Timegrid_Model_Timegrid extends Mage_Core_Model_Abstract
{

	static protected $_timegridGroups;
	
    public function _construct()
    {
        parent::_construct();

        $this->_init('timegrid/timegrid');
        $this->setIdFieldName('timegrid_id');
    }


     /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        $options = array();
        foreach(self::getTimegridGroups() as $timegridId=>$timegridGroup) {
            $options[$timegridId] = $timegridGroup['title'];
        }
        return $options;
    }
    
 
    
	public function toOptionArray()
    {
        $arr = array();
        foreach(self::getTimegridGroups() as $timegridId=>$timegridGroup) {
        	$arr[] = array('value'=>$timegridId, 'label'=>$timegridGroup['title']);
        }
        return $arr;
    }

    static public function getTimegridGroups()
    {
        if (is_null(self::$_timegridGroups)) {
            self::$_timegridGroups = Mage::getModel('timegrid/timegrid')->getCollection();
        }

        return self::$_timegridGroups;
    }


/**
     * Retireve all options
     *
     * @return array
     */
    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value'=>'', 'label'=> Mage::helper('catalog')->__('-- Please Select --'));
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

 /**
     * Retrieve option text
     *
     * @param int $optionId
     * @return string
     */
    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

   
 
     /**
     * Get Column(s) names for flat data building
     *
     * @return array
     */
    public function getFlatColums()
    {
        $columns = array();
        $columns[$this->getAttribute()->getAttributeCode()] = array(
            'type'      => 'int',
            'unsigned'  => false,
            'is_null'   => true,
            'default'   => null,
            'extra'     => null
        );
        return $columns;
   }

    /**
     * Retrieve Select for update Attribute value in flat table
     *
     * @param   int $store
     * @return  Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
    

}