<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zenon\Categoryattribute\Controller\Adminhtml\Category;

use \Magento\Catalog\Api\Data\CategoryAttributeInterface;

/**
 * Class Save
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Catalog\Controller\Adminhtml\Category\Save
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * The list of inputs that need to convert from string to boolean
     * @var array
     */
    protected $stringToBoolInputs = [
        'custom_use_parent_settings',
        'custom_apply_to_products',
        'is_active',
        'include_in_menu',
        'is_anchor',
        'use_default' => ['url_key'],
        'use_config' => ['available_sort_by', 'filter_price_range', 'default_sort_by']
    ];

    /**
     * Category save
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $category = $this->_initCategory();

        if (!$category) {
            return $resultRedirect->setPath('catalog/*/', ['_current' => true, 'id' => null]);
        }

        $data['general'] = $this->getRequest()->getPostValue();
        $categoryPostData = $data['general'];

        $isNewCategory = !isset($categoryPostData['entity_id']);
        $categoryPostData = $this->stringToBoolConverting($categoryPostData);
        $categoryPostData = $this->imagePreprocessing($categoryPostData);
        $categoryPostData = $this->dateTimePreprocessing($category, $categoryPostData);
        $storeId = isset($categoryPostData['store_id']) ? $categoryPostData['store_id'] : null;

        $storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store = $storeManager->getStore($storeId);
        $storeManager->setCurrentStore($store->getCode());
        $parentId = isset($categoryPostData['parent']) ? $categoryPostData['parent'] : null;
        if ($categoryPostData) {
            $category->addData($categoryPostData);
            if ($isNewCategory) {
                $parentCategory = $this->getParentCategory($parentId, $storeId);
                $category->setPath($parentCategory->getPath());
                $category->setParentId($parentCategory->getId());
            }

            /**
             * Process "Use Config Settings" checkboxes
             */

            $useConfig = [];
            if (isset($categoryPostData['use_config']) && !empty($categoryPostData['use_config'])) {
                foreach ($categoryPostData['use_config'] as $attributeCode => $attributeValue) {
                    if ($attributeValue) {
                        $useConfig[] = $attributeCode;
                        $category->setData($attributeCode, null);
                    }
                }
            }

            $category->setAttributeSetId($category->getDefaultAttributeSetId());

            if (isset($categoryPostData['category_products'])
                && is_string($categoryPostData['category_products'])
                && !$category->getProductsReadonly()
            ) {
                $products = json_decode($categoryPostData['category_products'], true);
                $category->setPostedProducts($products);
            }
            $this->_eventManager->dispatch(
                'catalog_category_prepare_save',
                ['category' => $category, 'request' => $this->getRequest()]
            );

            /**
             * Check "Use Default Value" checkboxes values
             */
            if (isset($categoryPostData['use_default']) && !empty($categoryPostData['use_default'])) {
                foreach ($categoryPostData['use_default'] as $attributeCode => $attributeValue) {
                    if ($attributeValue) {
                        $category->setData($attributeCode, null);
                    }
                }
            }

            /**
             * Proceed with $_POST['use_config']
             * set into category model for processing through validation
             */
            $category->setData('use_post_data_config', $useConfig);

            try {
                $categoryResource = $category->getResource();
                if ($category->hasCustomDesignTo()) {
                    $categoryResource->getAttribute('custom_design_from')->setMaxValue($category->getCustomDesignTo());
                }

                $validate = $category->validate();
                if ($validate !== true) {
                    foreach ($validate as $code => $error) {
                        if ($error === true) {
                            $attribute = $categoryResource->getAttribute($code)->getFrontend()->getLabel();
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('Attribute "%1" is required.', $attribute)
                            );
                        } else {
                            throw new \Exception($error);
                        }
                    }
                }

                $category->unsetData('use_post_data_config');

                $category->save();
                $this->messageManager->addSuccess(__('You saved the category.'));
            } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_getSession()->setCategoryData($categoryPostData);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_getSession()->setCategoryData($categoryPostData);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving the category.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_getSession()->setCategoryData($categoryPostData);
            }
        }

        $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );

        if ($this->getRequest()->getPost('return_session_messages_only')) {
            $category->load($category->getId());
            // to obtain truncated category name
            /** @var $block \Magento\Framework\View\Element\Messages */
            $block = $this->layoutFactory->create()->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData(
                [
                    'messages' => $block->getGroupedHtml(),
                    'error' => $hasError,
                    'category' => $category->toArray(),
                ]
            );
        }

        $redirectParams = $this->getRedirectParams($isNewCategory, $hasError, $category->getId(), $parentId, $storeId);

        return $resultRedirect->setPath(
            $redirectParams['path'],
            $redirectParams['params']
        );
    }

    /**
     * @param array $data Category data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function imagePreprocessing($data)
    {
        $eavConfig = $this->_objectManager->create('\Magento\Eav\Model\Config');
        $entityType = $eavConfig->getEntityType(CategoryAttributeInterface::ENTITY_TYPE_CODE);
        foreach ($entityType->getAttributeCollection() as $attributeModel) {
            $attributeCode = $attributeModel->getAttributeCode();
            $backendModel = $attributeModel->getBackend();

            if (isset($data[$attributeCode])) {
                continue;
            }

            if (!$backendModel instanceof \Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image) {
                continue;
            }

            $data[$attributeCode] = false;
        }

        return $data;
    }
}