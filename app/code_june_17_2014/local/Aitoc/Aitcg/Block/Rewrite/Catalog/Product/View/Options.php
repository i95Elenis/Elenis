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
class Aitoc_Aitcg_Block_Rewrite_Catalog_Product_View_Options extends Mage_Catalog_Block_Product_View_Options
{
    /**
     * Get option html block
     *
     * @param Mage_Catalog_Model_Product_Option $option
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        if(! Mage::helper('aitcg/options')->checkAitOption( $option ) || $option->getData("image_template_id") == 0)
        {
            return parent::getOptionHtml( $option );            
        }
        
        //forsing to change file-group template to our own
        $renderer = $this->getOptionRender( 'cgfile' );
        
        //PairedProduct forse add own options
        if($renderer['block'] !='aitcg/catalog_product_view_options_type_file') {
            $renderer['block'] = 'aitcg/catalog_product_view_options_type_file';
            $renderer['template'] = 'aitcg/view/options/type/cgfile.phtml';
        } 
        
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate( $renderer['template'] );
        }
                
        return $renderer['renderer']
            ->setProduct($this->getProduct())
            ->setOption($option)
            ->toHtml();     
    }
    
}