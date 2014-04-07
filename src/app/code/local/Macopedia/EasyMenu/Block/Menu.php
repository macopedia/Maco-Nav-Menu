<?php
class Macopedia_EasyMenu_Block_Menu extends Mage_Core_Block_Template
{

    public function getStoreCategories()
    {
        return Mage::registry('categories');
    }

    public function getCmsPages()
    {
        return Mage::registry('pages');
    }

    public function getStoreId()
    {
        return Mage::app()->getRequest()->getParam('store');
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('easymenu/adminhtml_index/save/',
            array(
                'store' => $this->getStoreId(),
            )
        );
    }

    public function getElementUrl()
    {
        return Mage::getUrl('easymenu/adminhtml_index/get/',
            array(
                'store' => $this->getStoreId(),
            )
        );
    }

    public function getDeleteUrl()
    {
        return Mage::getUrl('easymenu/adminhtml_index/delete/',
            array(
                'store' => $this->getStoreId(),
            )
        );
    }

    public function getTreeUrl()
    {
        return Mage::getUrl('easymenu/adminhtml_index/tree/',
            array(
                'store' => $this->getStoreId(),
            )
        );
    }
}