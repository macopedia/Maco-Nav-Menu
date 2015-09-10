<?php
class Macopedia_EasyMenu_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Easymenu
     */
    public function indexAction()
    {
        $this->_initAction();

        $store = Mage::getModel('core/store')->load($this->_helper()->getStoreId());
        $rootCategoryId = $store->getRootCategoryId();
        /** @var Mage_Catalog_Model_Resource_Category_Collection $categories */
        $categories = Mage::getModel('catalog/category')
            ->getCollection()
            ->setStore($store)
            ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
            ->addFieldToFilter('is_active' ,array("in"=>array('1')))
            ->addAttributeToSelect('name')
            ->setOrder('path', "ASC");
        Mage::register('categories', $categories);

        $cms = Mage::getModel('cms/page')->getCollection()
            ->addStoreFilter($this->_helper()->getStoreId())
            ->load();

        Mage::register('pages',$cms);

        $this->renderLayout();
    }

    /**
     * Init actions
     *
     * @return Macopedia_Quarticon_Adminhtml_QuarticonController
     */
    protected function _initAction()
    {
        $this->_title($this->__('Macopedia'))->_title($this->__('Easymenu'));

        $this->loadLayout();

        return $this;
    }

    /**
     * init menu element
     *
     * @return Macopedia_EasyMenu_Model_EasyMenu
     */
    protected function _initMenuElement()
    {
        $elementId = (int) $this->getRequest()->getParam('id');
        $element = Mage::getModel('EasyMenu/EasyMenu')->load($elementId);
        return $element;
    }

    /**
     * Get element
     */
    public function getAction()
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $elementId = $this->getRequest()->getParam('id', 0);
        if ($elementId > 0) {
            $element = Mage::getModel('EasyMenu/EasyMenu')->load($elementId)->toArray();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($element));
        } else $this->getResponse()->setBody(null);
    }

    /**
     * Delete element
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id',0);
        $model = Mage::getModel('EasyMenu/EasyMenu');
        $element = $model->load($id);
        $parent = $element->getParent();
        if($id){
            $children = $model->getChildrenCategories($id,true);
            foreach($children as $child){
                $child->setParent($parent);
                $child->save();
            }
            $element->delete();
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->newTreeAction();
    }

    /**
     * Save menu element
     */
    public function saveAction()
    {
        $storeId = $this->getRequest()->getParam('store');
        if(!$storeId) {
            $storeId = $this->getDefaultStoreId();
        }
        $id = $this->getRequest()->getParam('id', 0);
        $name = $this->getRequest()->getParam('name');
        $parent = $this->getRequest()->getParam('parent');
        $type = $this->getRequest()->getParam('type');
        $value = $this->getRequest()->getParam('value');
        $priority = $this->getRequest()->getParam('priority');
        /** @var Macopedia_Easymenu_Model_EasyMenu $model */
        $model = Mage::getModel('EasyMenu/EasyMenu');

        if ($id) {
            $children = $model->getDescendantsCategories($id);
            $element = $model->load($id);
            $elementParent = $element->getParent();

            foreach ($children as $child) {
                if ($child['id'] == $parent) {
                    $child = $model->load($child['id']);
                    $child->setParent($elementParent);
                    $child->save();
                }
            }
            $element = $model->load($id);
        }
        else{
            $element = Mage::getModel('EasyMenu/EasyMenu');

        }

        $element->setName($name);
        $element->setType($type);
        $element->setValue($value);

        $element->setStore($storeId);

        if ($parent != $element->getId()) {
            $element->setParent($parent);
        }

        $element->save();

        if (!$id) {
            $element->setPriority($element->getId());
            $element->save();
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->newTreeAction();
    }

    /**
     * Move element
     */
    public function moveAction()
    {
        $menuItemId = (int) $this->getRequest()->getParam('id');
        /* @var $menuItem Macopedia_EasyMenu_Model_EasyMenu */
        $menuItem = $this->_initMenuElement();
        if (!$menuItem) {
            $this->getResponse()->setBody(Mage::helper('cms')->__('Page move error'));
            return;
        }

        /**
         * New parent page identifier
         */
        $parentNodeId   = $this->getRequest()->getPost('pid', false);
        /**
         * Page id after which we have put our page
         */
        $prevNodeId     = $this->getRequest()->getPost('aid', false);

        try {
            $menuItem->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        }
        catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        }
        catch (Exception $e){
            $this->getResponse()->setBody(Mage::helper('cms')->__('Page move error'.$e));
            Mage::logException($e);
        }
    }

    /**
     * Get new tree
     */
    public function newTreeAction()
    {
        $block = $this->getLayout()->createBlock('EasyMenu/adminhtml_easyMenu_tree');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                    'data' => $block->getNewTree(),
                    'parameters' => array(
                        'text'        => 'Root menu',
                        'draggable'   => false,
                        'allowDrop'   => true,
                        'id'          => 'menu-root',
                        'root_visible'=> true
                    ))));
    }

    /**
     * @return string
     */
    public function jsonTreeAction()
    {
        $jsonArray = $this->_helper()->getMenuTree();
        return Zend_Json::encode($jsonArray);
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

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('macopedia/easymenu');
    }
}