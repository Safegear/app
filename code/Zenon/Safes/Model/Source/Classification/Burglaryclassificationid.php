<?php
namespace Zenon\Safes\Model\Source\Classification;

use Magento\Eav\Model\Config;

class Burglaryclassificationid implements \Magento\Framework\Data\OptionSourceInterface
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
        $attribute = $this->_modelConfig->getAttribute('catalog_product', 'burglary_classification');
        $burglaryclassificationCollection = $attribute->getSource()->getAllOptions();
        //$locktypeCollection = $this->_classification->getCollection()->addFieldToSelect('entity_id')->addFieldToSelect('name');
        foreach ($burglaryclassificationCollection as $burglaryclassification) {
            $options[] = [
                'label' => $burglaryclassification['label'],
                'value' => $burglaryclassification['value'],
            ];
        }
        return $options;
    }
}