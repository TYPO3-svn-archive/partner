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
 * TCA definitions (dynamic conf-file for ext_tables.php)
 * for the 'partner'-extension
 *
 * @author David Bruehlmeier <typo3@bruehlmeier.com>
 */

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Get the extension configuration
$confArr = unserialize($_EXTCONF);

$TCA['tx_partner_main'] = Array (
	'ctrl' => $TCA['tx_partner_main']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'type,label,status,contact_permission,data_source,external_id,preceding_title,title,letter_title,first_name,middle_name,last_name_prefix,last_name,maiden_name,general_suffix,initials,org_name,org_type,org_legal_form,department,building,floor,room,street,street_number,postal_code,locality,admin_area,country,po_number,po_no_number,po_postal_code,po_locality,po_admin_area,po_country,contact_info,formation_date,closure_date,birth_date,birth_place,death_date,death_place,gender,marital_status,nationality,religion,mother_tongue,preferred_language,join_date,leave_date,occupations,hobbies,courses,meeting_period,meeting_unit,meeting_start_date,image,fe_user'
	),
	'columns' => Array (
		'uid' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.uid',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 3,
			),
		),
		'pid' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.pid',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 3,
			),
		),
		'tstamp' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.tstamp',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 10,
			),
		),
		'crdate' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.crdate',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 10,
			),
		),
		'cruser_id' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.cruser_id',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 3,
			),
		),
		'deleted' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.deleted',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 1,
			),
		),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0',
				'size' => 1,
			)
		),
		'type' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.type',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:partner/locallang.php:tx_partner_main.type.I.0', '0', t3lib_extMgm::extRelPath('partner').'icons/selicon_tx_partner_main_type_0.gif'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_main.type.I.1', '1', t3lib_extMgm::extRelPath('partner').'icons/selicon_tx_partner_main_type_1.gif'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'label' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.label',
			'config' => Array (
				'type' => 'none',
				'size' => 50,
			)
		),
		'status' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.status',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_partner_select->status',
				'foreign_table' => 'tx_partner_val_status',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_status.pid=###CURRENT_PID### ' : '')
										. 'ORDER BY tx_partner_val_status.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'data_source' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.data_source',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'external_id' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.external_id',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'contact_permission' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.contact_permission',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'foreign_table' => 'tx_partner_val_contact_permissions',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_contact_permissions.pid=###CURRENT_PID### ' : '')
							.'ORDER BY tx_partner_val_contact_permissions.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'image' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.image',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => 500,
				'uploadfolder' => 'uploads/tx_partner',
				'show_thumbs' => 1,
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'relationships_overview' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.relationships_overview',
			'config' => Array (
				'type' => 'user',
				'size' => 50,
				'userFunc' => 'tx_partner_tce_user->createRelationshipsOverview',
			)
		),
		'preceding_title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.preceding_title',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.title',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'foreign_table' => 'tx_partner_val_titles',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_titles.pid=###CURRENT_PID### ' : '' )
							.'ORDER BY tx_partner_val_titles.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'letter_title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.letter_title',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
			)
		),
		'first_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.first_name',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
			)
		),
		'middle_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.middle_name',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'last_name_prefix' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.last_name_prefix',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '24',
			)
		),
		'last_name' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.last_name',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
				'eval' => 'required',
			)
		),
		'maiden_name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.maiden_name',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'general_suffix' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.general_suffix',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '24',
			)
		),
		'initials' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.initials',
			'config' => Array (
				'type' => 'input',
				'size' => '5',
				'max' => '24',
			)
		),
		'org_name' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.org_name',
			'config' => Array (
				'eval' => 'required',
				'type' => 'input',
				'size' => '48',
				'max' => '48',
			)
		),
		'org_type' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.org_type',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'foreign_table' => 'tx_partner_val_org_types',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_org_types.pid=###CURRENT_PID### ' : '')
							.'ORDER BY tx_partner_val_org_types.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'org_legal_form' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.org_legal_form',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'foreign_table' => 'tx_partner_val_legal_forms',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_legal_forms.pid=###CURRENT_PID### ' : '')
							.'ORDER BY tx_partner_val_legal_forms.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'department' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.department',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'building' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.building',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'floor' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.floor',
			'config' => Array (
				'type' => 'input',
				'size' => '3',
				'max' => '48',
			)
		),
		'room' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.room',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'street' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.street',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
			)
		),
		'street_number' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.street_number',
			'config' => Array (
				'type' => 'input',
				'size' => '3',
				'max' => '12',
			)
		),
		'postal_code' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.postal_code',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
			)
		),
		'locality' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.locality',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
			)
		),
		'admin_area' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.admin_area',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48'
			)
		),
		'country' => Array (
			'exclude' => 1,
			'displayCond' => 'EXT:static_info_tables:LOADED:true',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.country',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array (
					'table' => 'static_countries',
					'indexField' => 'uid',
					'prependHotlist' => 1,
					'hotlistLimit' => 5,
					'hotlistApp' => 'tx_partner',
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'po_number' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.po_number',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
			)
		),
		'po_no_number' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.po_no_number',
			'config' => Array (
				'type' => 'check',
				'size' => 1,
			)
		),
		'po_postal_code' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.po_postal_code',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
			)
		),
		'po_locality' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.po_locality',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'po_admin_area' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.po_admin_area',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '48',
			)
		),
		'po_country' => Array (
			'exclude' => 1,
			'displayCond' => 'EXT:static_info_tables:LOADED:true',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.po_country',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array (
					'table' => 'static_countries',
					'indexField' => 'uid',
					'prependHotlist' => 1,
					'hotlistLimit' => 5,
					'hotlistApp' => 'tx_partner',
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'contact_info' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.contact_info',
			'config' => Array (
				'type' => 'user',
				'size' => '50',
				'userFunc' => 'tx_partner_tce_user->createContactsInformationOverview',
			)
		),
		'formation_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.formation_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'closure_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.closure_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'birth_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.birth_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '8',
				'eval' => 'int',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'birth_place' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.birth_place',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
			)
		),
		'death_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.death_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '8',
				'eval' => 'int',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'death_place' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.death_place',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
			)
		),
		'gender' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.gender',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', '9'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_main.gender.I.0', '0'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_main.gender.I.1', '1'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_main.gender.I.2', '2'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'marital_status' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.marital_status',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'foreign_table' => 'tx_partner_val_marital_status',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_marital_status.pid=###CURRENT_PID### ' : '' )
							.'ORDER BY tx_partner_val_marital_status.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'nationality' => Array (
			'exclude' => 1,
			'displayCond' => 'EXT:static_info_tables:LOADED:true',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.nationality',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array (
					'table' => 'static_countries',
					'indexField' => 'uid',
					'prependHotlist' => 1,
					'hotlistLimit' => 5,
					'hotlistApp' => 'tx_partner',
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'religion' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.religion',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'foreign_table' => 'tx_partner_val_religions',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_religions.pid=###CURRENT_PID### ' : '' )
							.'ORDER BY tx_partner_val_religions.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'mother_tongue' => Array (
			'exclude' => 1,
			'displayCond' => 'EXT:static_info_tables:LOADED:true',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.mother_tongue',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array (
					'table' => 'static_languages',
					'indexField' => 'uid',
					'prependHotlist' => 1,
					'hotlistLimit' => 5,
					'hotlistApp' => 'tx_partner',
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'preferred_language' => Array (
			'exclude' => 1,
			'displayCond' => 'EXT:static_info_tables:LOADED:true',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.preferred_language',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array (
					'table' => 'static_languages',
					'indexField' => 'uid',
					'prependHotlist' => 1,
					'hotlistLimit' => 5,
					'hotlistApp' => 'tx_partner',
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'join_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.join_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'leave_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.leave_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'occupations' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.occupations',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_partner_val_occupations',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_occupations.pid=###CURRENT_PID### ' : '' )
							. 'ORDER BY tx_partner_val_occupations.sorting',
				'size' => 5,
				'autoSizeMax' => 20,
				'selectedListStyle' => 'width:220px',
				'itemListStyle' => 'width:180px',
				'minitems' => 0,
				'maxitems' => 99,
				'renderMode' => 'checkbox',
				'MM' => 'tx_partner_main_occupations_mm',
			)
		),
		'hobbies' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.hobbies',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_partner_val_hobbies',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_hobbies.pid=###CURRENT_PID### ' : '' )
							. 'ORDER BY tx_partner_val_hobbies.sorting',
				'size' => 5,
				'autoSizeMax' => 20,
				'selectedListStyle' => 'width:220px',
				'itemListStyle' => 'width:180px',
				'minitems' => 0,
				'maxitems' => 99,
				'renderMode' => 'checkbox',
				'MM' => 'tx_partner_main_hobbies_mm',
			)
		),
		'courses' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.courses',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_partner_val_courses',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_courses.pid=###CURRENT_PID### ': '' )
						. 'ORDER BY tx_partner_val_courses.sorting',
				'size' => 5,
				'autoSizeMax' => 20,
				'selectedListStyle' => 'width:220px',
				'itemListStyle' => 'width:180px',
				'minitems' => 0,
				'maxitems' => 99,
				'renderMode' => 'checkbox',
				'MM' => 'tx_partner_main_courses_mm',
			)
		),
		'meeting_period' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.meeting_period',
			'config' => Array (
				'type' => 'input',
				'size' => '3',
				'max' => '3',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array (
					'upper' => '999',
					'lower' => '1'
				),
				'default' => 0
			)
		),
		'meeting_unit' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.meeting_unit',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', ''),
					Array('LLL:EXT:partner/locallang.php:tx_partner_main.meeting_unit.I.0', '0'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_main.meeting_unit.I.1', '1'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_main.meeting_unit.I.2', '2'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_main.meeting_unit.I.3', '3'),
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'meeting_start_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.meeting_start_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'field_visibility' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.field_visibility',
			'config' => Array (
				'type' => 'user',
				'size' => '50',
				'userFunc' => 'tx_partner_tce_user->fieldVisibility',
			)
		),
		'remarks' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.remarks',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'size' => '100',
			)
		),
		'fe_user' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.fe_user',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'wizards' => Array(
					'_VALIGN' => 'top',
					'add' => Array(
						'type' => 'script',
						'title' => 'LLL:EXT:partner/locallang.php:tx_partner_main.fe_user.add',
						'icon' => 'add.gif',
						'params' => Array(
							'table'=>'fe_users',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'set'
						),
						'script' => 'wizard_add.php',
					),
					'edit' => Array(
						'type' => 'popup',
						'title' => 'LLL:EXT:partner/locallang.php:tx_partner_main.fe_user.edit',
						'script' => 'wizard_edit.php',
						'popup_onlyOpenIfSelected' => 1,
						'icon' => 'edit2.gif',
						'JSopenParams' => 'height=450,width=580,status=0,menubar=0,scrollbars=1',
					)
				)
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'type, label, status;;1, title;;2;;1-1-1, first_name;;4, last_name;;3, street;;5;;1-1-1, postal_code, locality;;10, country, po_number;;6;;1-1-1, po_postal_code;;7, image;;;;1-1-1, remarks, --div--;LLL:EXT:partner/locallang.php:tx_partner.label.contact_info_relationships, contact_info;;;;1-1-1, relationships_overview;;;;1-1-1, --div--;LLL:EXT:partner/locallang.php:tx_partner.label.dates, birth_date;;8;;1-1-1, death_date;;9, gender;;;;1-1-1, marital_status, nationality, religion, mother_tongue;;;;1-1-1, preferred_language, join_date;;;;1-1-1, leave_date, --div--;LLL:EXT:partner/locallang.php:tx_partner.label.activities, occupations;;;;1-1-1, hobbies, courses, --div--;LLL:EXT:partner/locallang.php:tx_partner.label.frontend, field_visibility;;;;1-1-1, fe_user'),
		'1' => Array('showitem' => 'type, label, status;;1, org_name;;;;1-1-1, org_type, org_legal_form, street;;5;;1-1-1, postal_code, locality;;10, country, po_number;;6;;1-1-1, po_postal_code;;7, image;;;;1-1-1, remarks, --div--;LLL:EXT:partner/locallang.php:tx_partner.label.contact_info_relationships, contact_info;;;;1-1-1, relationships_overview, --div--;LLL:EXT:partner/locallang.php:tx_partner.label.dates, formation_date;;;;1-1-1, closure_date, meeting_period;;;;1-1-1, meeting_unit, meeting_start_date')
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'contact_permission, data_source, external_id'),
		'2' => Array('showitem' => 'preceding_title, letter_title'),
		'3' => Array('showitem' => 'last_name_prefix, maiden_name, general_suffix, initials'),
		'4' => Array('showitem' => 'middle_name'),
		'5' => Array('showitem' => 'street_number, department, building, floor, room'),
		'6' => Array('showitem' => 'po_no_number'),
		'7' => Array('showitem' => 'po_locality, po_admin_area, po_country'),
		'8' => Array('showitem' => 'birth_place'),
		'9' => Array('showitem' => 'death_place'),
		'10' => Array('showitem' => 'admin_area'),
	)
);



