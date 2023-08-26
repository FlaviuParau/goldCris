<?php

$installer = $this;
$installer->startSetup();

// Update package/theme values
$configUpdate = Mage::getModel('core/config');
$configUpdate->saveConfig('design/package/name', 'blugento', 'default', 0);
$configUpdate->saveConfig('design/head/default_robots', 'NOINDEX,NOFOLLOW');
$configUpdate->saveConfig('design/head/demonotice', '1', 'default', 0);
$configUpdate->saveConfig('catalog/custom_options/date_fields_order', 'd,m,y');
$configUpdate->saveConfig('catalog/custom_options/time_format', '24h');

$query = 'UPDATE `cms_block` SET `content`=\'<ul>
<li>Despre noi</li>
<li><a href="{{store url="politica-de-confidentialitate"}}">Politica de confidenţialitate</a></li>
<li><a href="{{store url="politica-de-utilizare-cookie-uri"}}">Politica de utilizare cookie-uri</a></li>
<li><a href="#" class="show-gdpr-cookie">Schimbă-ți consimțământul</a></li>
<li><a href="{{store url="termeni-si-conditii"}}">Termeni si condiţii</a></li>
<li><a href="http://www.anpc.gov.ro">ANPC</a></li>
</ul>
<ul>
<li>Servicii</li>
<li><a href="{{store url="livrare"}}">Livrare</a></li>
<li><a href="https://webgate.ec.europa.eu/odr/main/index.cfm?event=main.home.chooseLanguage">Soluționarea litigiilor</a></li>
</ul>\' WHERE `identifier` LIKE \'footer_links\';
        INSERT INTO `cms_page` (`title`, `root_template`, `identifier`, `content`, `is_active`) VALUES (\'Termeni si conditii\', \'one_column\', \'termeni-si-conditii\', \'<p><strong>Termeni și Condiții</strong></p>
<p>www.magazinultau.ro</p>
<p>&nbsp;</p>
<p><strong>1. TERMENI ȘI CONDIȚII</strong></p>
<p>Folosirea site-ului&nbsp;www.magazinultau.ro&nbsp;presupune acceptarea prezentelor Termeni și Condiții de către utilizator.</p>
<p>&nbsp;</p>
<p><strong>2. COMPANIA</strong></p>
<p>COMPANIE SRL</p>
<p>Reg. com.: J../.../...</p>
<p>CUI:</p>
<p>IBAN:</p>
<p>Adresa:</p>
<p>Punct de lucru:</p>
<p>&nbsp;</p>
<p><strong>3. INFORMAȚII GENERALE</strong></p>
<p>Imaginile publicate pe site sunt conforme cu realitatea, &icirc;nsa uneori nuanțele culorilor produselor pot să difere ușor, ca urmare a procesului de execuție al fotografiilor sau a setărilor monitorului tau. Clienții&nbsp;www.magazinultau.ro&nbsp;vor fi &icirc;ntotdeauna informați dacă un produs este sau nu este &icirc;n stoc, dacă se poate produce la comandă, &icirc;n c&acirc;t timp se va prelucra și &icirc;n c&acirc;t timp va ajunge la ei. Aceste informații vor fi transmise prin e-mail sau telefon. Imaginile cu produsele, ofertele, prețurile și concursurile pot fi schimbate &icirc;n prealabil, fără o notificare specifică. Produsele comercializate prin intermediul&nbsp;www.magazinultau.ro&nbsp;, vor fi insoțite &icirc;n momentul livrării de factură fiscală sau de bon fiscal.</p>
<p>&nbsp;</p>
<p><strong>4. PROPRIETATEA - DREPTURI DE AUTOR</strong></p>
<p>Utilizarea site-ului&nbsp;www.magazinultau.ro&nbsp;(incluz&acirc;nd vizitarea și cumpărarea produselor) implică acceptarea anumitor termeni și condiții de utilizare, evident, sub forma unui contract legal &icirc;ntre utilizator și COMPANIE SRL, de aceea, se recomandă citirea cu atenție a paragrafelor următoare.</p>
<p>Vizitarea sau cumpărarea produselor de pe site-ul&nbsp;www.magazinultau.ro&nbsp;implică &icirc;n mod automat, acceptarea acestor termeni și condiții. De asemenea, folosirea oricăror alte servicii curente sau viitoare furnizate de COMPANIE SRL, implică acceptarea acelorași termeni și condiții.</p>
<p>&Icirc;ntregul conținut al site-ului&nbsp;www.magazinultau.ro&nbsp;este protejat conform legii dreptului de autor și legilor privind drepturile de proprietate intelectuală. Folosirea fără acordul scris al COMPANIE SRL. a oricăror elemente aparțin&acirc;nd site-ului&nbsp;www.magazinultau.ro&nbsp;se pedepsește conform legilor &icirc;n vigoare.</p>
<p>Societatea NU conferă dreptul utilizatorului de a modifica parțial sau integral site-ul web, de a reproduce parțial sau integral site-ul, de a copia, vinde sau exploata site-ul &icirc;n orice altă manieră fără acordul societății. Vizitatorul / Clientul nu va modifica, copia, distribui, transmite, afișa, publica, reproduce, acorda licențe, crea produse derivate, transfera sau vinde orice fel de informație sau servicii obținute de pe sau prin intermediul acestui site web. Vă aducem la cunoștință că BRAND este marcă &icirc;nregistrată, &icirc;n acest sens orice utilizare a mărcii fără acordul societății fiind pedepsită conform legii.</p>
<p>Conținutul prezentului site, inclusiv, dar fără a se limita la logo, reprezentări stilizate, simboluri, imagini, fotografii, conținut texte și altele asemenea, sunt proprietatea exclusivă a COMPANIE SRL. Este nepermisă copierea, distribuirea, publicarea, modificarea, completarea, utilizarea, expunerea, includerea, legarea, transmiterea, &icirc;ndepărtarea &icirc;nsemnelor, fotografiilor, imaginilor, bucăților de text, afișarea, vinderea, etc, a conținutului, datelor, informațiilor, fotografiilor sau altor informații găsite pe site, fără permisiunea expresă acordată &icirc;n scris de către COMPANIE SRL.</p>
<p>Niciun Client nu dob&acirc;ndește, prin utilizarea și accesarea site-ului vreun drept sau vreo licență de utilizare a vreuneia dintre informațiile de pe site. Niciun client nu are dreptul de a utiliza un dispozitiv automat sau manual pentru a monitoriza materialele disponibile pe site.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>5. CLIENȚII &ndash; CONFIDENȚIALITATE ȘI PROTECȚIA DATELOR CU CARACTER PERSONAL</strong></p>
<p>Clientul are obligația de a păstra confidențialitatea asupra informațiilor despre care a luat la cunoștință cu ocazia accesării și utilizării site-ului, precum și cu ocazia comunicărilor transmise pe parcursul &icirc;nregistrării, autentificării, plasării și onorării comenzilor și alte asemenea aspecte &icirc;n legătură cu utilizarea site-ului. Clientului &icirc;i este interzis să dezvăluie către terți raporturile sale cu COMPANIE SRL, fără consimțăm&acirc;ntul expres al societății, oferit &icirc;n prealabil &icirc;n scris.</p>
<p>Transmiterea de către Client de informații sau materiale prin intermediul acestui site, idei, concepte, know-how, tehnici, etc. ne oferă acces nerestricționat și irevocabil la acestea, dreptul de a utiliza, reproduce, afișa, modifica, transmite și distribui aceste materiale sau informații.</p>
<p>Disponibilitatea unui produs &icirc;n stoc, fie că face parte dintr-o promoție sau nu, este anunțată prin email după plasarea comenzii de către Client.&nbsp;</p>
<p>www.magazinultau.ro&nbsp;are o politică strictă de respectare a confidențialității datelor clienților.</p>
<p>Prin acesarea site-ului și a produselor noastre, COMPANIE SRL prelucrează datele dvs. cu caracter personal necesare &icirc;ndeplinirii scopurilor pentru care aceste date au fost solicitate și stocate. In ceea ce privește drepturile dvs. &icirc;n calitate de persoană vizată astfel cum sunt ele garantate de Regulamentul 2016/679/UE privind protecția persoanelor fizice &icirc;n ceea ce privește prelucrarea datelor cu caracter personal și privind libera circulație a acestor date vă rugăm să accesați Politica de Securitate pusă la dispoziție și celelalte măsuri implementate pentru garantarea drepturilor dvs.</p>
<p>&nbsp;</p>
<p><strong>6. INFORMAȚII PRODUSE ȘI PREȚURI</strong></p>
<p>Prețurile afișate &icirc;n cadrul site-ului&nbsp;www.magazinultau.ro&nbsp;sunt &icirc;n RON. Prețurile afișate sunt valabile la momentul accesării paginii unde acestea sunt afișate, COMPANIE SRL rezerv&acirc;ndu-și dreptul de a le modifica oric&acirc;nd, fără niciun preaviz sau notificare.</p>
<p>Prin efectuarea unei comenzi pe site-ul&nbsp;www.magazinultau.ro&nbsp;&nbsp;vă obligați irevocabil să plătiți contravaloarea produselor comandate. &Icirc;n acest sens vă informăm că, &icirc;n cadrul procedurii de &icirc;nregistrare a comenzilor dumneavoastră, veți fi redirecționați către site-urile colaboratorilor noștri &icirc;n vederea colectării datelor necesare plății.</p>
<p>&nbsp;</p>
<p><strong>7. &Icirc;NREGISTRAREA CA UTILIZATOR</strong></p>
<p>Orice client care dorește acest lucru poate să se &icirc;nregistreze pe site-ul nostru cu un simplu click pe butonul de &Icirc;nregistrare disponibil pe pagina principală și parcurgerea adecvată a pașilor solicitați &icirc;n vederea &icirc;nregistrării.</p>
<p>Pentru &icirc;nregistrare, vi se va solicita să vă introduceți numele, prenumele, adresa de email și o parolă. Aveți obligația de a introduce o adresă de email validă, &icirc;n caz contrar nu va fi posibilă continuarea procedurii de &icirc;nregistrare pe site. Sunteți pe deplin responsabil pentru păstrarea &icirc;n siguranță a parolei de acces precum și a oricărei informații privind &icirc;nregistrarea, autentificarea și contul. Sunteți pe deplin responsabil pentru orice comandă și/sau plată efectuată prin utilizarea site-ului nostru din contul dumneavoastră.</p>
<p>&nbsp;</p>
<p><strong>8. PLASAREA ȘI CONFIRMAREA COMENZII</strong></p>
<p>Plasarea comenzii se realizează prin parcurgerea următorilor pași:</p>
<p>1. selectați produsele dorite și le adăugați &icirc;n Coșul de cumpărături, cu un click pe butonul &bdquo;Adaugare &icirc;n Coș&rdquo;,</p>
<p>2. dacă doriți să adăugați mai multe produse &icirc;n coș, faceți click pe butonul &bdquo;Continuă cumpărăturile&rdquo;, dacă doriți să finalizați comanda faceți click pe butonul &bdquo;Finalizează comanda&rdquo;,</p>
<p>3. după selectarea produselor dorite, &icirc;n cazul &icirc;n care nu aveți un cont pe site și nu doriți să vă creați un cont pe site, selectați &ldquo;Finalizare comandă ca Vizitator&rdquo; introduceți adresa dvs de email, pentru confirmarea primirii comenzii</p>
<p>4. introduceți datele de facturare,</p>
<p>5. introduceți adresa de livrare,</p>
<p>6. livrarea se face prin curier,</p>
<p>7. alegeți metoda de plată,</p>
<p>8. bifați c&acirc;mpul &bdquo;de acord cu Termenii și condițiile de pe site&rdquo;,</p>
<p>9. aveți posibilitatea de a vă da acordul pentru primirea de newslettere,</p>
<p>10. plasați comada prin click pe butonul &bdquo;Finalizează comanda&rdquo;</p>
<p>&nbsp;</p>
<p><strong>9. PLĂȚI ȘI LIMITĂRI</strong></p>
<p>COMPANIE SRL &icirc;ncearcă, &icirc;n cadrul site-ului, să ofere clienților săi o descriere c&acirc;t mai detaliată asupra produselor sale, &icirc;nsă nu garantează că descrierea produselor sau orice alt conținut al site-ului&nbsp;www.magazinultau.ro&nbsp;&nbsp;este completă sau total lipsită de erori. De asemenea COMPANIE SRL nu &icirc;și asumă responsabilitatea pentru nici un tip de pierderi cum ar fi: pierderi de date, profituri pierdute, imposibilitatea de a utiliza datele de pe acest site, modificări fără anunțarea &icirc;n prealabil ale datelor, prețurilor sau structurii site-ului.</p>
<p>Clientul are obligația de a opta pentru modalitatea de plată aleasă mai &icirc;nainte de plasarea comenzii, prin bifarea opțiunii corespunzătoare, conform pașilor de plasare a comenzii.</p>
<p>V&acirc;nzătorul va emite către Client o factură pentru produsele livrate. &Icirc;n acest sens, Clientul are obligația de a furniza V&acirc;nzătorului toate informațiile necesare emiterii facturii, conform legislației &icirc;n vigoare. Factura va fi comunicată Clientului tipărită &icirc;n format material cu ocazia livrării produselor comandate.</p>
<p>&nbsp;</p>
<p><strong>10. POLITICA DE LIVRARE</strong></p>
<p>Produsele se livrează de către v&acirc;nzător, direct la domiciliul cumpărătorului / adresa specificată de acesta.</p>
<p>Transportul se asigură prin firma de curierat CURIER.</p>
<p>Taxa de transport este de ... RON.</p>
<p>Expedierea comenzii pentru produsele aflate &icirc;n stoc se va realiza &icirc;n termen de 24 ore de la confirmarea comenzii, prin email. Comanda ar trebui să ajungă la dumneavoastră &icirc;n termen de maxim 2 zile lucratoare de la data confirmării comenzii. Acest termen poate fi modificat de &icirc;mprejurări independente de voința noastră, fără a depăși un termen de 10 zile lucratoare, calculat de la data preluării comenzii dumneavoastră.</p>
<p>Comanda va fi expediată &icirc;n ambalaje care să asigure securitatea transportului. Răspunderea pentru orice deteriorare cauzată &icirc;n timpul transportului produsului, coletului sau pachetului trimis de către noi, revine transportatorului conform legislației locale &icirc;n vigoare.</p>
<p>&nbsp;</p>
<p><strong>11. PROCEDURA DE REZOLVARE A RECLAMAȚIILOR</strong></p>
<p>Orice nemulțumire legată de accesarea, utilizarea, &icirc;nregistrarea pe site-ul nostru, efectuarea unei comenzi, aspecte legate de comanda efectuată și alte asemenea, ne va fi comunicată direct, fie telefonic, la NR DE TELEFON 0700 000 000, fie prin email la adresa EMAIL@EMAIL.COM.</p>
<p>Nemulțumirea dvs va fi &icirc;nregistrată și veți primi un răspuns &icirc;n scris, pe adresa de email menționată cu ocazia aducerii la cunoștința noastră a nemulțumirii dvs, &icirc;n termen de cel mult 3 zile lucratoare.</p>
<p>Clientul declară că este de acord să nu facă publice aceste nemulțumiri (pe rețelele de socializare, media, discuții la petreceri private sau &icirc;n orice altă modalitate) sub rezerva suportării daunelor cauzate pentru prejudiciul de imagine adus Companiei prin aceste acțiuni.</p>
<p>&nbsp;</p>
<p><strong>12. MODIFICARI ALE TERMENILOR ȘI CONDIȚIILOR</strong></p>
<p>COMPANIE SRL &icirc;și rezervă dreptul, de a efectua schimbări &icirc;n orice moment cu privire la conținutul site-ului&nbsp;www.magazinultau.ro&nbsp;, politica, termenii și condițiile de utilizare, fără a fi necesară o notificare prealabilă către utilizatori &icirc;n acest sens, desi vom fi atenți să anunțăm clienții despre aceste modificări.</p>
<p>Utilizatorul site-ului&nbsp;www.magazinultau.ro&nbsp;are obligația de a verifica &icirc;n mod regulat termenii și conditiile de utilizare. &Icirc;n cazul &icirc;n care nu este de acord cu modificările aduse acestora de catre COMPANIE SRL., va &icirc;nceta utilizarea site-ului. Dacă utilizatorul continuă să folosească site-ul, se considera ca este de acord cu modificările efectuate.</p>
<p>&nbsp;</p>
<p><strong>13. RĂSPUNDEREA CONTRACTUALĂ</strong></p>
<p>Prin crearea și utilizarea Contului, Clientul &icirc;și asumă răspunderea pentru menținerea confidentialității datelor de Cont (user și parola) și pentru gestionarea accesării Contului, fiind responsabil &icirc;n condițiile legii de activitatea derulata prin intermediul Contului său.</p>
<p>Prin accesarea site-ului, crearea Contului, utilizarea site-ului, plasarea comenzilor, etc. Clientul acceptă &icirc;n mod expres și neechivoc Termenii și Condițiile site-ului &icirc;n ultima sa versiune comunicată &icirc;n cadrul site-ului. Ulterior creării Contului, utilizarea conținutului echivalează cu acceptarea modificărilor intervenite asupra Termenilor și condițiilor site-ului și/sau a versiunilor actualizate ale Termenilor și Condițiilor Site-ului. Clientul este responsabil pentru verificarea versiunii finale a Termenilor și Condițiilor ori de c&acirc;te ori utilizează site-ul.</p>
<p>Acceptarea Termenilor și Conditiilor site-ului se confirmă prin bifarea checkboxului corespunzator din site și/sau prin trimiterea Comenzii și/sau prin efectuarea unei plăți online.</p>
<p>Nu ne asumăm responsabilitatea pentru daune de orice fel pe care Clientul sau orice altă terță persoană le-ar putea suferi ca rezultat al &icirc;ndeplinirii de către noi a oricăreia dintre obligațiile noastre conform Comenzii și nici pentru daune care ar putea rezulta din utilizarea Bunurilor după livrare și cu at&acirc;t mai puțin pentru pierderea acestora.</p>
<p>Nu putem fi ținuți responsabili pentru daune aduse calculatorului dumneavoastră sau viruși care ar putea să vă infecteze calculatorul sau alt echipament ca urmare a accesării, utilizării de către dumneavoastră sau navigării pe site-ul nostru sau descărcarea de către dumneavoastră a vreunui conținut, informație, materiale, date, text, imagini, video sau audio de pe site-ul nostru.</p>
<p>&nbsp;</p>
<p><strong>14. POLITICA DE RETUR</strong></p>
<p>Clienții care nu sunt mulțumiți de produs &icirc;l pot returna și/sau pot alege un alt produs &icirc;n limita contravalorii produsului returnat, cu respectarea termenilor și condițiilor de mai jos. Nu vor fi acceptate pentru returnare produsele care vor fi desigilate, &nbsp;lipsa etichetelor sau a ambalajelor originale. &Icirc;nainte de a returna produsele, clienții trebuie să notifice prealabil comerciantul &icirc;n scris către adresa de e-mail EMAIL@EMAIL.COM, unde vor specifica următoarele:</p>
<p>- &nbsp;&nbsp;&nbsp;&nbsp;produsul/produsele pe care doresc să le returneze</p>
<p>- &nbsp;&nbsp;&nbsp;&nbsp;motivul returului</p>
<p>- &nbsp;&nbsp;&nbsp;&nbsp;opțiunea &icirc;nlocuirii sau rambursării sumei</p>
<p>&nbsp;</p>
<p><strong>15. FORȚA MAJORA</strong></p>
<p>Niciuna dintre părțile contractante nu răspunde de neexecutarea la termen sau/și de executarea &icirc;n mod necorespunzător - total sau parțial - a oricărei obligații care &icirc;i revine &icirc;n baza prezentului contract, daca neexecutarea sau executarea necorespunzătoare a obligației respective a fost cauzată de forța majoră, așa cum este definită de lege, din motive independente de părți.</p>
<p>Partea care invocă forța majoră este obligată să notifice celeilalte părți, &icirc;n termen de 5 (cinci) zile, producerea evenimentului și să ia toate măsurile posibile &icirc;n vederea limitării consecințelor lui.</p>
<p>&nbsp;</p>
<p><strong>16. LEGEA APLICABILĂ</strong></p>
<p>Contractul va fi guvernat și interpretat &icirc;n conformitate cu legea rom&acirc;nă. Orice ne&icirc;nțelegere intervenită &icirc;ntre Comerciant și Client &icirc;n legătură cu raporturile decurg&acirc;nd din prezentul Contract urmează să fie rezolvate pe cale amiabilă, iar &icirc;n caz de eșec, să fie supuse instanțelor competente material din ORAS.</p>
<p>&nbsp;</p>
<p><strong>17. DISPOZIȚII FINALE</strong></p>
<p>Acest site este deținut de către COMPANIE SRL, care vă oferă dreptul de a accesa și utiliza site-ul sub rezerva acceptării de către dumneavoastră a acestor Termeni și condiții. Acces&acirc;nd și utiliz&acirc;nd site-ul vă oferiți automat și neechivoc acordul pentru respectarea Termenilor și condițiilor de pe site.</p>
<p>COMPANIE SRL are dreptul de a modifica Termenii și condițiile oric&acirc;nd, fără o notificare prealabilă, post&acirc;nd varianta actualizată pe site, iar Clientul are obligația de a citi Termenii și condițiile ori de c&acirc;te ori accesează site-ul. Clientul are obligația de a respecta &icirc;ntocmai Termenii și condițiile de pe site și nu poate pretinde și nu se poate apăra cu necunoașterea Termenilor și condițiilor de pe site, valabili la data accesării, utilizării și/sau plasării unei comenzi pe site.</p>
<p>COMPANIE SRL organizează periodic diferite Promoții care sunt anunțate pe site. Promoțiile &icirc;și &icirc;ncep valabilitatea &icirc;n momentul activării acestora pe site și &icirc;și &icirc;ncetează valabilitatea din momentul inactivării acestora pe site. Promoțiile nu se cumulează &icirc;ntre ele sau cu alte reduceri și sunt valabile numai &icirc;n limita stocului.</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
\', \'0\');      
        UPDATE `cms_page` SET `identifier`=\'politica-de-confidentialitate\', `content`=\'<p align="center" style="text-align: center;">Politica de confidenţialitate privind protecția datelor cu caracter personal</p>
<p style="text-align: center;">&nbsp;</p>
<p style="text-align: justify;">&Icirc;ncep&acirc;nd cu data de 25 mai 2018, Regulamentul 2016/679/UE privind protecția persoanelor fizice &icirc;n ceea ce privește prelucrarea datelor cu caracter personal și privind libera circulație a acestor date (&icirc;n continuare "Regulamentul") va fi aplicat de toate statele Uniunii Europene. Prin intermediul acestui Regulament se dorește crearea unui cadru legislativ unitar și uniform pe teritoriul Uniunii Europene care să nu mai necesite măsuri naționale de implementare.</p>
<p style="text-align: justify;">Este responsabil pentru operarea paginii web www.BRAND.ro și are&nbsp;calitatea de operator&nbsp;COMPANIE SRL&nbsp;cu sediul &icirc;n _________________________, Str. _________________________, nr. _________________________, Jud. _________________________, &icirc;nregistrată la ORC sub nr. _________________________, av&acirc;nd CIF &nbsp;_________________________, prin reprezentant legal _________________________.</p>
<p style="text-align: justify;">Această pagină web se adresează exclusiv utilizatorilor cu v&acirc;rsta peste 16 ani.</p>
<p style="text-align: justify;">&Icirc;n vederea respectării obligațiilor care ne revin, deriv&acirc;nd din Regulament, și av&acirc;nd &icirc;n vedere că protecția datelor dvs reprezintă o preocupare majoră și constantă pentru noi, am elaborat prezentul document, care stabilește&nbsp;categoriile de date cu caracter personal&nbsp;pe care le colectăm la vizita dvs pe pagina noastră web,&nbsp;scopul și temeiul prelucrării,&nbsp;durata prelucrării,&nbsp;unde păstrăm și cui transmitem aceste date, &nbsp;precum și&nbsp;drepturile pe care le aveți &icirc;n calitatea dvs de persoana vizata,&nbsp;implementate special pentru a asigura protecția drepturilor și libertăților fundamentale ale dvs. și &icirc;n special a dreptului acestora la protecția datelor cu caracter personal.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>1. Definiții</strong></p>
<p style="text-align: justify;">&rdquo;date cu caracter personal&rdquo;&nbsp;orice informații privind o persoană fizică identificată sau identificabilă (&bdquo;persoana vizată&rdquo;); o persoană fizică identificabilă este o persoană care poate fi identificată, direct sau indirect, &icirc;n special prin referire la un element de identificare, cum ar fi un nume, un număr de identificare, date de localizare, un identificator online, sau la unul sau mai multe elemente specifice, proprii identității sale fizice, fiziologice, genetice, psihice, economice, culturale sau sociale.</p>
<p style="text-align: justify;">&bdquo;prelucrare&rdquo;&nbsp;&icirc;nseamnă orice operațiune sau set de operațiuni efectuate asupra datelor cu caracter personal sau asupra seturilor de date cu caracter personal, cu sau fără utilizarea de mijloace automatizate, cum ar fi colectarea, &icirc;nregistrarea, organizarea, structurarea, stocarea, adaptarea sau modificarea, extragerea, consultarea, utilizarea, divulgarea prin transmitere, diseminarea sau punerea la dispoziție &icirc;n orice alt mod, alinierea sau combinarea, restricționarea, ștergerea sau distrugerea;</p>
<p style="text-align: justify;">&bdquo;operator&rdquo;&nbsp;&icirc;nseamnă persoana fizică sau juridică, autoritatea publică, agenția sau alt organism care, singur sau &icirc;mpreună cu altele, stabilește scopurile și mijloacele de prelucrare a datelor cu caracter personal; atunci c&acirc;nd scopurile și mijloacele prelucrării sunt stabilite prin dreptul Uniunii sau dreptul intern, operatorul sau criteriile specifice pentru desemnarea acestuia pot fi prevăzute &icirc;n dreptul Uniunii sau &icirc;n dreptul intern;</p>
<p style="text-align: justify;">&rdquo; persoană &icirc;mputernicită&rdquo; &icirc;nseamnă persoana fizică sau juridică, autoritatea publică, agenția sau alt organism care prelucrează datele cu caracter personal &icirc;n numele operatorului;&nbsp;</p>
<p style="text-align: justify;">Vă asigurăm că datele dvs. cu caracter personal sunt prelucrate&nbsp;&icirc;n mod legal, echitabil și transparent, doar pentru &icirc;ndeplinirea scopurilor explicite care v-au fost aduse la cunoștință.</p>
<p style="text-align: justify;">COMPANIE SRL&nbsp;&icirc;n calitate de operator, prelucrează datele &icirc;ntr-un mod care asigură securitatea adecvată a datelor cu caracter personal, inclusiv protecția &icirc;mpotriva prelucrării neautorizate sau ilegale și &icirc;mpotriva pierderii, a distrugerii sau a deteriorării accidentale, prin luarea de măsuri tehnice sau organizatorice corespunzătoare.&nbsp;</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>&nbsp;2.&nbsp;Datele dvs. cu caracter personal pe care le prelucrăm. Scopul. Temeiul. Durata Prelucrării</strong></p>
<p style="text-align: justify;">Colectăm de la dvs. doar acele date cu caracter personal care ne sunt necesare pentru a vă putea oferi posibilitatea de utilizare a paginii noastre web, pentru a putea onora comenzile dvs. și pentru a vă putea oferi acces la produsele noastre și pentru a vă putea ține la curent cu produsele, serviciile și ofertele noastre (marketing direct), &icirc;n măsura &icirc;n care acest lucru este permis de reglementările legale sau se realizează pe baza consimțăm&acirc;ntului dvs.</p>
<p style="text-align: justify;">&Icirc;n general prelucrăm următoarele date cu caracter personal ale dvs.:</p>
<ul style="text-align: justify;" type="disc">
<li>Nume și prenume</li>
<li>Adresa de contact și adresa de livrare</li>
<li>Număr de telefon</li>
<li>Adresă de e-mail</li>
<li>IP</li>
</ul>
<p style="text-align: justify;">Fiecare categorie de date va fi colectată &icirc;n scopuri determinate, explicite și legitime și nu sunt prelucrate ulterior &icirc;ntr-un mod incompatibil cu aceste scopuri și vor fi pe o perioadă care nu depășește perioada necesară &icirc;ndeplinirii scopurilor &icirc;n care sunt prelucrat.</p>
<p style="text-align: justify;">Pentru a asigura principiul transparenței informațiilor la prelucrarea datelor dvs. cu caracter personal vă comunicăm &icirc;n cele ce urmează modul &icirc;n care prelucrăm datele tale, scopurile și temeiul juridic (baza legală) al activităților prin care prelucrăm datele tale.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>CREAREA UNUI CONT</strong></p>
<p style="text-align: justify;">&Icirc;n momentul creării unui cont de utilizator pe site-ul nostru&nbsp;www.BRAND.ro, vă solicităm și colectăm numele, prenumele, adresa, nr. de telefon și adresa de email, IP.</p>
<p style="text-align: justify;">Aceste date cu caracter personal ne sunt necesare &icirc;n scop precontractual, pentru a vă putea permite să accesați produsele noastre și pentru a putea efectua o comandă, temeiul prelucrării fiind obligația contractuală. &Icirc;n măsura &icirc;n care nu sunteți de acord cu prelucrarea datelor dvs.&nbsp;COMPANIE SRL&nbsp;nu va putea onora și livra comenzile dvs.</p>
<p style="text-align: justify;">&nbsp;Datele dvs. prelucrate la momentul creării contului nu fac obiectul unei decizii bazate exclusiv pe prelucrarea automată, inclusiv crearea de profiluri, &nbsp;vor fi divulgate niciunui terț și nu vor face obiectul unul transfer către o țară terță sau o organizație internațională.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>REALIZAREA UNEI COMENZI</strong></p>
<p style="text-align: justify;">&Icirc;n momentul completării și finalizării formularului de comandă vă solicităm și colectăm de la dvs. numele, prenumele, adresa de contact, adresa de livrare, nr. de telefon și adresa de email și IP</p>
<p style="text-align: justify;">Aceste date cu caracter personal ne sunt necesare &icirc;n scop contractual, pentru a-ți putea onora comenzile, a livra produsele la adresa indicată, &nbsp;a ne executa obligațiile de garanție asociate produselor, sau, după caz, a face un retur de produse. Temeiul prelucrării &icirc;l reprezintă obligația contractuală dintre părți astfel cum a fost ea stabilită prin Termeni și Condiții. &Icirc;n măsura &icirc;n care nu sunteți de acord cu prelucrarea datelor dvs.&nbsp;COMPANIE SRL&nbsp;nu va putea onora și livra comenzile dvs.</p>
<p style="text-align: justify;">Totodată datele dvs. cu caracter personal ne sunt necesare pentru a completa și livra facturile fiscale aferente produselor livrate. Datele dvs. de plată online nu vor fi accesibile si nici nu vor fi stocate de către&nbsp;COMPANIE SRL, ci doar de furnizorul serviciului de plată electronică sau de o altă entitate autorizată să presteze servicii de stocare date de identificare a cardului, despre a cărei identitate vei fi informat, anterior introducerii efective a detaliilor cardului pe care &icirc;l folosești pentru plata online. Singurele date privind plata efectuată pe care noi le vom stoca sunt cele legate de data inițierii și finalizării tranzacției precum și situația plății. &Icirc;n temeiul obligației legale datele dvs. cu caracter personal necesare &icirc;ntocmirii documentelor de plată vor fi furnizate partenerilor noștri contractuali care ne furnizează serviciile IT și vor fi folosite &icirc;n vederea depunerii declarațiilor fiscale și contabile la autoritățile fiscale.</p>
<p style="text-align: justify;">Datele dvs. cu caracter personal vor fi păstrate pe &icirc;ntreaga durată a raportului contractual precum și pe un termen de 3 ani de la data plasării ultimei comenzi acesta fiind termenul &nbsp;de prescripție.</p>
<p style="text-align: justify;">Pentru a ne putea executa la timp și conform obligațiile asumate, datele dvs. cu caracter personal for fi divulgate partenerilor noștri contractuali de &icirc;ncredere, selectați &icirc;n mod atent:</p>
<ul style="text-align: justify;" type="disc">
<li>furnizorul serviciului de stocare a datelor pe servere externe situat &icirc;n Rom&acirc;nia;</li>
<li>furnizorul serviciilor de contabilitate</li>
<li>furnizorul serviciilor de comunicări și transmiteri corespondență pe e-mail</li>
<li>furnizorul serviciilor de telefonie mobilă care ne ajută să ținem legătura cu tine, situat &icirc;n Rom&acirc;nia;</li>
<li>curieri cu care avem &icirc;ncheiat un raport contractual;</li>
<li>procesatorii serviciului de plăți online</li>
</ul>
<p style="text-align: justify;">Datele dvs. prelucrate la momentul creării contului nu fac obiectul unei decizii bazate exclusiv pe prelucrarea automată, inclusiv crearea de profiluri și nu vor face obiectul unul transfer către o țară terță sau o organizație internațională.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>NEWSLETTER &ndash; MARKETING DIRECT</strong></p>
<p style="text-align: justify;">Abonarea și dezabonarea la newsletter-ul BRAND este gratuită și voluntară&nbsp;și se face exclusiv &icirc;n baza consimțăm&acirc;ntului dvs.&nbsp;Newsletter-ul reprezintă un mijloc de informare periodic, exclusiv electronic (e-mail, SMS) asupra Bunurilor, Serviciilor, Promoțiilor, etc. V&acirc;nzătorului&icirc;ntr-o anumită perioadă, fără niciun angajament din partea V&acirc;nzătorului cu referire la informațiile conținute de acesta.</p>
<p style="text-align: justify;">Pentru a asigura serviciile de marketing colectăm de la dvs. adresa de e-mail, nr. de telefon, nume, prenume. Aceste date for fi folosite &icirc;n mod exclusiv pentru a vă ține la curent cu privire la produsele, serviciile și ofertele noastre. Temeiul prelucrării datelor cu caracter personal &icirc;l reprezintă consimțăm&acirc;ntul tău, durata prelucrării fiind &icirc;ntreaga durată a existenței consimțăm&acirc;ntului.</p>
<p style="text-align: justify;">Consimțăm&acirc;ntul poate fi retras oric&acirc;nd printr-un email trimis la adresa de mail ________________________ sau prin accesarea link-ului de dezabonare din cuprinsul e-mailului primit de la noi și va avea drept consecință &icirc;ncetarea prelucrării. Retragerea consimțăm&acirc;ntului nu va afecta legalitatea prelucrării efectuată &icirc;nainte de retragerea acestuia.</p>
<p style="text-align: justify;">Datele tale nu vor fi transferate către niciun alt operator sau &icirc;mputernicit și nu vor fi transmise către nicio țară terță și sau organizație internațională</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>COOKIE-URI</strong></p>
<p style="text-align: justify;">Site-ul www.BRAND.ro foloseşte cookie-uri, acestea fiind date stocate pe hard disk-ul utilizatorului, conţin&acirc;nd informaţii despre acesta. Folosirea mecanismului de tip cookie reprezintă un avantaj &icirc;n folosul vizitatorilor, acesta permiţ&acirc;nd memorarea unor opţiuni de navigare &icirc;n site precum limba &icirc;n care se afişează site-ul, tip de filtre care se aplică la afişarea unor anumite pagini, memorarea numelui de utilizator şi a parolei pentru un acces rapid la conţinutul site-ului. Neacceptarea unui cookie nu &icirc;nseamnă că utilizatorului &icirc;i va fi refuzat accesul de navigare &icirc;n site sau de citire a conţinutului acestuia.&nbsp;Cu ajutorul cookie-urilor, proprietarii de site-uri pot monitoriza şi segmenta interesele utilizatorilor faţă de anumite zone sau aplicaţii ale site-ului, fapt care le permite ulterior &icirc;mbunătăţirea experinţei de navigare, introducerea unui conţinut relevant pentru utilizator etc. Unii dintre partenerii noştri de afaceri folosesc cookie-uri pe site-ul nostru (ex: cei care &icirc;şi fac publicitate). Cu toate acestea, nu avem acces şi nici nu putem controla aceste cookie-uri.</p>
<p style="text-align: justify;">Aceste fișiere cookie ne permit să salvăm adresa dvs. de e-mail, astfel &icirc;nc&acirc;t să fiți recunoscut și logat automat la vizita dvs. următoare.</p>
<p style="text-align: justify;">Bine&icirc;nțeles că puteți accesa pagina noastră web și fără cookies.</p>
<p style="text-align: justify;">&Icirc;n cazul &icirc;n care nu doriți să recunoaștem computerul dvs., puteți să &icirc;mpiedicați stocarea de cookies pe hard discul dvs. prin dezactivarea stocărilor de cookies &icirc;n setările browser-ului dvs. Puteți consulta modul detaliat de funcționare &icirc;n instrucțiunile browser-ului dvs. Dacă nu acceptați cookies, aceasta poate conduce la limitări funcționale ale ofertelor noastre.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>LINK-URI</strong></p>
<p style="text-align: justify;">Acest site conţine link-uri către alte site-uri.&nbsp;COMPANIE SRL&nbsp;nu este responsabil de politica de confidenţialitate practicată de aceştia. Vă recomandăm consultarea &icirc;n prealabil a termenilor legali şi a celorlalte informaţii referitoare la colectarea informaţiilor cu caracter personal. Normele expuse &icirc;n acest text se aplică doar &icirc;n cazul informaţiilor colectate pe acest site.</p>
<p style="text-align: justify;">Atunci c&acirc;nd accesați o pagină web care conține plugins, browser-ul dvs. stabilește o legătură directă cu serverele furnizorilor respectivi. Conținutul plugins-ului va fi transmis de furnizorul respectiv direct către browser-ul dvs. și va fi integrat de acesta &icirc;n pagina web. Prin integrarea plugins-ului, furnizorul respectiv primește informația că browser-ul dvs. a accesat pagina noastră web. Aceasta se &icirc;nt&acirc;mplă indiferent dacă aveți un profil pe rețeaua socială respectivă sau dacă tocmai v-ați logat.</p>
<p style="text-align: justify;">&Icirc;n cazul &icirc;n care sunteți logat pe rețeaua socială respectivă, aceasta poate atribui vizita pe pagina noastră web profilului dvs. de pe rețeaua socială respectivă. Dacă interacționați cu plugins, de exemplu prin acționarea butonului &rdquo;&Icirc;mi place&rdquo; de pe Facebook sau faceți un comentariu, informația corespunzătoare va fi transmisă direct către furnizorul respectiv și stocată acolo.</p>
<p style="text-align: justify;">Informații privind scopul și volumul colectării datelor, prelucrarea și utilizarea ulterioară a datelor cu caracter personal prin furnizorul respectiv, c&acirc;t și drepturile dvs. și posibilitățile de setare referitoare la acestea &icirc;n vederea protecției vieții dvs. private găsiți &icirc;n instrucțiunile referitoare la protecția datelor ale furnizorului respectiv.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>3. Securitatea datelor dvs. cu caracter personal</strong></p>
<p style="text-align: justify;">Securitatea datelor dvs. cu caracter personal sunt o prioritate pentru noi. Te asigurăm de faptul că orice prelucrare a datelor se face cu respectarea principiilor garantate de Regulament și prelucrate &icirc;ntr-un mod are asigură securitatea adecvată a datelor cu caracter personal, inclusiv protecția &icirc;mpotriva prelucrării neautorizate sau ilegale și &icirc;mpotriva pierderii, a distrugerii sau a deteriorării accidentale, prin luarea de măsuri tehnice sau organizatorice corespunzătoare, prin punerea &icirc;n aplicare a unor politici interne adecvate de protecție a datelor.</p>
<p style="text-align: justify;">Acest site adoptă toate măsurile de securitate necesare protejării informaţiilor personale ale utilizatorilor nostri. &Icirc;n momentul completării datelor personale pe site-ul nostru, informaţiile vor fi protejate at&acirc;t offline c&acirc;t şi online. Toate informaţiile cu caracter personal vor fi prelucrate prin intermediul unor pagini securizate care folosesc sistemul de criptare SSL, marcate cu simbolul unui lacăt, poziţionat &icirc;n partea de jos a ferestrei de browser Microsoft Internet Explorer.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>4. Drepturile dvs. &icirc;n calitate de persoană vizată</strong></p>
<p style="text-align: justify;">Regulamentul 679/2016 asigură protecția drepturilor și libertăților fundamentale ale persoanelor fizice și &icirc;n special a dreptului acestora la protecția datelor cu caracter personal.</p>
<p style="text-align: justify;">&Icirc;n ceea ce privește datele dvs. cu caracter personal, aveți dreptul de a solicita exercitarea oricărui din următoarele drepturi &icirc;n vederea garantării protecției datelor dvs. cu caracter personal:</p>
<p style="text-align: justify;">-dreptul de a avea acces la datele dvs:&nbsp;puteți solicita să vi se comunice categoriile de date cu caracter personal care sunt prelucrate, scopul &icirc;n care are loc prelucrarea, destinatarii cărora le-au fost sau vor fi comunicate, perioada pentru care se preconizează că vor fi stocate sau, dacă acest lucru nu este posibil, criteriile utilizate pentru a stabili această perioadă; existența unui proces decizional automatizat incluz&acirc;nd crearea de profiluri;</p>
<p style="text-align: justify;">-dreptul de a solicita rectificarea datelor: &icirc;n situația &icirc;n care există erori cu privire la datele care vă sunt prelucrate, aveți posibilitatea de a solicita corectarea sau completarea lor. Subscrisa vom comunica rectificarea fiecărui destinatar la care au fost transmise datele, cu excepția cazului &icirc;n care acest lucru se dovedește imposibil sau presupune eforturi disproporționate</p>
<p style="text-align: justify;">-dreptul de a solicita restricționarea prelucrării datelor:&nbsp;aveți dreptul de a solicita restricționarea prelucrării datelor &icirc;n următoarele situații: dacă ați contestat exactitatea datelor, pentru o perioadă care ne permite să verificam exactitatea datelor; dacă prelucrarea este ilegală, iar dvs. vă opuneți ștergerii datelor cu caracter personal, solicit&acirc;nd&icirc;n schimb restricționarea utilizării lor; dacă subscrisa nu mai am nevoie de datele cu caracter personal &icirc;n scopul prelucrării, dar le solicitați &nbsp;pentru constatarea, exercitarea sau apărarea unui drept &icirc;n instanță; dacă v-ați opus prelucrării pentru intervalul de timp &icirc;n care se verifică dacă drepturile legitime ale operatorului prevalează asupra drepturilor dvs.</p>
<p style="text-align: justify;">-dreptul de a solicita ștergerea datelor: puteți solicita ștergerea datelor prelucrate, dacă datele nu mai sunt necesare pentru &icirc;ndeplinirea scopurilor pentru care au fost colectate sau prelucrate , dacă v-ați retras consimțăm&acirc;ntul și nu există niciun alt temei juridic pentru prelucrarea; dacă vă opuneți prelucrării și nu există motive legitime care să prevaleze &icirc;n ceea ce privește prelucrarea; datele cu caracter personal au fost prelucrate ilegal; datele cu caracter personal trebuie șterse pentru respectarea unei obligații legale; datele cu caracter personal au fost colectate &icirc;n legătură cu oferirea de servicii ale societății informaționale.</p>
<p style="text-align: justify;">-dreptul de a solicita transferul datelor (la portabilitatea datelor): puteți solicita transferul datelor dvs. către un alt operator dacă prelucrarea are la baza consimțăm&acirc;ntul dvs. și prelucrarea este una automată</p>
<p style="text-align: justify;">-dreptul de a va opune prelucrării:&nbsp;aveți dreptul să vă opuneți &icirc;n orice moment prelucrării datelor dvs. &icirc;n scop de marketing direct, inclusiv creării de profiluri, &icirc;n acest caz datele dvs. &nbsp;vor fi șterse.</p>
<p style="text-align: justify;">-&nbsp;dreptul de a vă opune să faceți obiectul unor decizii automate bazate exclusiv pe prelucrarea automată, inclusiv profilare.</p>
<p style="text-align: justify;">Toate aceste drepturi pot sa fi exercitate printr-o simplă cerere adresată COMPANIE SRL, &icirc;n calitate de operator, la sediul nostu sau la adresa de email ____________ sau prin accesarea funcțiilor puse la dispoziție pe site-ul nostru (link export și ștergere).</p>
<p style="text-align: justify;">&Icirc;n situația &icirc;n care consideri ca ți-au fost &icirc;ncălcate drepturile, ai posibilitatea să te adresezi cu o pl&acirc;ngere Autorității Naționale de Supraveghere a Prelucrării Datelor cu Caracter Personal</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><strong>5. Date de contact</strong></p>
<p style="text-align: justify;">Prezenta Politică se completează cu celelalte politici specifice ale&nbsp;COMPANIE SRL&nbsp;si Termenii și Condițiile pe care le poți accesa de pe site-ul nostru. Orice schimbare a termenilor prezentei politici va fi comunicată către utilizatori prin e-mail, astfel &icirc;nc&acirc;t aceştia să fie permanent informaţi cu privire la informaţiile pe care le colectăm, cum le utilizăm şi &icirc;n ce circumstanţe, dacă există, le facem publice. Utilizatorii vor putea să fie sau nu de acord cu utilizarea informaţiilor &icirc;n alte scopuri. Vom utiliza informaţiile &icirc;n concordanţă cu politica sub care au fost culese informaţiile.</p>
<p style="text-align: justify;">Prezenta informare &nbsp;a dvs. este limitată la pagina noastră web și nu este valabilă pentru paginile web ale operatorilor terți, care pot fi accesate prin intermediul acestei pagini web. Nu avem nicio influență asupra prelucrării datelor de către acești operatori terț și nu ne asumăm nicio responsabilitate sau răspundere &icirc;n legătură cu aceste pagini web.</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">COMPANIE SRL</p>
<p style="text-align: justify;">Persoană responsabilă: _________________________</p>
<p style="text-align: justify;">Telefon: &nbsp;____________</p>
<p style="text-align: justify;">Mobil: &nbsp;___________</p>
<p style="text-align: justify;">Mail: &nbsp;&nbsp;___________</p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;"><a href="{{store url="gdpruserdata/deletedata"}}"><span>Stergere Date Personale</span></a></p>
<p style="text-align: justify;"><a href="{{store url="gdpruserdata/exportdata"}}"><span>Exporta Date Personale</span></a></p>
<p style="text-align: justify;">&nbsp;</p>
<p style="text-align: justify;">&nbsp;</p>
\' WHERE `identifier` LIKE \'privacy-policy\';
        UPDATE `cms_page` SET `identifier`=\'politica-de-utilizare-cookie-uri\' WHERE `identifier` LIKE \'cookie-policy\';
';

$installer->run($query);

$installer->endSetup();
