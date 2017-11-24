<?php

namespace Zenon\Themesetting\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class CategoryProducts extends \Magento\Catalog\Block\Product\ListProduct {

    protected $_collection;
    protected $_productCollection;
    protected $_imageHelper;
    protected $_catalogLayer;
    protected $_postDataHelper;
    protected $urlHelper;
    protected $imageHelper;
    protected $_scopeConfig;
    protected $categoryRepository;
    protected $_productCollectionFactory;
    protected $_catalogProductVisibility;
    protected $_categoryFactory;
    protected $_storeManager;
    protected $_registry;
    protected $connection;
    protected $_resource;
    protected $_priceHelper;
    protected $_sortAttribute = 'position';
    protected $eavConfig;
    protected $_productloader;


    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ProductFactory $_productloader,

        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->urlHelper = $urlHelper;
        $this->_collection = $collection;
        $this->_scopeConfig = $scopeConfig;
        $this->_imageHelper = $imageHelper;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryCollection = $categoryCollection;
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        $this->_resource = $resource;
        $this->_priceHelper = $priceHelper;
        $this->eavConfig = $eavConfig;
        $this->_productloader = $_productloader;

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    /**
     * @return mixed Get Connection
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }

    /**
     * Get category collection
     */
    public function getChildCategoryCollection() {
        $collection = $this->_categoryCollection->create();
        $_category = $this->_registry->registry('current_category');
        $collection->addAttributeToSelect('*')->setStore($this->_storeManager->getStore());
        $collection->addIdFilter($_category->getChildren());

        foreach ($collection as $category) {
            $categoryId = $category->getId();
            $productCollection = $this->getCategory($categoryId)->getProductCollection();

            $cloneProducts =  clone $productCollection;//clone the collection so we can compare sizes
            $numberOfProducts = $productCollection->getSize();

            $productCollection->addStoreFilter();
            $productCollection->addFinalPrice();

            //$table = $this->_resource->getTableName('catalog_product_entity');
            $minPrice = $this->getConnection()->fetchRow("SELECT MIN(`t`.`final_price`) FROM ({$productCollection->getSelectSql(1)}) `t`");
            $minPrice = $minPrice["MIN(`t`.`final_price`)"];
            $category->setMinPrice($minPrice);
        }
        $items = $collection->getItems();
        foreach ($items as $i => $_item) {
            if (!$_item->getMinPrice()) {
                unset($items[$i]);
            }
        }
        usort($items, array($this, 'sortCategories'));
        return $items;
    }

    protected function sortCategories($a, $b)
    {
        $aVal = $a->getData($this->_sortAttribute);
        $bVal = $b->getData($this->_sortAttribute);
        return ($aVal < $bVal) ? -1 : (($aVal > $bVal) ? 1 : 0);
    }
    /**
     * Get formatted by price
     *
     * @param   $price
     * @return  array || float
     */
    public function getFormatedPrice($price)
    {
        $formattedPrice = $this->_priceHelper->currency($price, true, false);
        return $formattedPrice;
    }

    /**
     * @param $category
     * @return $bolting
     */
    public function getCategoryBolting($category)
    {
        $attribute = 'bolting';
        $collection = $category->getProductCollection();
        $collection->addStoreFilter();
        $collection->addAttributeToFilter($attribute, array('notnull' => true))->addAttributeToFilter($attribute, array('neq' => ''));
        $collection->groupByAttribute($attribute);
        $usedAttributeValues = $collection->getColumnValues($attribute);
        $values = array();
        foreach ($usedAttributeValues as $value) {
            $values = array_merge($values, explode(',', $value));
        }
        $values = array_unique($values);
        $attributeModel = $this->eavConfig->getAttribute('catalog_product', $attribute);

        $bolting = array_filter((array)$attributeModel->getSource()->getOptionText(implode(',', $values)));
        return $bolting;
    }

    /**
     * @param $category
     * @return string
     */
    public function getCategoryModelSeries($category)
    {
        $attribute = 'model_series';
        $collection = $category->getProductCollection();
        $collection->addStoreFilter();
        $collection->addAttributeToFilter($attribute, array('notnull' => true))->addAttributeToFilter($attribute, array('neq' => ''));
        $collection->groupByAttribute($attribute);
        $usedAttributeValues = $collection->getColumnValues($attribute);
        if ($usedAttributeValues) {
            $attributeModel = $this->eavConfig->getAttribute('catalog_product', $attribute);
            return $attributeModel->getSource()->getOptionText($usedAttributeValues[0]);
        }
        return '';
    }

    /**
     * @param $category
     * @return string
     */
    public function getCategoryProductAttribute($category, $attributeCode)
    {
        $attribute = $attributeCode;
        $collection = $category->getProductCollection();
        $collection->addStoreFilter();
        $collection->addAttributeToFilter($attribute, array('notnull' => true))->addAttributeToFilter($attribute, array('neq' => ''));
        $collection->groupByAttribute($attribute);
        $usedAttributeValues = $collection->getColumnValues($attribute);
        if ($usedAttributeValues) {
            $attributeModel = $this->eavConfig->getAttribute('catalog_product', $attribute);
            //return $attributeModel->getSource()->getOptionText($usedAttributeValues[0]);
            return $usedAttributeValues[0];
        }
        return '';
    }

    /**
     * @param $category
     * @return string
     */
    public function getDimensionForCategory($productAttribute, $category)
    {
        $collection = $category->getProductCollection();
        $collection->addStoreFilter();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter($productAttribute, array('notnull' => true))->addAttributeToFilter($productAttribute, array('neq' => ''));
        $collection->groupByAttribute($productAttribute);
        $values = array_filter($collection->getColumnValues($productAttribute));
        $value = '';
        if ($values) {
            $value = min($values) . '-' . max($values) . ' mm';
        }
        return $value;
    }

    public function getLoadProduct($productId)
    {
        return $this->_productloader->create()->load($productId);
    }

    /**
     * Get Current Category
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    /**
     * Get product collection
     */
    public function getProducts() {
        $limit = "3";//$this->getProductLimit();
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices(
            $collection
        )->addStoreFilter()->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        )->addAttributeToSort(
            'news_from_date',
            'desc'
        )->setPageSize(
            $limit
        )->setCurPage(
            1
        );

        $collection->getSelect()
            ->limit($limit);

        return $collection;


    }

    /**
     * load and return product collection
     */
    public function getLoadedProductCollection() {
        return $this->getProducts();
    }

    // get category by category id
    public function getCategory($categoryId)
    {
        $category = $this->_categoryFactory->create();
        $category->load($categoryId);
        return $category;
    }

    // get category product by category id
    public function getCategoryProducts($categoryId)
    {
        $limit = 3;
        $products = $this->getCategory($categoryId)->getProductCollection();
        $products->addAttributeToSelect('*');
        $products->addAttributeToSort('created_at', 'desc');
        $products->setPageSize($limit);
        return $products;
    }

    /**
     * Get grid mode
     */
    public function getMode() {
        return 'grid';
    }

    /**
     * Get image helper
     */
    public function getImageHelper() {
        return $this->_imageHelper;
    }

    /**
     * Check that module is enabled or not
     * @return int
     */
    public function getSectionStatus() {
        return $this->_scopeConfig->getValue('category_tab/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get the configured limit of products
     * @return int
     */
    public function getProductLimit() {
        return $this->_scopeConfig->getValue('category_tab/general/limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product) {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /*public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return 'This is test!';
    }*/

}
