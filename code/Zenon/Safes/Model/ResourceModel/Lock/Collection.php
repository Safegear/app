<?php
namespace Zenon\Safes\Model\ResourceModel\Lock;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = \Zenon\Safes\Model\Lock::LOCK_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Zenon\Safes\Model\Lock', 'Zenon\Safes\Model\ResourceModel\Lock');
    }

}