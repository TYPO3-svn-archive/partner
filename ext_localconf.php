<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2005 David Bruehlmeier (typo3@bruehlmeier.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * localconf for the extension 'partner'
 *
 * @author David Bruehlmeier <typo3@bruehlmeier.com>
 */

	if (!defined ('TYPO3_MODE')) die ('Access denied.');

		// Activate Hooks in TCE-Main (processDatamapClass)
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:partner/inc/class.tx_partner_tcemainprocdm.php:tx_partner_tcemainprocdm';

		// XCLASS Extension Manager to avoid getting a naming error for the ext_update class. Workaround (bug reported)
	$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['typo3/mod/tools/em/index.php'] = PATH_typo3conf.'ext/partner/inc/class.ux_sc_mod_tools_em_index.php';

		// Activate XCLASS of TCE-Main (workaround for TYPO3 3.7.0, as of 3.8.0 we're using the hook processDatamap_afterDatabaseOperations in TCE-Main)
	//$GLOBALS['TYPO3_CONF_VARS']['BE']['XCLASS']['t3lib/class.t3lib_tcemain.php'] = PATH_typo3conf.'ext/partner/inc/class.ux_t3lib_tcemain.php';

		// Define the formats available for converting data
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['partner']['formats'] = array(
		'CSV' => array(
			'external' => 1,
			'icon' => 'fileicons/csv.gif',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner.label.format.csv',
			'formatFunc' => 'EXT:partner/inc/class.tx_partner_format.php:tx_partner_format->formatAsCSV',
		),
		'XML' => array(
			'external' => 1,
			'icon' => 'fileicons/xml.gif',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner.label.format.xml',
			'formatFunc' => 'EXT:partner/inc/class.tx_partner_format.php:tx_partner_format->formatAsXML',
		),
		'XLS' => array(
			'external' => 1,
			'icon' => 'fileicons/xls.gif',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner.label.format.xls',
			'formatFunc' => 'EXT:partner/inc/class.tx_partner_format.php:tx_partner_format->formatAsXLS',
		),
		'BE_module' => array(
			'external' => 0,
			'icon' => 'fileicons/default.gif',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner.label.format.be_module',
			'formatFunc' => 'EXT:partner/inc/class.tx_partner_format.php:tx_partner_format->formatAsBEModule',
		),
		'HTML' => array(
			'external' => 0,
			'icon' => 'fileicons/html.gif',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner.label.format.html',
			'formatFunc' => 'EXT:partner/inc/class.tx_partner_format.php:tx_partner_format->formatAsHTML',
		),
	);

		// Add PDF as an additional format only in case the FPDF-extension is loaded
	if (t3lib_extMgm::isLoaded('fpdf'))		{
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['partner']['formats']['PDF'] = array(
			'external' => 1,
			'icon' => 'fileicons/pdf.gif',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner.label.format.pdf',
			'formatFunc' => 'EXT:partner/inc/class.tx_partner_format.php:tx_partner_format->formatAsPDF',
		);
	}

		// User-defined fields for use in reports. To avoid naming collisions with regular database-fields,
		// these fields must (by convention) always start with an underscore (e.g. '_name')
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['partner']['user_fields'] = array(
		'tx_partner_main' => array(
			'_name' => Array (
				'label' => 'LLL:EXT:partner/locallang.php:tx_partner.user_fields.name',
				'size' => '30',
				'userFunc' => 'EXT:partner/inc/class.tx_partner_user_fields.php:tx_partner_user_fields->name',
			),
			'_age' => Array (
				'label' => 'LLL:EXT:partner/locallang.php:tx_partner.user_fields.age',
				'size' => '10',
				'userFunc' => 'EXT:partner/inc/class.tx_partner_user_fields.php:tx_partner_user_fields->age',
			),
		),
		'tx_partner_contact_info' => array(
			'_prefix' => Array (
				'label' => 'LLL:EXT:partner/locallang.php:tx_partner.user_fields.prefix',
				'size' => '30',
				'userFunc' => 'EXT:partner/inc/class.tx_partner_user_fields.php:tx_partner_user_fields->prefix',
			),
		),
	);
	
		// Folder for cached reports
	if (!is_dir(PATH_site.'typo3temp/tx_partner/')) mkdir(PATH_site.'typo3temp/tx_partner/', 0777);

		// Define that tables for which to add the 'Save and New'-Buttons
	$saveDocNewTables = array(
		'tx_partner_main',
		'tx_partner_val_contact_permissions',
		'tx_partner_val_courses',
		'tx_partner_val_hobbies',
		'tx_partner_val_legal_forms',
		'tx_partner_val_marital_status',
		'tx_partner_val_occupations',
		'tx_partner_val_org_types',
		'tx_partner_val_rel_types',
		'tx_partner_val_religions',
		'tx_partner_val_status',
		'tx_partner_val_titles',
	);

		// Add the 'Save and New'-Buttons
	foreach ($saveDocNewTables as $theTable)		{
		t3lib_extMgm::addUserTSConfig('options.saveDocNew.'.$theTable.'=1');
	}

?>
