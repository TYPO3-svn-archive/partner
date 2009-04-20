<?php

########################################################################
# Extension Manager/Repository config file for ext: "partner"
#
# Auto generated 20-04-2009 08:54
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
	'version' => '0.5.20',
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
	'_md5_values_when_last_written' => 'a:90:{s:20:"class.ext_update.php";s:4:"c4fe";s:13:"constants.txt";s:4:"a647";s:21:"ext_conf_template.txt";s:4:"440a";s:12:"ext_icon.gif";s:4:"f3e2";s:17:"ext_localconf.php";s:4:"14f1";s:15:"ext_php_api.dat";s:4:"93bb";s:14:"ext_tables.php";s:4:"d362";s:14:"ext_tables.sql";s:4:"4c0d";s:15:"flexform_ds.xml";s:4:"fb45";s:16:"locallang_db.xml";s:4:"bf74";s:7:"tca.php";s:4:"5d42";s:37:"api/class.tx_partner_contact_info.php";s:4:"7abf";s:28:"api/class.tx_partner_div.php";s:4:"4d98";s:29:"api/class.tx_partner_lang.php";s:4:"3ff0";s:29:"api/class.tx_partner_main.php";s:4:"0fa9";s:30:"api/class.tx_partner_query.php";s:4:"321c";s:37:"api/class.tx_partner_relationship.php";s:4:"b644";s:12:"cli/conf.php";s:4:"1dcc";s:21:"cli/create_report.php";s:4:"999e";s:33:"csh/locallang_csh_MOD_partner.xml";s:4:"26ff";s:45:"csh/locallang_csh_tx_partner_contact_info.xml";s:4:"1de9";s:37:"csh/locallang_csh_tx_partner_main.xml";s:4:"5d15";s:46:"csh/locallang_csh_tx_partner_relationships.xml";s:4:"528a";s:56:"csh/locallang_csh_tx_partner_val_contact_permissions.xml";s:4:"9adf";s:44:"csh/locallang_csh_tx_partner_val_courses.xml";s:4:"b8fc";s:44:"csh/locallang_csh_tx_partner_val_hobbies.xml";s:4:"cfd2";s:48:"csh/locallang_csh_tx_partner_val_legal_forms.xml";s:4:"bb32";s:51:"csh/locallang_csh_tx_partner_val_marital_status.xml";s:4:"aaa4";s:48:"csh/locallang_csh_tx_partner_val_occupations.xml";s:4:"0c63";s:46:"csh/locallang_csh_tx_partner_val_org_types.xml";s:4:"17ea";s:46:"csh/locallang_csh_tx_partner_val_rel_types.xml";s:4:"160f";s:46:"csh/locallang_csh_tx_partner_val_religions.xml";s:4:"f805";s:43:"csh/locallang_csh_tx_partner_val_status.xml";s:4:"cfba";s:43:"csh/locallang_csh_tx_partner_val_titles.xml";s:4:"19c5";s:45:"csh/img/field_visibility_default_settings.png";s:4:"88f0";s:12:"doc/TODO.txt";s:4:"c555";s:20:"doc/TODO_new_ext.txt";s:4:"bbb4";s:28:"doc/empty_partner_tables.sql";s:4:"cab1";s:14:"doc/manual.sxw";s:4:"afb3";s:25:"doc/partner_demo_data.sql";s:4:"ed2f";s:38:"icons/icon_tx_partner_contact_info.gif";s:4:"f0e1";s:44:"icons/icon_tx_partner_contact_info_email.gif";s:4:"a368";s:42:"icons/icon_tx_partner_contact_info_fax.gif";s:4:"2f77";s:45:"icons/icon_tx_partner_contact_info_mobile.gif";s:4:"4ab3";s:44:"icons/icon_tx_partner_contact_info_phone.gif";s:4:"4cb4";s:42:"icons/icon_tx_partner_contact_info_url.gif";s:4:"8540";s:30:"icons/icon_tx_partner_main.gif";s:4:"30fb";s:43:"icons/icon_tx_partner_main_organisation.gif";s:4:"038a";s:37:"icons/icon_tx_partner_main_person.gif";s:4:"9792";s:39:"icons/icon_tx_partner_relationships.gif";s:4:"68be";s:49:"icons/icon_tx_partner_val_contact_permissions.gif";s:4:"a9fd";s:37:"icons/icon_tx_partner_val_courses.gif";s:4:"7f55";s:37:"icons/icon_tx_partner_val_hobbies.gif";s:4:"c6d1";s:41:"icons/icon_tx_partner_val_legal_forms.gif";s:4:"4e95";s:44:"icons/icon_tx_partner_val_marital_status.gif";s:4:"1949";s:41:"icons/icon_tx_partner_val_occupations.gif";s:4:"841f";s:39:"icons/icon_tx_partner_val_org_types.gif";s:4:"8d65";s:39:"icons/icon_tx_partner_val_rel_types.gif";s:4:"1e65";s:39:"icons/icon_tx_partner_val_religions.gif";s:4:"b132";s:36:"icons/icon_tx_partner_val_status.gif";s:4:"4fac";s:36:"icons/icon_tx_partner_val_titles.gif";s:4:"d98a";s:30:"icons/icon_web_txpartnerM1.gif";s:4:"a41a";s:48:"icons/selicon_tx_partner_contact_info_type_0.gif";s:4:"5a31";s:48:"icons/selicon_tx_partner_contact_info_type_1.gif";s:4:"4736";s:48:"icons/selicon_tx_partner_contact_info_type_2.gif";s:4:"ab2c";s:48:"icons/selicon_tx_partner_contact_info_type_3.gif";s:4:"b15e";s:48:"icons/selicon_tx_partner_contact_info_type_4.gif";s:4:"3773";s:36:"icons/selicon_tx_partner_default.gif";s:4:"8ed2";s:40:"icons/selicon_tx_partner_main_type_0.gif";s:4:"413a";s:40:"icons/selicon_tx_partner_main_type_1.gif";s:4:"bc7c";s:40:"inc/class.tx_partner_download_report.php";s:4:"cb5c";s:31:"inc/class.tx_partner_format.php";s:4:"f901";s:31:"inc/class.tx_partner_select.php";s:4:"6777";s:33:"inc/class.tx_partner_tce_user.php";s:4:"d032";s:38:"inc/class.tx_partner_tcemainprocdm.php";s:4:"75ee";s:36:"inc/class.tx_partner_user_fields.php";s:4:"1bff";s:38:"inc/class.ux_sc_mod_tools_em_index.php";s:4:"1391";s:30:"inc/class.ux_t3lib_tcemain.php";s:4:"2b02";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"fdc6";s:14:"mod1/index.php";s:4:"9437";s:37:"modfunc1/class.tx_partner_reports.php";s:4:"a031";s:50:"modfunc1/class.tx_partner_reports_birthdaylist.php";s:4:"4b4d";s:43:"modfunc1/class.tx_partner_reports_email.php";s:4:"fdf8";s:51:"modfunc1/class.tx_partner_reports_relationships.php";s:4:"661a";s:36:"modfunc1/class.tx_partner_search.php";s:4:"2f17";s:35:"modfunc1/class.tx_partner_tools.php";s:4:"6fdd";s:49:"modfunc1/class.tx_partner_tools_assignfeusers.php";s:4:"66e3";s:53:"modfunc1/class.tx_partner_tools_massrelationships.php";s:4:"2c39";s:50:"modfunc1/class.tx_partner_tools_reportdesigner.php";s:4:"c18e";}',
	'suggests' => array(
	),
);

?>