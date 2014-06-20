<?php

/**
 * Magento Bluejalappeno Order Export Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Bluejalappeno
 * @package    Bluejalappeno_OrderExport
 * @copyright  Copyright (c) 2010 Wimbolt Ltd (http://www.bluejalappeno.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Genevieve Eddison <sales@bluejalappeno.com>
 * */
class Bluejalappeno_Orderexport_Model_Export_Csv extends Bluejalappeno_Orderexport_Model_Export_Abstractcsv {
    const ENCLOSURE = ' ';
    const DELIMITER = '	';

    public function _dateToUtc($date) {
        if ($date === null) {
            return null;
        }
        $timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
        $myDateTime = new DateTime($date, new DateTimeZone($timezone));
        $myDateTime->setTimezone(new DateTimeZone('Etc/UTC'));
        $date = $myDateTime->format('Y-m-d H:i:s');


        return $date;
    }

    /**
     * Concrete implementation of abstract method to export given orders to csv file in var/export.
     *
     * @param $orders List of orders of type Mage_Sales_Model_Order or order ids to export.
     * @return String The name of the written csv file in var/export
     */
    public function exportOrders($orderIds) {

        $ordercount = count($orderIds);
        $i = 1;
        foreach ($orderIds as $order) {
            $order = Mage::getModel('sales/order')->load($order);
            if ($i < $ordercount)
                $realOrderIds .=$order->getRealOrderId() . '_';
            else
                $realOrderIds .=$order->getRealOrderId();
            $i++;
        }
        //echo  $realOrderIds;exit;
        $fileName = $realOrderIds . '.txt';
        $fp = fopen(Mage::getBaseDir('export') . '/netdot/' . $fileName, 'w');
        $this->writeHeadRow($fp);

        foreach ($orderIds as $order) {
            $order = Mage::getModel('sales/order')->load($order);
            $this->writeOrder($order, $fp);
        }

        fclose($fp);

        return true;
    }

    public function exportOrders1($orderIds) {



        foreach ($orderIds as $id) {

            $orderId = $id;
        }


        $fileName = $orderId . '.txt';
        $fp = fopen(Mage::getBaseDir('export') . '/netdottest/' . $fileName, 'w');
        $this->writeHeadRow($fp);

        foreach ($orderIds as $order) {
            $order = Mage::getModel('sales/order')->load($order);

            //echo "<pre>";
            //print_r($order->getData());exit;
            $this->writeOrder($order, $fp);
        }

        fclose($fp);

        /* $fullFilePath=Mage::getBaseDir('export') . '/netdottest/' . $fileName;

          $f = fopen($fullFilePath, "r");

          // Read line by line until end of file
          while(!feof($f)) {
          echofwrite($f,str_replace('"','',fgets($f)));
          }

          fclose($f);
         */
        return true;
    }

    /**
     * Writes the head row with the column names in the csv file.
     *
     * @param $fp The file handle of the csv file
     */
    protected function writeHeadRow($fp) {
        fputcsv($fp, $this->getHeadRowValues(), self::DELIMITER, self::ENCLOSURE);
    }

    /**
     * Writes the row(s) for the given order in the csv file.
     * A row is added to the csv file for each ordered item.
     *
     * @param Mage_Sales_Model_Order $order The order to write csv of
     * @param $fp The file handle of the csv file
     */
    protected function writeOrder($order, $fp) {
        $common = $this->getCommonOrderValues($order);

        $orderItems = $order->getItemsCollection();
        $maxItemCount = 10;
        $record = array();
        $i = 0;

        foreach ($orderItems as $item) {

            $i++;
            if (!$item->isDummy() && $i <= $maxItemCount) {
                $record = array_merge($record, $this->getOrderItemValues($item, $order));
                //  $record = array_merge($record, $this->getOrderItemsWithUnderscoreExcludeInSku($item, $order));
                //echo "<pre>";print_r($record);
            }
        }
        if ($i < $maxItemCount) {
            for ($j = ($i + 1); $j <= $maxItemCount; $j++) {

                $record = array_merge($record, $this->getOrderItemTabSpace());
            }
        }
        $completerecords = array_merge($common, $record);
        //echo "<pre>";print_r($completerecords);
        //$completerecords=array_push(array());
        // exit;


        fputcsv($fp, $completerecords, self::DELIMITER, self::ENCLOSURE);
    }

