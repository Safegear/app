<?php
namespace Zenon\Themesetting\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;

class SafeObserver implements ObserverInterface
{
    /**
     * @var \Zenon\Themesetting\Helper\Data
     */
    protected $_dataHelper;

    protected $_resultPageFactory;

    /**
     * StickybarObserver constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Zenon\Themesetting\Helper\Data $dataHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Zenon\Themesetting\Helper\Data $dataHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_registry = $registry;
        $this->_layout = $context->getLayout();
        $this->_dataHelper = $dataHelper;
        $this->_request = $request;
    }

    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');
        if ($action != 'catalog_category_view') {
            return $this;
        }

        $_category = $this->_registry->registry('current_category');
        if (!$_category) {
            return $this;
        }

        $specialLayoutCatIds = array_map('intval', explode(',', $this->_dataHelper->getConfig('safe_categories/general/zenon_safe_categories')));
        //$specialLayoutCatIds = $this->_dataHelper->getConfig('safe_categories/general/zenon_safe_categories');
        $_categoryId = (int)$_category->getId();

        if (in_array($_categoryId, $specialLayoutCatIds)) {
            $layout = $observer->getData('layout');
            //$layout->getUpdate()->addHandle('zenon_custom_handle');
            $layout->getUpdate()->addHandle('zenon_safe_category');
        } elseif (in_array((int)$_category->getParentId(), $specialLayoutCatIds)) {
            $layout = $observer->getData('layout');
            //$layout->getUpdate()->addHandle('zenon_custom_handle');
            $layout->getUpdate()->addHandle('zenon_safe_subcategory');
        }


        return $this;

    }
}