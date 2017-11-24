<?php
namespace Zenon\Safes\Model\Source\Classification;

use Magento\Eav\Model\Config;

class Fireclassificationid implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Zenon\Safes\Model\Classification
     */
    protected $_classification;

    protected $_modelConfig;

    /**
     * Constructor
     *
     * @param \Zenon\Safes\Model\Classification $classification
     */
    public function __construct(
        \Zenon\Safes\Model\Classification $classification,
        Config $modelConfig
    ) {
        $this->_classification = $classification;
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
        $attribute = $this->_modelConfig->getAttribute('catalog_product', 'fire_classification');
        $fireclassificationCollection = $attribute->getSource()->getAllOptions();
        //$locktypeCollection = $this->_classification->getCollection()->addFieldToSelect('entity_id')->addFieldToSelect('name');
        foreach ($fireclassificationCollection as $fireclassification) {
            $options[] = [
                'label' => $fireclassification['label'],
                'value' => $fireclassification['value'],
            ];
        }
        return $options;
    }
}