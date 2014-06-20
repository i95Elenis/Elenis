<?php

class Elenis_CustomCheckoutMultipleAddress_Model_Multiship extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init("customcheckoutmultipleaddress/multiship");
    }

    public function quoteAddressItemsListByConditionColumns($condition) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('sales/quote_address_item');
        $query = "SELECT * FROM {$table} where  quote_item_id=" . (int) $condition . ";";
        // echo $query."<br/>";
        return $readConnection->fetchAll($query);
    }

    public function quoteItemsListByConditionColumns($quoteId, $storeId, $prodId) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("sales/quote_item");

        $query = "SELECT item_id FROM {$table} where  quote_id=" . (int) $quoteId . " AND store_id=" . (int) $storeId . " AND product_id=" . (int) $prodId . " and qty>=1;";

        return $readConnection->fetchOne($query);
    }

    public function loadByColumns($condition) {

        $collection = $this->getCollection();


        foreach ($condition as $key => $values) {
            $collection->addFieldToFilter($key, $values);
        }
        return $collection;
    }

    public function updateCheckMulti($id) {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName("sales/quote_address_item");
        //echo $qty.$quoteId;exit;
        $query = "update   {$table} set check_multiple=1 WHERE address_item_id = " . (int) $id;

        $writeConnection->query($query);
    }

    public function deleteRecord($id) {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName("customcheckoutmultipleaddress/multiship");
        //echo $qty.$quoteId;exit;
        $query = "delete  from {$table}  WHERE id = " . (int) $id;

        $writeConnection->query($query);
    }

    public function loadSplitNumber($one, $two, $three) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("customcheckoutmultipleaddress/multiship");

        $query = "SELECT MAX(number_splits) FROM {$table} where  customer_id=" . (int) $one . " AND product_id=" . (int) $two . " AND quote_id=" . (int) $three . " and qty>=1 ;";

        return $readConnection->fetchOne($query);
    }

    public function loadId($one, $two, $three) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("customcheckoutmultipleaddress/multiship");

        $query = "SELECT id,number_splits,qty FROM {$table} where  customer_id=" . (int) $one . " AND product_id=" . (int) $two . " AND quote_id=" . (int) $three . " and qty>=1;";

        return $readConnection->fetchAll($query);
    }

    public function loadAddressItemByQuoteItemId($quoteItemId) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("sales/quote_address_item");

        // $query = "SELECT address_item_id,quote_item_id FROM {$table} WHERE quote_address_id=".(int)$quoteItemId;
        $query = "SELECT address_item_id,quote_address_id  FROM {$table} WHERE quote_item_id=" . (int) $quoteItemId;
        // echo $query."<br/>";
        return $readConnection->fetchAll($query);
    }

    public function loadAddressIdByQuoteAddressItemId($quoteItemId) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("sales/quote_address_item");

        // $query = "SELECT address_item_id,quote_item_id FROM {$table} WHERE quote_address_id=".(int)$quoteItemId;
        $query = "SELECT quote_address_id  FROM {$table} WHERE address_item_id=" . (int) $quoteItemId;
        // echo $query."<br/>";
        return $readConnection->fetchOne($query);
    }

    public function loadById($id) {
        $collection = $this->getCollection()
                ->addFieldToFilter('id', $id);

        return $collection->getData();
    }

    public function multidimensionalArrayToSingleDimensionArray($array) {
        if (!$array)
            return false;
        $flat = array();
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));

        foreach ($iterator as $value)
            $singhal[] = $value;
        return $singhal;
    }

    public function updateQuanties($fields, $condition, $modelTable) {

        $updateValues = "";
        $updateCondition = "";
        $query = "";
        foreach ($fields as $key => $values) {
            $updateValues.=$key . "=" . $values . ",";
        }
        foreach ($condition as $conKey => $conValue) {
            $updateCondition.=$conKey . "=" . $conValue;
        }
        $updateValues = trim($updateValues, ",");
        $updateCondition = trim($updateCondition, ",");

        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName($modelTable);

        $query = "update  {$table} set {$updateValues} where {$updateCondition} ;";
         //echo $query."<br/>";
        $writeConnection->query($query);
    }

    public function loadQuoteAddressId($addressId, $quoteId, $customerId) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("sales/quote_address");

        $query = "SELECT address_id FROM {$table} where  address_type='shipping'  AND customer_address_id={$addressId} and quote_id={$quoteId} and customer_id={$customerId} order by address_id desc limit 1 ";
        //  echo $query."<br/>";
        return $readConnection->fetchOne($query);
    }

    public function countQuoteAddressIds($addressId, $quoteId, $customerId) {
        // echo $addressId.$quoteId.$customerId;exit;
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("sales/quote_address");
        $query = "SELECT count(*) FROM {$table} where  address_type='shipping'  AND customer_address_id={$addressId} and quote_id={$quoteId} and customer_id={$customerId}";
        // echo $query."<br/>";
        return $readConnection->fetchOne($query);
    }

    public function getMultishipData($one, $two) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("customcheckoutmultipleaddress/multiship");

        $query = "SELECT id,address_id,qty,product_id FROM {$table} where  customer_id=" . (int) $two . "  AND quote_id=" . (int) $one . " ;";
        //echo $query."<br/>";
        return $readConnection->fetchAll($query);
    }

    public function importCustomerAddressItems($quoteId, $custId, $qty, $custAddressId) {
        $multiShipData = $this->getMultishipData($quoteId, $custId);
        //echo "<pre>";print_r($multiShipData);exit;
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quoteId = $quote->getId();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $addressId = Mage::getModel("customcheckoutmultipleaddress/multiship")->loadQuoteAddressId($custAddressId, $quoteId, $customerId);

        $mapId = Mage::getModel("customcheckoutmultipleaddress/multiship")->loadQuoteAddressItemByAddressItemId($addressId);

        //$quote = Mage::getModel('sales/quote')->load($quoteId);
        /* foreach ($quote->getAllItems() as $item) {
          $product[] = $item->getProduct()->getId();
          }
         */
        //echo "<pre>";print_r($product);exit;
        //echo "<pre>";print_r($multiShipData);exit;
        $quoteAddressItem = Mage::getModel('sales/quote_address_item');
        $multiShipModel = Mage::getModel("customcheckoutmultipleaddress/multiship");

        $quoteAddressItem->load($mapId);
        // echo "<pre>";print_r($quoteAddressItem->getData());exit;

        foreach ($multiShipData as $multiShip) {
            // $quoteAddressItem->load($multiShip['address_id']);
            $this->insertQuoteAddressItem($quoteAddressItem, $multiShip['product_id'], $qty);
            // echo "<pre>";print_R($quoteAddressItem->getData());
            // $this->insertQuoteAddressItem($quoteAddressItem);



            /* $quoteAddressItem->load($multiShip['address_id']);
              $multiShipModel->load($multiShip['id']);
              // Mage::helper('core')->copyFieldset('sales_quote_address_item', 'to_quote_address_item', $quoteAddressItem, $quoteAddressItem);
              if ($multiShip['id'] == $multiShipModel->getId() && in_array($multiShip['product_id'], $product)) {

              $multiShipModel->setQty($qty)->setId($multiShipModel->getId())->save();
              }
              // echo "<pre>";print_r($multiShipModel->getData());exit;
             */
        }
        // exit;
    }

    public function getCustomerAddressId($id) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $quoteAddressTable = $resource->getTableName("sales/quote_address_item");


        // $query = "SELECT address_item_id,quote_item_id FROM {$table} WHERE quote_address_id=".(int)$quoteItemId;
        //$query = "SELECT quote_address_id  FROM {$table} WHERE quote_address_id=" . (int) $customerId;
        $query = "select quote_address_id from {$quoteAddressTable} where address_item_id=" . (int) $id;
        // echo $query."<br/>";
        return $readConnection->fetchOne($query);
    }

    public function loadQuoteAddressItemByCustomerId($customerId) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("sales/quote_address_item");

        // $query = "SELECT address_item_id,quote_item_id FROM {$table} WHERE quote_address_id=".(int)$quoteItemId;
        $query = "SELECT quote_address_id  FROM {$table} WHERE quote_address_id=" . (int) $customerId;
        // echo $query."<br/>";
        return $readConnection->fetchOne($query);
    }

    public function loadQuoteAddressItemByAddressItemId($addressId) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName("sales/quote_address_item");

        // $query = "SELECT address_item_id,quote_item_id FROM {$table} WHERE quote_address_id=".(int)$quoteItemId;
        $query = "SELECT address_item_id  FROM {$table} WHERE quote_address_id=" . (int) $addressId;
        //  echo $query."<br/>";
        return $readConnection->fetchOne($query);
    }

    public function insertQuoteAddressItem($quoteAddressItem, $prodId, $qty) {

        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName("sales/quote_address_item");
        $writeConnection->beginTransaction();
        $fields = "(";
        // $i = 0;
        $values = "('";
        $tempArr = array();

        foreach ($quoteAddressItem->getData() as $key => $value) {

            if ($key != 'address_item_id' && $key != 'created_at' && $key != 'updated_at') {
                $fields.=$key . ",";
                if ($key != 'qty' && $key != 'parent_item_id') {
                    $values.= $value . "','";
                }
                if ($key == 'parent_item_id') {
                    $values .= $quoteAddressItem->getId() . "','";
                }
                //  $temp[]=$value;
                if ($key == 'product_id') {
                    $tempArr = $value;
                    //  $temp[]=$value;
                }
                if ($key == 'qty') {
                    $values.=$qty . "','";
                }
            }


            // $i++;
            //echo "<pre>";print_r($key);print_r($value);
        }
        $fields = trim($fields, ",");
        $fields.=")";
        $values = substr($values, 0, -2);
        $values.=")";

        // echo $fields.$values;exit;
        //  echo $fields;echo "<pre>";print_r($values);exit;
        //  echo $tempArr.$prodId;echo "<pre>";print_r($temp);exit;
        if ($tempArr == $prodId) {
            $sql = "REPLACE  INTO {$table} {$fields} VALUES {$values} ";

            //echo $sql;exit;
            $writeConnection->query($sql);
        }



        $writeConnection->commit();
    }

    /* public function loadMultshipData($one, $three) {

      $resource = Mage::getSingleton('core/resource');
      $readConnection = $resource->getConnection('core_read');
      $table = $resource->getTableName("customcheckoutmultipleaddress/multiship");

      $query = "SELECT id,product_id FROM {$table} where  customer_id=" . (int) $one . "  AND quote_id=" . (int) $three . " and qty>1;";
      echo $query."<br/>";
      return $readConnection->fetchAll($query);
      }
     */

    public function insertQuoteItem($quoteItem, $prodId, $qty) {

        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $table = $resource->getTableName("sales/quote_item");
        $writeConnection->beginTransaction();
        $fields = "(";
        // $i = 0;
        $values = "('";
        $tempArr = array();
        // $values .= $quoteAddressItem->getId();
        foreach ($quoteItem->getData() as $key => $value) {

            if ($key != 'item_id' && $key != 'created_at' && $key != 'updated_at') {
                $fields.=$key . ",";
                if ($key != 'qty' && $key != 'parent_item_id') {
                    $values.= $value . "','";
                }
                if ($key == 'parent_item_id') {
                    $values .= $quoteItem->getId() . "','";
                }
                //  $temp[]=$value;
                if ($key == 'product_id') {
                    $tempArr = $value;
                    //  $temp[]=$value;
                }

                if ($key == 'qty') {
                    $values.=$qty . "','";
                }
            }


            // $i++;
            //echo "<pre>";print_r($key);print_r($value);
        }
        $fields = trim($fields, ",");
        $fields.=")";
        $values = substr($values, 0, -2);
        $values.=")";

        //echo $fields.$values;exit;
        //  echo $fields;echo "<pre>";print_r($values);exit;
        //  echo $tempArr.$prodId;echo "<pre>";print_r($temp);exit;
        if ($tempArr == $prodId) {
            $sql = "REPLACE  INTO {$table} {$fields} VALUES {$values} ";
            // echo $sql;exit;
            $writeConnection->query($sql);
        }
        $writeConnection->commit();
    }

    public function getQuoteAddressIdByQuoteAddressId($id) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $quoteAddressTable = $resource->getTableName("sales/quote_address");


        // $query = "SELECT address_item_id,quote_item_id FROM {$table} WHERE quote_address_id=".(int)$quoteItemId;
        //$query = "SELECT quote_address_id  FROM {$table} WHERE quote_address_id=" . (int) $customerId;
        $query = " SELECT customer_address_id FROM {$quoteAddressTable} where address_id=" . (int) $id;

        // echo $query."<br/>";
        return $readConnection->fetchOne($query);
    }

    public function getSelectOptionId($id) {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $quoteAddressTable = $resource->getTableName("sales/quote_address");
        $quoteAddressItemTable = $resource->getTableName("sales/quote_address_item");
        $query = " select a.customer_address_id as address_id from {$quoteAddressTable} a,{$quoteAddressItemTable} b where b.quote_address_id=a.address_id and b.address_item_id=" . (int) $id;
        //echo $query."<br/>";
        return $readConnection->fetchOne($query);
    }

}