<?php
namespace Zenon\Safes\Model\Source\Classification;

use Magento\Eav\Model\Config;

class FireExtinguisherCertAdd2 implements \Magento\Framework\Data\OptionSourceInterface
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
        $attribute = $this->_modelConfig->getAttribute('catalog_product', 'fire_extinguisher_cert_add2');
        $fireExtinguisherCertAdd2s = $attribute->getSource()->getAllOptions();
        foreach ($fireExtinguisherCertAdd2s as $fireExtinguisherCertAdd2) {
            $options[] = [
                'label' => $fireExtinguisherCertAdd2['label'],
                'value' => $fireExtinguisherCertAdd2['value'],
            ];
        }
        return $options;
    }
}