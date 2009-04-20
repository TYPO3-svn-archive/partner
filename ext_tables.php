<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 David Bruehlmeier (typo3@bruehlmeier.com)
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
 * Tables definition for the 'partner'-extension
 *
 * @author David Bruehlmeier <typo3@bruehlmeier.com>
 */

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// *************************************
// *** Include Libraries
// *************************************

if (TYPO3_MODE=='BE')	{
		// Load class for handling selects
	include_once(t3lib_extMgm::extPath('partner').'inc/class.tx_partner_select.php');

		// Load class for userFunc fields in TCE Forms
	include_once(t3lib_extMgm::extPath('partner').'inc/class.tx_partner_tce_user.php');
}

// *************************************
// *** Get the conf array (installation)
// *************************************
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['partner']);


// *************************************
// *** Add BE-Module and Submodules
// *************************************

if (TYPO3_MODE=='BE')	{
		// Add the backend-module
	t3lib_extMgm::addModule('web','txpartnerM1',"",t3lib_extMgm::extPath($_EXTKEY).'mod1/');

		// Main Function 'Search'
	t3lib_extMgm::insertModuleFunction(
		'web_txpartnerM1',
		'tx_partner_search',
		t3lib_extMgm::extPath('partner').'modfunc1/class.tx_partner_search.php',
		'LLL:EXT:partner/locallang_db.xml:tx_partner.modfunc.search',
		'function'
	);
	
		// Main Function 'Reports'
	t3lib_extMgm::insertModuleFunction(
		'web_txpartnerM1',
		'tx_partner_reports',
		t3lib_extMgm::extPath('partner').'modfunc1/class.tx_partner_reports.php',
		'LLL:EXT:partner/locallang_db.xml:tx_partner.modfunc.reports',
		'function'
	);

		// Main Function 'Tools'
	t3lib_extMgm::insertModuleFunction(
		'web_txpartnerM1',
		'tx_partner_tools',
		t3lib_extMgm::extPath('partner').'modfunc1/class.tx_partner_tools.php',
		'LLL:EXT:partner/locallang_db.xml:tx_partner.modfunc.tools',
		'function'
	);

		// Submodule 'Reports->Birthday List'
	t3lib_extMgm::insertModuleFunction(
		'web_txpartnerM1',
		'tx_partner_reports_birthdaylist',
		t3lib_extMgm::extPath('partner').'modfunc1/class.tx_partner_reports_birthdaylist.php',
		'LLL:EXT:partner/locallang_db.xml:tx_partner.modfunc.reports.birthdaylist',
		'tx_partner_reports'
	);
	
		// Submodule 'Reports->Email'
	t3lib_extMgm::insertModuleFunction(
		'web_txpartnerM1',
		'tx_partner_reports_email',
		t3lib_extMgm::extPath('partner').'modfunc1/class.tx_partner_reports_email.php',
		'LLL:EXT:partner/locallang_db.xml:tx_partner.modfunc.reports.email',
		'tx_partner_reports'
	);

		// Submodule 'Reports->Overview Relationships'
	t3lib_extMgm::insertModuleFunction(
		'web_txpartnerM1',
		'tx_partner_reports_relationships',
		t3lib_extMgm::extPath('partner').'modfunc1/class.tx_partner_reports_relationships.php',
		'LLL:EXT:partner/locallang_db.xml:tx_partner.modfunc.reports.relationships',
		'tx_partner_reports'
	);
	
		// Submodule 'Tools->Mass Change Relationships'
	t3lib_extMgm::insertModuleFunction(
		'web_txpartnerM1',
		'tx_partner_tools_massrelationships',
		t3lib_extMgm::extPath('partner').'modfunc1/class.tx_partner_tools_massrelationships.php',
		'LLL:EXT:partner/locallang_db.xml:tx_partner.modfunc.tools.massrelationships',
		'tx_partner_tools'
	);

		// Submodule 'Tools->Assign FE-Users'
	t3lib_extMgm::insertModuleFunction(
		'web_txpartnerM1',
		'tx_partner_tools_assignfeusers',
		t3lib_extMgm::extPath('partner').'modfunc1/class.tx_partner_tools_assignfeusers.php',
		'LLL:EXT:partner/locallang_db.xml:tx_partner.modfunc.tools.assignfeusers',
		'tx_partner_tools'
	);

		// Submodule 'Tools->Report Designer'
	t3lib_extMgm::insertModuleFunction(
		'web_txpartnerM1',
		'tx_partner_tools_reportdesigner',
		t3lib_extMgm::extPath('partner').'modfunc1/class.tx_partner_tools_reportdesigner.php',
		'LLL:EXT:partner/locallang_db.xml:tx_partner.modfunc.tools.reportdesigner',
		'tx_partner_tools'
	);

}




