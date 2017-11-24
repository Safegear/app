<?php
namespace Zenon\Safes\Model\ResourceModel\Classification;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = \Zenon\Safes\Model\Classification::CLASSIFICATION_ID;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Zenon\Safes\Model\Classification', 'Zenon\Safes\Model\ResourceModel\Classification');
    }

}