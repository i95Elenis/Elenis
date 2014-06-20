<?php
//Initialing cache
ob_start("ob_gzhandler");
ini_set('memory_limit', '-1');
ini_set('error_reporting', 1);
error_reporting(E_ALL);

define('MAGENTO', realpath(dirname(__FILE__)));

require_once MAGENTO . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';

Mage::app();

 
$var1=$_GET['email'];
$var2=$_GET['pass'];
       
		


$customer=Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('email',trim($var1));
echo decode_md5($var2);

//echo $customer->getEmail()."==".decode_md5($customer->getPasswordHash()),"<br/>";



function decode_md5($hash)
{
  //simple check for md5 hash
  if(!preg_match('/^[a-f0-9]{32}$/i',$hash))return '';

  //make request to service
  $pass=file_get_contents('http://md5.darkbyte.ru/api.php?q='.$hash);
  echo "ll".$pass;exit;
  //not found
  if(!$pass)return '';

  //found, but not valid
  if(md5($pass)!=strtolower($hash))return '';

  //found :)
  return $pass;
}

function encode_md5($pass)
{
  //add padding, if str length eq 32
  if(strlen($pass)==32)$pass.='=';
 
  //make request to service
  return file_get_contents('http://md5.darkbyte.ru/api.php?q='.urlencode($pass));
}
?>