    /**
     * Returns the head column names.
     *
     * @return Array The array containing all column names
     */
    protected function getHeadRowValues() {
        return array(
            'import_id',
            'customer_id',
            'order_date',
            'delivery_date',
            'payment_date',
            'bill_first_name',
            'bill_last_name',
            'bill_street1',
            'bill_street2',
            'bill_city',
            'bill_state',
            'bill_zip',
            'bill_phone',
            'bill_email',
            'ship_first_name',
            'ship_last_name',
            'ship_street1',
            'ship_street2',
            'ship_city',
            'ship_state',
            'ship_zip',
            'shipping_method',
            'shipping_amt',
            'tax_code',
            'gift_card',
            'order_total',
            'transaction_id',
            'payee_name',
            'cc_number',
            'cc_exp',
            'payment_type',
            'product_notes',
            'authorization_id',
            'shipping_phone',
            'extra_name1',
            'extra_name2',
            'discount_code',
            'product_1_sku',
            'product_1_name',
            'product_1_qty',
            'product_1_price',
            'product_2_sku',
            'product_2_name',
            'product_2_qty',
            'product_2_price',
            'product_3_sku',
            'product_3_name',
            'product_3_qty',
            'product_3_price',
            'product_4_sku',
            'product_4_name',
            'product_4_qty',
            'product_4_price',
            'product_5_sku',
            'product_5_name',
            'product_5_qty',
            'product_5_price',
            'product_6_sku',
            'product_6_name',
            'product_6_qty',
            'product_6_price',
            'product_7_sku',
            'product_7_name',
            'product_7_qty',
            'product_7_price',
            'product_8_sku',
            'product_8_name',
            'product_8_qty',
            'product_8_price',
            'product_9_sku',
            'product_9_name',
            'product_9_qty',
            'product_9_price',
            'product_10_sku',
            'product_10_name',
            'product_10_qty',
            'product_10_price'
        );
    }

    /**
     * Returns the values which are identical for each row of the given order. These are
     * all the values which are not item specific: order data, shipping address, billing
     * address and order totals.
     *
     * @param Mage_Sales_Model_Order $order The order to get values from
     * @return Array The array containing the non item specific values
     */
    protected function getCommonOrderValues($order) {


        $orderItems = $order->getItemsCollection();
        $productNote = '';
        $imageString = '';
        $mergedImage = '';
        $skuNote = "";
        $customOptionValue = "";
        foreach ($orderItems as $item) {
            // echo "<pre>";print_r( explode("_",$this->getItemSku($item),2));
            //print_R($item->getProductOptions());

            if ($item->getProductOptions()) {
                $options = $item->getProductOptions();
                if ($options['options']) {
                    //  echo "<pre>";print_r($options['options']);

                    foreach ($options['options'] as $customOptions) {
                        //echo "<pre>";print_r($customOptions);
                        if ($customOptions['option_type'] != 'aitcustomer_image') {
                            $customOptionValue.=$customOptions['label'] . " : " . $customOptions['value'] . " , ";
                        }
                    }
                    //echo "<pre>";print_r($options['options']);
                }

                //exit;
                $i = 0;
                foreach ($item->getProductOptions() as $values) {
                    //echo "<pre>";print_r($values->getType());exit;
                    $imageOption = $values['options'] ? $values['options'] : '';
                    //  print_r($imageOption);die('12121');
                    if (!empty($imageOption)) {
                        //print_r($imageOption);exit;
                        foreach ($imageOption as $imgValues) {

                            $mergedImage = $imgValues['image_path'] ? $imgValues['image_path'] : '';
                            $img_data = $imgValues['img_data'] ? $imgValues['img_data'] : '';
                            $allImages = json_decode($img_data);
                            foreach ($allImages as $arrImage) {
                                //  $imageString .=$arrImage->src . ',';
                                $imageString .= 'Original - ' . $arrImage->src . ',' . ' Preview - ' . $mergedImage;
                                echo '<br/>';
                            }
                        }
                    }
                }
            }
            $skuExcludeData = explode("_", $this->getItemSku($item), 2);
            if ($skuExcludeData[1] != "") {
                $skuNote.="SKU - " . $this->getItemSku($item) . " : SKU Option- " . $skuExcludeData[1];
            }
        }
        //exit;

        foreach ($orderItems as $item) {
            if ($item->getProductOptions()) {
                foreach ($item->getProductOptions() as $values) {

                    // echo "<pre>";
                    //print_r($values);
                    if ($values['super_attribute'])
                    //   print_r($values['super_attribute']);
                        foreach ($values['super_attribute'] as $attributeCode => $attributeValue) {
                            $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeCode);
                            $options = $attribute->getSource()->getAllOptions();
                            $optionValues = array();
                            foreach ($options as $option) {
                                $optionValues[$option['value']] = $option['label'];
                            }
                            $allProductoptions .=$optionValues[$attributeValue] . ',';
                        }
                }
            }
        }
        //echo $allProductoptions;  exit;
        //echo $customOptionValue;exit;
        if ($imageString != '') {
            $productNote = $imageString;
        }

