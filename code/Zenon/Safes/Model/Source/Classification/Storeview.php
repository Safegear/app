<?php
namespace Zenon\Safes\Model\Source\Classification;

use \Magento\Store\Model\StoreRepository;

class Storeview implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Zenon\Safes\Model\Classification
     */
    protected $_classification;

    protected $_systemStore;

    protected $_storeRepository;

    /**
     * Constructor
     *
     * @param \Zenon\Safes\Model\Classification $classification
     */
    public function __construct(
        \Zenon\Safes\Model\Classification $classification,
        \Magento\Store\Model\System\Store $systemStore,
        StoreRepository $storeRepository
    ) {
        $this->_classification = $classification;
        $this->_systemStore = $systemStore;
        $this->_storeRepository = $storeRepository;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $stores = $this->_storeRepository->getList();
        $websiteIds = array();
        $storeLists = array();
        foreach ($stores as $store) {
            $websiteId = $store["website_id"];
            $storeId = $store["store_id"];
            $storeName = $store["name"];
            $storeLists[$storeId] = $storeName;
            array_push($websiteIds, $websiteId);
            if($storeId == 0):
                $options[] = [
                    'label' => __('All Store Views'),
                    'value' => 0
                ];
            else:
                $options[] = [
                    'label' => $store['name'],
                    'value' => $store['store_id'],
                ];
            endif;
        }

        return $options;
    }
}