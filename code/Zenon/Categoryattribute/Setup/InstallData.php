<?php

namespace Zenon\Categoryattribute\Setup;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'thumbnail',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Thumbnail Image',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'cat_heading',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Category Heading',
                'input' => 'text',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'sort_order' => 2,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'graph_img',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Graph Image',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 3,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'long_description',
            [
                'type' => 'text',
                'label' => 'Long Description',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 4,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'is_html_allowed_on_front' => true,
                'group' => 'General Information',
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'height',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Height',
                'input' => 'text',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'sort_order' => 5,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'width',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Width',
                'input' => 'text',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'sort_order' => 6,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'depth',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Depth',
                'input' => 'text',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'sort_order' => 7,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
            ]
        );

        /*----- Image Gallery Attributes -----*/
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_1',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 1',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 8,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_2',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 2',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 9,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_3',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 3',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 10,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_4',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 4',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 11,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_5',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 5',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 12,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_6',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 6',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 13,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_7',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 7',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 14,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_8',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 8',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 15,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_9',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 9',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 16,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'gallery_image_10',
            [
                'group' => 'General Information',
                'type' => 'varchar',
                'label' => 'Gallery Image 10',
                'input' => 'image',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'backend' => 'Zenon\Categoryattribute\Model\Category\Attribute\Backend\Image',
                'sort_order' => 17,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
            ]
        );

        $setup->endSetup();
    }
}