<?php
/**
 * Custom Product Preview
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitcg
 * @version      11.2.2
 * @license:     AJifvLXz2Jhov40GDpSzkNqfs4dkmEPJtRhHJxJI2y
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitcg_Model_Rewrite_Catalog_Product_Option_Type_File extends Mage_Catalog_Model_Product_Option_Type_File
{
    private $_is15 = false;
    
    public function __construct() {
        $this->_is15 = (version_compare(Mage::getVersion(), '1.10.0.0') >= 0)? true : false;
    }

    /**
     * Validate user input for option
     *
     * @throws Mage_Core_Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return Mage_Catalog_Model_Product_Option_Type_File
     */
    public function validateUserValue($values)
    {
        $option = $this->getOption();           
       
        
        if ( Mage::helper('aitcg/options')->checkAitOption( $option ) ) {
            Mage::getSingleton('checkout/session')->setUseNotice(false);
  
           $this->setIsValid(true);
            
            $value = null;
            if (isset($values[$option->getId()]) && !empty($values[$option->getId()])) {
                $value = $values[$option->getId()];
                if(is_array($value) && isset($value['img_data']))
                {
                    $value=$value['img_data'];
                }
              $manager = Mage::getModel('aitcg/image_manager');
             
              $mergedImagePath=   Mage::getModel('core/session')->getData('merged_image'); 
             
           // echo $mergedImagePath;die('$mergedImagePath');
                
                $template_id = $manager->addImage($value, Mage::app()->getRequest()->getParam('product'), $option->getId(), $option) ;
                $this->setUserValue(array(
                    'template_id' => $template_id,
                    'img_data' => $value,
                    'secret_key' => '',
                    'image_path' =>$mergedImagePath,
                ));
                return $this;

            }
            
            if ($this->getProduct()->getSkipCheckRequiredOption()) {
                $this->setUserValue(null);
                return $this;
            }            
            
            if($value==null && $option->getIsRequire())
            {
                        Mage::throwException(
                            Mage::helper('catalog')->__('Please specify the product\'s required option(s).'));                  
            }
            
            return $this;
        } else {
            //sce check code
            return parent::validateUserValue($values);
        }        
    }      



    
    /**
     * Validate file
     *
     * @throws Mage_Core_Exception
     * @param array $optionValue
     * @return Mage_Catalog_Model_Product_Option_Type_Default
     */
    protected function _validateFileCopy($optionValue)
    {
        $option = $this->getOption();
        
        $fileFullPath == $this->_getFileFullPath($optionValue);
        if ($fileFullPath === null) {
            return false;
        }

        $validatorChain = new Zend_Validate();

        $_dimentions = $this->_getFileDimentions($option);
        
        if (count($_dimentions) > 0) {
            $validatorChain->addValidator(
                new Zend_Validate_File_ImageSize($_dimentions)
            );
        }
        // File extension
        $_allowed = $this->_parseExtensionsString($option->getFileExtension());
        if ($_allowed !== null) {
            $validatorChain->addValidator(new Zend_Validate_File_Extension($_allowed));
        } else {
            $_forbidden = $this->_parseExtensionsString($this->getConfigData('forbidden_extensions'));
            if ($_forbidden !== null) {
                $validatorChain->addValidator(new Zend_Validate_File_ExcludeExtension($_forbidden));
            }
        }

        // Maximum filesize
        $validatorChain->addValidator(
                new Zend_Validate_File_FilesSize(array('max' => $this->_getUploadMaxFilesize()))
        );


        if ($validatorChain->isValid($fileFullPath)) {
            $ok = is_readable($fileFullPath)
                && isset($optionValue['secret_key'])
                && substr(md5(file_get_contents($fileFullPath)), 0, 20) == $optionValue['secret_key'];

            return $ok;
        } elseif ($validatorChain->getErrors()) {
            $errors = $this->_getValidatorErrors($validatorChain->getErrors(), $optionValue);

            if (count($errors) > 0) {
                $this->setIsValid(false);
                Mage::throwException( implode("\n", $errors) );
            }
        } else {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s)'));
        }
    }    
    protected function _getFileFullPath($optionValue)
    {
            /**
         * @see Mage_Catalog_Model_Product_Option_Type_File::_validateUploadFile() - there setUserValue() sets correct \n
         * fileFullPath only for quote_path. So we must form both full paths manually and check them.
         */
        $checkPaths = array();
        if (isset($optionValue['quote_path'])) {
            $checkPaths[] = Mage::getBaseDir() . $optionValue['quote_path'];
        }
        if (isset($optionValue['order_path']) && !$this->getUseQuotePath()) {
            $checkPaths[] = Mage::getBaseDir() . $optionValue['order_path'];
        }
        $fileFullPath = null;
        foreach ($checkPaths as $path) {
            if (!is_file($path)) {
                continue;
            }
            $fileFullPath = $path;
            break;
        }
            return $fileFullPath;
    } 
    protected function _getFileDimentions($option)
    {
            $_dimentions = array();

        if ($option->getImageSizeX() > 0) {
            $_dimentions['maxwidth'] = $option->getImageSizeX();
        }
        if ($option->getImageSizeY() > 0) {
            $_dimentions['maxheight'] = $option->getImageSizeY();
        }
        if (count($_dimentions) > 0 && !$this->_isImage($fileFullPath)) {
            return false;
        }
            return $_dimentions;
    }
    /**
     * Return option html
     *
     * @param array $optionInfo
     * @return string
     */
    public function getCustomizedView($optionInfo)
    {                                            
        try {
            if(isset($optionInfo['option_value'])) {
                $result = $this->_getOptionHtml($optionInfo['option_value']);
            } else {
                $result = $optionInfo['value'];
            }
            return $result;
        } catch (Exception $e) {
            return $optionInfo['value'];
        }
    }    
    
    /**
     * Return printable option value
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getPrintableOptionValue($optionValue)
    {
        $option = $this->getOption();
        if(! Mage::helper('aitcg/options')->checkAitOption( $option ) || $option->getData("image_template_id") == 0 ) {
            return parent::getPrintableOptionValue($optionValue);
        } else {
            $data = $this->getData('aitcg_model');

            if((isset($data['file_name'],$data['image']["thumbnail_url"]) || isset($data['img_data'])) && $data['id']>0) {
                $mult = min(1, $data['image']['thumbnail_size'][0] / $data['image']['default_size'][0], 
                    $data['image']['thumbnail_size'][1] / $data['image']['default_size'][1]);
                $model = Mage::getModel('aitcg/image');                    
                $model->setData($data);                

                $replace = array(
                    $data['file_name'],
                    $data['image']["thumbnail_url"],
                    'left: '.max(0,round($data['area_offset_x']*$mult-1)).'px; top: '.max(0,round($data['area_offset_y']*$mult-1)).'px; '.
                    'width: '.round(($data['area_size_x']-1)*$mult).'px; height: '.round($data['area_size_y']*$mult).'px;',
                    $data['temp_thumbnail_url'],
                    'width: '. 1 .'px; height: '. 1 .'px; '.
                    'top: '. 0 .'px; left: '. 0 .'px;',                    
                );
                    
                return '|||aitcgimage|||'. implode('|||', $replace). '|||';
            } else {
                return parent::getPrintableOptionValue($optionValue);
            }
        }
    }    

    /**
     * Format File option html
     *
     * @param string|array $optionValue Serialized string of option data or its data array
     * @return string
     */
    protected function _getOptionHtml($optionValue)
    {
        $value = $this->_getValueOption($optionValue);

        try 
        {
            $option = $this->getOption();
            $isTempData = false;
            $data = array();
            if( Mage::helper('aitcg/options')->checkAitOption( $option ) &&  $option->getData("image_template_id") > 0)
            {
                
                $model = Mage::getModel('aitcg/image');
                
                $data = $this->_getDateImage($model, $value) + $this->_getDateQuote();
				$data = $this->_getDateArea($option, $model, $data);
                    
                $isTempData = true;
                    
            }
            return $this->_sprintOption($value, $optionValue, $option, $data, $isTempData);
            
        } catch (Exception $e) 
        {
            //Mage::logException($e);
            Mage::throwException(Mage::helper('catalog')->__("File options format is not valid."));
        }
    }  
    
    protected function _sprintOption($value, $optionValue, $option, $data, $isTempData)
    {
    
        $js = "";
        if($isTempData)
        {
            $sBlockName = $this->_getBlockName();
            $js =  Mage::app()->getLayout()
                ->createBlock($sBlockName, null, $data)
                ->setProduct( $option->getProduct() )
                ->setOption( $option )
                ->toHtml();
                
            return $js . '<p>' .  sprintf('<a href="%s" target="_blank">%s</a> %s',
                    $this->_getOptionDownloadUrl($value['url']['route'], $value['url']['params']),
                    (isset($value['title'])?Mage::helper('core')->htmlEscape($value['title']):''),
                    $this->_getSizes($optionValue)
                ) . '</p>'; 
        }
        else
        {
            
            return $js . sprintf('<a href="%s" target="_blank">%s</a> %s',
                $this->_getOptionDownloadUrl($value['url']['route'], $value['url']['params']),
                Mage::helper('core')->htmlEscape($value['title']),
                $this->_getSizes($optionValue)
            );
        }
    }
    protected function _getBlockName()
    {
        $request = Mage::app()->getRequest();
        if( $request->getModuleName() == 'checkout' && $request->getControllerName() == 'cart') {
            //allowing moving/uploading images only at cart
            return 'aitcg/checkout_cart_item_option_cgfile';
        }
        return 'aitcg/checkout_cart_item_option_cgfile_lite';
    }
    protected function _getDateImage($model, $value)
    {
        $request = Mage::app()->getRequest();
        $data = array();
        if( isset($value["template_id"]) && $value["template_id"] > 0 ) 
        {                    
            $model->load($value["template_id"]);                    
            if(!$model->isEmpty()) {
                if($request->getActionName()=='saveOrder' || 
                    ($request->getModuleName() == 'checkout' && $request->getControllerName() == 'multishipping' && $request->getActionName()=='overviewPost')
                )
                //if action is saveOrder we will save all images to store folder and replace all links (from getFullData & getMediaImage) for this constant path
                {
                    $model->storeImage();
                }
                $data = $model->getFullData();
            }
        }
        return $data;
    }
    protected function _getDateArea($option, $model, $data = array())
    {
        $request = Mage::app()->getRequest();
        if( $request->getModuleName() == 'checkout' && $request->getControllerName() == 'cart') {
            //allowing moving/uploading images only at cart
           if($this->hasData('quote_item')) {
                $options = $this->getQuoteItem()->getOptions();
            } else {
                $options = $this->getQuoteItemOption()->getItem()->getOptions();
            }
            foreach($options as $buyRequest ) {
                if($buyRequest['code'] == 'info_buyRequest') {
                    break;
                }
            }                    
        }    
        
        if(!isset($data['area_size_x'])) {
                $data['area_size_x'] = $option->getAreaSizeX();
                $data['area_size_y'] = $option->getAreaSizeY();
                $data['area_offset_x']= $option->getAreaOffsetX();
                $data['area_offset_y']= $option->getAreaOffsetY();
        }

        $data["image"] = $model->getMediaImage( $option->getProductId(), $option->getImageTemplateId() );

        $this->setData('aitcg_model', $data);

        if(isset($buyRequest))
                $data['buy_request'] = $buyRequest;
        if($model->hasDataChanges()) {
                $model->save();
        }
        return $data;
    }
    protected function _getSizes($optionValue)
    {
        $value = $this->_getValueOption($optionValue);
        if (isset($value['width'],$value['height']) && $value['width'] > 0 && $value['height'] > 0) 
        {
                $sizes = $value['width'] . ' x ' . $value['height'] . ' ' . Mage::helper('catalog')->__('px.');
        } else 
        {
                $sizes = '';
        }
        return $sizes;
    }
    protected function _getDateQuote()
    {
            $date=array();
            if($this->_is15) {
                    $configurationItemOption =  $this->getConfigurationItemOption();
            } else {
                    $configurationItemOption =  $this->getQuoteItemOption();
            }
            $data["quote_option_id"] =  $configurationItemOption->getOptionId();
            $data["quote_item_id"] =    $configurationItemOption->getItemId();
            $data["quote_option_code"]= $configurationItemOption->getCode();
            return $date;
    }
    protected function _getValueOption($optionValue)
    {
        if(is_array($optionValue)) 
        {
            return $optionValue;
        } else 
        {
            try 
            {
                return unserialize($optionValue);
            } catch (Exception $e) {
                return $optionValue;
            }
        }
        }
}