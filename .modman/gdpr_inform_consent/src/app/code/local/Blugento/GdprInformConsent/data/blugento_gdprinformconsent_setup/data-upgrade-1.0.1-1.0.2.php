<?php

$installer = $this;
$installer->startSetup();

// Create "Blugento Newsletter Checkbox Consent DE" CMS Block
$identifier = 'blugento-newsletter-checkbox-consent-de';
$block = Mage::getModel('cms/block');

if (!$block->load($identifier)->getIdentifier()) {
    $content = '<p>Ich stimme der Verarbeitung 
        meiner personenbezogenen Daten durch den Betreiber hinsichtlich E-Mail-Adresse, Name, Vorname, Telefonnummer zu, für 
        das Direktmarketing, beziehungsweise um weitere Angebote des Verantwortlichers bezüglich der angebotenen Dienste und 
        Produkte zu erhalten. Ihre Daten werden sicher verarbeitet. Einzelheiten zu unseren Richtlinien und Ihren Rechten 
        bezüglich der Verarbeitung personenbezogener Daten erhalten Sie über die <a href=\"{{store url=\'datenschutzrichtlinie\'}}\">
        Datenschutz-Grundverordnung</a>. Die Grundlage der Datenverarbeitung ist die Zustimmung und die Dauer der Verarbeitung 
        ist die gesamte Dauer seiner Existenz. Die Einwilligung kann widerrufen werden, indem Sie den Abmelde-Link im Newsletter 
        besuchen oder indem Sie das Newsletter Kästchen auf dem Benutzerkonto deaktivieren, was zur Beendigung der Verarbeitung 
        führen wird.</p>';
    $query = '
        INSERT INTO `cms_block` (`title`, `identifier`, `content`, `is_active`)
        VALUES ("Blugento Newsletter Checkbox Consent", "blugento-newsletter-checkbox-consent-de", "'.$content.'", "0");
    ';
    $installer->run($query);
}

// Create "Blugento Checkout GDPR acknowledfement DE" CMS Block
$identifier = 'blugento-checkout-gdpr-acknowledgement-de';
$block = Mage::getModel('cms/block');

if (!$block->load($identifier)->getIdentifier()) {
    $content = '<p>Ich stimme der 
        Verarbeitung meiner personenbezogenen Daten zu, um eine Bestellung aufzugeben und gegebenenfalls das Konto zu erstellen, 
        wenn die Option ausgewählt ist. Die Grundlage der Verarbeitung ist die vertragliche Verpflichtung zur Lieferung der 
        bestellten Produkte, wobei die Dauer der Bearbeitung die 3-jährige Verjährungsfrist ab Bestelldatum ist. Soweit Sie 
        mit der Verarbeitung Ihrer Daten nicht einverstanden sind, werden wir Sie darüber informieren, dass wir die bestellten 
        Produkte nicht liefern können. Ihre Rechte als natürliche Person werden durch die <a href=\"{{store url=\'datenschutzrichtlinie\'}}\">
        Datenschutz-Grundverordnung</a> garantiert.</p>';

    $query = '
        INSERT INTO `cms_block` (`title`, `identifier`, `content`, `is_active`)
        VALUES ("Blugento Checkout GDPR acknowledfement", "blugento-checkout-gdpr-acknowledgement-de", "'.$content.'", "0");
    ';
    $installer->run($query);
}

// Create "Termeni si Conditii DE" CMS Block
$identifier = 'allgemeine-geschäftsbedingungen';
$page = Mage::getModel('cms/page');

