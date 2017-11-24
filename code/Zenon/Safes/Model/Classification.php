<?php
namespace Zenon\Safes\Model;

use \Magento\Framework\Model\AbstractModel;

class Classification extends AbstractModel
{
    const CLASSIFICATION_ID = 'entity_id'; // We define the id fieldname

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'safes'; // parent value is 'core_abstract'

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'classification'; // parent value is 'object'

    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = self::CLASSIFICATION_ID; // parent value is 'id'

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Zenon\Safes\Model\ResourceModel\Classification');
    }

    public function getEnableStatus() {
        return 1;
    }

    public function getDisableStatus() {
        return 0;
    }

    public function getAvailableStatuses() {
        return [$this->getDisableStatus() => __('Disabled'), $this->getEnableStatus() => __('Enabled')];
    }
}