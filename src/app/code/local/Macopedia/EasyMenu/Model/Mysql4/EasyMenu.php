<?php

class Macopedia_EasyMenu_Model_Mysql4_EasyMenu extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('EasyMenu/EasyMenu', 'id');
    }

    /**
     * Change parent
     *
     * @param      $menuItem
     * @param      $newParent
     * @param null $afterMenuItemId
     *
     * @return $this
     */
    public function changeParent($menuItem, $newParent, $afterMenuItemId = null)
    {
        $table = $this->getMenuTable();
        $adapter = $this->_getWriteAdapter();

        $priority = $this->_processPriority($menuItem, $newParent, $afterMenuItemId);

        /**
         * Update moved page data
         */
        if (is_null($newParent)) {
            $data = array('parent' => 0);
        } else {
            $data = array('parent' => $newParent->getId(), 'priority' => $priority);
        }

        $adapter->update($table, $data, $adapter->quoteInto('id=?', $menuItem->getId()));

        // Update page object to new data
        $menuItem->addData($data);

        return $this;
    }

    /**
     * process menu item priority
     *
     * @param $menuItem
     * @param $newParent
     * @param $afterMenuItemId
     *
     * @return int|string
     */
    protected function _processPriority($menuItem, $newParent, $afterMenuItemId)
    {
        $table = $this->getMenuTable();
        $adapter = $this->_getWriteAdapter();

        $sql = "UPDATE {$table} SET `priority`=`priority`-1 WHERE " . $adapter->quoteInto(
                'parent=? AND ', $menuItem->getParent()
            ) . $adapter->quoteInto('priority>?', $menuItem->getPriority());
        $adapter->query($sql);

        /**
         * Prepare priority value
         */
        if ($afterMenuItemId) {
            $sql = "SELECT `priority` FROM {$table} WHERE id=?";
            $priority = $adapter->fetchOne($adapter->quoteInto($sql, $afterMenuItemId));

            $sql = "UPDATE {$table} SET `priority`=`priority`+1 WHERE " . $adapter->quoteInto(
                    'parent=? AND ', $newParent->getId()
                ) . $adapter->quoteInto('priority>?', $priority);
            $adapter->query($sql);
        } elseif ($afterMenuItemId !== null) {
            $priority = 0;
            $sql = "UPDATE {$table} SET `priority`=`priority`+1 WHERE " . $adapter->quoteInto(
                    'parent=? AND ', $newParent->getId()
                ) . $adapter->quoteInto('priority>?', $priority);
            $adapter->query($sql);
        } else {
            $sql = "SELECT MIN(`priority`) FROM {$table} WHERE parent=?";
            $priority = $adapter->fetchOne($adapter->quoteInto($sql, $newParent->getId()));
        }
        $priority += 1;

        return $priority;
    }

    /**
     * @return string
     */
    public function getMenuTable()
    {
        return $this->getTable('EasyMenu/EasyMenu');
    }
}