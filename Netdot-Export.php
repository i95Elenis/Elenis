<?php

//echo "123";

// include the core code we are going to use
require_once('app/Mage.php');
$orderId=base64_decode(Mage::app()->getRequest()->getParam('order_id'));
//echo $orderId;exit;
umask (0);
Mage::app('default');
 // resources
$resource = Mage::getSingleton('core/resource');
 
// db access
$db_read = $resource->getConnection('core_read');
$db_write = $resource->getConnection('core_write');
 
// support table prefix if one is being used
$table_prefix = Mage::getConfig()->getTablePrefix();
 
// working code below this line
###############################
 
// count the orders
$order_num = $db_read->fetchOne("SELECT COUNT(*) AS num FROM {$table_prefix}sales_flat_order WHERE status = 'pending'");
 
// get an array of the orders
$orders = $db_read->fetchAll("SELECT * FROM {$table_prefix}sales_flat_order WHERE status = 'pending'");
 

 //$orders = array(0=>$orderId);
 
 $file = Mage::getModel('bluejalappeno_orderexport/export_csv')->exportOrderSaveAs($orderId);
 $fileName=explode("/",$file);
 //echo "<pre>";print_r($fileName);
 //echo count($fileName);exit;
 $savedFileName=end($fileName);
 header('MIME-Version: 1.0');
 header('Content-Type: text/plain; charset=us-ascii');
 header('Content-Transfer-Encoding: 7bit');
header('Content-Disposition: attachment; filename='.$savedFileName);
//echo readfile($file);
echo preg_replace("|\<br.*\><br.*/>(.*\n*)\</br\><br/>|","",readfile($file));
 
?>
