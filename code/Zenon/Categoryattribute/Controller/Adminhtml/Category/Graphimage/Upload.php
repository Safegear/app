<?php
namespace Zenon\Categoryattribute\Controller\Adminhtml\Category\Graphimage;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 */
class Upload extends \Magento\Backend\App\Action
{
    protected $baseTmpPath;
    protected $imageUploader;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Model\ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
        parent::__construct($context);

    }
    public function execute() {

        try {
            /*$attributeCode = $this->getRequest()->getParam('attribute_code');
            if (!$attributeCode) {
                throw new \Exception('attribute_code missing');
            }*/

            //$basePath = 'catalog/category/zenon/' . $attributeCode;
            //$baseTmpPath = 'catalog/category/zenon/tmp/' . $attributeCode;

            //$this->imageUploader->setBasePath($basePath);
            //$this->imageUploader->setBaseTmpPath($baseTmpPath);

            //$result = $this->imageUploader->saveFileToTmpDir($attributeCode);

            $result = $this->imageUploader->saveFileToTmpDir('graph_img');

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}