$TCA['tx_partner_contact_info'] = Array (
	'ctrl' => $TCA['tx_partner_contact_info']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'type,label,nature,standard,country,area_code,number,extension,email,url,remarks,uid_foreign'
	),
	'columns' => Array (
		'uid' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.uid',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 3,
			),
		),
		'pid' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.pid',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 3,
			),
		),
		'tstamp' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.tstamp',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 10,
			),
		),
		'crdate' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.crdate',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 10,
			),
		),
		'cruser_id' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.cruser_id',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 10,
			),
		),
		'deleted' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.deleted',
			'config' => Array (
				'type' => 'passthrough',
				'size' => 1,
			),
		),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0',
				'size' => '1',
			)
		),
		'uid_foreign' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.uid_foreign',
			'config' => Array (
				'type' => 'passthrough',
				'size' => '3',
			)
		),
		'type' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.type',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:partner/locallang.php:tx_partner_contact_info.type.I.0', '0', t3lib_extMgm::extRelPath('partner').'icons/selicon_tx_partner_contact_info_type_0.gif'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_contact_info.type.I.1', '1', t3lib_extMgm::extRelPath('partner').'icons/selicon_tx_partner_contact_info_type_1.gif'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_contact_info.type.I.2', '2', t3lib_extMgm::extRelPath('partner').'icons/selicon_tx_partner_contact_info_type_2.gif'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_contact_info.type.I.3', '3', t3lib_extMgm::extRelPath('partner').'icons/selicon_tx_partner_contact_info_type_3.gif'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_contact_info.type.I.4', '4', t3lib_extMgm::extRelPath('partner').'icons/selicon_tx_partner_contact_info_type_4.gif'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'nature' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.nature',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:partner/locallang.php:tx_partner_contact_info.nature.I.0', '0'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_contact_info.nature.I.1', '1'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'standard' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.standard',
			'config' => Array (
				'type' => 'check',
				'size' => 1,
				'default' => 1,
			)
		),
		'label' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.label',
			'config' => Array (
				'type' => 'none',
				'size' => 50,
			)
		),
		'country' => Array (
			'exclude' => 1,
			'displayCond' => 'EXT:static_info_tables:LOADED:true',
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.country',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('',0),
				),
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array (
					'table' => 'static_countries',
					'indexField' => 'uid',
					'prependHotlist' => 1,
					'hotlistLimit' => 5,
					'hotlistApp' => 'tx_partner',
				),
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'area_code' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.area_code',
			'config' => Array (
				'type' => 'input',
				'size' => '24',
				'max' => '24',
			)
		),
		'number' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.number',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
				'eval' => 'required',
			)
		),
		'extension' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.extension',
			'config' => Array (
				'type' => 'input',
				'size' => '24',
				'max' => '24',
			)
		),
		'email' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.email',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '255',
				'checkbox' => '',
				'eval' => 'trim',
			)
		),
		'url' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.url',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '255',
				'checkbox' => '',
				'eval' => 'trim',
			)
		),
		'remarks' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_contact_info.remarks',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'size' => '100',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'type, label, nature, standard, country;;;;1-1-1, area_code, number, extension, remarks'),
		'1' => Array('showitem' => 'type, label, nature, standard, country;;;;1-1-1, area_code, number, remarks'),
		'2' => Array('showitem' => 'type, label, nature, standard, country;;;;1-1-1, area_code, number, extension, remarks'),
		'3' => Array('showitem' => 'type, label, nature, standard, email;;;;1-1-1, remarks'),
		'4' => Array('showitem' => 'type, label, nature, standard, url;;;;1-1-1, remarks'),
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_relationships'] = Array (
	'ctrl' => $TCA['tx_partner_relationships']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'type,status,uid_primary,uid_secondary,established_date,lapsed_date,lapsed_reason'
	),
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'type' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_relationships.type',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_partner_select->types',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'uid_primary' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_relationships.uid_primary',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_partner_main',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'selectedListStyle' => 'width:270px',
			)
		),
		'uid_secondary' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_relationships.uid_secondary',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_partner_main',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'selectedListStyle' => 'width:270px',
			)
		),
		'status' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_main.status',
			'config' => Array (
				'type' => 'select',
				'itemsProcFunc' => 'tx_partner_select->status',
				'foreign_table' => 'tx_partner_val_status',
				'foreign_table_where' => ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'AND tx_partner_val_status.pid=###CURRENT_PID### ' : '' )
							. 'ORDER BY tx_partner_val_status.sorting',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'established_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_relationships.established_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'lapsed_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_relationships.lapsed_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'lapsed_reason' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_relationships.lapsed_reason',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'type, status, uid_primary;;;;1-1-1, uid_secondary, established_date;;;;1-1-1, lapsed_date, lapsed_reason'),
		'1' => Array('showitem' => 'type, status, uid_primary;;;;1-1-1, uid_secondary, established_date;;;;1-1-1, lapsed_date, lapsed_reason'),
		'2' => Array('showitem' => 'type, status, uid_primary;;;;1-1-1, uid_secondary, established_date;;;;1-1-1, lapsed_date, lapsed_reason'),
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_status'] = Array (
	'ctrl' => $TCA['tx_partner_val_status']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'st_descr_short,st_descr,allowed_tables'
	),
	'columns' => Array (
		'st_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_status.st_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'st_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_status.st_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
		'allowed_tables' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_status.allowed_tables',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:partner/locallang.php:tx_partner_val_status.allowed_tables.I.0', 'tx_partner_main'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_val_status.allowed_tables.I.1', 'tx_partner_relationships'),
				),
				'size' => 2,
				'maxitems' => 2,
				'renderMode' => 'checkbox',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'st_descr_short;;;;1-1-1, st_descr, allowed_tables')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_contact_permissions'] = Array (
	'ctrl' => $TCA['tx_partner_val_contact_permissions']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'cp_descr_short,cp_descr'
	),
	'columns' => Array (
		'cp_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_contact_permissions.cp_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'cp_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_contact_permissions.cp_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'cp_descr_short;;;;1-1-1, cp_descr')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_titles'] = Array (
	'ctrl' => $TCA['tx_partner_val_titles']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'ti_descr_short,ti_descr,ti_letter_default'
	),
	'columns' => Array (
		'ti_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_titles.ti_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'ti_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_titles.ti_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
		'ti_letter_default' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_titles.ti_letter_default',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'ti_descr_short;;;;1-1-1, ti_descr, ti_letter_default')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_org_types'] = Array (
	'ctrl' => $TCA['tx_partner_val_org_types']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'ot_descr_short,ot_descr'
	),
	'columns' => Array (
		'ot_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_org_types.ot_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'ot_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_org_types.ot_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'ot_descr_short;;;;1-1-1, ot_descr')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_legal_forms'] = Array (
	'ctrl' => $TCA['tx_partner_val_legal_forms']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'lf_descr_abbr,lf_descr_short,lf_descr'
	),
	'columns' => Array (
		'lf_descr_abbr' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_legal_forms.lf_descr_abbr',
			'config' => Array (
				'type' => 'input',
				'size' => '5',
				'max' => '5',
			)
		),
		'lf_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_legal_forms.lf_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'lf_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_legal_forms.lf_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'lf_descr_abbr;;;;1-1-1, lf_descr_short, lf_descr')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_marital_status'] = Array (
	'ctrl' => $TCA['tx_partner_val_marital_status']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'ms_descr_short,ms_descr'
	),
	'columns' => Array (
		'ms_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_marital_status.ms_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'ms_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_marital_status.ms_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'ms_descr_short;;;;1-1-1, ms_descr')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_religions'] = Array (
	'ctrl' => $TCA['tx_partner_val_religions']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'rl_descr_short,rl_descr'
	),
	'columns' => Array (
		'rl_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_religions.rl_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'rl_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_religions.rl_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'rl_descr_short;;;;1-1-1, rl_descr')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_occupations'] = Array (
	'ctrl' => $TCA['tx_partner_val_occupations']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'oc_descr_short,oc_descr'
	),
	'columns' => Array (
		'oc_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_occupations.oc_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'oc_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_occupations.oc_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'oc_descr_short;;;;1-1-1, oc_descr')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_hobbies'] = Array (
	'ctrl' => $TCA['tx_partner_val_hobbies']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hb_descr_short,hb_descr'
	),
	'columns' => Array (
		'hb_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_hobbies.hb_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'hb_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_hobbies.hb_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hb_descr_short;;;;1-1-1, hb_descr')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);



$TCA['tx_partner_val_courses'] = Array (
	'ctrl' => $TCA['tx_partner_val_courses']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'cs_name_short,cs_name,cs_descr,start_date,end_date'
	),
	'columns' => Array (
		'cs_name_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_courses.cs_name_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'cs_name' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_courses.cs_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
		'cs_descr' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_courses.cs_descr',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'start_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_courses.start_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'end_date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_courses.end_date',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'cs_name_short;;;;1-1-1, cs_name, cs_descr, start_date, end_date')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

$TCA['tx_partner_val_rel_types'] = Array (
	'ctrl' => $TCA['tx_partner_val_rel_types']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'rt_descr_short, rt_descr, allowed_categories, primary_title, secondary_title'
	),
	'columns' => Array (
		'rt_descr_short' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_rel_types.rt_descr_short',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '12',
				'eval' => 'required',
			)
		),
		'rt_descr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_rel_types.rt_descr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
		'allowed_categories' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_rel_types.allowed_categories',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:partner/locallang.php:tx_partner_val_rel_types.allowed_categories.I.0', '0'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_val_rel_types.allowed_categories.I.1', '1'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_val_rel_types.allowed_categories.I.2', '2'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_val_rel_types.allowed_categories.I.3', '3'),
				),
				'size' => 4,
				'maxitems' => 4,
				'renderMode' => 'checkbox',
			)
		),
		'primary_title' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_rel_types.primary_title',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
				'eval' => 'required',
			)
		),
		'secondary_title' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_val_rel_types.secondary_title',
			'config' => Array (
				'type' => 'input',
				'size' => '48',
				'max' => '48',
				'eval' => 'required',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'rt_descr_short, rt_descr, allowed_categories, primary_title, secondary_title')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

