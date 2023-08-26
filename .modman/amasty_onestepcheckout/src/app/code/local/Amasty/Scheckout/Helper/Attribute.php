<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Scheckout
 */


class Amasty_Scheckout_Helper_Attribute extends Amasty_Scheckout_Helper_Data
{
    /**
     * @param $fields
     * @param $attributePrefix
     * @return string
     */
    public function displayExtrafield($field, $attributePrefix)
    {
        $html     = '';
        $moduleName = $this->getModuleNameByAttributePrefix($attributePrefix);

        if (!Mage::helper('core')->isModuleEnabled($moduleName) || $field['field_disabled']) {
            return '';
        }

        $attributeField = $this->_collectExtraFields($field, $attributePrefix);
        $attributes = $this->getAttributeCollection($attributeField, $attributePrefix);

        if ($attributes->getFirstItem()->getId()) {
            $attribute = $attributes->getFirstItem();

            $html .= $this->getExtraField($field, $moduleName, $attribute);
        }

        return $html;
    }

    /**
     * @param $key
     * @param $fieldPrefix
     * @return bool
     */
    public function checkAmastyAttributesFieldKey($key, $fieldPrefix)
    {
        return strpos($key, $fieldPrefix) !== false;
    }

    /**
     * @param $fields
     * @return string
     */
    public function displayExtrafields($fields)
    {
        $html     = '';
        $extraFields = array(
            self::CUSTOMERATTR_MODULE_NAME => self::FIELD_PREFIX_CUSTOMERATTR,
            self::ORDERATTR_MODULE_NAME => self::FIELD_PREFIX_ORDERATTR
        );

        foreach ($extraFields as $moduleName => $attributePrefix) {
            if (!Mage::helper('core')->isModuleEnabled($moduleName)) {
                continue;
            }

            $attributeFields = $this->_collectExtraFields($fields, $attributePrefix);
            $attributes = $this->getAttributeCollection($attributeFields, $attributePrefix);

            foreach ($attributes as $attribute) {
                $attributeCode = $attribute->getAttributeCode();

                foreach ($fields as $field) {
                    if ($field['field_key'] == $attributeCode) {
                        $html .= $this->getExtraField($field, $moduleName, $attribute);
                    }
                }
            }
        }

        return $html;
    }

    /**
     * @param $area
     * @return string
     */
    public function displayCustomerAttributeRelations($typeAddress = 'billing')
    {
        $html = '';
        if (Mage::helper('ambase')->isModuleEnabled('Amasty_Customerattr')) {
            $blockCustomAttr = $this->getLayout()->createBlock('amscheckout/customerattr');
            $blockCustomAttr->setTypeAddress($typeAddress);
            $html = $blockCustomAttr->setBlockId('amasty_customerattr')->toHtml();
        }
        return $html;
    }

    /**
     * @param array $fields
     * @param string $typeField
     * @return array
     */
    protected function _collectExtraFields($fields = array(), $typeField = self::FIELD_PREFIX_ORDERATTR)
    {
        $isMultidemnsional = function ($array) {
            return count($array) == count($array, COUNT_RECURSIVE);
        };

        $extraFields = array();

        if ($isMultidemnsional($fields)) {
            $extraFields[] = $fields['field_key'];
        } else {
            foreach ($fields as $key => $field) {
                if ($this->checkAmastyAttributesFieldKey($field['field_db_key'], $typeField)) {
                    $extraFields[] = $field['field_key'];
                }
            }
        }

        return $extraFields;
    }

    /**
     * @param array $extraFields
     * @param string $attributePrefix
     * @return mixed
     */
    protected function getAttributeCollection($extraFields = array(), $attributePrefix = 'oa_')
    {
        $collection = $this->getEavAttributeCollection($attributePrefix);
        $collection->addFieldToFilter('attribute_code', array('in' => $extraFields));
        if ($this->canUseOrderAmastyAddiotionAttribute($attributePrefix)) {
            $collection->getSelect()->order('sorting_order');
        }

        return $collection->load();
    }

    /**
     * @param string $attributePrefix
     * @return mixed
     */
    protected function getEavAttributeCollection($attributePrefix = 'oa_')
    {
        if ($attributePrefix == 'ca_') {
            $collection = Mage::getModel('customer/attribute')->getCollection();
            $collection = Mage::helper('amcustomerattr')->addFilters(
                $collection, 'eav_attribute'
            );
            return $collection;
        }

        return Mage::getModel('eav/entity_attribute')->getCollection();
    }

