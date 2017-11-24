<?php
namespace Zenon\Safes\Block\Adminhtml\Classification\Edit;

use \Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_status;

    protected $_burglaryclassification;

    protected $_fireclassification;

    protected $_fireExtinguisherCert;

    protected $_fireExtinguisherCertAdd1;

    protected $_fireExtinguisherCertAdd2;

    protected $_easTechnology;

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
        \Zenon\Safes\Model\Source\Classification\Status $status,
        \Zenon\Safes\Model\Source\Classification\Burglaryclassificationid $burglaryclassification,
        \Zenon\Safes\Model\Source\Classification\Fireclassificationid $fireclassification,
        \Zenon\Safes\Model\Source\Classification\FireExtinguisherCert $fireExtinguisherCert,
        \Zenon\Safes\Model\Source\Classification\FireExtinguisherCertAdd1 $fireExtinguisherCertAdd1,
        \Zenon\Safes\Model\Source\Classification\FireExtinguisherCertAdd2 $fireExtinguisherCertAdd2,
        \Zenon\Safes\Model\Source\Classification\EasTechnology $easTechnology,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_burglaryclassification = $burglaryclassification;
        $this->_fireclassification = $fireclassification;
        $this->_fireExtinguisherCert = $fireExtinguisherCert;
        $this->_fireExtinguisherCertAdd1 = $fireExtinguisherCertAdd1;
        $this->_fireExtinguisherCertAdd2 = $fireExtinguisherCertAdd2;
        $this->_easTechnology = $easTechnology;
        $this->_wysiwygConfig = $wysiwygConfig;
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
        $this->setId('classification_form');
        $this->setTitle(__('Classification Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Zenon\Safes\Model\Classification $model */
        $model = $this->_coreRegistry->registry('safes_classification');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post', 'enctype' => 'multipart/form-data']]
        );

        $form->setHtmlIdPrefix('classification_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $burglaryclassification = $this->_burglaryclassification->toOptionArray();
        $fieldset->addField(
            'bc_option_id',
            'select',
            ['name' => 'bc_option_id', 'label' => __('Burglary Classification'), 'title' => __('Burglary Classification'), 'style' => 'width:100%', 'required' => false, 'values' => $burglaryclassification]
        );

        $fireclassification = $this->_fireclassification->toOptionArray();
        $fieldset->addField(
            'fc_option_id',
            'select',
            ['name' => 'fc_option_id', 'label' => __('Fire Classification'), 'title' => __('Fire Classification'), 'style' => 'width:100%', 'required' => false, 'values' => $fireclassification]
        );

        $fireExtinguisherCert = $this->_fireExtinguisherCert->toOptionArray();
        $fieldset->addField(
            'fec_option_id',
            'select',
            ['name' => 'fec_option_id', 'label' => __('Fire Extinguisher Cert'), 'title' => __('Fire Extinguisher Cert'), 'style' => 'width:100%', 'required' => false, 'values' => $fireExtinguisherCert]
        );

        $fireExtinguisherCertAdd1 = $this->_fireExtinguisherCertAdd1->toOptionArray();
        $fieldset->addField(
            'feca1_option_id',
            'select',
            ['name' => 'feca1_option_id', 'label' => __('Fire Extinguisher Cert Add1'), 'title' => __('Fire Extinguisher Cert Add1'), 'style' => 'width:100%', 'required' => false, 'values' => $fireExtinguisherCertAdd1]
        );

        $fireExtinguisherCertAdd2 = $this->_fireExtinguisherCertAdd2->toOptionArray();
        $fieldset->addField(
            'feca2_option_id',
            'select',
            ['name' => 'feca2_option_id', 'label' => __('Fire Extinguisher Cert Add2'), 'title' => __('Fire Extinguisher Cert Add2'), 'style' => 'width:100%', 'required' => false, 'values' => $fireExtinguisherCertAdd2]
        );

        $easTechnology = $this->_easTechnology->toOptionArray();
        $fieldset->addField(
            'et_option_id',
            'select',
            ['name' => 'et_option_id', 'label' => __('Eas Technology'), 'title' => __('Eas Technology'), 'style' => 'width:100%', 'required' => false, 'values' => $easTechnology]
        );

        $fieldset->addField(
            'image',
            'image',
            ['title' => __('Image'), 'label' => __('Image'), 'name' => 'image', 'note' => 'Allow image type: jpg, jpeg, gif, png', 'required' => false]
        );

        $fieldset->addField(
            'store_id',
            'select',
            ['name'     => 'store_id', 'label'    => __('Store View'), 'title'    => __('Store View'), 'required' => true, 'values'   => $this->_systemStore->getStoreValuesForForm(false, true)]
        );

        $fieldset->addField(
            'description',
            'editor',
            ['name' => 'description', 'label' => __('Description'), 'title' => __('Description'), 'style' => 'height:10em', 'required' => false, 'config' => $this->_wysiwygConfig->getConfig()]
        );

        $fieldset->addField(
            'info',
            'editor',
            ['name' => 'info', 'label' => __('Info'), 'title' => __('Info'), 'style' => 'height:10em', 'required' => false, 'config' => $this->_wysiwygConfig->getConfig()]
        );

        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );

        $status = $this->_status->toOptionArray();
        $fieldset->addField(
            'status',
            'select',
            ['name' => 'status', 'label' => __('Status'), 'title' => __('Status'), 'required' => false, 'values' => $status]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}