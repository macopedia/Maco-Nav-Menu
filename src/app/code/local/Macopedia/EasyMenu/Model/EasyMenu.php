<?php
class Macopedia_EasyMenu_Model_EasyMenu extends Mage_Core_Model_Abstract
{
     public function _construct()
     {
         parent::_construct();
         $this->_init('EasyMenu/EasyMenu');
     }

    public function getRootCategories()
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('store', array(
            'eq' => $this->getStoreId()
        ));
        $rootCategories = $collection->addFilter('parent', 0)->setOrder('priority','asc')->getData();
        return $rootCategories;
    }

    public function getDescendantsCategories($catId)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('store', array(
                'eq' => $this->getStoreId()
            ));
        $childrenCategories = $collection->addFilter('parent', $catId)->setOrder('priority','asc')->getData();
        foreach($childrenCategories as $child)
            foreach($this->getDescendantsCategories($child['id']) as $element)
                array_push($childrenCategories,$element);
        return $childrenCategories;
    }

    public function getChildrenCategories($catId,$object = false)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('store', array(
                'eq' => $this->getStoreId()
            ));

        if(!$object) {
            $childrenCategories = $collection->addFilter('parent', $catId)->setOrder('priority','asc')->getData();
        } else {
            $childrenCategories = $collection->addFilter('parent', $catId);
        }

        return $childrenCategories;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        if(Mage::app()->getStore()->isAdmin()) {
            $storeId = Mage::app()->getRequest()->getParam('store');
            if(!$storeId) {
                $storeId = $this->getDefaultStoreId();
            }
        } else {
            $storeId = Mage::app()->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * @return mixed
     */
    public function getDefaultStoreId()
    {
        return Mage::app()
            ->getWebsite(true)
            ->getDefaultGroup()
            ->getDefaultStoreId();
    }
}