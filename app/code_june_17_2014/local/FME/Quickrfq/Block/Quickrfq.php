<?php
/**
 * Quickrfq extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Quickrfq
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Quickrfq_Block_Quickrfq extends Mage_Core_Block_Template
{
	 const XML_PATH_UPLOAD          = 'quickrfq/upload/allow';
	 const XML_PATH_DATE          = 'quickrfq/option/date';
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
    
     public function getQuickrfq()     
     { 
        if (!$this->hasData('quickrfq')) {
            $this->setData('quickrfq', Mage::registry('quickrfq'));
        }
        return $this->getData('quickrfq');
        
    }
    /***************************************************************
     this function returns the action of the form while submitting
    ***************************************************************/
       public function getPostActionUrl()
    {
        return Mage::getUrl('quickrfq/index/post');
        
    }
    public function getEstimateDate()
    {
	$default=Mage::getStoreConfig(self::XML_PATH_DATE);
	$dayerror=false;
	if (!Zend_Validate::is(trim($default) , 'digits')) { 
                    $dayerror = true;
                }
	if($dayerror)
	{
		$estimatedate=date("Y-m-d", strtotime("+5 day"));
	}
	else
	{
		$estimatedate=date("Y-m-d", strtotime("+$default day"));
	}
	
        return $estimatedate;
        
    }
     public function getAllowedExtensions()
    {
	$files=Mage::getStoreConfig(self::XML_PATH_UPLOAD);
	return $files;
    }
    public function getUploadFile()
    {
	$ext=$this->getAllowedExtensions();
	if(!empty($ext))
	{
		echo "<input type='file' name='prd' id='file'/>";
	}
	else
	{
		echo "<label>".Mage::helper('quickrfq')->__('This Feature Is Disabled Temporarily By The Admin') . "</label>";
	}
        
        
    }
    /***************************************************************
     this function draws the path of FME_quickrfq folder on local
     and returns the path to the frontend from where it is called
    ***************************************************************/
      public function getSecureImageUrl()
    {
	$path = Mage::getBaseUrl('media');
	$pos =strripos($path,'media');
	//$apppath =substr($path,0, $pos) . 'FME_quickrfq' . DS . 'captcha/';
    $apppath =substr($path,0, $pos) . 'FME_quickrfq/captcha/';
        return $apppath;
       
    }
    /***************************************************************
     this function gets a new unique value by sending request to the
     assign_rand_value() function which returns a character and it
     adds the character in its variable and returns to the form at
     frontend
    ***************************************************************/
    
    function getNewrandCode($length)
	{
	  if($length>0) 
	  { 
	  $rand_id="";
	   for($i=1; $i<=$length; $i++)
	   {
	   mt_srand((double)microtime() * 1000000);
	   $num = mt_rand(1,36);
	   $rand_id .= $this->assign_rand_value($num);
	   }
	  }
	return $rand_id;
	}
	
	function assign_rand_value($num)
{
// accepts 1 - 36
  switch($num)
  {
    case "1":
     $rand_value = "a";
    break;
    case "2":
     $rand_value = "b";
    break;
    case "3":
     $rand_value = "c";
    break;
    case "4":
     $rand_value = "d";
    break;
    case "5":
     $rand_value = "e";
    break;
    case "6":
     $rand_value = "f";
    break;
    case "7":
     $rand_value = "g";
    break;
    case "8":
     $rand_value = "h";
    break;
    case "9":
     $rand_value = "i";
    break;
    case "10":
     $rand_value = "j";
    break;
    case "11":
     $rand_value = "k";
    break;
    case "12":
     $rand_value = "z";
    break;
    case "13":
     $rand_value = "m";
    break;
    case "14":
     $rand_value = "n";
    break;
    case "15":
     $rand_value = "o";
    break;
    case "16":
     $rand_value = "p";
    break;
    case "17":
     $rand_value = "q";
    break;
    case "18":
     $rand_value = "r";
    break;
    case "19":
     $rand_value = "s";
    break;
    case "20":
     $rand_value = "t";
    break;
    case "21":
     $rand_value = "u";
    break;
    case "22":
     $rand_value = "v";
    break;
    case "23":
     $rand_value = "w";
    break;
    case "24":
     $rand_value = "x";
    break;
    case "25":
     $rand_value = "y";
    break;
    case "26":
     $rand_value = "z";
    break;
    case "27":
     $rand_value = "0";
    break;
    case "28":
     $rand_value = "1";
    break;
    case "29":
     $rand_value = "2";
    break;
    case "30":
     $rand_value = "3";
    break;
    case "31":
     $rand_value = "4";
    break;
    case "32":
     $rand_value = "5";
    break;
    case "33":
     $rand_value = "6";
    break;
    case "34":
     $rand_value = "7";
    break;
    case "35":
     $rand_value = "8";
    break;
    case "36":
     $rand_value = "9";
    break;
  }
return $rand_value;
}

    
    
    
    
}