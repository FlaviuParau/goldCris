<?php

class IntelligentIT_SmartBill_Block_Adminhtml_HowToContent extends Mage_Adminhtml_Block_System_Config_Form_Field
{
  const HTML = '<strong>Pentru a factura comenzile online in Smart Bill trebuie sa aveti pachetul Smart Bill Cloud.</strong>
<ol>
  <li>In meniul Smart Bill > Autentificare se salveaza configurarile cu numele si parola utilizatorului din Smart Bill. Daca se folosesc date valide, se afiseaza mesaj de succes.</li>
  <li>In meniul Smart Bill > Setari se salveaza configurarile dorite: compania pe care se vor emite documentele (in cazul in care contul Smart Bill e legat la mai multe companii), setarile legate de TVA daca firma e platitoare de TVA, tipul de document si seria implicita pe care se vor emite documentele si celelalte setari. Atentie si la indicatiile de sub fiecare selector.</li>
  <li>Optiunile din sectiunea de Setari emitere documente se vor regasi in documentele emise in Smart Bill si trebuie sa fie in concordanta cu restul setarilor din magazinul online.</li>
</ol>';
  const CSS = '<style type="text/css">
.columns .form-list td.value {
  width: 100% !important;
}
.columns .form-list td.value ol {
  list-style: decimal outside;
  padding-left: 15px;
}
.columns .form-list td.value ol li {
  padding-left: 5px;
}
.columns .form-list colgroup,
.columns .form-list td.label,
.columns .form-list td.scope-label{
  display: none !important;
}
</style>';
   /**
    * Returns html part of the setting
    *
    * @param Varien_Data_Form_Element_Abstract $element
    * @return string
    */
   protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
   {
      return self::HTML.self::CSS;
   }
}
