<?php
namespace Zenon\Safes\Model\Source\Classification;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Zenon\Safes\Model\Classification
     */
    protected $_classification;

    /**
     * Constructor
     *
     * @param \Zenon\Safes\Model\Classification $classification
     */
    public function __construct(\Zenon\Safes\Model\Classification $classification)
    {
        $this->_classification = $classification;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->_classification->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}