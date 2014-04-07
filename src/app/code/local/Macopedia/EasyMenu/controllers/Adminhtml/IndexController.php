<?php
class Macopedia_EasyMenu_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')->load();
        $cms = Mage::getModel('cms/page')->getCollection()->load();
        Mage::register('pages',$cms);
        Mage::register('categories', $categories);
        $this->renderLayout();
    }

    public function getAction()
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $elementId = $this->getRequest()->getParam('id', 0);
        if ($elementId > 0) {
            $element = Mage::getModel('EasyMenu/EasyMenu')->load($elementId)->toArray();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($element));
        } else $this->getResponse()->setBody(null);
    }

    public function deleteAction(){
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
        $this->getResponse()->setBody(json_encode($this->getTree()));
    }

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
        $model = Mage::getModel('EasyMenu/EasyMenu');

        if ($id) {
            $children = $model->getDescendantsCategories($id);
            $element = $model->load($id);
            $elementParent = $element->getParent();
            //var_dump($elementParent);
            foreach ($children as $child) {
                //var_dump($child['id'].' '.$parent);
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
        $element->setPriority($priority);
        $element->setStore($storeId);

        if ($parent != $element->getId()) {
            $element->setParent($parent);
        }

        $element->save();

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($this->getTree()));
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

    public function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store');
        if(!$storeId) {
            $storeId = $this->getDefaultStoreId();
        }
        return $storeId;
    }

    public function treeAction()
    {
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($this->getTree()));
    }

    private function getTree()
    {
        $menuCollection = Mage::getModel('EasyMenu/EasyMenu')
            ->getCollection()->addFieldToFilter('store', array(
                'eq' => $this->getStoreId()
            ));
        $data['elements'] = $menuCollection->getData();
        $data['html'] = $this->getLayout()->createBlock(
            'EasyMenu/Tree',
            'Tree',
            array('template' => 'macopedia/easymenu/tree.phtml')
        )->toHtml();
        return $data;
    }

}