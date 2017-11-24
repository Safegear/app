<?php
namespace Zenon\Safes\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Lock post mysql resource
 */
class Lock extends AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        // Table Name and Primary Key column
        $this->_init('zenon_lock', 'entity_id');
    }

}