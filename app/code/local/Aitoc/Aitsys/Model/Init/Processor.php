<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_Init_Processor
{
    // this class stays here to prevent crashes after upgrade to 2.20.2
    // this class will not be used after the deletion of App.php
    public function isInstallerEnabled()
    {
        return false;
    }
}