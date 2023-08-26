<?php

class IntelligentIT_SmartBill_Block_Adminhtml_CompanyChange extends Mage_Adminhtml_Block_System_Config_Form_Field
{
  const CHANGED = '<strong id="row_settings_companydata_company_change_message">Salvati configuratia pentru a incarca datele companiei alese si apoi efectuati setarile pentru emiterea corecta a documentelor in Smart Bill</strong>.';
  const JS = "<script type=\"text/javascript\">
  setTimeout(function() {
    var \$message = document.getElementById('row_settings_companydata_company_change_message');
    var \$company = document.getElementById('settings_companydata_company');
    var \$tmpSelects = document.getElementsByClassName('validate-select');
    var \$selects = [];
    var companyInitValue = '';

    for (i in \$tmpSelects) {
      var \$elem = document.getElementById(\$tmpSelects[i].id);

      if (\$elem!=null) {
        \$selects.push(\$elem);
      }
    }

    if (\$company!=null) {
      companyInitValue = \$company.value;
      \$company.onchange = function(){
        var hideSections = ['settings_vatsettingsdata', 'settings_docssettingsdata'];
        for(i in hideSections) {
          if (\$company.value!=companyInitValue) {
            try {
              document.getElementById(hideSections[i]).style = 'height:0px;overflow:hidden;padding:0;margin:0;';
              Fieldset.applyCollapse(hideSections[i]);
              Fieldset.toggleCollapse(hideSections[i]);

              \$message.style = 'display:block';
            } catch (e) {
            }
          } else {
            try {
              document.getElementById(hideSections[i]).style = '';
              Fieldset.toggleCollapse(hideSections[i]);

              \$message.style = '';
            } catch (e) {}
          }
        }

        // validation & notices
        if (\$company.value!=companyInitValue) {
          // remove select class for validation & notice DIV
          // for (i in \$selects) {
          for (i=0; i<\$selects.length; i++) {
            var \$elem = \$selects[i];

            if (\$elem.getAttribute('class').indexOf('validate-select')!=-1) {
              var \$parent = \$elem.parentNode;
              var \$advice = \$parent.getElementsByClassName('validation-advice')[0];

              \$elem.setAttribute('class', 'select validate-select-backup');
              try {
                \$advice.style.display = 'none';
                \$advice.style.opacity = '0';
              } catch(e) {}
            }
          }
        } else {
          // add select class for validation
          // for (i in \$selects) {
          for (i=0; i<\$selects.length; i++) {
            var \$elem = \$selects[i];

            if (\$elem.getAttribute('class').indexOf('validate-select-backup')!=-1) {
              var \$parent = \$elem.parentNode;
              var \$advice = \$parent.getElementsByClassName('validation-advice')[0];

              \$elem.setAttribute('class', 'select validate-select');
              try {
                \$advice.style.display = '';
                \$advice.style.opacity = '';
              } catch(e) {}
            }
          }
        }       

        return false;
      };
    }
  }, 100);

  setTimeout(function(){  
    // pre-validate  
    configForm.validate();
  }, 400);
  </script>";
  const CSS = "<style type=\"text/css\">
  #row_settings_companydata_company_change .scope-label,
  .entry-edit .section-config:first-child .entry-edit-head {
    display: none !important;
  }
  #row_settings_companydata_company_change_message {
    color: #df280a;
    display: none;
  }
  </style>";
   /**
    * Returns html part of the setting
    *
    * @param Varien_Data_Form_Element_Abstract $element
    * @return string
    */
   protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
   {
      return self::CHANGED.self::JS.self::CSS;
   }
}
