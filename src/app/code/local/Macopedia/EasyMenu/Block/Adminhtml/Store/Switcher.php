<?php

class Macopedia_EasyMenu_Block_Adminhtml_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('store/switcher.phtml');
        $this->setDefaultStoreName($this->__('Default'));
    }
}
