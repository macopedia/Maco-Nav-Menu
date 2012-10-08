<?php
class Macopedia_EasyMenu_Block_Menu extends Mage_Core_Block_Template
{

    public function test(){
        //die("test");
    }


    public function getStoreCategories(){
        return Mage::registry('categories');
    }

    public function getCmsPages(){
        return Mage::registry('pages');
    }
}