<?php
namespace Zenon\Safes\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Classification post mysql resource
 */
class Classification extends AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        // Table Name and Primary Key column
        $this->_init('zenon_classification', 'entity_id');
    }

}