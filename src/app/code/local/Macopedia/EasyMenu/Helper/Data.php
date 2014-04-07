<?php

class Macopedia_EasyMenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('easymenu/options/enable');
    }
}