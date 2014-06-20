<?php   
class Elenis_VideoLink_Block_Index extends Mage_Core_Block_Template{   

public function getVideoData()
{
    $videoCollection=Mage::getSingleton('videolink/videolink')->getCollection();
    return $videoCollection->getData();
}
        



}