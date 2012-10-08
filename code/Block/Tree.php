<?php
class Macopedia_EasyMenu_Block_Tree extends Mage_Core_Block_Template
{

    public function getRootCategories(){
        return Mage::getModel('EasyMenu/EasyMenu')->getRootCategories();
    }

    public function getDescendantsCategories($catId)
    {
        return Mage::getModel('EasyMenu/EasyMenu')->getDescendantsCategories($catId);
    }

    public function getChildrenCategories($catId)
    {
        return Mage::getModel('EasyMenu/EasyMenu')->getChildrenCategories($catId);
    }

    public function renderCategory($category)
    {
        $children = $this->getChildrenCategories($category['id']);
        $html = '<li class="x-tree-node"><span id="el-'.$category['id'].'" onclick="getElement(' . $category['id'] . ',this);">' . $category['name'] . '</span>';
        if (count($children)) $html .= '<ul>';
        foreach ($children as $child) {
            $html .= $this->renderCategory($child);
        }
        if (count($children)) $html .= '</ul>';
        $html .= '</li>';
        return $html;
    }

}