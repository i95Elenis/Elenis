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
class AdjustWare_Deliverydate_Model_Mysql4_Setup extends Mage_Sales_Model_Mysql4_Setup
{
    /**
     * Add or update attribute in FLAT tables. Code simmilar to addAttribute function
     *
     * @param int|string $entityTypeId
     * @param string $code
     * @param mixed $attr
     * @return Mage_Sales_Model_Resource_Setup
     */
    public function replaceFlatAttribute($entityTypeId, $code, $attr)
    {
        //checking that attributes are inside flat table
        if (isset($this->_flatEntityTables[$entityTypeId]) && $this->_flatTableExist($this->_flatEntityTables[$entityTypeId]))
        {
            $this->_replaceFlatAttribute($this->_flatEntityTables[$entityTypeId], $code, $attr);
            $this->_replaceGridAttribute($this->_flatEntityTables[$entityTypeId], $code, $attr, $entityTypeId);
        }
        return $this;
    }

    /**
     * @param string $table
     * @param string $attribute
     * @param array $attr
     * @return Mage_Sales_Model_Resource_Setup
     */
    protected function _replaceFlatAttribute($table, $attribute, $attr)
    {
        $table = $this->getTable($table);
        if(!$table) {
            return $this;
        }
        $tableInfo = $this->getConnection()->describeTable($table);
        if (isset($tableInfo[$attribute])) {
            $this->getConnection()->changeColumn($table, $attribute, $attribute, $attr);
        } else {
            if(is_array($attr)) {
                $attr = $this->_getAttributeColumnDefinition($attribute, $attr);
            }
            $this->getConnection()->addColumn($table, $attribute, $attr);
        }
        return $this;
    }
    /**
     * @param string $table
     * @param string $attribute
     * @param array $attr
     * @param string $entityTypeId
     * @return Mage_Sales_Model_Resource_Setup
     */
    protected function _replaceGridAttribute($table, $attribute, $attr, $entityTypeId)
    {
        if (in_array($entityTypeId, $this->_flatEntitiesGrid) && !empty($attr['grid'])) {
            $this->_replaceFlatAttribute($table . '_grid', $attribute, $attr);
            // Change column don't change salesarchive/*_grid tables. Updating them by force
            $this->_replaceFlatAttribute('enterprise_salesarchive/'.$entityTypeId.'_grid', $attribute, $attr);
        }
        return $this;
    }

}