<?php
namespace Zenon\Safes\Controller\Adminhtml\Classification;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Zenon_Safes::classification';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zenon_Safes::classification');
        $resultPage->addBreadcrumb(__('Safes'), __('Safes'));
        $resultPage->addBreadcrumb(__('Manage Classification'), __('Manage Classification'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Classification'));

        return $resultPage;
    }
}