<?php

/**
 *
 * @method string getName()
 * @method int getParent()
 * @method int getId()
 * @method int getValue()
 * @method string getPriority()
 * @method int getStore()
 */
class Macopedia_EasyMenu_Model_EasyMenu extends Mage_Core_Model_Abstract
{
    const NODE_TYPE_CATEGORY = 1;
    const NODE_TYPE_CMS = 2;
    const NODE_TYPE_EXTERNAL = 3;

    /**
     * construct
     */
    public function _construct()
     {
         parent::_construct();
         $this->_init('EasyMenu/EasyMenu');
     }

    /**
     * Get root categories
     *
     * @return array
     */
    public function getRootCategories()
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('store', array(
            'eq' => $this->_helper()->getStoreId()
        ));
        $rootCategories = $collection->addFilter('parent', 0)->setOrder('priority','asc')->getData();
        return $rootCategories;
    }

    public function getDescendantsCategories($catId)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('store', array(
                'eq' => $this->_helper()->getStoreId()
            ));
        $childrenCategories = $collection->addFilter('parent', $catId)
            ->setOrder('priority','asc')->getData();
        foreach($childrenCategories as $child)
            foreach($this->getDescendantsCategories($child['id']) as $element)
                array_push($childrenCategories,$element);
        return $childrenCategories;
    }

    /**
     * @param $catId
     * @param bool $object
     *
     * @return mixed
     */
    public function getChildrenCategories($catId,$object = false)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('store', array(
                'eq' => $this->_helper()->getStoreId()
            ))
            ->setOrder('priority','asc');

        if(!$object) {
            $childrenCategories = $collection->addFilter('parent', $catId)->setOrder('priority','asc')->getData();
        } else {
            $childrenCategories = $collection->addFilter('parent', $catId);
        }

        return $childrenCategories;
    }

    /**
     * @param $parentId
     * @param $afterMenuItemId
     *
     * @return $this
     * @throws Exception
     */
    public function move($parentId, $afterMenuItemId)
    {

        $parent = Mage::getModel('EasyMenu/EasyMenu')->load($parentId);

        $this->_getResource()->beginTransaction();
        try {

            $this->getResource()->changeParent($this, $parent, $afterMenuItemId);

            $this->_getResource()->commit();

            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }

        return $this;
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