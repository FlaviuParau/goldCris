<?php
class Blugento_GdprCookies_Block_System_Config_Form_Field_Textarea extends Mage_Core_Block_Template
{
    public function _toHtml()
    {
        $inputName = $this->getInputName();
        $columnName = $this->getColumnName();
        $column = $this->getColumn();
        return '<textarea type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"'.
            (isset($column['style']) ? ' style="'.$column['style'] . '"' : '') . '>#{' . $columnName . '}</textarea>';
    }
}