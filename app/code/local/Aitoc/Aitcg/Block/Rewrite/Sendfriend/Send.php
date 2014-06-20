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
/**
* @copyright  Copyright (c) 2012 AITOC, Inc. 
*/

class Aitoc_Aitcg_Block_Rewrite_Sendfriend_Send extends Mage_Sendfriend_Block_Send
{
    /**
     * overwrite parent
     * enables sending custom images to a friend
     */
    public function getSendUrl()
    {
        $aitcgSharedImgId = $this->getRequest()->getParam('aitcg_shared_img_id');
        if(isset($aitcgSharedImgId))
        {
            return Mage::getUrl('*/*/sendmail', array(
                'id'     => $this->getProductId(),
                'cat_id' => $this->getCategoryId(),
                'aitcg_shared_img_id' => $aitcgSharedImgId
                ));
        }

        return parent::getSendUrl();
    }

    /**
     * overwrite parent
     * enables sending custom images to a friend
     */
    public function getUrl($route = '', $params = array())
    {
        $aitcgSharedImgId = $this->getRequest()->getParam('aitcg_shared_img_id');
        if(isset($aitcgSharedImgId) && $route == '*/*/sendmail')
        {
            $params['aitcg_shared_img_id'] = $aitcgSharedImgId;
            return Mage::getUrl('*/*/sendmail', $params);
        }

        return parent::getUrl($route, $params);
    }
}