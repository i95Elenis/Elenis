<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Conf
*/
class Amasty_Conf_Model_Observer 
{
    public function onListBlockHtmlBefore($observer)//core_block_abstract_to_html_after    
    {                 
      if (($observer->getBlock() instanceof Mage_Catalog_Block_Product_List) && Mage::getStoreConfig('amconf/list/enable_list')) {
          $html = $observer->getTransport()->getHtml();
          preg_match_all("/product-price-([0-9]+)/", $html, $productsId) ;
          if(!$productsId[0]){
               preg_match_all("/price-including-tax-([0-9]+)/", $html, $productsId) ;
          }
          Mage::register('isList', 1);
          foreach ($productsId[1] as $key => $productId){  
              $_product = Mage::getModel('catalog/product')->load($productId);
              // @see Mage_Catalog_Block_Product_Abstract::getProduct()
              if (!is_null(Mage::registry('product')))
              {
                Mage::unregister('product');
              }
              Mage::register('product', $_product);
              if($_product->isSaleable() && $_product->isConfigurable()){
                    $template = '@(product-price-'.$productId.'">(.*?))</div>(.*?)/button>@s';
                    preg_match_all($template, $html, $res);
                    if(!$res[0]){
                         $template = '@(price-including-tax-'.$productId.'">(.*?))</div>(.*?)/button>@s';
                         preg_match_all($template, $html, $res);
                         if(!$res[0]){
                             $template = '@(price-excluding-tax-'.$productId.'">(.*?))</div>(.*?)/button>@s';
                             preg_match_all($template, $html, $res);
                        }
                    }
                    if($res[0]){
                         $replace =  Mage::helper('amconf')->getHtmlBlock($_product, $res[1][0]);
                         $html= str_replace($res[0][0], $replace, $html);
                    }
              }
          }
          $observer->getTransport()->setHtml($html);
      }
    }
    
    protected function getSuperProductAttributesJS($product_id){
        $collection = Mage::getModel('amconf/product_attribute')->getCollection();
        
        $collection->getSelect()->join( array(
            'prodcut_super_attr' => $collection->getTable('catalog/product_super_attribute')),
                'main_table.product_super_attribute_id = prodcut_super_attr.product_super_attribute_id', 
                array('prodcut_super_attr.product_id')
            );
        
        $collection->addFieldToFilter('prodcut_super_attr.product_id', $product_id);

        $attributes = $collection->getItems();
        
        $ids = array();

        foreach($attributes as $attribute){
            if ($attribute->getUseImageFromProduct()){
                $ids[] = $attribute->getProductSuperAttributeId();
            }
        }

        $js = '<script>
                Event.observe(window, \'load\', function(){
                    var ids = '.  Zend_Json::encode($ids).';
                    checkUseImageProducts(ids);
                })
            </script>
        ';
        
        return $js;
    }
    
    public function onSuperProductAttributesConfigurationAfter($observer) {//core_block_abstract_to_html_after   
        if (($observer->getBlock() instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config)){
            $html = $observer->getTransport()->getHtml();
            $product_id = NULL;
            preg_match("/product\/([0-9]+)\//", $html, $productsId) ;
            if (isset($productsId[1])){
                
                $product_id = $productsId[1];

                $from = '<label for="__id___label_use_default">';
                $to = '</label>';

                $fromInd = strpos($html, $from);
                $toInd = strpos($html, $to, $fromInd) + strlen($to);

                $str = substr($html, $fromInd, $toInd - $fromInd);

                $controls = '&nbsp;&nbsp;&nbsp;&nbsp;<input onclick=\'return onUseImageProductClick(this);\' type=\'checkbox\' rel="use_image_from_product" id="__id___use_image_from_product" />&nbsp;'.
                        '<label for="__id___use_image_from_product">'. Mage::helper('amconf')->__('Use image from product') .'</label>';

                $html = str_replace($str, $str.$controls, $html);

                $html .= $this->getSuperProductAttributesJS($product_id);

                $observer->getTransport()->setHtml($html);
            }
        }
    }
    
    public function onSuperProductAttributesPrepareSave($observer){
        
        $configurable_attributes_data = Mage::helper('core')->jsonDecode($observer->getRequest()->getPost('configurable_attributes_data'));
        if (is_array($configurable_attributes_data)){
            foreach($configurable_attributes_data as $attribute){
                
                if ($attribute['id'] !== NULL){
                    $confAttr = Mage::getModel('amconf/product_attribute')->load($attribute['id'], 'product_super_attribute_id');

                    if (!$confAttr->getId())
                    {
                        $confAttr->setProductSuperAttributeId($attribute['id']);
                    }
                    $use_image_from_product  = isset($attribute['use_image_from_product']) ? intval($attribute['use_image_from_product']) : 0;

                    $confAttr->setUseImageFromProduct($use_image_from_product);
                    $confAttr->save();
                }
            }
        }
        
    }    
}
