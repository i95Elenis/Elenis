<?php
class Webdziner_Ajaxsearch_Block_Ajaxsearch extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function _toHtml()
     {
	 	$objModel = Mage::getModel('ajaxsearch/ajaxsearch')->load(1);
		$_productCollection = Mage::getModel('catalog/product')->getCollection();
		$_productCollection->addAttributeToSelect('price');
		$_productCollection->addAttributeToFilter('status',1);
		$_productCollection->addAttributeToFilter('visibility',array("neq"=>1));
		
		if( $objModel->getData('no_of_product') != '' && is_numeric($objModel->getData('no_of_product')) )
			$_productCollection->setPageSize($objModel->getData('no_of_product'));

		if($objModel->getData('show_thumbnail'))
			$_productCollection->addAttributeToSelect('thumbnail');

		$no_short_description_char = $objModel->getData('no_short_description_char');

		if($objModel->getData('short_description')):
			$_productCollection->addAttributeToSelect('short_description');
		else:
			$no_short_description_char = 0;
		endif;

		$_productCollection->addAttributeToFilter(
                array(
                    array('attribute'=>'name', 'like'=>'%'.$this->getRequest()->getParam('q').'%'),
                    array('attribute'=>'description', 'like'=>'%'.$this->getRequest()->getParam('q').'%')
                )
            );
		
		$searchResultHtml = '<ul>';
		$count=0;
		foreach($_productCollection as $product):
			$count++;
			//$searchResultHtml .= '<li class="ajaxsearch_item" onclick="window.location=\''.$product->getProductUrl().'\'"><a href="'.$product->getProductUrl().'">';
			$searchResultHtml .= '<li class="ajaxsearch_item" onclick="window.location=\''.$product->getProductUrl().'\'">';

			$searchResultHtml .= '<div>';
			
			if($objModel->getData('show_thumbnail')){
				$searchResultHtml .= '<p class="product_img"><img src="'.$this->helper('catalog/image')->init($product, 'thumbnail')->keepAspectRatio(TRUE)->resize(50,50).'"></p>';
			}
			
			$searchResultHtml .= '<p class="product_info"><strong>'.$product->getName().'</strong>';
			
			if($no_short_description_char > 0){
				$searchResultHtml .= '<br><span class="ajaxsearch-desc">'.substr($product->getShortDescription(),0,$no_short_description_char).'  ...</span><br><span class="as-price">'.Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().number_format($product->getPrice(),2).'</span>';
			}

			$searchResultHtml .= '</p>';
			$searchResultHtml .='</div>';
			
			$searchResultHtml .= '</li>';
			
			if($objModel->getData('no_of_product') == $count)
				break;
			//if($count == 4)
				//break;
		endforeach;
		
		if(count($_productCollection) == 0):
			$searchResultHtml .= '<li class="no-result">Sorry!!! no product found</li>';
		else:
			$searchResultHtml .= '<li class="ajaxsearch_more"><a href="'.$this->getUrl('').'catalogsearch/result/?q='.$this->getRequest()->getParam('q').'">more result...</li></a>';
		endif;
		$searchResultHtml .= '</ul><div class="clear"></div>';

        return $searchResultHtml;
        
    }
}