if (!$page->load($identifier)->getIdentifier()) {
    $content = '<p class="Default"><strong>Allgemeine Gesch&auml;ftsbedingungen</strong></p>
    <p class="Default"><a href="http://www.magazinultau.ro/">www.yourstore.ro</a></p>
    <p class="Default"><strong>1. </strong><strong>Allgemeine Gesch&auml;ftsbedingungen</strong></p>
    <p class="Default">Der Gebrauch der Website www.yourstore.com bedeutet, dass diese Gesch&auml;ftsbedingungen vom Benutzer akzeptiert werden.</p>
    <p class="Default"></p>
    <p class="Default"><strong>2. Unternehmen</strong></p>
    <p class="Default">Gesellschaft mit beschr&auml;nkter Haftung (COMPANY )</p>
    <p class="Default">J../.../...</p>
    <p class="Default">USt-IdNr.( Umsatzsteuer-Identifikationsnummer):</p>
    <p class="Default">IBAN:</p>
    <p class="Default">Adresse:</p>
    <p class="Default">Standort:</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>3. ALLGEMEINE INFORMATIONEN</strong></p>
    <p class="Default">Die auf der Website ver&ouml;ffentlichten Bilder entsprechen der Realit&auml;t, aber manchmal k&ouml;nnen sich die Nuancen der Produktfarben leicht unterscheiden, infolge des Ausf&uuml;hrungsvorgangs der Fotos oder Ihrer Monitoreinstellungen. Kunden von www.yourstore.com werden immer informiert, wenn ein Produkt auf Lager ist oder nicht, wenn es bestellt werden kann, wie lange es verarbeitet wird und wie lange es bei ihnen ankommt. Diese Informationen werden per E-Mail oder Telefon gesendet. Bilder von Produkten, die Angeboten, Preisen und Gewinnspiele k&ouml;nnen ohne vorherige Ank&uuml;ndigung im Voraus ge&auml;ndert werden. Die &uuml;ber www.yourstore.com kommerzialisierten Produkte werden zum Zeitpunkt der Lieferung durch eine Steuerrechnung oder einen Steuerbeleg begleitet.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>4. BESITZ - URHEBERRECHTE</strong></p>
    <p class="Default">Die Nutzung der Website www.yourstore.com (einschlie&szlig;lich Besuch und Kauf der Produkte) impliziert die Zustimmung bestimmter Nutzungsbedingungen, offensichtlich in Form eines Rechtsvertrags zwischen dem Nutzer und COMPANY (Gesellschaft mit Beschr&auml;nkter Haftung), daher wird empfohlen, die folgenden Abschnitte sorgf&auml;ltig zu lesen.</p>
    <p class="Default">Der Besuch oder der Kauf von Produkten auf der Website www.yourstore.com bedeutet automatisch die Zustimmung dieser Allgemeinen Gesch&auml;ftsbedingungen. Auch die Nutzung anderer aktueller oder zuk&uuml;nftiger Dienstleistungen von COMPANY impliziert die Zustimmung der gleichen Bedingungen.</p>
    <p class="Default">Der ganze Inhalt der Website www.yourstore.com ist durch Urheberrechtsgesetze und Gesetze zum geistigen Eigentum gesch&uuml;tzt. Die Verwendung von irgendwelchen Gegenst&auml;nden der Website www.yourstore.com ohne schriftliche Zustimmung von COMPANY ist strafbar nach den geltenden Gesetzen.</p>
    <p class="Default">Das Unternehmen gew&auml;hrt dem Benutzer kein Recht, die Website teilweise oder vollst&auml;ndig zu ver&auml;ndern, die Website teilweise oder vollst&auml;ndig zu reproduzieren, zu kopieren, zu verkaufen oder die Website in anderer Weise ohne die Zustimmung des Unternehmens zu nutzen. Der Besucher / Auftraggeber wird keine Informationen oder Dienstleistungen, die von oder &uuml;ber diese Website bezogen werden, ver&auml;ndern, kopieren, verteilen, &uuml;bermitteln, anzeigen, ver&ouml;ffentlichen, reproduzieren, wird keine Lizenzen gew&auml;hren, wird keine abgeleiteten Produkte erstellen, und wird diese Informationen und Dienstleistungen auch nicht &uuml;bertragen oder verkaufen. Wir weisen Sie hiermit darauf hin, dass BRAND eine eingetragene Marke ist, in welchem Sinne jede Verwendung des Markenzeichens ohne Zustimmung des Unternehmens strafbar ist.</p>
    <p class="Default">Die Inhalte dieser Website, einschlie&szlig;lich, aber nicht beschr&auml;nkt auf Logo, stilisierte Darstellungen, Symbole, Bilder, Fotos, Textinhalt und andere ebenfalls, sind das ausschlie&szlig;liche Eigentum von COMPANY . Man darf keine Zeichen, Fotos, Bilder, Textteile kopieren, verteilen, ver&ouml;ffentlichen, ver&auml;ndern, erg&auml;nzen, verwenden, ausstellen, einschlie&szlig;en, verbinden, &uuml;bermitteln oder beseitigen und auch nicht Inhalte, Daten, Informationen, Fotos oder anderen Informationen auf der Website ausstellen, verkaufen, usw, ohne die ausdr&uuml;ckliche schriftliche Genehmigung von COMPANY.</p>
    <p class="Default">Kein Kunde erwirbt, durch Verwendung und Zugriff der Website, ein Recht oder eine Lizenz um die Informationen auf der Website zu nutzen. Kein Kunde hat das Recht, ein automatisches oder manuelles Ger&auml;t zu verwenden, um die auf der Website verf&uuml;gbaren Materialien zu &uuml;berwachen.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>5. KUNDEN &ndash; VERTRAULICHKEIT UND DER SCHUTZ VON personenbezogener Daten</strong></p>
    <p class="Default">Der Kunde ist verpflichtet, die Informationen, die er &uuml;ber den Zugriff und die Verwendung der Website erhalten hat, vertraulich zu behandeln, sowie auf den Kommunikationen, die w&auml;hrend der Registrierung, Authentifizierung, Unterbringung und Ausf&uuml;hrung von Bestellungen &uuml;bermittelt werden, und andere solche Aspekte im Zusammenhang mit der Verwendung der Website. Dem Kunden ist es verboten, seine Beziehungen mit COMPANY gegen&uuml;ber Dritten, ohne die ohne ausdr&uuml;ckliche, vorherige und schriftliche Zustimmung des Unternehmens, offen zu legen.</p>
    <p class="Default">Die &Uuml;bermittlung von Informationen oder Materialien &uuml;ber diese Website seitens des Kundes, &nbsp;sowie die &Uuml;bermittlung von Ideen, Konzepten, Know-how, Techniken und so weiter, gew&auml;hrt uns ungehinderten und unwiderruflichen Zugriff auf das Recht, diese Materialien oder Informationen zu verwenden, zu reproduzieren, anzuzeigen, zu modifizieren, zu &uuml;bertragen und zu verteilen.</p>
    <p class="Default">Die Verf&uuml;gbarkeit eines Produkts im Lager, unabh&auml;ngig davon, ob es sich um eine Werbeaktion handelt oder nicht, wird nach der Bestellung des Kundes per E-Mail angek&uuml;ndigt.</p>
    <p class="Default">www.yourstore.com verfolgt eine strikte Politik f&uuml;r die Erhaltung der Vertraulichkeit von Kundendaten.</p>
    <p class="Default">Durch den Zugriff unserer Website und unserer Produkten, verarbeitet COMPANY Ihre pers&ouml;nlichen Daten, um die Zwecke zu erf&uuml;llen, f&uuml;r die diese Daten angefordert und gespeichert wurden. Was Ihre Rechte als betroffenen Person betrifft, die durch die Verordnung 2016/679 / EU &uuml;ber den Schutz nat&uuml;rlicher Personen bei der Verarbeitung personenbezogener Daten und den freien Datenverkehr garantiert sind, bitte greifen Sie auf die bereitgestellten Sicherheitsrichtlinien und die anderen Ma&szlig;nahmen zur Gew&auml;hrleistung Ihrer Rechte zu.</p>
    <p class="Default"></p>
    <p class="Default"><strong>6. PRODUKT- UND PREISINFORMATIONEN</strong></p>
    <p class="Default">Die Preise auf der Website www.yourstore.com sind in RON angegeben. Die Preise sind g&uuml;ltig, wenn Sie auf die Seite, auf der sie angezeigt werden, zugreifen. COMPANY beh&auml;lt sich das Recht vor, diese jederzeit ohne vorherige Ank&uuml;ndigung oder Mitteilung zu ver&auml;ndern.</p>
    <p class="Default">Mit der Bestellung auf www.yourstore.com verpflichten Sie sich unwiderruflich, den Wert der bestellten Produkte zu bezahlen. In diesem Zusammenhang teilen wir Ihnen mit, dass Sie bei der Registrierung Ihrer Bestellungen, auf die Websites unserer Mitarbeiter weitergeleitet werden, um die erforderlichen Zahlungsdaten zu sammeln.</p>
    <p class="Default"></p>
    <p class="Default"><strong>7. REGISTRIERUNG ALS BENUTZER</strong></p>
    <p class="Default">Jeder Kunde, der dies w&uuml;nscht, kann sich auf unserer Website registrieren, indem er einfach auf den Registrierungs-Taste klickt, der auf der Homepage verf&uuml;gbar ist, und wenn er die notwendigen Schritte zur Registrierung durchf&uuml;hrt.</p>
    <p class="Default">Zur Registrierung werden Sie gebeten, Ihren Vornamen, Nachnamen, Ihre E-Mail-Adresse und Ihr Passwort einzugeben. Sie m&uuml;ssen eine g&uuml;ltige E-Mail-Adresse eingeben, da sonst die Registrierung auf der Website nicht fortgesetzt werden kann. Sie sind voll verantwortlich f&uuml;r die sichere Erhaltung Ihres Zugangskennworts sowie aller Registrierungs-, Authentifizierungs- und Kontoinformationen. Sie sind voll verantwortlich f&uuml;r jede Bestellung und / oder Zahlung, die Sie bei der Verwendung unserer Website von Ihrem Konto durchgef&uuml;hrt haben.</p>
    <p class="Default"></p>
    <p class="Default"><strong>8. BESTELLUNG UND AUFTRAGSBEST&Auml;TIGUNG</strong></p>
    <p class="Default">Um Ihre Bestellung aufzugeben, bitte folgen Sie den n&auml;chsten Schritten:</p>
    <p class="Default">1. W&auml;hlen Sie die gew&uuml;nschten Produkte aus und f&uuml;gen Sie sie in Ihren Warenkorb ein, indem Sie auf die Taste "In den Warenkorb" klicken,</p>
    <p class="Default">2. Wenn Sie mehr Produkte zu Ihrem Warenkorb hinzuf&uuml;gen m&ouml;chten, klicken Sie auf "weiter einkaufen", wenn Sie die Bestellung abschlie&szlig;en m&ouml;chten, klicken Sie auf "zur Kasse&rdquo;,</p>
    <p class="Default">3. Wenn Sie kein Konto auf der Website haben und auch kein Konto erstellen m&ouml;chten, w&auml;hlen Sie nach der Auswahl der gew&uuml;nschten Produkte "Bestellung als Besucher abschlie&szlig;en" und geben Sie Ihre E-Mail-Adresse ein, um den Eingang der Bestellung zu best&auml;tigen,</p>
    <p class="Default">4. Geben Sie Ihre Zahlungsinformationen ein,</p>
    <p class="Default">5. Geben Sie die Lieferadresse ein,</p>
    <p class="Default">6. Lieferung erfolgt durch Curier,</p>
    <p class="Default">7. W&auml;hlen Sie die Zahlungsmethode,</p>
    <p class="Default">8. Ticken Sie das Feld "Ich stimme den Nutzungsbedingungen zu",</p>
    <p class="Default">9. Sie k&ouml;nnen uns die Erlaubnis geben, Ihnen Newsletter zu senden,</p>
    <p class="Default">10. Senden Sie die Bestellung, indem Sie auf die Taste "Bestellung senden" klicken</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>9. ZAHLUNGSARTEN UND BESCHR&Auml;NKUNGEN</strong></p>
    <p class="Default">YOUR COMPANY versucht seinen Kunden eine detaillierte Beschreibung ihrer Produkte innerhalb der Website anzubieten, garantiert jedoch nicht, dass die Beschreibung der Produkte oder anderer Inhalte der Website www.yourstore.com vollst&auml;ndig oder v&ouml;llig fehlerfrei ist. YOUR COMPANY &uuml;bernimmt auch keine Verantwortung f&uuml;r Verluste wie zum Beispiel: Datenverlust, Gewinnverlust, die Unm&ouml;glichkeit, die Daten auf dieser Website zu verwenden oder &Auml;nderungen ohne vorherige Ank&uuml;ndigung der Daten, Preis oder Website-Struktur.</p>
    <p class="Default">Der Kunde ist verpflichtet, vor der Bestellung, die Zahlungsart zu w&auml;hlen, indem er die entsprechende Option gem&auml;&szlig; den Bestellschritten tickt.</p>
    <p class="Default">Der Verk&auml;ufer wird dem Kunden f&uuml;r die gelieferten Produkte eine Rechnung ausstellen. In dieser Hinsicht ist der Kunde verpflichtet, dem Verk&auml;ufer alle erforderlichen Informationen zur Ausstellung der Rechnung gem&auml;&szlig; den geltenden Rechtsvorschriften zu &uuml;bermitteln. Die Rechnung wird dem Kunden bei der Lieferung der bestellten Produkte in gedruckter Form mitgeteilt.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>10. LIEFERUNGSPOLITIK</strong></p>
    <p class="Default">Die Produkte werden vom Verk&auml;ufer direkt an die Adresse des K&auml;ufers / die vom K&auml;ufer angegebene Adresse &nbsp;geliefert.</p>
    <p class="Default">Die Lieferung erfolgt durch die Kurierfirma CURIER.</p>
    <p class="Default">Die Versandkosten betragen .... EUR.</p>
    <p class="Default">Der Versand der bestellten Produkte auf Lager erfolgt innerhalb von 24 Stunden nach Erhalt der Auftragsbest&auml;tigung per E-Mail. Die Bestellung sollte bei Ihnen innerhalb von 2 Arbeitstagen nach dem Datum der Auftragsbest&auml;tigung erreichen. Dieser Termin kann durch Umst&auml;nde au&szlig;erhalb unserer Kontrolle ge&auml;ndert werden, ohne dass ein Zeitraum von 10 Tagen, gerechnet ab dem Tag des Eingangs Ihrer Bestellung, &uuml;berschritten wird.</p>
    <p class="Default">Die Bestellung wird in Paketen versandt, um die Transportsicherheit zu gew&auml;hrleisten. Die Haftung f&uuml;r alle Besch&auml;digungen, die w&auml;hrend des Transports des von uns versandten Produkts, Frachtst&uuml;ckes oder Pakets entstehen, liegt in der Verantwortung des Verfracters gem&auml;&szlig; den geltenden lokalen Gesetzen.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>11. L&ouml;sung der Beschwerden</strong></p>
    <p class="Default">Jede Unzufriedenheit mit dem Zugriff, der Verwendung, der Registrierung auf unserer Website, mit der Bestellung oder Fragen im Zusammenhang mit der durchgef&uuml;hrten Bestellung und andere ebenfalls werden uns direkt telefonisch unter TELEFONNUMMER 0700 000 000 oder per E-Mail an EMAIL@EMAIL.COM mitgeteilt.</p>
    <p class="Default">Ihre Unzufriedenheit wird eingetragen und Sie werden eine schriftliche Antwort an die angegebene E-Mail-Adresse erhalten, wenn Sie innerhalb von 3 Arbeitstagen uns &uuml;ber Ihre Unzufriedenheit informieren.</p>
    <p class="Default">Der Kunde erkl&auml;rt, dass er sich verpflichtet, diese Beschwerden (auf sozialen Netzwerken, Medien, durch privaten Partygespr&auml;che oder auf andere Weise) nicht offen zu legen, andernfalls tr&auml;gt er die Sch&auml;den, die dem Image des Unternehmens durch diese Handlungen zugef&uuml;gt wird.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>12. &Auml;NDERUNGEN DER ALGEMEINEN BEDINGUNGEN</strong></p>
    <p class="Default">YOUR COMPANY beh&auml;lt sich das Recht vor, jederzeit &Auml;nderungen in Bezug auf den Inhalt der Website www.yourstore.com, die Richtlinien, die Verwendungsbedingungen, ohne die vorherige Benachrichtigung der Benutzer in dieser Hinsicht, obwohl wir darauf achten, unsere Kunden &uuml;ber diese &Auml;nderungen zu informieren.</p>
    <p class="Default">Der Benutzer der Website www.yourstore.com ist verpflichtet, die Verwendungsbedingungen regelm&auml;&szlig;ig zu &uuml;berpr&uuml;fen. Wenn er den von COMPANY vorgenommenen &Auml;nderungen nicht zustimmt, Er wird aufh&ouml;ren, die Website zu benutzen. Wenn der Benutzer die Website weiterhin verwendet, wird als &uuml;bereinstimmend mit den vorgenommenen &Auml;nderungen betrachtet.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>13. VERTRAGLICHE HAFTUNG</strong></p>
    <p class="Default">Durch die Erstellung und Verwendung des Kontos &uuml;bernimmt der Kunde die Verantwortung f&uuml;r die Erhaltung der Vertraulichkeit der Kontodaten (User und Passwort), auch f&uuml;r die Verwaltung des Zugriffs auf das Konto und ist rechtlich verantwortlich f&uuml;r die Aktivit&auml;t, die &uuml;ber sein Konto ausgef&uuml;hrt wird.</p>
    <p class="Default">Indem Sie auf die Website zugreifen, Ihr Konto erstellen, die Website verwenden, Ihre Bestellungen aufgeben und so weiter, der Kunde akzeptiert ausdr&uuml;cklich und unmissverst&auml;ndlich die Allgemeinen Gesch&auml;ftsbedingungen der Website in seiner neuesten Version, die auf der Website kommuniziert wird. Nach der Erstellung des Kontos entspricht die Verwendung des Inhalts der Annahme der &Auml;nderungen, die an den Allgemeinen Gesch&auml;ftsbedingungen der Website und / oder den aktualisierten Versionen der Verwendungsbedingungen der Website vorgenommen wurden. Der Kunde ist verantwortlich f&uuml;r die &Uuml;berpr&uuml;fung der letzten Version der Verwendungsbedingungen, wann immer er die Website nutzt.</p>
    <p class="Default">Die Annahme der Allgemeinen Gesch&auml;ftsbedingungen der Website wird best&auml;tigt, indem das entsprechende Checkbox auf der Website tickt und / oder die Bestellung gesendet und / oder eine Online-Zahlung vorgenommen wird.</p>
    <p class="Default">Wir &uuml;bernehmen keine Haftung f&uuml;r Sch&auml;den jeglicher Art, die dem Kunden oder einer anderen Drittperson durch die Erf&uuml;llung unserer Verpflichtungen, die aus der Bestellung entstehen, sowie f&uuml;r Sch&auml;den, die sich aus der Verwendung der Waren nach der Lieferung ergeben und &nbsp;noch weniger f&uuml;r ihren Verlust.</p>
    <p class="Default">Wir k&ouml;nnen nicht f&uuml;r Sch&auml;den an Ihrem Computer verantwortlich gemacht werden oder f&uuml;r Viren, die Ihren Computer oder andere Ger&auml;te infizieren k&ouml;nnen, wenn Sie auf unsere Website zugreifen, sie verwenden oder auf unserer Website surfen oder Inhalte, Informationen, Materialien, Daten, Texte, Bilder, Videos oder Audio auf unserer Website herunterladen.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>16. R&Uuml;CKGABEPOLITIK</strong></p>
    <p class="Default">Die Kunden, die mit dem Produkt nicht zufrieden sind, k&ouml;nnen es zur&uuml;ckgeben oder k&ouml;nnen &nbsp;sie ein anderes Produkt, innerhalb des Gegenwerts des zur&uuml;ckgegebenen Produkts, ausw&auml;hlen, vorbehaltlich der unten angegebenen Bedingungen. die R&uuml;cksendung der Produkte, die entsiegelt werden, ohne Etiketten oder Originalverpackungen werden nicht akzeptiert. Vor der R&uuml;cksendung von Produkten m&uuml;ssen die Kunden den H&auml;ndler im Voraus schriftlich an EMAIL@EMAIL.COM benachrichtigen, wo sie Folgendes angeben m&uuml;ssen:</p>
    <p class="Default">-&nbsp;&nbsp;&nbsp;&nbsp; das Produkt / die Produkte, die sie zur&uuml;ckgeben m&ouml;chten</p>
    <p class="Default">-&nbsp;&nbsp;&nbsp;&nbsp; der Grund der R&uuml;cksendung</p>
    <p class="Default">-&nbsp;&nbsp;&nbsp;&nbsp; die M&ouml;glichkeit, das Produkt zu ersetzen oder R&uuml;ckzahlung des Betrages</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>17. H&ouml;here Gewalt</strong></p>
    <p class="Default">Keine der Vertragsparteien haftet f&uuml;r die Nichterf&uuml;llung und / oder mangelhafte Erf&uuml;llung einer Verpflichtung aus diesem Vertrag - ganz oder teilweise - wenn die Nichtausf&uuml;hrung oder die unzureichende Erf&uuml;llung dieser Verpflichtung durch h&ouml;here Gewalt verursacht wurde, wie dies gesetzlich vorgeschrieben ist, aus Gr&uuml;nden, die von den Parteien unabh&auml;ngig sind.</p>
    <p class="Default">Die Partei, die sich auf h&ouml;here Gewalt vorbringt, ist verpflichtet, die andere Partei innerhalb von 5 (f&uuml;nf) Tagen nach Eintritt des Ereignisses zu benachrichtigen und alle m&ouml;glichen Ma&szlig;nahmen zur Begrenzung ihrer Folgen zu treffen.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>18. anwendbares Gesetz</strong></p>
    <p class="Default">Der Vertrag unterliegt rum&auml;nischem Recht. Jegliches Missverst&auml;ndnis zwischen dem H&auml;ndler und dem Kunden in Bezug auf die Beziehungen, die sich aus dieser Vereinbarung ergeben, ist einvernehmlich zu l&ouml;sen und, im Falle eines Misserfolgs, den zust&auml;ndigen lokalen Gerichten vorzulegen.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>19. SCHLUSSBESTIMMUNGEN</strong></p>
    <p class="Default">Diese Website geh&ouml;rt COMPANY , die Ihnen das Recht gibt, auf die Website zuzugreifen und sie zu nutzen, sofern Sie diese Verwendungsbedingungen akzeptieren. Durch den Zugriff und die Verwendung der Website, geben Sie automatisch und unmissverst&auml;ndlich Ihre Einwilligung zur Einhaltung der Verwendungsbedingungen auf der Website.</p>
    <p class="Default">COMPANY beh&auml;lt sich das Recht vor, die Allgemeinen Gesch&auml;ftsbedingungen jederzeit und ohne vorherige Ank&uuml;ndigung zu &auml;ndern, indem es die aktualisierte Version auf der Website ver&ouml;ffentlicht wird und der Kunde ist verpflichtet, die allgemeinen Gesch&auml;ftsbedingungen zu lesen, sobald er auf die Website zugreift. Der Kunde ist verpflichtet, die Allgemeinen Gesch&auml;ftsbedingungen auf der Website strikt einzuhalten und kann nicht behaupten oder sich verteidigen, dass er diese Allgemeinen Gesch&auml;ftsbedingungen nicht kennt, Gesch&auml;ftsbedingungen, die zum Zeitpunkt des Zugriffs, der Nutzung und / oder der Bestellung auf der Website, g&uuml;ltig sind.</p>
    <p class="Default">COMPANY organisiert regelm&auml;&szlig;ig verschiedene Promotionen, die auf der Website angek&uuml;ndigt werden. Aktionen werden nach der Aktivierung auf der Website g&uuml;ltig und werden ung&uuml;ltig, sobald sie auf der Website inaktiv gemacht werden. Die Promotionen kumulieren sich nicht miteinander oder mit andere Rabatte und sind verf&uuml;gbar solange wir die Produkte auf Lager haben.</p>
    <p align="right" class="Default"><strong>&nbsp;</strong></p>';

    $query = "
        INSERT INTO `cms_page` (`title`, `root_template`, `identifier`, `content`, `is_active`)
        VALUES ('Termeni si Conditii DE', 'one_column', 'allgemeine-geschäftsbedingungen', ' . $content . ', '0');
    ";
    $installer->run($query);
}


