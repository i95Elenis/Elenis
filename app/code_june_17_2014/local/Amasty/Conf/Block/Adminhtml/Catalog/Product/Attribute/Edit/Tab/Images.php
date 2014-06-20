<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Conf
*/
class Amasty_Conf_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Images extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amconf/icons.phtml');
        $this->_doUpload();
    }
    
    protected function _doUpload()
    {
        $uploadDir = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 
                                                    'amconf' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                                                    
        /**
        * Deleting
        */
        $toDelete = Mage::app()->getRequest()->getPost('amconf_icon_delete');
        if ($toDelete)
        {
            foreach ($toDelete as $optionId => $del)
            {
                if ($del)
                {
                    @unlink($uploadDir . $optionId . '.jpg');
                }
            }
        }
        
        /**
        * Uploading files
        */
        if (isset($_FILES['amconf_icon']) && isset($_FILES['amconf_icon']['error']))
        {
            foreach ($_FILES['amconf_icon']['error'] as $optionId => $errorCode)
            {
                if (UPLOAD_ERR_OK == $errorCode)
                {
                    move_uploaded_file($_FILES['amconf_icon']['tmp_name'][$optionId], $uploadDir . $optionId . '.jpg');
                }
            }
        }

        if (Mage::app()->getRequest()->isPost())
        {
            // saving attribute 'use_image' property
            $confAttr = Mage::getModel('amconf/attribute')->load(Mage::registry('entity_attribute')->getId(), 'attribute_id');
            if (!$confAttr->getId())
            {
                $confAttr->setAttributeId(Mage::registry('entity_attribute')->getId());
            }

            $confAttr->setUseImage(intval(Mage::app()->getRequest()->getPost('amconf_useimages')));
            $confAttr->save();
        }
    }
    
    public function getUseImage()
    {
        $confAttr = Mage::getModel('amconf/attribute')->load(Mage::registry('entity_attribute')->getId(), 'attribute_id');
        return (boolean) $confAttr->getUseImage();
    }
    
    public function getOptionsCollection()
    {
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter(Mage::registry('entity_attribute')->getId())
                ->setPositionOrder('desc', true)
                ->load();
        return $optionCollection;
    }
    
    public function getIcon($option)
    {
        return Mage::helper('amconf')->getImageUrl($option->getId());
    }
    
    public function getSubmitUrl()
    {
        $url = Mage::helper('core/url')->getCurrentUrl();
        if (isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS'] && '' != $_SERVER['HTTPS'])
        {
            $url = str_replace('http:', 'https:', $url);
        }
        return $url;
    }
}
