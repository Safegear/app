<?php
namespace Zenon\Safes\Block\Adminhtml\Lock\Edit;

use \Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_locktype;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Zenon\Safes\Model\Source\Locktype $locktype,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_locktype = $locktype;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lock_form');
        $this->setTitle(__('Lock Type Image Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Zenon\Safes\Model\Lock $model */
        $model = $this->_coreRegistry->registry('safes_lock');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
        );

        $form->setHtmlIdPrefix('lock_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $locktypes = $this->_locktype->toOptionArray();
        $fieldset->addField(
            'option_id',
            'select',
            ['name' => 'option_id', 'label' => __('Lock Type'), 'title' => __('Lock Type'), 'required' => true, 'values' => $locktypes]
        );

        $fieldset->addField(
            'image',
            'image',
            ['title' => __('Lock Image'), 'label' => __('Lock Image'), 'name' => 'image', 'note' => 'Allow image type: jpg, jpeg, gif, png', 'required' => true]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}