// Create "Termeni si Conditii EN" CMS Page
$identifier = 'terms-and-conditions';
$page = Mage::getModel('cms/page');

if (!$page->load($identifier)->getIdentifier()) {
    $content = '<p class="Default"><strong>Terms </strong><strong>and Conditions</strong></p>
<p class="Default"><a href="http://www.magazinultau.ro/">www.magazinultau.ro</a></p>
<p class="Default"><strong>1. TERMS AND CONDITIONS</strong></p>
<p class="Default">In order to access the website <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> , the user must accept these Terms and Conditions.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>2. COMPANY</strong></p>
<p class="Default">COMPANY</p>
<p class="Default">J../.../...</p>
<p class="Default">Tax Identification No.</p>
<p class="Default">IBAN:</p>
<p class="Default">Address:</p>
<p class="Default">Work unit:</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>3. GENERAL INFORMATION</strong></p>
<p class="Default">The pictures displayed on the website are realistic; however, the colours may be slightly different from those of the actual products, due to photo shooting or your computer monitor settings. The clients of <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> will always be notified if a product is available or not, whether it can be custom-ordered, how long it will take to manufacture and deliver it to the client. Such information will be sent by e-mail or phone. The product images, offers, prices and contests may be changed without prior notice in this respect. The products purchased on <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> &nbsp;will be shipped together with the related invoice or receipt.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>4. PROPERTY - COPYRIGHT</strong></p>
<p class="Default">In order to access the website <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> (which includes viewing and purchasing the products), certain terms and conditions of use must be accepted, in the form of a legal agreement between the user and THE COMPANY. Therefore, reading the paragraphs below is highly recommended.</p>
<p class="Default">Viewing or buying products on <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> requires the prior acceptance of such terms and conditions. Additionally, using any other current or future services provided by THE COMPANY, involves the acceptance of the same terms and conditions.</p>
<p class="Default">The entire content of the website <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> is protected by copyright and intellectual property rights. The use of any elements belonging to <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> without the written consent of THE COMPANY is punished according to the applicable law.</p>
<p class="Default">The user is NOT allowed to change or reproduce the content of the website, in full or in part, to copy, sell or use the website in any other manner without the company&rsquo;s consent. The Visitor/Client will not alter, copy, distribute, deliver, display, publish, duplicate or grant licenses, create by-products, transfer or sell any information or service obtained on or by means of this website. We hereby inform you that the BRAND is a registered trademark; therefore its use without the company&rsquo;s approval is punished according to the applicable law.</p>
<p class="Default">The content of this website, including without limitation, the logo, visual representations, symbols, pictures, photographs, texts and related elements, are the exclusive property of THE COMPANY. The duplication, distribution, publication, alteration, supplementation, use, presentation, integration, collation, dissemination or removal of marks, photos, images, text chunks, as well as the display, sale etc. of the content, data, information, photos or other information found on the website without the express written consent of THE COMPANY are forbidden.</p>
<p class="Default">By accessing and using the website, no Client will acquire any right or license to use any information available on the website. No Client is entitled to use any automated or manual device to monitor the materials available on the website.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>5. CLIENTS - PERSONAL DATA PRIVACY AND PROTECTION</strong></p>
<p class="Default">The Client must keep confidential the information acknowledged when accessing and using the website, as well as the notices received during the registration and authentication process, the placement and completion of orders and other actions related to the use of the website. The Client must not disclose to third parties its relationship with THE COMPANY, without the latter&rsquo;s prior written consent.</p>
<p class="Default">The Client&rsquo;s sharing of information, materials, ideas, concepts, know-how, techniques etc. through this website gives us the unrestricted and irrevocable right to access, use, duplicate, display, alter, disseminate and share such materials or information.</p>
<p class="Default">The availability of a product, whether or not as part of a sales campaign, is notified to the Client by e-mail after placing the order.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> has a strict privacy policy regarding personal data protection.</p>
<p class="Default">When you access our website and products, THE COMPANY processes your personal data in order to fulfill the purposes for which such data were collected and stored. As concerns your rights as a Data Subject pursuant to Regulation (EU) 2016/679 on the protection of natural persons with regard to the processing of personal data and on the free movement of such data, please check our Security Policy and the other measures implemented to protect your rights.</p>
<p class="Default"><strong>6. PRODUCT INFORMATION AND PRICES</strong></p>
<p class="Default">The prices displayed on <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> are in RON. The displayed prices are valid when accessing the page where they are displayed, THE COMPANY being entitled to change them anytime at its own discretion, with no prior notice.</p>
<p class="Default">By placing an order on the website <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a>&nbsp; , you irrevocably undertake to pay the equivalent amount of the ordered products. In this respect, we hereby inform you that, during the order registration process, you will be redirected to the websites of our partners in order for your personal data to be collected for payment purposes.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>7. CREATING AN ACCOUNT</strong></p>
<p class="Default">Any client is free to register on our website by a simple click on the &ldquo;Create account&rdquo; button on the home page and by following the requested steps in order to create the account.</p>
<p class="Default">You will be requested to insert your surname, name, e-mail address and a password. You must insert a valid e-mail address, otherwise you will not be able to continue the registration process. You are fully responsible for safekeeping the access password, as well as any other registration, authentication and account details. You are fully responsible for any order and/or payment made on our website by using your account.</p>
<p class="Default"><strong>8. ORDER PLACEMENT AND CONFIRMATION</strong></p>
<p class="Default">In order to place an order, you must follow these steps:</p>
<p class="Default">1. select the products and add them to your Shopping cart, by clicking the &ldquo;Add to cart&rdquo; button,</p>
<p class="Default">2. if you want to add more products, click &ldquo;Continue shopping&rdquo;; if you want to complete your order, you have to click &ldquo;Complete order&rdquo;,</p>
<p class="Default">3. after selecting the products, if you do not have and do not wish to create an account on the website, you have to click &ldquo;Complete order as Visitor&rdquo; and insert your e-mail address to receive the order confirmation,</p>
<p class="Default">4. insert the invoice details,</p>
<p class="Default">5. insert the delivery address,</p>
<p class="Default">6. delivery is made by courier,</p>
<p class="Default">7. select the payment method,</p>
<p class="Default">8. tick &ldquo;I agree with the website Terms and Conditions&rdquo;,</p>
<p class="Default">9. you may also choose to receive newsletters,</p>
<p class="Default">10. place your order by clicking the &ldquo;Complete order&rdquo; button.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>9. PAYMENTS AND LIMITATIONS </strong></p>
<p class="Default">THE COMPANY tries to provide to its clients a description as detailed as possible of the products available on the website, but cannot guarantee that such description or the content of the website <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a>&nbsp; is complete or error free. Moreover, THE COMPANY will not be liable for: data losses, profit losses, impossibility to access the website data, modification of information, prices or website features without prior notice.</p>
<p class="Default">The client must select the payment method before placing the order, by choosing the appropriate option during the order placement stages.</p>
<p class="Default">The Seller will issue an invoice to the Client in respect of the delivered products. To this effect, the Client must provide to the Seller all the information required for issuing the invoice, according to the applicable law. The invoice will be delivered to the Client in printed form, together with the ordered products.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>10. SHIPPING POLICY</strong></p>
<p class="Default">The products are shipped by the seller at the domicile address/other address mentioned by the buyer.</p>
<p class="Default">The products are shipped by COURIER.</p>
<p class="Default">Shipping costs amount to ... RON.</p>
<p class="Default">Available products will be shipped within 24 hours since the order confirmation by e-mail. You should receive your products within maximum 2 days since the order confirmation. This period may vary due to causes independent of our will, but without exceeding 10 working days since the date of your order registration.</p>
<p class="Default">The products will be properly packed in order to ensure their safety during shipping. According to the applicable law, the courier will be liable for any damage caused to the product or parcel during shipping.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>11. COMPLAINTS POLICY</strong></p>
<p class="Default">Should you experience any problem accessing, using or creating an account on our website, placing an order, or regarding an existing order and other similar issues, you may contact us by phone at 0700 000 000, or by e-mail at EMAIL@EMAIL.COM.</p>
<p class="Default">Your complaint will be recorded and you will receive a response in writing within maximum 3 working days, at the e-mail address provided when submitting your complaint.</p>
<p class="Default">The Client agrees not to make such complaints public (on social media, at private reunions, or in other circumstances), otherwise being liable for remedies in respect of the reputational damage caused to the Company through such actions.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>12. AMENDMENTS TO TERMS AND CONDITIONS</strong></p>
<p class="Default">THE COMPANY reserves the right to change at any time the content of the website <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> , the related policy, terms and conditions, without any obligation of prior notice sent to the users in this respect, although we will do our best to inform our clients on such changes.</p>
<p class="Default">The user of the website <a href="http://www.magazinultau.ro/">www.magazinultau.ro</a> must check on a regular basis the terms and conditions. Should the user disagree with the changes in the terms and conditions operated by THE COMPANY, he/she will no longer be able to use the website. If the user continues to use the website, it will be deemed that he/she agrees with such changes.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>13. CONTRACTUAL LIABILITY</strong></p>
<p class="Default">By creating and using the account, the Client assumes responsibility for the confidentiality of its account details (user and password) and for managing account access, being liable for the activity carried out through its account according to the applicable law.</p>
<p class="Default">By accessing the website, creating the account, using the website, placing orders etc., the Client agrees expressly and unambiguously with the latest Terms and Conditions, as communicated on the website. After creating the account, the use of the website content implies the acceptance of the changes operated in the Terms and Conditions and/or the updated versions of such Terms and Conditions. The Client is responsible for checking the latest version of the Terms and Conditions, whenever accessing the website.</p>
<p class="Default">The acceptance of the website Terms and Conditions is confirmed by ticking the appropriate checkbox on the website and/or by sending the Order and/or by making an online payment.</p>
<p class="Default">We will not be liable for any damage that the Client or a third party may incur as a result of our fulfillment of the obligations related to the Order, or for any damage resulting from the use of the Goods after their delivery, and least of all for their loss.</p>
<p class="Default">We will not be held responsible for any damage to your computer or for viruses that may affect your computer or any other equipment following your access, use, navigation, download of any content, information, material, data, text, picture, video or audio file on our website.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>16. RETURN POLICY</strong></p>
<p class="Default">The Clients who are not satisfied with the purchased product may return it and/or select another product for the amount of the returned product, according to the following terms and conditions. Unsealed products, or products missing the original label or packaging will not be accepted for return. Before returning the products, clients must notify the merchant in advance, by sending an e-mail at EMAIL@EMAIL.COM, specifying the following:</p>
<p class="Default">- the product/products to be returned</p>
<p class="Default">- the reason for product return</p>
<p class="Default">- the option for replacement or refund</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>17. FORCE MAJEURE</strong></p>
<p class="Default">Neither party will be liable for the failure to perform in due time or/and for the inappropriate performance - in total or in part - of any of its obligations arising from the herein agreement, if such failure to perform or inappropriate performance was caused by a force majeure event, as defined by the law, for reasons independent of the parties.</p>
<p class="Default">The party claiming force majeure must notify the other party within 5 (five) days regarding the occurrence of such event and take all reasonable measures in order to mitigate its consequences.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>18. GOVERNING LAW</strong></p>
<p class="Default">The agreement is governed by and interpreted according to the Romanian law. Any dispute between the Merchant and the Client in connection with the herein agreement will be solved in an amicable manner, or otherwise submitted to the competent court in THE CITY.</p>
<p class="Default">&nbsp;</p>
<p class="Default"><strong>19. FINAL PROVISIONS</strong></p>
<p class="Default">This website is owned by THE COMPANY, which gives you the right to access and use it, subject to your acceptance of these Terms and Conditions. By accessing and using the website, you agree expressly and unambiguously with the Terms and Conditions.</p>
<p class="Default">THE COMPANY is entitled to change the Terms and Conditions at any time, without prior notice, by posting their updated version on the website; the Client must read the Terms and Conditions whenever accessing the website. The Client must fully observe the website Terms and Conditions and cannot pretend or defend himself/herself by not knowing the Terms and Conditions displayed on the website at the date of accessing, using and/or placing an order on the website.</p>
<p class="Default">THE COMPANY organises various promotional sales on a periodical basis, which are announced on the website. The sales campaigns are valid starting from their activation until their deactivation on the website. The promotions cannot be cumulated with other promotions or discounts and are valid subject to product availability.</p>';

    $query = "
        INSERT INTO `cms_page` (`title`, `root_template`, `identifier`, `content`, `is_active`)
        VALUES ('Termeni si Conditii EN', 'one_column', 'terms-and-conditions', ' . $content . ', '0');
    ";
    $installer->run($query);
}

