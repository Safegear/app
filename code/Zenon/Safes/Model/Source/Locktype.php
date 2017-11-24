<?php
namespace Zenon\Safes\Model\Source;

use Magento\Eav\Model\Config;

class Locktype implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Zenon\Safes\Model\Lock
     */
    protected $_lock;

    protected $_modelConfig;

    protected $_coreRegistry;

    /**
     * Constructor
     *
     * @param \Zenon\Safes\Model\Lock $locktype
     */
    public function __construct(
        \Zenon\Safes\Model\Lock $lock,
        \Magento\Framework\Registry $registry,
        Config $modelConfig
    ) {
        $this->_lock = $lock;
        $this->_coreRegistry = $registry;
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

        $lockCollections = $this->_lock->getCollection();
        $collection = $lockCollections->getData();
        $lockCollection = array();
        foreach ($collection as $item):
            $lockCollection[] = $item['option_id'];
        endforeach;

        $model = $this->_coreRegistry->registry('safes_lock');

        foreach ($locktypeCollection as $locktype) {
            $optionId = $locktype['value'];
            if ($model->getId()):
                $locktypeEditId = $model->getId();
                foreach ($collection as $item):
                    $itemId = $item['entity_id'];
                    if($itemId == $locktypeEditId):
                        $itemOptionId = $item['option_id'];
                        if($itemOptionId == $optionId):
                            $options[] = [
                                'label' => $locktype['label'],
                                'value' => $locktype['value'],
                            ];
                        endif;
                    endif;
                endforeach;
            else:
                if (!in_array($optionId, $lockCollection)):
                    $options[] = [
                        'label' => $locktype['label'],
                        'value' => $locktype['value'],
                    ];
                endif;
            endif;

        }
        return $options;
    }
}