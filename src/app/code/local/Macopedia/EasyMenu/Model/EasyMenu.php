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
        $rootCategories = $collection->addFilter('parent', 0)->setOrder('priority','asc')->getData();
        return $rootCategories;
    }

    public function getDescendantsCategories($catId)
    {
        $collection = $this->getCollection();
        $childrenCategories = $collection->addFilter('parent', $catId)->setOrder('priority','asc')->getData();
        foreach($childrenCategories as $child)
            foreach($this->getDescendantsCategories($child['id']) as $element)
                array_push($childrenCategories,$element);
        return $childrenCategories;
    }

    public function getChildrenCategories($catId,$object = false)
    {
        $collection = $this->getCollection();

        if(!$object) {
            $childrenCategories = $collection->addFilter('parent', $catId)->setOrder('priority','asc')->getData();
        } else {
            $childrenCategories = $collection->addFilter('parent', $catId);
        }

        return $childrenCategories;
    }
}