// *************************************
// *** CSH (Context-Sensitive Help)
// *************************************

	// Define the context-sensitive help file for all tables
$cshFiles = array(
	'tx_partner_main',
	'tx_partner_contact_info',
	'tx_partner_relationships',
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
	'tx_partner_val_titles'
);

	// Load all context-sensitive (CSH) help files for tables
foreach ($cshFiles as $theCshFile)		{
	t3lib_extMgm::addLLrefForTCAdescr($theCshFile,'EXT:partner/csh/locallang_csh_'.$theCshFile.'.xml');
}

	// Load the CSH for the partner-mdule
t3lib_extMgm::addLLrefForTCAdescr('_MOD_partner','EXT:partner/csh/locallang_csh_MOD_partner.xml');


// *************************************
// *** TCA-Definitions tx_partner
// *************************************
$TCA['tx_partner_main'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_main',
		'label' => 'label',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'type',
		'default_sortby' => 'ORDER BY label',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'dividers2tabs' => $confArr['noTabDividers']?FALSE:TRUE,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_main.gif",
		'typeicon_column' => 'type',
		'typeicons' => Array (
			'0' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_main_person.gif",
			'1' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_main_organisation.gif",
		),
		'fe_cruser_id' => 'fe_user',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'type, label, status, title, first_name, last_name, street, postal_code, locality, country, po_number, po_postal_code, image, birth_date, death_date, gender, marital_status, nationality, religion, mother_tongue, preferred_language',
	),
);

$TCA['tx_partner_contact_info'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_contact_info',
		'label' => 'label',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'type',
		'default_sortby' => 'ORDER BY label',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_contact_info.gif",
		'typeicon_column' => 'type',
		'typeicons' => Array (
			'0' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_contact_info_phone.gif",
			'1' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_contact_info_mobile.gif",
			'2' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_contact_info_fax.gif",
			'3' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_contact_info_email.gif",
			'4' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_contact_info_url.gif",
		)
	),
);

$TCA['tx_partner_relationships'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_relationships',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'type' => 'type',
		'default_sortby' => 'ORDER BY uid',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_relationships.gif",
	),
);

$TCA['tx_partner_val_status'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_status',
		'label' => 'st_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_status.gif",
	),
);

$TCA['tx_partner_val_contact_permissions'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_contact_permissions',
		'label' => 'cp_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_contact_permissions.gif",
	),
);

$TCA['tx_partner_val_titles'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_titles',
		'label' => 'ti_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_titles.gif",
	),
);

$TCA['tx_partner_val_org_types'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_org_types',
		'label' => 'ot_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_org_types.gif",
	),
);

$TCA['tx_partner_val_legal_forms'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_legal_forms',
		'label' => 'lf_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_legal_forms.gif",
	),
);

$TCA['tx_partner_val_marital_status'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_marital_status',
		'label' => 'ms_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_marital_status.gif",
	),
);

$TCA['tx_partner_val_religions'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_religions',
		'label' => 'rl_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_religions.gif",
	),
);

$TCA['tx_partner_val_occupations'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_occupations',
		'label' => 'oc_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_occupations.gif",
	),
);

$TCA['tx_partner_val_hobbies'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_hobbies',
		'label' => 'hb_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_hobbies.gif",
	),
);

$TCA['tx_partner_val_courses'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_courses',
		'label' => 'cs_name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_courses.gif",
	),
);

$TCA['tx_partner_val_rel_types'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_val_rel_types',
		'label' => 'rt_descr',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_val_rel_types.gif",
	),
);

$TCA['tx_partner_reports'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:partner/locallang_db.xml:tx_partner_reports',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'default_sortby' => 'ORDER BY title',
		'dividers2tabs' => $confArr['noTabDividers']?FALSE:TRUE,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."icons/icon_tx_partner_main.gif",
	),
);
?>