        /* Payment Information */

        $payarry = $order->getPayment()->getData();
        $cardinfo = $payarry['additional_information'];
        $expiryDdate = '';
        $cc_exp_month = $payarry['cc_exp_month'] ? $payarry['cc_exp_month'] : '';
        $cc_last4 = $payarry['cc_last4'] ? $payarry['cc_last4'] : '';
        $cc_exp_year = $payarry['cc_exp_year'] ? $payarry['cc_exp_year'] : '';
        $last_trans_id = $payarry['last_trans_id'] ? $payarry['last_trans_id'] : '';
        $cc_type = $payarry['cc_type'] ? $payarry['cc_type'] : '';
        $cc_owner = $payarry['cc_owner'] ? $payarry['cc_owner'] : '';
        $cc_exp_month = $payarry['cc_exp_month'] ? $payarry['cc_exp_month'] : '';
        $cc_exp_year = $payarry['cc_exp_year'] ? $payarry['cc_exp_year'] : '';

        $ccExp = $cc_exp_month . '/' . '01' . '/' . $cc_exp_year;
        if ($ccExp != '') {
            $expiryDdate = (string) date("m/d/Y", strtotime($ccExp));
        }

        if ($cc_type == 'VI') {
            $cc_type = 'CC_VISA';
        }
        if ($cc_type == 'MC') {
            $cc_type = 'CC_MC';
        }
        if ($cc_type == 'AE') {
            $cc_type = 'CC_AMEX';
        }
        if ($cc_type == 'DI') {
            $cc_type = 'CC_DISC';
        }

        $deliveryDateNotes = $order->getDeliveryDate() ? $order->getDeliveryDate() : '';

        if ($deliveryDateNotes != '') {
            $deliveryDateNotesU = date("m/d/Y  H:i", strtotime($deliveryDateNotes));
            $deliveryDateNotesArray = explode(' ', $deliveryDateNotes);
            if ($deliveryDateNotesArray['1'] == '00:00:00') {
                $deliveryDateNotesU = date("m/d/Y", strtotime($deliveryDateNotes));
            }
            $deliveryDateNotesU = 'Will pickup on ' . $deliveryDateNotesU;
        }
        // echo $order->getExpectedDelivery();die('here');

        if ($order->getExpectedDelivery()) {
            $deliveryDateNotesU = 'Will pickup on ' . $order->getExpectedDelivery();
        }



        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
        //echo "<pre>";
        //print_r($billingAddress->getData());die();


