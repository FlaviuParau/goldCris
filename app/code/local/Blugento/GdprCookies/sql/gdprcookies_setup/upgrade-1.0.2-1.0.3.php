<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

if ($installer->tableExists('gdprcookies/list')) {
    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('Google tag Manager', 2, 'Acest serviciu este folosit pentru a analiza activitatea clienților pe site și pentru a măsura conversiile.');");

    $installer->run("UPDATE {$this->getTable('gdprcookies/list')} SET cookie_description = 'Acest serviciu este folosit în scopul cuantificării vizitelor pe site.' 
                         WHERE  cookie_name like 'Google Analytics';");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('Google Maps', 1, 'Acest serviciu este folosit pentru afișarea locațiilor magazinelor noastre.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('2Performant', 1, 'Acest serviciu este folosit în scopul identificării vizitelor și implicit a comenzilor venite de pe site-urile partenerilor noștri în vederea plații comisionului.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('Blugento CDN', 1, 'Pentru afișarea cât mai rapidă a pozelor, site-ul folosește un server dedicat.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('Blugento Pop-Up', 1, 'Această opțiune o folosim în scopul Abonării la newsletter-ul nostru.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('frontend', 1, 'Cookie-ul este creat indiferent de pagina vizitată.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('frontend_cid', 1, 'Cookie-ul este creat atunci când este vizitată o pagină HTTPS.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('__utma', 1, 'Folosit pentru a distinge utilizatorii și sesiunile. Cookie-ul este creat atunci când se execută biblioteca javascript și nu există cookie-uri __utma existente. Cookie-ul este actualizat de fiecare dată când datele sunt trimise către Google Analytics.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('__utmt', 1, 'Folosit pentru a accelera rata de solicitare.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('__utmb', 1, 'Folosit pentru a determina noi sesiuni / vizite. Cookie-ul este creat atunci când biblioteca javascript se execută și nu există cookie-uri __utmb existente. Cookie-ul este actualizat de fiecare dată când datele sunt trimise către Google Analytics.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('__utmc', 1, 'Nu este utilizat în ga.js. Set pentru interoperabilitate cu urchin.js. Istoric, acest cookie a funcționat împreună cu cookie-ul __utmb pentru a determina dacă utilizatorul a fost într-o nouă sesiune / vizită.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('__utmz', 1, 'Stochează sursa de trafic sau campania care explică modul în care utilizatorul a ajuns pe site-ul dvs. Cookie-ul este creat atunci când se execută biblioteca javascript și este actualizat de fiecare dată când datele sunt trimise către Google Analytics.');");

    $installer->run("INSERT  INTO {$this->getTable('gdprcookies/list')} (`cookie_name`, `cookie_category`, `cookie_description`)
      VALUES ('__utmv', 1, 'Folosit pentru a stoca date variabile personalizate la nivel de vizitator. Acest cookie este creat atunci când un dezvoltator utilizează metoda _setCustomVar cu o variabilă personalizată la nivel de vizitator. Acest cookie a fost folosit și pentru metoda de demontare _setVar. Cookie-ul este actualizat de fiecare dată când datele sunt trimise către Google Analytics.');");

}
$installer->endSetup();

