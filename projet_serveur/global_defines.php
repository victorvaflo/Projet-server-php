<?php
/**
 * ce fichier contient toutes les declaration
 * des constantes ,configuration du project.
 */
 if (!isset($index_loaded)) {
     http_response_code(403);
     die('acces direct a ce fichier est interdit');
 }

//info compagnie
define('COMPANY_NAME', 'ScooterElectrique.com');

define('COMPANY_STREET_ADDRESS', '5340 St-Laurent');

define('COMPANY_CITY', 'Montréal');

define('COMPANY_PROVINCE', 'QC');

define('COMPANY_COUNTRY', 'Canada');

define('COMPANY_POSTAL_CODE', 'J0P 1T0');

define('COMPAGNIE_EMAIL', 'SCOOTER@ISI.COM');

define('COMPAGNIE_TEL', '514-123-4567');

//info page web
define('WEB_SITE_ICON', 'web_site_icon.jpg');