        /*
         * Shipping methods convert to NetDot's Shipping method types 
         * 
         *  1.       Store Pickup = PICKUP
          2.       24 Hour Doorman Delivery NYC ONLY= MESSDOOR
          3.       Next day messenger delivery NYC ONLY= MESS_1D (this also usually imports with the time they choose in the product notes section of the import)
          4.       UPS Ground= UPSGND
          5.       UPS 2 Day Air= UPS2D
          6.       UPS 3 Day = UPS3D
          7.       UPS Next Day Air= UPS1D
          8.       UPS Next Day Saturday Delivery= UPSSAT

         * 
         */

        $orderData = $order->getData();
        /* start of changed shipping functionality */
        $shippingMethod = $orderData['shipping_method'];
        $avaialbleShippingMethods = $this->getActiveShippingMethodForExport();
        $defaultMessengerValues = Mage::getStoreConfig('elenissec/elenisgrp/messenger_delivery_option');
        $defaultMessengerData = explode(",", $defaultMessengerValues);
        foreach ($avaialbleShippingMethods as $t) {
            switch ($t) {
                case "matrixrate":
                    $separateDeliveryNumber = explode("_", $shippingMethod);
                    $getMessengerDelivery = Mage::getModel('matrixrate/mysql4_carrier_matrixrate')->loadByPk($separateDeliveryNumber[2]);
                    $deliveryMessenger = explode(" - ", $orderData['shipping_description'], 2);
                    $deliveryMessenger = trim($deliveryMessenger[1]);
                    $getMessengerDeliveryType = trim($getMessengerDelivery['delivery_type']);
                    foreach ($defaultMessengerData as $eachValue) {
                        $eachDelivery = explode("=>", $eachValue);
                        if (strcmp($getMessengerDeliveryType, $deliveryMessenger) == 0) {
                            if (strcmp(trim($eachDelivery[0]), $getMessengerDeliveryType) == 0) {
                                $shippingMethod = $eachDelivery[1];
                                break;
                            }
                        }
                    }
                    break;
                case "ups":
                    foreach ($defaultMessengerData as $eachValue) {
                        $eachDelivery = explode("=>", $eachValue);
                        if (strcmp(trim($eachDelivery[0]), $shippingMethod) == 0) {
                            $shippingMethod = $eachDelivery[1];
                            break;
                        }
                    }
                    break;
                case "storepickupmodule":
                    foreach ($defaultMessengerData as $eachValue) {
                        $eachDelivery = explode("=>", $eachValue);
                        if (strcmp(trim($eachDelivery[0]), $shippingMethod) == 0) {
                            $shippingMethod = $eachDelivery[1];
                            break;
                        }
                    }
                    break;
                default:
                    $shippingMethod = $shippingMethod;
            }
        }

        /* end of changed shipping functionality */
      //  echo $shippingMethod;exit;

        /* Gift Message */

        $gift_message_id = $orderData['gift_message_id'];
        $gift_message = '';
        if (!is_null($gift_message_id)) {
            $message = Mage::getModel('giftmessage/message');
            $message->load((int) $gift_message_id);
            $gift_message = $message->getData('message');
        }
        $orderCreatedDate = Mage::helper('core')->formatDate($order->getCreatedAt(), 'medium', true);
        $orderCreatedDate = date("m/d/Y", strtotime($orderCreatedDate));

        $deliveryDate = $order->getDeliveryDate();

        if ($deliveryDate != '') {
            $deliveryDate = date("m/d/Y", strtotime($deliveryDate));
        }
        if ($deliveryDate == '') {
            $deliveryDate = $order->getExpectedDelivery();
            $deliveryDate = date("m/d/Y", strtotime($deliveryDate));
        }

        $discountCode = $orderData['coupon_code'] ? $orderData['coupon_code'] : '';
        $extraName1 = '';
        $extraName2 = '';
        $shippingAmount = $this->formatPrice($order->getData('shipping_amount'), $order);
        $shippingAmount = str_replace("$", " ", $shippingAmount);

        $taxCode = $shippingAddress ? $shippingAddress->getRegionCode() : '';

