<?php
namespace Zenon\Safes\Controller\Adminhtml\Lock;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Zenon_Safes::lock';

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
        $resultPage->setActiveMenu('Zenon_Safes::lock');
        $resultPage->addBreadcrumb(__('Safes'), __('Safes'));
        $resultPage->addBreadcrumb(__('Manage Locks'), __('Manage Locks'));
        $resultPage->getConfig()->getTitle()->prepend(__('Lock Type Image'));

        return $resultPage;
    }
}