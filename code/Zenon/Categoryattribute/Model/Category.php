<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zenon\Categoryattribute\Model;

class Category extends \Magento\Catalog\Model\Category implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Magento\Catalog\Api\Data\CategoryInterface,
    \Magento\Catalog\Api\Data\CategoryTreeInterface
{
    /**
     * @param string $attributeCode
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageUrl($attributeCode = 'image')
    {
        $url = false;
        $image = $this->getData($attributeCode);
        if ($image) {
            if (is_string($image)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/' . $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }
}
