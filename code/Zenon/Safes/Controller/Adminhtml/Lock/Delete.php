<?php
namespace Zenon\Safes\Controller\Adminhtml\Lock;

use Magento\Backend\App\Action;

class Delete extends Action
{
    protected $_model;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param \Zenon\Safes\Model\Lock $model
     */
    public function __construct(
        Action\Context $context,
        \Zenon\Safes\Model\Lock $model
    ) {
        parent::__construct($context);
        $this->_model = $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zenon_Safes::lock_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_model;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('Lock Type Image deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Lock Type Image does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}