<?php

/** Add all the cities from Belgium (Brussels region). */

$installer = $this;
$installer->startSetup();

$sql = "INSERT INTO `blugento_cautocomplete_city` (`city`, `region`, `region_id`, `zipcode`, `country_code`, `priority`)
VALUES
('Bruxelles', 'Brussels', '', '1000', 'BE', 99),
('Ixelles', 'Brussels', '', '1050', 'BE', 99),
('Molenbeek', 'Brussels', '', '1080', 'BE', 99),
('Saint Josse', 'Brussels', '', '1210', 'BE', 99),
('Anderlecht', 'Brussels', '', '1070', 'BE', 99),
('Etterbeek', 'Brussels', '', '1080', 'BE', 99),
('Evere', 'Brussels', '', '1140', 'BE', 99),
('Koekelberg', 'Brussels', '', '1081', 'BE', 99),
('Saint Gilles', 'Brussels', '', '1060', 'BE', 99),
('Schaerbeek', 'Brussels', '', '1030', 'BE', 99),
('Berchem sainte Agathe', 'Brussels', '', '1082', 'BE', 99),
('Forest', 'Brussels', '', '1090', 'BE', 99),
('Ganshoren', 'Brussels', '', '1083', 'BE', 99),
('Jette', 'Brussels', '', '1090', 'BE', 99),
('Laeken', 'Brussels', '', '1020', 'BE', 99),
('Uccle', 'Brussels', '', '1180', 'BE', 99),
('Woluwe Saint Lambert', 'Brussels', '', '1200', 'BE', 99),
('Woluwe Saint Pierre', 'Brussels', '', '1150', 'BE', 99),
('Auderghem', 'Brussels', '', '1160', 'BE', 99),
('Watermael Boitsfort', 'Brussels', '', '1170', 'BE', 99);
";

$installer->run($sql);

$installer->endSetup();