<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Zenon\Categoryattribute\Model\Category;

use Magento\Catalog\Model\Category\Attribute\Backend\Image as ImageBackendModel;

/**
 * Class DataProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Smartwave\Megamenu\Model\Category\DataProvider
{
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $category = $this->getCurrentCategory();
        if ($category) {
            $categoryData = $category->getData();
            $categoryData = $this->addUseDefaultSettings($category, $categoryData);
            $categoryData = $this->addUseConfigSettings($categoryData);
            $categoryData = $this->filterFields($categoryData);
            $categoryData = $this->convertValues($category, $categoryData);

            $this->loadedData[$category->getId()] = $categoryData;
        }
        return $this->loadedData;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param array $categoryData
     * @return array
     */
    protected function convertValues($category, $categoryData)
    {
        foreach ($category->getAttributes() as $attributeCode => $attribute) {
            if (!isset($categoryData[$attributeCode])) {
                continue;
            }

            if ($attribute->getBackend() instanceof ImageBackendModel) {
                unset($categoryData[$attributeCode]);

                $categoryData[$attributeCode][0]['name'] = $category->getData($attributeCode);
                $categoryData[$attributeCode][0]['url'] = $category->getImageUrl($attributeCode);
            }
        }

        return $categoryData;
    }
}
