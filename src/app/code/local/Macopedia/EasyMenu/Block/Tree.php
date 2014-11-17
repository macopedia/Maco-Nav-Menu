<?php
class Macopedia_EasyMenu_Block_Tree extends Mage_Core_Block_Template
{
    const REGISTRY_KEY_ACTIVE_ELEMENT = 'find_active_element';

    /**
     * Set cache data
     */
    protected function _construct()
    {
        $this->addCacheTag(array(
            Mage_Catalog_Model_Category::CACHE_TAG,
            Mage_Core_Model_Store_Group::CACHE_TAG,
            Mage_Core_Model_Store::CACHE_TAG,
            Mage_Cms_Model_Page::CACHE_TAG
        ));
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheId = array(
            'MACOPEDIA_EASY_MENU',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
        );
        return $cacheId;
    }

    public function getRootCategories()
    {
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

    /**
     * @param int $type
     * @param mixed $value
     * @return string
     */
    protected function getLink($type, $value)
    {
        $url = '';
        if ($type == Macopedia_EasyMenu_Model_EasyMenu::NODE_TYPE_CATEGORY) {
            $url = Mage::getModel("catalog/category")->load($value)->getUrl();

        } elseif ($type == Macopedia_EasyMenu_Model_EasyMenu::NODE_TYPE_CMS) {
            $url = Mage::helper('cms/page')->getPageUrl($value);

        } elseif ($type == Macopedia_EasyMenu_Model_EasyMenu::NODE_TYPE_EXTERNAL) {
            $url = $value;
        }
        return $url;
    }

    /**
     * @param array $category
     * @param bool $admin
     * @return string
     */
    public function renderCategory($category, $admin = true)
    {
        $children = $this->getChildrenCategories($category['id']);
        $html = '';
        if ($admin) {
            $html .= '<li><span id="el-' . $category['id'] . '" onclick="getElement(' . $category['id'] . ',this);">' . $category['name'] . '</span>';
        } else {
            $url = $this->getLink($category['type'], $category['value']);
            if ($url) {

                $html .= '<li';

                if (count($children)) {
                    $html .= ' class="has-sublist"';
                }

                $html .= '>';

                $html .='<a ' . $this->getLinkClassAttribute($category, $url) . ' href="' .$url. '" id="el-' . $category['id'] . '">' . $category['name'] . '</a>';
            }
        }

        if (count($children)) $html .= '<ul id="children-of-' . $category['parent'] . '">';
        foreach ($children as $child) {
            $html .= $this->renderCategory($child, $admin);
        }
        if (count($children)) $html .= '</ul>';
        $html .= '</li>';
        return $html;
    }

    protected function getLinkClassAttribute($category, $url) {
        $attributes = array();
        $attributes[] = $this->getTypeClass($category);

        if ($this->isElementActive($url)) {
            $attributes[] = 'current-page';
        }

        return !empty($attributes)? 'class="' . implode(', ', $attributes) . '"' : '';
    }

    protected function getTypeClass($category) {
        switch ($category['type']) {
            case 1:
                return 'menu-item-category-' . $category['value'];
            case 2:
                return 'menu-item-cms-' . $category['value'];
            default:
                return 'menu-item-direct';
        }
    }

    /**
     * Check if element in menu is active
     *
     * @param $url
     *
     * @return bool
     */
    public function isElementActive($url)
    {
        //do not cache active state
        if (is_null($this->getCacheLifetime()) && $url === $this->getCurrentUrl()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        $params = array(
            '_nosid' => true,
            '_current' => true,
            '_use_rewrite' => true,
            '_store_to_url' => true
        );

        return Mage::getUrl('', $params);
    }
}