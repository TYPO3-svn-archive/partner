<?php

########################################################################
# Extension Manager/Repository config file for ext: "partner"
#
# Auto generated 19-02-2007 08:16
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Partner Management',
	'description' => 'This extension includes everything you need to manage partners (persons and organisations). You can save everything from addresses to occupations, from legal forms to birth dates. Unlimited contact information records (e.g. phone numbers) can be managed for each partner. You can manage relationships between partners as well, for instance to build a hierarchy of partners. If you would like to display partner data on you website, you can use the extension partner_fe. The data model is based on the xCIL/xCRL standards from OASIS, so it is easy to exchange data with other partner management tools.',
	'category' => 'be',
	'shy' => 0,
	'version' => '0.5.8',
	'dependencies' => 'static_info_tables',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'David Bruehlmeier',
	'author_email' => 'typo3@bruehlmeier.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.8.1-0.0.0',
			'static_info_tables' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:88:{s:20:"class.ext_update.php";s:4:"66f3";s:13:"constants.txt";s:4:"a647";s:21:"ext_conf_template.txt";s:4:"440a";s:12:"ext_icon.gif";s:4:"f3e2";s:17:"ext_localconf.php";s:4:"656c";s:15:"ext_php_api.dat";s:4:"93bb";s:14:"ext_tables.php";s:4:"2404";s:14:"ext_tables.sql";s:4:"9472";s:15:"flexform_ds.xml";s:4:"840a";s:13:"locallang.php";s:4:"d67c";s:7:"tca.php";s:4:"32e7";s:37:"api/class.tx_partner_contact_info.php";s:4:"7abf";s:28:"api/class.tx_partner_div.php";s:4:"36cf";s:29:"api/class.tx_partner_lang.php";s:4:"1261";s:29:"api/class.tx_partner_main.php";s:4:"58ab";s:30:"api/class.tx_partner_query.php";s:4:"1d4c";s:37:"api/class.tx_partner_relationship.php";s:4:"b644";s:12:"cli/conf.php";s:4:"1dcc";s:21:"cli/create_report.php";s:4:"999e";s:29:"csh/locallang_csh_partner.php";s:4:"a2be";s:42:"csh/locallang_csh_partner_contact_info.php";s:4:"83bf";s:34:"csh/locallang_csh_partner_main.php";s:4:"2655";s:43:"csh/locallang_csh_partner_relationships.php";s:4:"70e4";s:53:"csh/locallang_csh_partner_val_contact_permissions.php";s:4:"511e";s:41:"csh/locallang_csh_partner_val_courses.php";s:4:"6543";s:41:"csh/locallang_csh_partner_val_hobbies.php";s:4:"11b4";s:45:"csh/locallang_csh_partner_val_legal_forms.php";s:4:"6c20";s:48:"csh/locallang_csh_partner_val_marital_status.php";s:4:"ded9";s:45:"csh/locallang_csh_partner_val_occupations.php";s:4:"be6a";s:43:"csh/locallang_csh_partner_val_org_types.php";s:4:"4392";s:43:"csh/locallang_csh_partner_val_rel_types.php";s:4:"6429";s:43:"csh/locallang_csh_partner_val_religions.php";s:4:"0703";s:40:"csh/locallang_csh_partner_val_status.php";s:4:"60bd";s:40:"csh/locallang_csh_partner_val_titles.php";s:4:"3489";s:45:"csh/img/field_visibility_default_settings.png";s:4:"88f0";s:12:"doc/TODO.txt";s:4:"7289";s:20:"doc/TODO_new_ext.txt";s:4:"bbb4";s:28:"doc/empty_partner_tables.sql";s:4:"1543";s:14:"doc/manual.sxw";s:4:"1b3f";s:25:"doc/partner_demo_data.sql";s:4:"6585";s:38:"icons/icon_tx_partner_contact_info.gif";s:4:"f0e1";s:44:"icons/icon_tx_partner_contact_info_email.gif";s:4:"a368";s:42:"icons/icon_tx_partner_contact_info_fax.gif";s:4:"2f77";s:45:"icons/icon_tx_partner_contact_info_mobile.gif";s:4:"4ab3";s:44:"icons/icon_tx_partner_contact_info_phone.gif";s:4:"4cb4";s:42:"icons/icon_tx_partner_contact_info_url.gif";s:4:"8540";s:30:"icons/icon_tx_partner_main.gif";s:4:"30fb";s:43:"icons/icon_tx_partner_main_organisation.gif";s:4:"038a";s:37:"icons/icon_tx_partner_main_person.gif";s:4:"9792";s:39:"icons/icon_tx_partner_relationships.gif";s:4:"68be";s:49:"icons/icon_tx_partner_val_contact_permissions.gif";s:4:"a9fd";s:37:"icons/icon_tx_partner_val_courses.gif";s:4:"7f55";s:37:"icons/icon_tx_partner_val_hobbies.gif";s:4:"c6d1";s:41:"icons/icon_tx_partner_val_legal_forms.gif";s:4:"4e95";s:44:"icons/icon_tx_partner_val_marital_status.gif";s:4:"1949";s:41:"icons/icon_tx_partner_val_occupations.gif";s:4:"841f";s:39:"icons/icon_tx_partner_val_org_types.gif";s:4:"8d65";s:39:"icons/icon_tx_partner_val_rel_types.gif";s:4:"1e65";s:39:"icons/icon_tx_partner_val_religions.gif";s:4:"b132";s:36:"icons/icon_tx_partner_val_status.gif";s:4:"4fac";s:36:"icons/icon_tx_partner_val_titles.gif";s:4:"d98a";s:30:"icons/icon_web_txpartnerM1.gif";s:4:"a41a";s:48:"icons/selicon_tx_partner_contact_info_type_0.gif";s:4:"5a31";s:48:"icons/selicon_tx_partner_contact_info_type_1.gif";s:4:"4736";s:48:"icons/selicon_tx_partner_contact_info_type_2.gif";s:4:"ab2c";s:48:"icons/selicon_tx_partner_contact_info_type_3.gif";s:4:"b15e";s:48:"icons/selicon_tx_partner_contact_info_type_4.gif";s:4:"3773";s:40:"icons/selicon_tx_partner_main_type_0.gif";s:4:"413a";s:40:"icons/selicon_tx_partner_main_type_1.gif";s:4:"bc7c";s:40:"inc/class.tx_partner_download_report.php";s:4:"fb91";s:31:"inc/class.tx_partner_format.php";s:4:"ff1b";s:31:"inc/class.tx_partner_select.php";s:4:"439e";s:33:"inc/class.tx_partner_tce_user.php";s:4:"88bc";s:38:"inc/class.tx_partner_tcemainprocdm.php";s:4:"75ee";s:36:"inc/class.tx_partner_user_fields.php";s:4:"1bff";s:38:"inc/class.ux_sc_mod_tools_em_index.php";s:4:"1391";s:30:"inc/class.ux_t3lib_tcemain.php";s:4:"2b02";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"ddf1";s:14:"mod1/index.php";s:4:"8dfb";s:37:"modfunc1/class.tx_partner_reports.php";s:4:"4aa6";s:50:"modfunc1/class.tx_partner_reports_birthdaylist.php";s:4:"493e";s:51:"modfunc1/class.tx_partner_reports_relationships.php";s:4:"2874";s:36:"modfunc1/class.tx_partner_search.php";s:4:"2fdf";s:35:"modfunc1/class.tx_partner_tools.php";s:4:"6fdd";s:49:"modfunc1/class.tx_partner_tools_assignfeusers.php";s:4:"92a9";s:53:"modfunc1/class.tx_partner_tools_massrelationships.php";s:4:"a921";s:50:"modfunc1/class.tx_partner_tools_reportdesigner.php";s:4:"dbf2";}',
	'suggests' => array(
	),
);

?>