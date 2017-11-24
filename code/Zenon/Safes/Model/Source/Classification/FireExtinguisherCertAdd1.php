<?php
namespace Zenon\Safes\Model\Source\Classification;

use Magento\Eav\Model\Config;

class FireExtinguisherCertAdd1 implements \Magento\Framework\Data\OptionSourceInterface
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
        $attribute = $this->_modelConfig->getAttribute('catalog_product', 'fire_extinguisher_cert_add1');
        $fireExtinguisherCertAdd1s = $attribute->getSource()->getAllOptions();
        foreach ($fireExtinguisherCertAdd1s as $fireExtinguisherCertAdd1) {
            $options[] = [
                'label' => $fireExtinguisherCertAdd1['label'],
                'value' => $fireExtinguisherCertAdd1['value'],
            ];
        }
        return $options;
    }
}