<?php
ini_set('error_reporting',1);
define('MAGENTO', realpath(dirname(__FILE__)));

require_once MAGENTO .DIRECTORY_SEPARATOR. 'app'.DIRECTORY_SEPARATOR.'Mage.php';

Mage::app();
$host = "elenidev2.vanwestmedia.com/index.php";
$wsdl1="http://".$host."/api/v2_soap/?wsdl";
echo $wsdl1;exit;
$client = new SoapClient("http://".$host."/api/v2_soap/?wsdl"); //soap handle

$wsdl = trim(file_get_contents($wsdl1));   
try {
    $a = new SoapClient($wsdl);
    $apiuser="apiuser";
$apikey="apikey";
$sess_id= $a->login($apiuser, $apikey); //we do login
echo "session".$sess_id;exit;


			
			$file = 'final-sheet.csv';
			$csv = new Varien_File_Csv();
			$data = $csv->getData($file);
			
			for($i=1; $i<count($data); $i++)
			{
			//echo "<pre>";print_r($data[$i]);exit;
			
			$customer = Mage::getModel("customer/customer");
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
			$customer->loadByEmail($data[$i][3]);
			/*if($data[$i][15]==231)
			{
				$country_id='US';
				
			}*/
			//echo $data[$i][13]."-". $data[$i][16];
			$regionModel = Mage::getModel('directory/region')->loadByCode($data[$i][13], $data[$i][16]);
			$regionId = $regionModel->getId();
			//echo	$regionId;	exit;
			//echo "<br/>".$customer->getId();exit;
			//$regionModel = Mage::getModel('directory/region')->loadByCode($regionCode, $countryCode);
			//$regionId = $regionModel->getId();


				//var_dump( $data[$i] );
				            $result = $client->customerAddressCreate($sess_id, $customer->getId(), array('firstname' => (($data[$i][5])?$data[$i][5]:$data[$i][4]), 'lastname' => (($data[$i][6])?$data[$i][6]:$data[$i][4]), 'street' => array($data[$i][11], $data[$i][12]), 'city' => $data[$i][15], 'country_id' => $data[$i][16], 'region' => $data[$i][14], 'region_id' => $regionId, 'postcode' => $data[$i][17], 'telephone' => $data[$i][10], 'is_default_billing' => FALSE, 'is_default_shipping' => FALSE));

			echo $result."\n";
                        }
} catch (SoapFault $e) {
    var_dump(libxml_get_last_error());
    var_dump($e);
}



