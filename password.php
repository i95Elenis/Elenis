<?php

ob_start("ob_gzhandler");
error_reporting(E_ALL | E_STRICT);
ini_set('memory_limit', '-1');
ini_set('error_reporting', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
Mage::app();
$file = 'customer-' . date('dmY_His') . '.csv';
//echo $file;
$readFile = 'customers-1.csv';
$readCsv = new Varien_File_Csv();
$writeCsv = new Varien_File_Csv();
$csvdata = array();
$data = $readCsv->getData($readFile);
for ($i = 1; $i < count($data); $i++) {
    $customer = Mage::getModel("customer/customer");
    $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
   // echo $data[$i][0] . "<br/>";
    $customer->loadByEmail($data[$i][0]);
    //echo strtolower(substr( $customer->getFirstname(), 0, 3).substr( $customer->getLastname(), 0, 3).substr( $customer->getEmail(), 0, 2).rand(111,999));
    //echo strtolower(substr($customer->getFirstname(), 0, 3) . substr($customer->getLastname(), 0, 3) . substr($customer->getEmail(), 0, 2));
   // $password=strtolower(substr($customer->getFirstname(), 0, 3) . substr($customer->getLastname(), 0, 3) . substr($customer->getEmail(), 0, 2));
     $password=rand(100000,100000000);
    $customer->setPassword($password)->save();
    //$customerInfo['firstname'] = $customer->getFirstname();
    //$customerInfo['lastname'] = $customer->getLastname();
    $customerInfo['email'] = $customer->getEmail();
    $customerInfo['password'] = $customer->getPassword();
    $customerInfo['password_hash'] = $customer->getPasswordHash();
    $csvdata[] = $customerInfo;
}
//exit;
$writeCsv->saveData($file, $csvdata);
?>