        if ($taxCode == 'NY') {
            $taxCode = 'NY';
        } else {
            $taxCode = 'OOS';
        }

        $grandTotal = $this->formatPrice($order->getData('grand_total'), $order);
        $grandTotal = str_replace("$", " ", $grandTotal);
        $customerId = $order->getCustomerId();
        if ($customerId != '') {
            $customerId = '<' . $order->getCustomerId() . '>';
        }

        $order_id = $order->getEntityId();
        $sagepaymentspro = Mage::getModel('ebizmarts_sagepaymentspro/transaction')->getCollection()
                        ->addFieldToFilter('order_id', $order_id);

        $postCodeReusult = '';
        foreach ($sagepaymentspro as $transaction) {
            $postCodeReusult = $transaction->getPostCodeResult();
        }

        return array(
            $order->getRealOrderId(),
            $customerId,
            $orderCreatedDate,
            $deliveryDate,
            $orderCreatedDate,
            $billingAddress->getFirstname(),
            $billingAddress ? $billingAddress->getLastname() : '',
            $billingAddress ? str_replace('"', '', $billingAddress->getStreet(1)) : '',
            $billingAddress ? $billingAddress->getStreet(2) : '',
            $billingAddress->getData("city"),
            $billingAddress->getRegionCode(),
            $billingAddress->getData("postcode"),
            $billingAddress->getData("telephone"),
            $order->getCustomerEmail(),
            $shippingAddress ? $shippingAddress->getFirstname() : '',
            $shippingAddress ? $shippingAddress->getLastname() : '',
            $shippingAddress ? $shippingAddress->getStreet(1) : '',
            $shippingAddress ? $shippingAddress->getStreet(2) : '',
            $shippingAddress ? $shippingAddress->getData("city") : '',
            $shippingAddress ? $shippingAddress->getRegionCode() : '',
            $shippingAddress ? $shippingAddress->getData("postcode") : '',
            $shippingMethod,
            $shippingAmount,
            $taxCode,
            $gift_message,
            $grandTotal,
            $postCodeReusult,
            $cc_owner,
            $cc_last4 ? 'XXXXXXXXXXXX' . $cc_last4 : '',
            $expiryDdate,
            $cc_type,
            $deliveryDateNotesU . ' ' . $allProductoptions . ' ' . $productNote . ' ' . $skuNote . ' ' . $customOptionValue,
            $last_trans_id,
            $shippingAddress->getData("telephone"),
            $extraName1,
            $extraName2,
            $discountCode,
        );
    }

    /**
     * Returns the item specific values.
     *
     * @param Mage_Sales_Model_Order_Item $item The item to get values from
     * @param Mage_Sales_Model_Order $order The order the item belongs to
     * @return Array The array containing the item specific values
     */
    protected function getOrderItemValues($item, $order) {

        $itemPrice = $this->formatPrice($item->getOriginalPrice(), $order);
        $itemPrice = str_replace('$', '', $itemPrice);
        //echo "<pre>";print_r($this->getItemSku($item));exit;
        $itemSku = explode("_", $this->getItemSku($item), 2);
        // echo $itemSku[0];
        return array(
            //$this->getItemSku($item),
            $itemSku[0],
            //$itemSku[1],
            $item->getName(),
            (int) $item->getQtyOrdered(),
            $itemPrice,
        //$productNote
        //$this->formatPrice($item->getData('price'), $order),
        );
    }

    protected function getOrderItemTabSpace() {



        return array(
            "",
            "",
            "",
            "",
        //$productNote
        //$this->formatPrice($item->getData('price'), $order),
        );
    }

    public function getActiveShippingMethodForExport() {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $shipMethods = array();
        foreach ($methods as $shippigCode => $shippingModel) {
            $shippingTitle = Mage::getStoreConfig('carriers/' . $shippigCode . '/title');
            //$shipMethods[$shippigCode] = $shippingTitle;
            $shipMethods[] = $shippigCode;
        }
        return $shipMethods;
    }

}

?>