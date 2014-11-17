<?php
class Macopedia_EasyMenu_Block_Menu extends Mage_Core_Block_Template
{
    /**
     * @return mixed
     */
    public function getStoreCategories()
    {
        return Mage::registry('categories');
    }

    /**
     * @return mixed
     */
    public function getCmsPages()
    {
        return Mage::registry('pages');
    }

    /**
     * Save element url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return Mage::getUrl('easymenu/adminhtml_index/save/',
            array(
                'store' => $this->_helper()->getStoreId(),
            )
        );
    }

    /**
     * Delete element url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return Mage::getUrl('easymenu/adminhtml_index/delete/',
            array(
                'store' => $this->_helper()->getStoreId(),
            )
        );
    }

    /**
     * Json source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        return $this->getUrl('*/*/jsonTree');
    }

    /**
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('*/*/move',
            array('store'=>$this->getRequest()->getParam('store')));
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
     * Helper
     *
     * @return Macopedia_EasyMenu_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('EasyMenu');
    }

    public function getPathWithName($cat)
    {
        $category = Mage::getModel('catalog/category')->load($cat->getId());
        $coll = $category->getResourceCollection();
        $pathIds = $category->getPathIds();
        $coll->addAttributeToSelect('name');
        $coll->addAttributeToFilter('entity_id', array('in' => $pathIds));
        $result = '';
        foreach ($coll as $singleCat) {
            $result .= $singleCat->getName().'/';
        }
        return $result;
    }
}