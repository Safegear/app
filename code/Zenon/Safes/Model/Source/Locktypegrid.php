<?php
namespace Zenon\Safes\Model\Source;

use Magento\Eav\Model\Config;

class Locktypegrid implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Zenon\Safes\Model\Lock
     */
    protected $_lock;

    protected $_modelConfig;

    /**
     * Constructor
     *
     * @param \Zenon\Safes\Model\Lock $locktype
     */
    public function __construct(
        \Zenon\Safes\Model\Lock $lock,
        Config $modelConfig
    ) {
        $this->_lock = $lock;
        $this->_modelConfig = $modelConfig;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $attribute = $this->_modelConfig->getAttribute('catalog_product', 'lock_type');
        $locktypeCollection = $attribute->getSource()->getAllOptions();
        //$locktypeCollection = $this->_lock->getCollection()->addFieldToSelect('entity_id')->addFieldToSelect('name');
        foreach ($locktypeCollection as $locktype) {
            $options[] = [
                'label' => $locktype['label'],
                'value' => $locktype['value'],
            ];
        }
        return $options;
    }
}