    /**
     * @param string $attributePrefix
     * @return boolean
     */
    protected function canUseOrderAmastyAddiotionAttribute($attributePrefix = 'oa_')
    {
        $canUseOrder = false;
        $amastyOrderAttributeEnabled = Mage::helper('core')->isModuleEnabled('Amasty_Ordattr');
        $amastyCustomerAttributeEnabled = Mage::helper('core')->isModuleEnabled('Amasty_Customerattr');
        if (($attributePrefix == 'oa_' && $amastyOrderAttributeEnabled)
            || ($attributePrefix == 'ca_' && $amastyCustomerAttributeEnabled)) {
                $canUseOrder = true;
        }

        return $canUseOrder;
    }

    /**
     * @param $_field
     * @param string $moduleName
     * @param null $attribute
     * @return string
     */
    public function getExtraField($_field, $moduleName = self::ORDERATTR_MODULE_NAME, $attribute = null)
    {
        $ret = "";
        $ret .= $this->getBeforeControlHtml($_field, array(), false);

        switch ($moduleName) {
            case self::ORDERATTR_MODULE_NAME:
                $ret .= strtr(Mage::helper('amorderattr')->field(array(
                    $_field['field_key']
                )), array(
                    "float: left;"  => "",
                    "<ul" => "<div",
                    "<li" => "<div",
                    "</ul>" => "</div>",
                    "</li>" => "</div>",
                    "h4" => "div",
                    "form-list" => ""
                ));

                break;
            case self::CUSTOMERATTR_MODULE_NAME:
                $ret .= $this->getAttributeHtml($attribute, $_field);

                break;
        }

        $ret .= $this->getAfterControlHtml($_field);

        return $ret;
    }

