<?php

ini_set('error_reporting', 1);
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
Mage::app();
try {

    $file = 'final-sheet1.csv';
    $csv = new Varien_File_Csv();
    $data = $csv->getData($file);
    for ($i = 1; $i < count($data); $i++) {
        //echo "<pre>";print_r($data[$i]);exit;

        $customer = Mage::getModel("customer/customer");
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($data[$i][3]);
        /* if($data[$i][15]==231)
          {
          $country_id='US';

          } */
        //echo $data[$i][13]."-". $data[$i][16];
        $regionModel = Mage::getModel('directory/region')->loadByCode($data[$i][13], $data[$i][16]);
        $regionId = $regionModel->getId();
       // echo $regionId;
       // exit;
        //echo "<br/>".$customer->getId();exit;
        //$regionModel = Mage::getModel('directory/region')->loadByCode($regionCode, $countryCode);
        //$regionId = $regionModel->getId();
        $_custom_address = array('firstname' => (($data[$i][5]) ? $data[$i][5] : $data[$i][4]), 'lastname' => (($data[$i][6]) ? $data[$i][6] : $data[$i][4]), 'street' => array($data[$i][11], $data[$i][12]), 'city' => $data[$i][15], 'country_id' => $data[$i][16], 'region' => $data[$i][14], 'region_id' => $regionId, 'postcode' => $data[$i][17], 'telephone' => $data[$i][10],'comm_residence'=>'422', 'is_default_billing' => FALSE, 'is_default_shipping' => FALSE);
        $customAddress = Mage::getModel('customer/address');
        //$customAddress = new Mage_Customer_Model_Address();
        $customAddress->setData($_custom_address)
                ->setCustomerId($customer->getId())
                ->setIsDefaultBilling('0')
                ->setIsDefaultShipping('0')
                ->setSaveInAddressBook('1');
        try {
            $customAddress->save();
        } catch (Exception $ex) {
            Zend_Debug::dump($ex->getMessage());
        }

        //var_dump( $data[$i] );
        //$result = $client->customerAddressCreate($sess_id, $customer->getId(), array('firstname' => (($data[$i][5])?$data[$i][5]:$data[$i][4]), 'lastname' => (($data[$i][6])?$data[$i][6]:$data[$i][4]), 'street' => array($data[$i][11], $data[$i][12]), 'city' => $data[$i][15], 'country_id' => $data[$i][16], 'region' => $data[$i][14], 'region_id' => $regionId, 'postcode' => $data[$i][17], 'telephone' => $data[$i][10], 'is_default_billing' => FALSE, 'is_default_shipping' => FALSE));
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