// Create "Politica de confidentialitate DE" CMS Block
$identifier = 'datenschutzrichtlinie';
$page = Mage::getModel('cms/page');

if (!$page->load($identifier)->getIdentifier()) {
    $content = '<h1 align="center">Datenschutzrichtlinie zum Schutz personenbezogener Daten</h1>
    <p class="Default">&nbsp;</p>
    <p>Ab dem 25. Mai 2018 wird die Verordnung 2016/679 / EU zum Schutz nat&uuml;rlicher Personen bei der Verarbeitung personenbezogener Daten und zum freien Verkehr solchen Daten (im Folgenden "die Verordnung") von allen Mitgliedstaaten der Europ&auml;ischen Union angewendet. Mit dieser Verordnung soll ein zusammenh&auml;ngender und einheitlicher Gesetzesrahmen in der Europ&auml;ischen Union geschaffen werden, der keine nationalen Umsetzungsma&szlig;nahmen mehr erfordert..</p>
    <p>COMPANIE SRL ist verantwortlich f&uuml;r den Betrieb der Website www.BRAND.ro und gilt als <strong>Betreiber</strong>&nbsp;mit Hauptsitz in &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;., &hellip;&hellip;&hellip;&hellip;&hellip;&hellip; Str., Nr. &hellip;&hellip;&hellip;.., Kreis &hellip;&hellip;., registriert beim Nationales Handelsregisteramt (ORC) unter Nummer &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;., mit USt-IdNr &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;, durch gesetzlichen Vertreter &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;.</p>
    <p class="Default"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Diese Webseite ist nur f&uuml;r Benutzer &uuml;ber 16 Jahre alt geeignet. </strong></p>
    <p>Um unseren Verpflichtungen aus der Verordnung einzuhalten, und da Ihr Datenschutz f&uuml;r uns ein wichtiges und best&auml;ndiges Anliegen ist, haben wir dieses Dokument entwickelt, in dem die Kategorien personenbezogener Daten aufgef&uuml;hrt sind, die wir bei Ihrem Besuch auf unsere Website sammeln, <strong>der Zweck und die Grundlage der Verarbeitung, die Dauer der Verarbeitung, wo wir diese Daten speichern und an wen wir sie weitergeben</strong>, &nbsp;und die Rechte, die Sie in Ihrer Eigenschaft als betroffene Person haben, speziell umgesetzt, um den Schutz Ihrer Grundrechte und Grundfreiheiten zu gew&auml;hrleisten und insbesondere ihr Recht auf Schutz personenbezogener Daten.</p>
    <p>&nbsp;</p>
    <p><strong>1.&nbsp;&nbsp;&nbsp;&nbsp; </strong><strong>Begriffsbestimmungen</strong></p>
    <p><strong><em>&rdquo;</em></strong> <strong><em>personenbezogene Daten&rdquo;</em></strong> alle Informationen, die sich auf eine identifizierte oder identifizierbare nat&uuml;rliche Person (im Folgenden &bdquo;betroffene Person&ldquo;) beziehen; als identifizierbar wird eine nat&uuml;rliche Person angesehen, die direkt oder indirekt, insbesondere mittels Zuordnung zu einer Kennung wie einem Namen, zu einer Kennnummer, zu Standortdaten, zu einer Online-Kennung oder zu einem oder mehreren besonderen Merkmalen identifiziert werden kann, die Ausdruck der physischen, physiologischen, genetischen, psychischen, wirtschaftlichen, kulturellen oder sozialen Identit&auml;t dieser nat&uuml;rlichen Person sind.</p>
    <p><strong><em>&bdquo;Verarbeitung&rdquo;</em></strong> jeden mit oder ohne Hilfe automatisierter Verfahren ausgef&uuml;hrten Vorgang oder jede solche Vorgangsreihe im Zusammenhang mit personenbezogenen Daten wie das Erheben, das Erfassen, die Organisation, das Ordnen, die Speicherung, die Anpassung oder Ver&auml;nderung, das Auslesen, das Abfragen, die Verwendung, die Offenlegung durch &Uuml;bermittlung, Verbreitung oder eine andere Form der Bereitstellung, den Abgleich oder die Verkn&uuml;pfung, die Einschr&auml;nkung, das L&ouml;schen oder die Vernichtung;</p>
    <p><strong><em>&bdquo;Verantwortlicher&rdquo;</em></strong> die nat&uuml;rliche oder juristische Person, Beh&ouml;rde, Einrichtung oder andere Stelle, die allein oder gemeinsam mit anderen &uuml;ber die Zwecke und Mittel der Verarbeitung von personenbezogenen Daten entscheidet; sind die Zwecke und Mittel dieser Verarbeitung durch das Unionsrecht oder das Recht der Mitgliedstaaten vorgegeben, so k&ouml;nnen der Verantwortliche beziehungsweise die bestimmten Kriterien seiner Benennung nach dem Unionsrecht oder dem Recht der Mitgliedstaaten vorgesehen werden;</p>
    <p>&rdquo;<strong><em>Auftragsverarbeiter</em></strong>&rdquo; eine nat&uuml;rliche oder juristische Person, Beh&ouml;rde, Einrichtung oder andere Stelle, die personenbezogene Daten im Auftrag des Verantwortlichen verarbeitet;</p>
    <p>&nbsp;</p>
    <p>Wir stellen Sie sicher, dass Ihre personenbezogenen Daten legal, fair und transparent, nur f&uuml;r die Erf&uuml;llung der ausdr&uuml;cklichen Zwecke, die Ihnen zur Kenntnis gebracht wurden, verarbeitet werden.</p>
    <p>COMPANY SRL als Verantwortlicher verarbeitet die Daten in einer Weise, die durch entsprechende technische oder organisatorische Ma&szlig;nahmen die angemessene Sicherheit personenbezogener Daten, einschlie&szlig;lich des Schutzes vor unbefugter oder rechtswidriger Verarbeitung, sowie Verlust, Zerst&ouml;rung oder versehentliche Besch&auml;digung, gew&auml;hrleistet.&nbsp; &nbsp;</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>2.&nbsp;&nbsp;&nbsp;&nbsp; </strong><strong>Ihre personenbezogenen Daten, die wir verarbeiten. Zweck. Grundlage. Bearbeitungsdauer</strong></p>
    <p>Wir sammeln von Ihnen nur jene personenbezogenen Daten, die erforderlich sind, um unsere Webseite nutzen zu k&ouml;nnen, um Ihre Bestellungen zu erf&uuml;llen und Ihnen Zugang zu unseren Produkten anzubieten und um Sie &uuml;ber unsere Produkte, Dienstleistungen und Angebote auf dem Laufenden zu halten (Direktmarketing), soweit dies aufgrund gesetzlicher Vorschriften oder aufgrund Ihrer Einwilligung zul&auml;ssig ist.</p>
    <p>Wir verarbeiten im allgemeinen die folgenden personenbezogenen Daten von Ihnen.:</p>
    <ul>
    <li>Name und Vorname</li>
    <li>Kontaktadresse und Lieferadresse</li>
    <li>Telefonnummer</li>
    <li>E-mail Adresse</li>
    <li>IP</li>
    </ul>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Jede Kategorie von Daten wird f&uuml;r bestimmte, eindeutige und legitime Zwecke gesammelt und nicht in einer Weise verarbeitet, die mit diesen Zwecken unvereinbar ist, und wird f&uuml;r einen Zeitraum nicht l&auml;nger als die Zeit, die zur Erf&uuml;llung der Zwecke ben&ouml;tigt wird, f&uuml;r die sie verarbeitet werden.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Um den Grundsatz der Transparenz von Informationen bei der Verarbeitung Ihrer personenbezogenen Daten zu gew&auml;hrleisten, teilen wir Ihnen hiermit mit, wie wir Ihre Daten, die Zwecke und die Rechtsgrundlage der T&auml;tigkeiten, durch die wir Ihre Daten verarbeiten.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>KONTOERSTELLUNG</strong></p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Wenn Sie ein Benutzerkonto auf unserer Website www.BRAND.ro erstellen, bieten wir an und sammeln wir Name, Vorname, Adresse, Telefonnummer, E-Mail-Adresse und IP.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Diese personenbezogenen Daten werden f&uuml;r vorvertragliche Zwecke ben&ouml;tigt, damit Sie auf unsere Produkte zugreifen und eine Bestellung aufgeben k&ouml;nnen, die Grundlage der Verarbeitung die vertragliche Verpflichtung ist. Falls Sie mit der Verarbeitung Ihrer Daten nicht einverstanden sind, ist COMPANIA.SRL nicht in der Lage, Ihre Bestellungen entgegenzunehmen und anzuliefern.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ihre personenbezogenen Daten werden f&uuml;r die gesamte Existenz des angelegten Kontos solange aufbewahrt, bis das Konto gel&ouml;scht wurde, jedoch nicht l&auml;nger als 3 Jahre ab dem letzten Login oder der letzten Bestellung. Wir werden Sie benachrichtigen, bevor wir Ihr Kundenkonto schlie&szlig;en und alle mit diesem Kundenkonto verbundenen Daten l&ouml;schen. Wenn Sie nach der Erstellung eines Kontos eine Bestellung aufgeben, werden Ihre Daten f&uuml;r 3 Jahre ab dem Zeitpunkt der letzten Bestellung aufbewahrt. Dies ist die Verj&auml;hrungsfrist.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; Ihre zum Zeitpunkt der Kontoerstellung verarbeiteten Daten sind nicht Gegenstand einer Entscheidung, die ausschlie&szlig;lich auf automatischer Verarbeitung, einschlie&szlig;lich Profilerstellung, beruht, werden nicht an Drittpersonen weitergegeben und unterliegen nicht der &Uuml;bertragung in ein Drittland oder eine internationale Organisation.</p>
    <p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>BESTELLVORGANG</strong></p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Beim Ausf&uuml;llung und Fertigstellung des Bestellformulars bitten wir Ihnen den Namen, den Nachnamen, die Kontaktadresse, die Lieferanschrift, die Telefonnummer und E-Mail und IP-Adresse.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Diese personenbezogenen Daten werden f&uuml;r Vertragszwecke ben&ouml;tigt, um Ihre Bestellungen entgegennehmen zu k&ouml;nnen, die Produkte an die angegebene Adresse zu liefern, unsere Garantieverpflichtungen im Zusammenhang mit den Produkten zu erf&uuml;llen oder, falls notwendig, ein Produkt zur&uuml;ckzusenden. Die Grundlage der Verarbeitung ist die vertragliche Verpflichtung zwischen den Parteien, wie in den Allgemeinen Gesch&auml;ftsbedingungen festgesetzt ist. Wenn Sie mit der Verarbeitung Ihrer Daten nicht einverstanden sind, COMPANIA.SRL kann Ihre Bestellungen nicht entgegennehmen und ausliefern.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Gleichzeitig sind Ihre personenbezogenen Daten erforderlich, um die Steuerrechnungen f&uuml;r die gelieferten Produkte auszuf&uuml;llen und zuzustellen. Ihre Online-Zahlungsdaten werden von COMPANIE SRL nicht zug&auml;nglich gemacht oder gespeichert, sondern nur von dem Anbieter elektronischer Zahlungsdienste oder einer anderen Einheit, die zur Bereitstellung von Datenspeicherdiensten, um die Karte zu identifizieren, berechtigt ist. Sie werden &uuml;ber die Identit&auml;t dieser Einheit informiert, bevor Sie die Details der Karte eingeben, die Sie f&uuml;r die Online-Zahlung verwenden. Die einzigen Zahlungsdaten, die wir speichern, sind diejenigen, die sich auf das Einf&uuml;hrungsdatum und Abschlussdatum der Transaktion, sowie den Zahlungsstatus, beziehen. Aufgrund der gesetzlichen Verpflichtung werden Ihre personenbezogenen Daten, die f&uuml;r die Erstellung von Zahlungsdokumenten ben&ouml;tigt werden, unseren Vertragspartnern, die unsere IT-Dienstleistungen erbringen, zur Verf&uuml;gung gestellt, und werden verwendet, um Steuer- und Kontoerkl&auml;rungen bei den Steuerbeh&ouml;rden einzureichen.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ihre personenbezogenen Daten werden f&uuml;r die Dauer des Vertrages und f&uuml;r einen Zeitraum von 3 Jahren ab dem Zeitpunkt der letzten Bestellung aufbewahrt, wobei dies die Verj&auml;hrungsfrist ist.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Um p&uuml;nktlich und im Einklang mit unseren Verpflichtungen zu sein, werden Ihre personenbezogenen Daten an unsere vertrauensw&uuml;rdigen Partner weitergegeben, sorgf&auml;ltig ausgew&auml;hlt:</p>
    <ul>
    <li>der Anbieter des Datenspeicherdienstes auf externen Servern in Rum&auml;nien;</li>
    <li>der Buchhaltungsdienstleister;</li>
    <li>der Anbieter von Kommunikations und E-Mail-Verkehr;</li>
    <li>der Anbieter von Mobiltelefoniediensten, der uns hilft, mit Ihnen in Kontakt zu bleiben, mit dem Sitz in Rum&auml;nien;</li>
    <li>die Kuriere, mit denen wir eine vertragliche Beziehung abgeschlossen haben;</li>
    <li>die Bearbeiter des Online-Bezahldienstes</li>
    </ul>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ihre zum Zeitpunkt der Kontoerstellung verarbeiteten Daten sind nicht Gegenstand einer Entscheidung, die ausschlie&szlig;lich auf automatischer Verarbeitung, einschlie&szlig;lich Profilerstellung, beruht, werden nicht an Drittpersonen weitergegeben und unterliegen nicht der &Uuml;bertragung in ein Drittland oder eine internationale Organisation.</p>
    <p></p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>NEWSLETTER &ndash; DIREKTES MARKETING </strong></p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Das An- und Abmelden des BRAND-Newsletters ist kostenlos und freiwillig und erfolgt <strong>ausschlie&szlig;lich auf der Grundlage Ihrer Zustimmung.</strong> Der Newsletter ist eine regelm&auml;&szlig;ige, elektronische (E-Mail, SMS) Information &uuml;ber die Waren, Dienstleistungen, Promotionen, etc., innerhalb einer bestimmten Zeit, ohne jegliche Verpflichtung seitens des Verk&auml;ufers in Bezug auf die darin enthaltenen Informationen.</p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Um die Marketing-Dienstleistungen sicherzustellen, sammeln wir von Ihnen die E-Mail-Adresse, die Telefonnummer, der Name und Nachname. Diese Daten werden ausschlie&szlig;lich dazu verwendet, Sie &uuml;ber unsere Produkte, Dienstleistungen und Angebote auf dem Laufenden zu halten. Die Grundlage f&uuml;r die Verarbeitung der personenbezogenen Daten ist Ihre Zustimmung und die Dauer der Verarbeitung die gesamte Dauer dieser Zustimmung ist.</p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Die Zustimmung kann jederzeit durch eine E-Mail an die folgende E-Mail-Adresse ________________________ &nbsp;oder durch Zugriff auf den Link zum Abmelden in der E-Mail, die Sie von uns erhalten haben, zur&uuml;ckgezogen werden, was zur Beendigung der Verarbeitung f&uuml;hren wird. Die R&uuml;ckzug der Zustimmung hat keinen Einfluss auf die Rechtm&auml;&szlig;igkeit der Verarbeitung, die vor diesen R&uuml;ckzug erfolgt ist.</p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ihre Daten werden nicht an andere Betreiber oder bevollm&auml;chtigte Personen weitergegeben und auch nicht an Drittstaaten und / oder internationale Organisationen weitergeleitet.</p>
    <p class="Default"></p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;<strong>COOKIES</strong></p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Die Website BRAND.ro verwendet Cookies. Dabei handelt es sich um Daten, die auf der Festplatte des Benutzers gespeichert sind und enth&auml;lt Informationen dar&uuml;ber. Die Verwendung eines Cookie-Mechanismus ist ein Vorteil f&uuml;r die Besucher, da sie das Speichern einiger Browser-Optionen auf der Site erm&ouml;glicht, z.B. die Sprache, in der die Website angezeigt wird, &nbsp;den Filter f&uuml;r die Anzeige bestimmter Seiten sowie den Benutzernamen und das Passwort f&uuml;r einen schnellen Zugriff auf den Website-Inhalt. Die Ablehnung eines Cookies bedeutet nicht, dass dem Benutzer der Zugriff auf die Website verweigert wird. Mit Cookies k&ouml;nnen Websitebesitzer Nutzerinteressen in bestimmten Bereichen oder Website-Apps &uuml;berwachen und segmentieren. Dadurch k&ouml;nnen sie ihre Browser-Erfahrung verbessern, relevante Nutzerinhalte einf&uuml;hren usw. Einige unserer Gesch&auml;ftspartner verwenden Cookies auf unserer Website (z. B. diejenigen die ihre Eigenwerbung treiben). Wir haben jedoch keinen Zugriff auf diese Cookies und k&ouml;nnen wir sie nicht kontrollieren.</p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Mit diesen Cookies k&ouml;nnen wir Ihre E-Mail-Adresse speichern, so dass Sie bei Ihrem n&auml;chsten Besuch automatisch erkannt und eingeloggt werden.</p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Selbstverst&auml;ndlich k&ouml;nnen Sie unsere Website auch ohne Cookies besuchen.</p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Wenn Sie nicht m&ouml;chten, dass wir Ihren Computer wiedererkennen, k&ouml;nnen Sie das Speichern von Cookies auf Ihrer Festplatte verhindern, indem Sie Cookies in Ihren Browsereinstellungen deaktivieren. Sie k&ouml;nnen die detaillierte Funktionsweise in den Anweisungen Ihres Browsers sehen. Wenn Sie keine Cookies akzeptieren, kann dies zu Funktionsbeschr&auml;nkungen unserer Angebote f&uuml;hren.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;LINKS</strong></p>
    <p class="Default">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Diese Website enth&auml;lt Links zu anderen Websites. COMPANIE S.R.L. ist nicht verantwortlich f&uuml;r die Vertraulichkeitsrichtlinie, die sie anwenden. Wir empfehlen Ihnen die vorherige Untersuchung der rechtlichen Bedingungen und anderer Informationen &uuml;ber die Sammlung von personenbezogenen Informationen. Die in diesem Text beschriebenen Regeln gelten nur f&uuml;r Informationen, die auf dieser Website gesammelt werden.</p>
    <p>Wenn Sie auf eine Plugins-Webseite zugreifen, baut Ihr Browser eine direkte Verbindung mit den Servern dieser Anbieter auf. Der Inhalt des Plugins wird von diesem Anbieter direkt an Ihren Browser &uuml;bermittelt und von diesem in die Webseite eingebunden. Durch die Einbindung des Plugins erh&auml;lt dieser Anbieter die Information, dass Ihr Browser auf unsere Webseite zugegriffen hat. Dies geschieht unabh&auml;ngig davon, ob Sie ein Profil auf diesem sozialen Netzwerk haben oder ob sich gerade angemeldet haben.</p>
    <p>Wenn Sie bei diesem sozialen Netzwerk angemeldet sind, kann es den Besuch auf unsere Webseite Ihrem Profil in diesem sozialen Netzwerk zuordnen. Wenn Sie mit Plugins interagieren, zum Beispiel den Facebook-Button "Gef&auml;llt mir" dr&uuml;cken oder einen Kommentar abgeben, wird die entsprechende Information direkt an den Anbieter &uuml;bermittelt und dort gespeichert.</p>
    <p>Informationen &uuml;ber den Zweck und Umfang der Datenerfassung, Verarbeitung und Weiterverwendung personenbezogener Daten durch diesen Anbieter, sowie Ihre Rechte und Einstellungsm&ouml;glichkeiten zum Schutz Ihres Privatleben, finden Sie in den Datenschutzrichtlinien dieses Anbieters.</p>
    <p class="Default">&nbsp;</p>
    <p class="Default"><strong>3.&nbsp;&nbsp;&nbsp;&nbsp; </strong><strong>Sicherheit Ihrer personenbezogenen Daten</strong></p>
    <p class="Default"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </strong>Die Sicherheit Ihrer personenbezogenen Daten hat f&uuml;r uns Priorit&auml;t. Wir stellen Sie sicher, dass jede Datenverarbeitung in &Uuml;bereinstimmung mit den in der Verordnung garantierten Grunds&auml;tzen erfolgt und so verarbeitet wird, dass die angemessene Sicherheit personenbezogener Daten gew&auml;hrleistet ist, einschlie&szlig;lich des Schutzes vor unbefugter oder rechtswidriger Verarbeitung sowie vor Verlust, Zerst&ouml;rung oder versehentlicher Besch&auml;digung durch geeignete technische oder organisatorische Ma&szlig;nahmen oder durch Umsetzung geeigneter interner Datenschutzrichtlinien.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Diese Website &uuml;bernimmt alle Sicherheitsma&szlig;nahmen, die zum Schutz der personenbezogenen Daten unserer Benutzer erforderlich sind. Nach der Ausf&uuml;llung Ihrer personenbezogenen Daten auf unsere Website, werden die Informationen sowohl offline als auch online gesch&uuml;tzt. Alle personenbezogenen Informationen werden &uuml;ber sichere Seiten mit dem SSL-Verschl&uuml;sselungssystem verarbeitet, das mit einem Schloss-Symbol unterseits des Browserfensters von Microsoft Internet Explorer gekennzeichnet ist.</p>
    <p>&nbsp;</p>
    <p><strong>4.&nbsp;&nbsp;&nbsp;&nbsp; </strong><strong>Ihre Rechte als betroffene Person</strong></p>
    <p>&nbsp;</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Die Verordnung 679/2016 gew&auml;hrleistet den Schutz der Grundrechte und Grundfreiheiten der nat&uuml;rlicher Personen und insbesondere Ihren Rechte auf Schutz personenbezogener Daten.</strong></p>
    <p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; In Bezug auf Ihre personenbezogenen Daten haben Sie das Recht, die Aus&uuml;bung der folgenden Rechte zu verlangen, um den Schutz Ihrer personenbezogenen Daten sicherzustellen:</strong></p>
    <p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Recht auf Ihre Daten zugreifen:</strong> Sie k&ouml;nnen verlangen, dass Sie &uuml;ber die Kategorien der in Verarbeitung befindlichen personenbezogenen Daten, den Zweck, f&uuml;r den die Verarbeitung erfolgt, die Empf&auml;nger, denen sie mitgeteilt wurden oder werden, den Zeitraum, f&uuml;r den sie gespeichert werden sollen, informiert werden oder, falls dies nicht m&ouml;glich ist, die Kriterien, die zur Bestimmung dieses Zeitraums verwendet wurden; die Existenz eines automatisierten Entscheidungsprozesses einschlie&szlig;lich der Erstellung von Profilen;<strong></strong></p>
    <p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </strong><strong>- Recht auf Berichtigung</strong>: Wenn bei den von Ihnen bearbeiteten Daten Fehler auftreten, k&ouml;nnen Sie verlangen, dass sie korrigiert oder vervollst&auml;ndigt werden. Der Unterzeichnende teilt jedem Empf&auml;nger, dem die Daten &uuml;bermittelt wurden, die Berichtigung mit, sofern dies nicht unm&ouml;glich ist oder unverh&auml;ltnism&auml;&szlig;ige Anstrengungen erfordert;</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - <strong>Recht auf Einschr&auml;nkung der Verarbeitung: </strong>Sie haben das Recht, von dem Verantwortlichen die Einschr&auml;nkung der Verarbeitung zu verlangen, wenn eine der folgenden Voraussetzungen gegeben ist: wenn Sie die Richtigkeit der personenbezogenen Daten bestritten haben, und zwar f&uuml;r eine Dauer, die es uns erm&ouml;glicht, die Richtigkeit der personenbezogenen Daten zu &uuml;berpr&uuml;fen; wenn die Verarbeitung unrechtm&auml;&szlig;ig ist und Sie die L&ouml;schung der personenbezogenen Daten ablehnen und stattdessen die Einschr&auml;nkung der Nutzung der personenbezogenen Daten verlangen; der Unterzeichnende die personenbezogenen Daten f&uuml;r die Zwecke der Verarbeitung nicht l&auml;nger ben&ouml;tigt, Sie diese Daten jedoch zur Geltendmachung, Aus&uuml;bung oder Verteidigung von Rechtsanspr&uuml;chen ben&ouml;tigen; wenn Sie der Verarbeitung f&uuml;r die Dauer der &Uuml;berpr&uuml;fung widersprochen haben, dass die legitimen Rechte des Verantwortlichers Vorrang vor Ihren Rechten haben.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - <strong>Recht auf L&ouml;schung ("Recht auf Vergessenwerden")</strong>: Sie k&ouml;nnen die L&ouml;schung der verarbeiteten Daten verlangen, wenn die personenbezogenen Daten f&uuml;r die Zwecke, f&uuml;r die sie erhoben oder auf sonstige Weise verarbeitet wurden, nicht mehr notwendig sind; wenn Sie Ihre Zustimmung widerrufen haben und es keine andere Rechtsgrundlage f&uuml;r die Verarbeitung gibt; wenn Sie Widerspruch gegen die Verarbeitung einlegen und es keine vorrangigen berechtigten Gr&uuml;nde f&uuml;r die Verarbeitung vorliegen; die personenbezogenen Daten wurden unrechtm&auml;&szlig;ig verarbeitet; Die L&ouml;schung der personenbezogenen Daten ist zur Erf&uuml;llung einer rechtlichen Verpflichtung erforderlich; datele cu caracter personal au fost colectate &icirc;n legătură cu oferirea de servicii ale societății informaționale. <strong></strong></p>
    <p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - Recht auf Daten&uuml;bertragbarkeit</strong>: Sie k&ouml;nnen die Weitergabe Ihrer Daten an einen anderen Verantwortlicher verlangen, wenn die Verarbeitung auf Ihrer Zustimmung beruht und die Verarbeitung automatisch erfolgt.<strong></strong></p>
    <p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - das Recht, sich der Verarbeitung zu widersetzen: </strong>Sie haben jederzeit das Recht, die Verarbeitung Ihrer Daten f&uuml;r Direktmarketing, einschlie&szlig;lich der Erstellung von Profilen, abzulehnen. In diesem Fall werden Ihre Daten gel&ouml;scht.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; - <strong>das Recht, automatischen Entscheidungen, die ausschlie&szlig;lich auf automatischer Verarbeitung beruhen, einschlie&szlig;lich Profiling, zu widersprechen.</strong></p>
    <p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Alle diese Rechte k&ouml;nnen durch eine einfache Anfrage, die an COMPANIE SRL als Verantwortlicher an unserem Sitz oder an die E-Mail-Adresse ____________ gerichtet ist, oder durch Zugriff auf die Funktionen auf unserer Website (Link Export und L&ouml;schen).</strong></p>
    <p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </strong></p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Wenn Sie glauben, dass Ihre Rechte verletzt wurden, k&ouml;nnen Sie eine Beschwerde an die nationale Aufsichtsbeh&ouml;rde f&uuml;r die Verarbeitung personenbezogener Daten richten.<strong></strong></p>
    <p class="Default"><strong>&nbsp;</strong></p>
    <p class="Default"><strong></strong><strong>5.&nbsp;&nbsp;&nbsp;&nbsp; </strong><strong>Kontaktdaten</strong></p>
    <p class="Default"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </strong>Diese Richtlinie wird durch die anderen spezifischen Richtlinien von COMPANY SRL und die Allgemeinen Gesch&auml;ftsbedingungen erg&auml;nzt, auf die Sie &uuml;ber unsere Website zugreifen k&ouml;nnen. Jegliche &Auml;nderungen der Bedingungen dieser Richtlinie werden den Benutzern per E-Mail mitgeteilt, damit sie permanent &uuml;ber die von uns gesammelten Informationen informiert werden, wie wir sie verwenden und unter welchen Umst&auml;nden - wenn &uuml;berhaupt - wir sie ver&ouml;ffentlichen. Die Benutzer k&ouml;nnen der Verwendung von Informationen f&uuml;r andere Zwecke zustimmen oder nicht. Wir werden die Informationen in &Uuml;bereinstimmung mit den Richtlinien verwenden, unter denen die Informationen gesammelt wurden.</p>
    <p>Diese Benachrichtigung ist auf unsere Website eingeschr&auml;nkt und gilt nicht f&uuml;r die Websites von Dritten, auf die &uuml;ber diese Website zugegriffen werden kann. Wir haben keinen Einfluss auf die Datenverarbeitung durch diese Dritte Verantwortlichen und &uuml;bernehmen keine Verantwortung oder Haftung in Verbindung mit diesen Webseiten.</p>
    <p><span style="text-decoration: underline;">&nbsp;</span></p>
    <p>COMPANY&nbsp;SRL</p>
    <p>Verantwortlicher _________________________</p>
    <p>Telefon&nbsp; ____________</p>
    <p>Mobil&nbsp; ___________</p>
    <p>Mail&nbsp;&nbsp; ___________</p>
    <p>&nbsp;&nbsp;</p>
    <p></p>
    <p><a href="{{store url="gdpruserdata/deletedata"}}"><span>DELETE DATA</span></a></p>
    <p><a href="{{store url="gdpruserdata/exportdata"}}"><span>EXPORT DATA</span></a></p>';

    $query = "
        INSERT INTO `cms_page` (`title`, `root_template`, `identifier`, `content`, `is_active`)
        VALUES ('Politica de confidentialitate DE', 'one_column', 'datenschutzrichtlinie', ' . $content . ', '0');
    ";
    $installer->run($query);
}

