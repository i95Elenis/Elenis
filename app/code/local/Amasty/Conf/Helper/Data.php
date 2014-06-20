<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Conf
*/
class Amasty_Conf_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_NOIMG_IMG            = 'amconf/general/noimage_img';
    const XML_PATH_USE_SIMPLE_PRICE     = 'amconf/general/use_simple_price';
    const XML_PATH_OPTIONS_IMAGE_SIZE   = 'amconf/list/listimg_size';
    
    protected $onClick;
    
    protected $amConf;
    
    public function getImageUrl($optionId)
    {
        $uploadDir = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 
                                                    'amconf' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        if (file_exists($uploadDir . $optionId . '.jpg'))
        {
            return Mage::getBaseUrl('media') . 'amconf' . '/' . 'images' . '/' . $optionId . '.jpg';
        }
        return '';
    }
    
    public function getNoimgImgUrl()
    {
        if (Mage::getStoreConfig(self::XML_PATH_NOIMG_IMG))
        {
            return Mage::getBaseUrl('media') . 'amconf/noimg/' . Mage::getStoreConfig(self::XML_PATH_NOIMG_IMG);
        }
        return '';
    }
    
    public function getConfigUseSimplePrice()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_SIMPLE_PRICE);
    } 
    
    public function getOptionsImageSize()
    {
        return Mage::getStoreConfig(self::XML_PATH_OPTIONS_IMAGE_SIZE);
    } 
    
    public function getAmconfAttr()
    {
        return $this->amConf;
    } 
    
    public function getHtmlBlock($_product, $html)
    {
        $blockForForm = Mage::app()->getLayout()->createBlock('amconf/catalog_product_view_type_configurable', 'amconf.catalog_product_view_type_configurable', array('template'=>"amconf/configurable.phtml"));
        $blockForForm->setProduct($_product);
        $blockForForm->setNameInLayout('product.info.options.configurable');
        $attributes = $blockForForm->getAttributes();
        $submitUrl = $blockForForm->getSubmitUrl($_product);
        $onClick = "formSubmit(this,'".$submitUrl."', '".$_product->getId()."', ".$attributes.")";
        $amConf = "createForm('".$submitUrl."', '".$_product->getId()."', ".$attributes.")";
        $html .= '<div id="insert" style="display: none;"></div>' . $blockForForm->toHtml() . '</div><div class="actions">
                  <button type="button" title="' . $this->__('Add to Cart') . '" class="button btn-cart" onclick="' . $onClick . '"  amconf="' . $amConf . '">
                        <span>
                            <span>'.$this->__('Add to Cart').'</span>
                        </span>
                  </button>' ;
        return $html;
    }
}
