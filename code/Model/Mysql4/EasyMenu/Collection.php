<?php
class Macopedia_EasyMenu_Model_Mysql4_EasyMenu_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
 {
     public function _construct()
     {
         parent::_construct();
         $this->_init('EasyMenu/EasyMenu');
     }
}