// Create "Politica de confidentialitate EN" CMS Page
$identifier = 'privacy-policy-page';
$page = Mage::getModel('cms/page');

if (!$page->load($identifier)->getIdentifier()) {
    $content = '<h1 class="western" lang="en-GB"><span style="color: #000000;"><span style="font-size: x-large;">Privacy policy regarding personal data protection</span></span></h1>
<p lang="en-GB"></p>
<p lang="en-GB"><span style="font-size: medium;">From May 25, 2018, the Regulation (EU) 2016/679 on the protection of natural persons with regard to the processing of personal data and on the free movement of such data (the &ldquo;Regulation&rdquo;) will apply in all the countries of the European Union. The purpose of the Regulation is to provide a uniform and coherent legal framework across the European Union, which should not require further implementation measures at national level.</span></p>
<p><span lang="en-GB">THE COMPANY, headquartered in &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;., Str. &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;, no. &hellip;&hellip;&hellip;.., County &hellip;&hellip;., Trade Register No. &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;., Tax Identification No. &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;, legally represented by &hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;&hellip;., as </span><span lang="en-GB"><strong>Data Controller</strong></span><span lang="en-GB">, is responsible for the operation of the website www.BRAND.ro.</span></p>
<p lang="en-US"><span style="color: #000000;"><span style="font-family: Calibri, sans-serif;"><span style="font-size: medium;"><span lang="en-GB"> </span><span lang="en-GB"><strong>This web page is dedicated exclusively to users above the age of 16 </strong></span></span></span></span></p>
<p><span style="font-size: medium;"><span lang="en-GB">In order to comply with our obligations arising from the Regulation and considering that personal data protection is a major and ongoing concern to us, </span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB">we have created this document, which describes </span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB"><strong>the categories of personal data</strong></span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB"> that we collect when you access our web page, </span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB"><strong>the purpose and grounds for data processing</strong></span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB">, </span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB"><strong>the duration of the processing</strong></span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB">, </span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB"><strong>where we store and with whom we share such data</strong></span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB">, as well as </span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB"><strong>your rights as a Data Subject</strong></span></span></span><span style="color: #000000;"><span style="font-size: medium;"><span lang="en-GB">,</span></span></span><span style="font-size: medium;"><span lang="en-GB"> these aspects being implemented to ensure the protection of your fundamental rights and freedoms and particularly your right with respect to personal data security.</span></span></p>
<p lang="en-GB"></p>
<ol>
<li>
<p lang="en-GB"><span style="font-size: medium;"><strong>Definitions</strong></span></p>
</li>
</ol>
<p>&ldquo;<span style="font-size: medium;"><span lang="en-GB"><strong>personal data&rdquo; </strong></span></span><span lang="en-GB">means any information relating to an identified or identifiable natural person (&ldquo;data subject&rdquo;); an identifiable natural person is one who can be identified, directly or indirectly, in particular by reference to an identifier such as a name, an identification number, location data, an online identifier or to one or more factors specific to the physical, physiological, genetic, mental, economic, cultural or social identity of that natural person;</span></p>
<p>&ldquo;<span lang="en-GB"><strong>processing&rdquo;</strong></span><span lang="en-GB"> means any operation or set of operations which is performed on personal data or on sets of personal data, whether or not by automated means, such as collection, recording, organisation, structuring, storage, adaptation or alteration, retrieval, consultation, use, disclosure by transmission, dissemination or otherwise making available, alignment or combination, restriction, erasure or destruction;</span></p>
<p>&ldquo;<span lang="en-GB"><strong>controller&rdquo; </strong></span><span lang="en-GB">means the natural or legal person, public authority, agency or other body which, alone or jointly with others, determines the purposes and means of the processing of personal data; where the purposes and means of such processing are determined by Union or Member State law, the controller or the specific criteria for its nomination may be provided for by Union or Member State law;</span></p>
<p>&ldquo;<span lang="en-GB"><strong>processor&rdquo;</strong></span><span lang="en-GB"> means a natural or legal person, public authority, agency or other body which processes personal data on behalf of the controller;</span></p>
<p lang="en-GB"></p>
<p><span style="font-size: medium;"><span lang="en-GB">We assure you that your personal data are processed </span></span><span lang="en-GB">in a lawful, fair and transparent manner, solely for the explicit purposes brought to your attention. </span></p>
<p><span lang="en-GB">THE COMPANY, as Controller, processes personal data in a manner that ensures the proper security of such data, including the protection against unauthorised or unlawful processing, loss, accidental destruction or damage, by implementing appropriate technical and organisational measures. </span><span style="font-size: medium;"><span lang="en-GB"> </span></span></p>
<p lang="en-GB"></p>
<ol start="2">
<li>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: small;"><strong>Personal data that we process. Purpose. Grounds. Duration of processing. </strong></span></span></p>
</li>
</ol>
<p lang="en-GB">We only collect from you the personal data that are necessary in order to allow you to access our web page, to process your orders, to give you access to our products and to keep you updated on our products, services and offers (direct marketing), to the extent permitted by the applicable laws or based on your consent.</p>
<p lang="en-GB">As a rule, we process the following data about you:</p>
<ul>
<li>
<p lang="en-GB">Full name</p>
</li>
<li>
<p lang="en-GB">Contact address and delivery address</p>
</li>
<li>
<p lang="en-GB">Phone number</p>
</li>
<li>
<p lang="en-GB">E-mail address</p>
</li>
<li>
<p lang="en-GB">IP address</p>
</li>
</ul>
<p><span lang="en-GB"> Each category of data is collected for specific, explicit and legitimate purposes, will not be subsequently processed in a manner that is inconsistent with these purposes and will only be processed for as long as necessary to fulfil the processing purposes.</span></p>
<p lang="en-GB">In order to be in line with the transparency principle as concerns the processing of your personal data, we present below the manner, the purposes and the legal grounds (legal basis) of our data processing activities.</p>
<p lang="en-GB"></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>CREATING AN ACCOUNT</strong></span></p>
<p><span lang="en-GB"> When you create a user account on our website </span><span style="color: #0563c1;"><span style="text-decoration: underline;"><a href="http://www.BRAND.ro/"><span lang="en-GB">www.BRAND.ro</span></a></span></span><span lang="en-GB">, we request and collect your name, surname, address, phone number, e-mail address and IP address.</span></p>
<p lang="en-GB">We need such personal data for pre-contractual purposes, in order to allow you to access our products and to place an order, the grounds for such processing being represented by the contractual obligation. Unless you agree to the processing of your personal data THE COMPANY will not be able to process and deliver your orders.</p>
<p lang="en-GB">Your personal data will be stored during the entire existence period of the account, until its deletion, but no longer than 3 years from the last login date or the last order. We will notify you accordingly before closing your client account and deleting all the data linked to it. If you place an order after creating your account, your data will be stored for a period of 3 years since the last order date; this represents the limitation period.</p>
<p lang="en-GB">The personal data processed upon the creation of your account are not subject to a decision based solely on automated processing, including profiling, will not be disclosed to any third party and will not be transferred to any third country or international organisation.</p>
<p lang="en-GB"></p>
<p lang="en-GB"></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>PLACING AN ORDER</strong></span></p>
<p lang="en-GB">When you fill in and complete the order form, we request and collect your name, surname, contact address, delivery address, phone number, e-mail address and IP.</p>
<p><span lang="en-GB"> We need such personal data for contractual purposes, in order to process your orders, to deliver your products at the mentioned address, to fulfil our warranty obligations in respect of the products, or to allow you to return the products. The grounds for such processing are represented by the contractual obligation of the parties, as established in the Terms and Conditions. Unless you agree to the processing of your personal data THE COMPANY will not be able to process and deliver your orders.</span></p>
<p lang="en-GB">Moreover, we need your personal data in order to fill in and remit the invoices in connection with the products delivered to you. Your online payment details will not be available to or stored by THE COMPANY, but only by the provider of electronic payment services or by another entity authorised to store the card identification details, of whose identity you will be notified prior to inserting your card details for online payment. The only payment-related details that we will store are those regarding the date when the transaction is initiated or completed and the payment status. Subject to the legal requirements, your personal data required for the issuance of payment documents will be transmitted to our IT service providers and used for the purpose of filing the requested tax and accounting statements with the tax authorities.</p>
<p lang="en-GB">Your data will be stored during the entire period of the contractual relationship and for a period of 3 years since the last order date; this represents the limitation period.</p>
<p><span lang="en-GB"> In order for us to fulfil our obligations in a timely and appropriate manner, your personal data will be disclosed to our reliable and carefully selected business partners:</span></p>
<ul>
<li>
<p lang="en-GB">the provider of external server data storage services, located in Romania;</p>
</li>
</ul>
<ul>
<li>
<p lang="en-GB">the provider of accounting services;</p>
</li>
</ul>
<ul>
<li>
<p lang="en-GB">the provider of communication and e-mail correspondence services;</p>
</li>
<li>
<p lang="en-GB">the provider of mobile communication services located in Romania, through which we stay in contact with you;</p>
</li>
</ul>
<ul>
<li>
<p lang="en-GB">the agreed shipping companies;</p>
</li>
</ul>
<ul>
<li>
<p lang="en-GB">the online payment processors.</p>
</li>
</ul>
<p lang="en-GB">The personal data processed upon the creation of your account are not subject to a decision based solely on automated processing, including profiling and will not be transferred to any third country or international organisation.</p>
<p lang="en-GB"></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>NEWSLETTER &ndash; DIRECT MARKETING </strong></span></p>
<p lang="en-US"><span style="color: #000000;"><span style="font-family: Calibri, sans-serif;"><span style="font-size: medium;"><span lang="en-GB"> Subscribing to and unsubscribing from the BRAND newsletter is free, optional and </span></span><span lang="en-GB"><strong>solely based on your consent.</strong></span></span><span lang="en-GB"> The newsletter is a form of periodical notification via electronic channels only (e-mail, SMS), with regard to the Goods, Services and Promotions of the Seller available during a specific period, but with no commitment on behalf of the Seller as concerns the information content of such newsletter.</span></span></p>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"> For the purpose of marketing services, the data we collect from you are your e-mail address, phone number and full name. Such data will be used solely for keeping you up to date regarding our products, services and offers. The legal grounds for personal data processing are represented by your consent and the duration of such processing equals the entire existence of the consent.</span></span></p>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"> You may withdraw your consent at any time, by sending an e-mail to ________________________ or by accessing the unsubscribe link in the e-mail received from us, which results in the cessation of processing. The withdrawal of your consent will not affect the lawfulness of the processing carried out prior to such withdrawal.</span></span></p>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"> Your data will not be transferred to any other controller or processor, or to any third country or international organisation.</span></span></p>
<p lang="en-GB"></p>
<p lang="en-US"><span style="color: #000000;"><span style="font-family: Calibri, sans-serif;"><span style="font-size: medium;"><span style="color: #ff0000;"><span lang="en-GB"> </span></span><span lang="en-GB"><strong>COOKIES</strong></span></span></span></span></p>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"> The BRAND.ro website uses cookies, which are stored on the user&rsquo;s hard disk and contain information about the respective user. The use of cookies is an advantage to the website visitors, allowing the storage or certain navigation options, such as the display language, the filters applied, the user&rsquo;s name and password, thus ensuring easier access to the website content. The refusal of cookies does not imply the user&rsquo;s interdiction to access the website or to read its content. By using cookies, website owners can monitor and group the users&rsquo; interests around certain website zones or applications, thus enhancing user experience and creating relevant content. Some of our business partners use cookies on our website (for advertising purposes). However, we do not have access to and cannot control such cookies.</span></span></p>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"> Cookie files allow us to save your e-mail address, so that you are automatically recognized and logged in on your next visit.</span></span></span></p>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"> But you can also access our web page without cookie acceptance. </span></span></p>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"> If you do not want your computer to be recognized, you can block the storage of cookies on your hard drive by configuring your web browser to disable cookies. For detailed instructions on cookies, you may check your browser support page. Should you not accept cookies, this may cause functional restrictions related to our offers.</span></span></p>
<p lang="en-GB"></p>
<p lang="en-US"><span style="color: #000000;"><span style="font-family: Calibri, sans-serif;"><span style="font-size: medium;"><span lang="en-GB"> </span><span lang="en-GB"><strong> LINKS</strong></span></span></span></span></p>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"> This website contains links to other websites. THE COMPANY is not responsible for their privacy policy. We recommend that you read in advance the legal conditions and other information regarding the collection of personal data. The standards described here apply only to the information collected on this website.</span></span></p>
<p lang="en-GB"><span style="color: #000000;">When you access web pages that contain plug-ins, your browser creates a direct connection to the servers of the respective providers. The plug-in content is transferred from the respective provider to your browser and embedded on the web page. By integrating plug-ins, the provider is notified that your browser has accessed our web page. This happens irrespective of whether you have a profile on the respective social network or you have just logged in.</span></p>
<p lang="en-GB"><span style="color: #000000;">If you are already logged in, the social network may assign the visit on our website to your profile. For example, if you use plug-ins and you click the &ldquo;Like&rdquo; button or make a comment on Facebook, the related information is sent directly to the respective provider and stored.</span></p>
<p lang="en-GB"><span style="color: #000000;">Further details regarding the purpose and volume of the collected data, the subsequent use and processing of such data through the respective provider, your rights and privacy settings are available in the data protection guidelines of the respective provider.</span></p>
<p lang="en-GB"></p>
<ol start="3">
<li>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"><strong>Personal data security </strong></span></span></p>
</li>
</ol>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"> Personal data security is a top priority for us. We assure you that we process your personal data in line with the Regulation principles, in a manner that ensures the proper security of the data, including the protection against unauthorised or unlawful processing, loss, accidental destruction or damage, by implementing appropriate technical and organisational measures and by applying adequate internal policies in terms of data protection.</span></span></p>
<p><span lang="en-GB"> </span><span lang="en-GB">This website applies all the security measures required for the protection of our users&rsquo; personal data. The personal data filled in on our website will be protected both in offline and in online mode. All personal data will be processed via secure pages using SSL encryption and displaying a lock icon at the bottom of the Microsoft Internet Explorer</span><span style="color: #717171;"><span style="font-size: large;"><span lang="en-GB"> browser window.</span></span></span></p>
<p lang="en-GB"></p>
<ol start="4">
<li>
<p lang="en-GB"><strong>Your rights as a Data Subject</strong></p>
</li>
</ol>
<p lang="en-GB"></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>Regulation 679/2016 aims to ensure the protection of natural persons with regard to their fundamental rights and freedoms, in particular their right to the protection of personal data.</strong></span></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>You are entitled to exercise any of the following rights in order to ensure the protection of your personal data:</strong></span></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>-the right of access:</strong></span><span lang="en-GB"> you may request information regarding the categories of personal data processed, the purposes of the processing, the recipients to whom the personal data have been or will be disclosed, the envisaged period for which the personal data will be stored, or, if not possible, the criteria used to determine that period, the existence of automated decision-making, including profiling;</span></p>
<p><span style="color: #000000;"><span lang="en-GB"> </span></span><span style="color: #000000;"><span lang="en-GB"><strong>-the right to rectification:</strong></span></span><span style="color: #000000;"><span lang="en-GB"> should there be any errors regarding your personal data, you have the right to obtain their rectification or to have incomplete personal data completed. We will notify such rectification to each recipient to whom the personal data have been disclosed, unless this proves impossible or involves disproportionate effort. </span></span></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>-right to restriction of processing:</strong></span><span lang="en-GB"> you have the right to request the restriction of processing in the following situations: you contested the accuracy of the personal data, for a period enabling us to verify the accuracy of the personal data; the processing is unlawful and you oppose the erasure of the personal data and request the restriction of their use instead; the undersigned company no longer needs the personal data for the purposes of the processing, but they are required by you for the establishment, exercise or defence of legal claims; you have objected to processing, pending the verification whether the legitimate grounds of the controller override yours.</span></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>-right to erasure:</strong></span><span lang="en-GB"> you may request the erasure of your personal data if: they are no longer necessary in relation to the purposes for which they were collected or otherwise processed; you withdraw the consent on which the processing is based and where there is no other legal ground for the processing; you object to the processing and there are no overriding legitimate grounds for the processing; the personal data have been unlawfully processed; the personal data have to be erased for compliance with a legal obligation</span><span style="color: #000000;"><span lang="en-GB">; the personal data have been collected in relation to the offer of information society services. </span></span></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>-right to data portability:</strong></span><span lang="en-GB"> you may request the transfer of your personal data to another controller, where the processing is based on your consent and the processing is carried out by automated means.</span></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>-right to object: </strong></span><span lang="en-GB">you may object at any time to the processing of your personal data for direct marketing purposes, including profiling, in which case your personal data will be erased.</span></p>
<p><span lang="en-GB"> </span><span lang="en-GB"><strong>-right not to be subject to a decision based solely on automated processing, including profiling.</strong></span></p>
<p><span style="color: #000000;"><span lang="en-GB"> </span></span><span style="color: #000000;"><span lang="en-GB"><strong>You may exercise these rights by sending a request to THE COMPANY, as controller, at our headquarters, or at the e-mail address ____________, or by using the options available on our website (link to export and delete).</strong></span></span></p>
<p lang="en-GB"></p>
<p lang="en-GB"><span style="color: #000000;"> If you consider that your rights have been violated, you may file a complaint with the National Supervisory Authority for Personal Data Processing.</span></p>
<p lang="en-GB"></p>
<ol start="5">
<li>
<p lang="en-GB"><span style="color: #000000;"><span style="font-size: medium;"><strong>Contact details</strong></span></span></p>
</li>
</ol>
<p lang="en-US"><span style="color: #000000;"><span style="font-family: Calibri, sans-serif;"><span style="font-size: medium;"><span lang="en-GB"> </span><span lang="en-GB">This Policy is supplemented by the other specific policies of THE COMPANY and by the Terms and Conditions available on our website. Any change in the provisions of this policy will be notified to the users by e-mail, in order to ensure that they are permanently updated regarding the information that we collect, how we use it and in what circumstances, if any, we disclose it. The users may agree or disagree with the use of the information for other purposes. We will use the information in accordance with the policy based on which it was collected</span><span style="color: #ff0000;"><span lang="en-GB">. </span></span></span></span></span></p>
<p lang="en-GB">This notification is limited to our web page and is not valid for the websites of third-party controllers, which may be accessed via this website. We have no control of the processing of data by such third-party controllers and we decline any responsibility or liability in relation to the respective web pages.</p>
<p lang="en-GB"></p>
<p lang="en-GB">COMPANY</p>
<p lang="en-GB">Person in charge _________________________</p>
<p lang="en-GB">Telephone no. ____________</p>
<p lang="en-GB">Mobile no. ___________</p>
<p lang="en-GB">E-Mail ___________</p>
<p lang="en-GB"></p>
<p lang="en-GB"></p>
<p lang="en-GB"><span style="color: #ff0000;"><span style="font-size: medium;">.</span></span></p>
    <p>&nbsp;&nbsp;</p>
    <p></p>
    <p><a href="{{store url="gdpruserdata/deletedata"}}"><span>DELETE DATA</span></a></p>
    <p><a href="{{store url="gdpruserdata/exportdata"}}"><span>EXPORT DATA</span></a></p>';

    $query = "
        INSERT INTO `cms_page` (`title`, `root_template`, `identifier`, `content`, `is_active`)
        VALUES ('Politica de confidentialitate EN', 'one_column', 'privacy-policy-page', ' . $content . ', '0');
    ";
    $installer->run($query);
}

$installer->endSetup();