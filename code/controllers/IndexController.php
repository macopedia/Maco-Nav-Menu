<?php

class Macopedia_EasyMenu_IndexController extends Mage_Core_Controller_Front_Action
{
   	public function indexAction(){
   		//$this->loadLayout();
        $collection = Mage::getModel('EasyMenu/EasyMenu')->getCollection();
        echo '<pre>';
        foreach($collection as $data){
            var_dump($data->getData());
        }
        var_dump($collection);
        //$this->renderLayout();
   	}
}