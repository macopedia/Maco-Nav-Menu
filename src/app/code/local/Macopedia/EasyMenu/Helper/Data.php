<?php

class Macopedia_EasyMenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('easymenu/options/enable');
    }

    /**
     * Get store id from request
     *
     * @return mixed
     */
    public function getStoreId()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $storeId = Mage::app()->getRequest()->getParam('store');
            if (!$storeId) {
                $storeId = $this->getDefaultStoreId();
            }
        } else {
            $storeId = Mage::app()->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Get default menu
     *
     * @return mixed
     */
    public function getDefaultStoreId()
    {
        return Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();
    }

    /**
     * Get menu tree
     *
     * @return array
     */
    public function getMenuTree()
    {
        $menuCollection = Mage::getModel('EasyMenu/EasyMenu')->getCollection()->addFieldToFilter(
                'store', array('eq' => $this->getStoreId())
            )->addFieldToFilter(
                'parent', array('eq' => 0)
            );

        $menuCollection->setOrder('priority', 'asc');

        $jsonArray = array();

        foreach ($menuCollection as $item) {
            /* @var $item Macopedia_EasyMenu_Model_EasyMenu */
            if ($item->getParent() == 0) {

                $jsonElement = $this->prepareItem($item);

                $jsonChildren = $this->prepareChildren($item);
                if (!empty($jsonChildren)) {
                    $jsonElement['children'] = $this->prepareChildren($item);
                }

                $jsonArray[] = $jsonElement;
            }

        }

        return $jsonArray;
    }

    /**
     * Prepare children
     *
     * @param $item Macopedia_EasyMenu_Model_EasyMenu
     *
     * @return array
     */
    protected function prepareChildren($item)
    {
        /* @var $model Macopedia_EasyMenu_Model_EasyMenu */
        $model = Mage::getModel('EasyMenu/EasyMenu');

        $children = $model->getChildrenCategories($item->getId(), true);

        $jsonChildren = array();
        foreach ($children as $child) {
            $json = $this->prepareItem($child);
            $children = $this->prepareChildren($child);
            if (!empty($children)) {
                $json['children'] = $this->prepareChildren($child);
            }
            $jsonChildren[] = $json;
        }

        return $jsonChildren;
    }

    /**
     * @param $item Macopedia_EasyMenu_Model_EasyMenu
     *
     * @return array
     */
    protected function prepareItem($item)
    {
        return array('text'  => $item->getName(), 'id' => $item->getId(), 'parent' => $item->getParent(),
                     'value' => $item->getValue(), 'priority' => $item->getPriority(), 'cls' => 'folder',
                     'store' => $item->getStore());
    }

}