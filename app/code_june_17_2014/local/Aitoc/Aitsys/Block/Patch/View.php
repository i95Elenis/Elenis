<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Block_Patch_View extends Mage_Adminhtml_Block_Abstract
{
    protected $_collectedFiles = array();
    protected $_filesNumber = 0;

    protected function _construct()
    {
        $pathes = array();
        $designDir = Mage::getBaseDir('design');
        $source = array(
            'admin' => $designDir.DS.'adminhtml'.DS ,
            'front' => $designDir.DS.'frontend'.DS
        );
        foreach ($source as $type => $src)
        {
            $paths = glob($src.'*');
            if ($paths)
            {
                foreach ($paths as $path)
                {
                    $package = pathinfo($path,PATHINFO_FILENAME);
                    $paths = glob($path.DS.'*');
                    if ($paths)
                    {
                        foreach ($paths as $path)
                        {
                            $theme = pathinfo($path,PATHINFO_FILENAME);
                            $tmp = $path.DS.'template'.DS.'aitcommonfiles'.DS;
                            if (!isset($pathes[$type][$package][$theme]))
                            {
                                $pathes[$type][$package][$theme] = array();
                            }
                            $tmps = glob($tmp.'*');
                            if ($tmps)
                            {
                                foreach ($tmps as $file)
                                {
                                    $pathes[$type][$package][$theme][] = $file;
                                    $this->_filesNumber++;
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->_collectedFiles = $pathes;
    }
    
    protected function _getPatchesDir()
    {
        return Aitoc_Aitsys_Model_Platform::getInstance()->getVarPath() . Aitoc_Aitsys_Model_Aitpatch::PATCH_DIR;
    }
    
    public function getAitcommonThemes()
    {
        return $this->_collectedFiles;
    }
    
    public function getFilesNumber()
    {
        return $this->_filesNumber;
    }
}