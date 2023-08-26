Blugento ConfigurableSwatch module

Cu modulul enable in orice list de produse unde e introdus va adauga un display de swatch attribute pe atributul pe baza caruia sa creat produsul configurabil(in default magento poti alege doar un singur atribut si nu poti sa introduci swatches in orice list)

Adaugarea de functionalitate se face in orice list de produse prin adaugarea urmatorului rand in pozitia in care se vrea afisarea: <?php echo $this->getLayout()>createBlock('blugento_configurableswatch/list')>setProduct($_product)->toHtml(); ?>

By default magento pt swatches la load collection pt list incarca toate informatiile pe product collection ceeea ce urca putin load time-ul la list-ul de produse...modulul asta incarca doar informatiile vitale, restul fiind incarcate prin ajax la fiecare click de schimbare variatii atribut. (aici s-ar mai putea face putin refine)

Imaginile de la variatile de atribut cu modulul asta nu mai trebuie setate pe fiecare produs individual in media gallery cu label valoarea atributului cum face magento ( http://prntscr.com/iinsez ), se pot urca direct din wysiwyg in media si daca fisierul are denumirea valuri atributului, ex: red.png va fi utilizat direct pt attribute label (dupa ce i se face si un resize la dimensiunile stabilite in config: http://prntscr.com/iio6cj). Daca se seteaza o poza cu label red pe produs va fi folosita aceasta poza avand prioritate. Dupa modificarea de attribute image label se poate rula un clear image cache de aici: http://prntscr.com/iio79h