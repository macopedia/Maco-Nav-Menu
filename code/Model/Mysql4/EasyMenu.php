<?php
class Macopedia_EasyMenu_Model_Mysql4_EasyMenu extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('EasyMenu/EasyMenu','id');
     }
}