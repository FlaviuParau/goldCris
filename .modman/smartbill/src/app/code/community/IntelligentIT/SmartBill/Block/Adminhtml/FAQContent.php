<?php

class IntelligentIT_SmartBill_Block_Adminhtml_FAQContent extends Mage_Adminhtml_Block_System_Config_Form_Field
{
  const HTML = '<ol>
  <li><strong>Ce drepturi trebuie sa aiba utilizatorul Smart Bill cu care voi emite documente din magazinul online?</strong><br/>Pentru o functionare corespunzatoare, utilizatorul Smart Bill folosit la autentificare in extensia de facturare trebuie sa aiba drepturi de vizualizare, emitere si editare pe tipul de documente pe care se va face emiterea.</li>
  <li><strong>Daca am mai multe firme in Smart Bill, pe care dintre ele se vor emite documentele mele?</strong><br/>In sectiunea de setari se poate alege compania pe care se vor emite documentele. Alegand compania, restul setarilor se vor prelua de pe respectiva companie (serii, unitati de masura etc), functie si de restrictiile pe serii ale utilizatorului conectat. Pentru functionarea corespunzatoare, contul de Smart Bill trebuie sa fie conectat pe aceeasi firma cu cea selectata in magazinul online.</li>
  <li><strong>Care e diferenta intre factura Magento si documentul Smart Bill?</strong><br/>Ambele documente vor fi accesibile din magazinul online si vor putea fi trimise clientului. Dar numai documentul Smart Bill va aparea in programul de facturare. Factura Magento este un document care confirma trecerea comenzii online in alta stare de prelucrare.</li>
  <li><strong>De unde si in ce stadiu al comenzii online pot emite documentul in Smart Bill?</strong><br/>Documentul Smart Bill poate fi emis indiferent de starea comenzii online. In ecranul de vizualizare a detaliilor unei comenzi apare butonul de Emitere in Smart Bill, in momentul in care extensia e corect instalata si conexiunea cu Smart Bill e realizata cu succes.</li>
  <li><strong>De ce suma din comanda Magento e diferita de cea de pe documentul Smart Bill?</strong><br/>Foarte probabil nu coincid setarile magazinului cu cele din extensia Smart Bill. Trebuie verificate setarile de cota TVA/tax, includerea transportului in documentul Smart Bill, locul de unde sunt preluate preturile produselor, cantitatea implicita utilizata la emiterea documentului Smart Bill.</li>
  <li><strong>De ce apare cantitatea 0 pe documentul Smart Bill?</strong><br/>Comenzile in Magento pot avea mai multe stari. Daca in extensia Smart Bill optam sa folosim pe documente cantitatea livrata, iar comanda este in stadiul de prelucrare, atunci cantitatea livrata e considerata 0. Trebuie modificata starea comenzii pentru ca programul sa preia cantitatea corespunzatoare (comandata/facturata/livrata, corespunzator starilor prelucrare sau in asteptare/facturata/livrata).</li>
  <li><strong>Ce se intampla cu optiunile de facturare din Magento daca modific configurarile din Smart Bill?</strong><br/>Dupa ce realizati o modificare in configurarile programului de facturare, trebuie improspatata conexiunea Magento-Smart Bill prin salvarea configurarilor din sectiunea Smart Bill > Autentificare si Smart Bill > Setari.</li>
  <li><strong>Cum stiu care comenzi sunt facturate in Smart Bill?</strong><br/>In tabelul de comenzi din sectiunea administrativa a magazinului online, langa coloana cu numarul comenzilor apare o coloana noua, cu link spre documentul Smart Bill emis pentru respectiva comanda. Linkul deschide documentul Smart Bill care poate fi rapid vizualizat/modificat/trimis prin email.</li>
  <li><strong>Nu apare linkul spre documentul Smart Bill, desi stiu ca am intocmit in Smart Bill</strong><br/>Daca linkul spre documentul Smart Bill nu apare in tabelul comenzilor, probabil ca documentul emis pentru acea comanda nu a fost finalizat si a ramas in stadiul de ciorna. In acest caz, se poate emite din nou documentul, iar programul va suprascrie ciorna precedenta, ori se poate accesa programul de facturare si se poate finaliza documentul din raportul de documente emise.</li>
  <li><strong>Am o eroare pe un document Smart Bill si vreau sa o raportez</strong><br/>In tabelul de comenzi din sectiunea administrativa a magazinului online, se bifeaza casuta in dreptul comenzii cu documentul problematic, iar din lista de Actiuni din dreapta sus se alege optiunea Trimite erori Smart Bill si se apasa Submit/Trimitere.</li>
  <li><strong>Contact Smart Bill</strong><br/>Informatii legate de programul de facturare Smart Bill se pot obtine de pe <a href="http://www.facturionline.ro/" rel="noopener" target="_blank">www.facturionline.ro</a> sau la <a href="mailto:cloud@smartbill.ro">cloud@smartbill.ro</a></li>
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
