<?php

class Macopedia_EasyMenu_Block_Adminhtml_EasyMenu_Tree extends Mage_Adminhtml_Block_Template
{
    /**
     * Get menu json tree
     *
     * @return string
     */
    public function getTreeJson()
    {
        $jsonArray = $this->_getNodeJson();
        return Zend_Json::encode($jsonArray);
    }

    /**
     * @return array
     */
    protected function _getNodeJson()
    {
        return $this->_helper()->getMenuTree();
    }

    /**
     * Get json tree
     *
     * @return array
     */
    public function getNewTree()
    {
        return $this->_getNodeJson();
    }

    /**
     * Get new tree
     *
     * @return string
     */
    public function getNewTreeUrl()
    {
        return $this->getUrl('*/*/newTree',
            array('store'=>$this->getRequest()->getParam('store')));
    }

    /**
     * Json source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        return $this->getUrl('*/*/jsonTree',
            array('store'=>$this->getRequest()->getParam('store')));
    }

    /**
     * Get move url
     *
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/*/move',
            array('store'=>$this->getRequest()->getParam('store')));
    }

    /**
     * Get edit url
     *
     * @return string
     */
    public function getEditUrl()
    {
        return Mage::getUrl('*/*/get/',
            array(
                'store' => $this->_helper()->getStoreId(),
            )
        );
    }

    /**
     * Helper
     *
     * @return Macopedia_EasyMenu_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('EasyMenu');
    }
}