$TCA['tx_partner_reports'] = Array (
	'ctrl' => $TCA['tx_partner_reports']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'title,query,contact_info_scope,processed_values,tech_keys,blank_values,csv_delimiter,csv_wrap,csv_line_end,pdf_options,field_selection,preview'
	),
	'columns' => Array (
		'title' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '30',
				'eval' => 'required',
			)
		),
		'query' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.query',
			'config' => Array (
				'type' => 'text',
				'cols' => '48',
				'rows' => '5',
				'wrap' => 'virtual',
			)
		),
		'field_scope' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.field_scope',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:partner/locallang.php:tx_partner_reports.field_scope.I.all', 'all'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_reports.field_scope.I.field_selection', 'field_selection'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'contact_info_scope' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.contact_info_scope',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:partner/locallang.php:tx_partner_reports.contact_info_scope.I.none', '0'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_reports.contact_info_scope.I.only_std', '1'),
					Array('LLL:EXT:partner/locallang.php:tx_partner_reports.contact_info_scope.I.all', '2'),
				),
				'size' => 1,
				'maxitems' => 1,
			)
		),
		'processed_values' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.processed_values',
			'config' => Array (
				'type' => 'check',
			)
		),
		'tech_keys' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.tech_keys',
			'config' => Array (
				'type' => 'check',
			)
		),
		'blank_values' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.blank_values',
			'config' => Array (
				'type' => 'check',
			)
		),
		'allowed_formats' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.allowed_formats',
			'config' => Array (
				'type' => 'select',
					// Attention! If the order within the items-array changes, don't forget to change
					// the unset statement in the itemsProcFunc!
				'items' => Array (
				),
				'itemsProcFunc' => 'tx_partner_select->allowed_formats',
				'size' => 3,
				'minitems' => 0,
				'maxitems' => 3,
				'renderMode' => 'checkbox',
			)
		),
		'format_options' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.format_options',
			'config' => Array (
				'type' => 'flex',
				'ds_pointerField' => 'default',
				'ds' => array(
					'default' => 'FILE:EXT:partner/flexform_ds.xml',
				)
			)
		),
		'field_selection' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.field_selection',
			'config' => Array (
				'type' => 'user',
				'userFunc' => 'tx_partner_tce_user->getReportFieldSelection',
			)
		),
		'preview' => Array (
			'label' => 'LLL:EXT:partner/locallang.php:tx_partner_reports.preview',
			'config' => Array (
				'type' => 'user',
				'userFunc' => 'tx_partner_tce_user->getReportPreview',
			),
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'title,query,field_scope,contact_info_scope,processed_values,tech_keys,blank_values,csv_delimiter,csv_wrap,csv_line_end,pdf_options,--div--;LLL:EXT:partner/locallang.php:tx_partner.label.field_selection,field_selection,--div--;LLL:EXT:partner/locallang.php:tx_partner.label.formats,allowed_formats,format_options,--div--;LLL:EXT:partner/locallang.php:tx_partner.label.preview,preview')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);
?>