    /**
     * @param $attribute
     * @return string
     */
    public function getAttributeHtml($attribute, $field)
    {
        $storeIds = explode(',', $attribute->getData('store_ids'));
        $currentStore = Mage::app()->getStore()->getId();
        $form = new Varien_Data_Form();

        if (!in_array($currentStore, $storeIds) && (0 != $currentStore)
            && !in_array(0, $storeIds)
        ) {
            return false;
        }

        if (!($inputType = $attribute->getFrontend()->getInputType())) {
            return false;
        }

        $typeInternal = $attribute->getTypeInternal();
        $inputTypes = array(
            'statictext'  => 'note',
            'selectgroup' => 'select',
            'boolean' => 'select'
        );

        $inputType = isset($inputTypes[$inputType]) ? $inputTypes[$inputType] : $inputType;

        if ($typeInternal) {
            $inputType = isset($inputTypes[$typeInternal]) ? $inputTypes[$typeInternal] : $typeInternal;
        }

        $rendererClass = self::VARIEN_DATA_FORM_ELEMENT_CLASS_NAME_PREFIX . ucfirst($inputType);
        $fieldKey = isset($field['field_db_key']) ? $field['field_db_key'] : '';
         $fieldRequired = $field['field_required'];
        if ($this->getTypeExternalField($fieldKey) == self::FIELD_PREFIX_CUSTOMERATTR) {
            $rendererClass = self::CUSTOMATTR_DATA_FORM_ELEMENT_CLASS_NAME_PREFIX . ucfirst($inputType);
            $fieldRequired = $attribute->getIsRequired();
        }

        if (!class_exists($rendererClass)) {
            return false;
        }

        $fieldName = 'billing[' . self::INPUT_NAME_PREFIX_CUSTOMERATTR . '][' . $attribute->getAttributeCode() . ']';

        if ('file' == $inputType) {
            $fieldName = self::INPUT_NAME_PREFIX_CUSTOMERATTR . '[' . $attribute->getAttributeCode() . ']';
        }

        // default_value
        $attributeValue = $attribute->getData('default_value');

        $fileAttributeValue = '';
        if ('file' == $inputType) {
            $fileAttributeValue = $attributeValue;
            $attributeValue = '';
        }

        $class = $attribute->getFrontend()->getClass();

        if ($inputType == 'text') {
            $class .= ' input-text';
        }

        $config = array(
            'name'     => $fieldName,
            'label'    => $field['field_label'],
            'class'    => $class,
            'required' => $fieldRequired,
            'disabled' => $field['field_disabled'],
            'note'     => $attribute->getNote(),
            'value'    => $attributeValue,
            'text'     => $attributeValue,
            'html_id'  => $this->getAttributeId($field)
        );

        $afterElementHtml = '';

        if ('date' == $inputType) {
            $config['readonly'] = 1;
            $config['class'] = ' amasty-datepicker';
            $config['onclick'] = 'amcustomerattr_trig('
                . '\'billing:' . $attribute->getAttributeCode() . '_trig\')';
            $afterElementHtml .= '<script type="text/javascript">'
                . 'function amcustomerattr_trig(id)'
                . '{ $(id).click(); }'
                . '</script>';
        }

        $element = new $rendererClass($config);

        if ('file' == $inputType) {
            if ($fileAttributeValue) {
                // to Controller
                $fileName = Mage::helper('amcustomerattr')->cleanFileName($fileAttributeValue);
                $downloadUrl = Mage::helper('amcustomerattr')->getAttributeFileUrl($fileAttributeValue, true, true);

                $afterElementHtml .= '<br /><a href="' . $downloadUrl
                    . '"><img alt="' . $this->__(
                        'Download File'
                    ) . '" title="' . $this->__(
                        'Download File'
                    ) . '" src="' . Mage::getDesign()->getSkinUrl(
                        'images/fam_bullet_disk.gif'
                    ) . '" class="v-middle"></a>'
                    . '<a href="' . $downloadUrl . '">' . $fileName[3]
                    . '</a><br />'
                    . '<input type="checkbox" id="'
                    . $attribute->getAttributeCode()
                    . '_delete_file" name="amcustomerattr_delete['
                    . $attribute->getAttributeCode() . ']" value="'
                    . $fileAttributeValue . '" /> Delete File'
                    . '<input type="hidden" id="'
                    . $attribute->getAttributeCode()
                    . '" name="amcustomerattr['
                    . $attribute->getAttributeCode() . ']" value="'
                    . $fileAttributeValue . '" />'
                    . '<div style="padding: 4px;"></div>';
            } else {
                $afterElementHtml .= '<input type="hidden" id="'
                    . $attribute->getAttributeCode()
                    . '" name="amcustomerattr['
                    . $attribute->getAttributeCode() . ']" value="" />'
                    . '<div style="padding: 4px;"></div>';
                $afterElementHtml .= '<div style="padding: 4px;"></div>';
            }
        } else {
            $element->setText($attributeValue);
            $afterElementHtml .= '<div style="padding: 4px;"></div>';
        }

        $element->setAfterElementHtml($afterElementHtml);

        if ($inputType == 'select' || $inputType == 'selectimg'
            || $inputType == 'multiselect'
            || $inputType == 'multiselectimg'
        ) {

            // getting values translations
            $valuesCollection = Mage::getResourceModel(
                'eav/entity_attribute_option_collection'
            )
                ->setAttributeFilter($attribute->getId())
                ->setStoreFilter($currentStore, false)
                ->load();
            foreach ($valuesCollection as $item) {
                $values[$item->getId()] = $item->getValue();
            }

            $options = $attribute->getSource()->getAllOptions(true, true);
            $defaultValue = $attribute->getDefaultValue();
            foreach ($options as $i => $option) {
                if (isset($values[$option['value']])) {
                    $options[$i]['label'] = $values[$option['value']];
                }

                if ($defaultValue == $option['value']) {
                    $options[$i]['default'] = true;
                }
            }

            $element->setValues($options);
        } elseif ($inputType == 'date') {
            $dateImage = $this->getSkinUrl(
                'images/grid-cal.gif',
                array('_area' => 'adminhtml', '_package' => 'default')
            );

            if ($attribute->getIsReadOnly()) {
                $dateImage = '';
            }

            $element->setImage($dateImage);
            $element->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
        }

        $element->setForm($form);

        return $element->getDefaultHtml();
    }

    protected function getTypeExternalField($fieldKey = false)
    {
        $typeField = self::FIELD_PREFIX_ORDERATTR;

        if (!$fieldKey) {
            return $typeField;
        }

        if (strstr($fieldKey, 'oa_')) {
            $typeField = self::FIELD_PREFIX_ORDERATTR;
        } else {
            if (strstr($fieldKey, 'ca_')) {
                $typeField = self::FIELD_PREFIX_CUSTOMERATTR;
            }
        }

        return $typeField;
    }
}