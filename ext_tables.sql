#
# Table structure for table 'tx_partner_main_occupations_mm'
#
#
CREATE TABLE tx_partner_main_occupations_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_partner_main_hobbies_mm'
#
#
CREATE TABLE tx_partner_main_hobbies_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_partner_main_courses_mm'
#
#
CREATE TABLE tx_partner_main_courses_mm (
  uid_local int(11) unsigned DEFAULT '0' NOT NULL,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);



#
# Table structure for table 'tx_partner_main'
#
CREATE TABLE tx_partner_main (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	type blob NOT NULL,
	label tinytext NOT NULL,
	status int(11) unsigned DEFAULT '0' NOT NULL,
	data_source tinytext NOT NULL,
	external_id tinytext NOT NULL,
	contact_permission int(11) unsigned DEFAULT '0' NOT NULL,
	fe_user blob NOT NULL,
	image blob NOT NULL,
	remarks text NOT NULL,
	preceding_title tinytext NOT NULL,
	title int(11) unsigned DEFAULT '0' NOT NULL,
	letter_title tinytext NOT NULL,
	first_name tinytext NOT NULL,
	middle_name tinytext NOT NULL,
	last_name_prefix tinytext NOT NULL,
	last_name tinytext NOT NULL,
	maiden_name tinytext NOT NULL,
	general_suffix tinytext NOT NULL,
	initials tinytext NOT NULL,
	org_name tinytext NOT NULL,
	org_type int(11) unsigned DEFAULT '0' NOT NULL,
	org_legal_form int(11) unsigned DEFAULT '0' NOT NULL,
	department tinytext NOT NULL,
	building tinytext NOT NULL,
	floor tinytext NOT NULL,
	room tinytext NOT NULL,
	street tinytext NOT NULL,
	street_number tinytext NOT NULL,
	postal_code tinytext NOT NULL,
	locality tinytext NOT NULL,
	admin_area tinytext NOT NULL,
	country int(11) unsigned DEFAULT '0' NOT NULL,
	po_number tinytext NOT NULL,
	po_no_number tinyint(3) unsigned DEFAULT '0' NOT NULL,
	po_postal_code tinytext NOT NULL,
	po_locality tinytext NOT NULL,
	po_admin_area tinytext NOT NULL,
	po_country int(11) unsigned DEFAULT '0' NOT NULL,
	formation_date int(11) DEFAULT '0' NOT NULL,
	closure_date int(11) DEFAULT '0' NOT NULL,
	birth_date int(11) DEFAULT '0' NOT NULL,
	birth_place tinytext NOT NULL,
	death_date int(11) DEFAULT '0' NOT NULL,
	death_place tinytext NOT NULL,
	gender int(11) unsigned DEFAULT '0' NOT NULL,
	marital_status int(11) unsigned DEFAULT '0' NOT NULL,
	nationality int(11) unsigned DEFAULT '0' NOT NULL,
	religion int(11) unsigned DEFAULT '0' NOT NULL,
	mother_tongue int(11) unsigned DEFAULT '0' NOT NULL,
	preferred_language int(11) unsigned DEFAULT '0' NOT NULL,
	join_date int(11) DEFAULT '0' NOT NULL,
	leave_date int(11) DEFAULT '0' NOT NULL,
	occupations int(11) unsigned DEFAULT '0' NOT NULL,
	hobbies int(11) unsigned DEFAULT '0' NOT NULL,
	courses int(11) unsigned DEFAULT '0' NOT NULL,
	meeting_period int(11) DEFAULT '0' NOT NULL,
	meeting_unit int(11) unsigned DEFAULT '0' NOT NULL,
	meeting_start_date int(11) DEFAULT '0' NOT NULL,
	field_visibility blob NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_contact_info'
#
CREATE TABLE tx_partner_contact_info (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	uid_foreign tinytext NOT NULL,
	type int(11) unsigned DEFAULT '0' NOT NULL,
	nature int(11) unsigned DEFAULT '0' NOT NULL,
	standard tinyint(3) unsigned DEFAULT '0' NOT NULL,
	label tinytext NOT NULL,
	country int(11) unsigned DEFAULT '0' NOT NULL,
	area_code tinytext NOT NULL,
	number tinytext NOT NULL,
	extension tinytext NOT NULL,
	email tinytext NOT NULL,
	url tinytext NOT NULL,
	remarks text NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_relationships'
#
CREATE TABLE tx_partner_relationships (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	type int(11) unsigned DEFAULT '0' NOT NULL,
	uid_primary int(11) unsigned DEFAULT '0' NOT NULL,
	uid_secondary int(11) unsigned DEFAULT '0' NOT NULL,
	status int(11) unsigned DEFAULT '0' NOT NULL,
	established_date int(11) DEFAULT '0' NOT NULL,
	lapsed_date int(11) DEFAULT '0' NOT NULL,
	lapsed_reason text NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_status'
#
CREATE TABLE tx_partner_val_status (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	st_descr_short tinytext NOT NULL,
	st_descr tinytext NOT NULL,
	allowed_tables tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_contact_permissions'
#
CREATE TABLE tx_partner_val_contact_permissions (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	cp_descr_short tinytext NOT NULL,
	cp_descr tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_titles'
#
CREATE TABLE tx_partner_val_titles (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	ti_descr_short tinytext NOT NULL,
	ti_descr tinytext NOT NULL,
	ti_letter_default tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_org_types'
#
CREATE TABLE tx_partner_val_org_types (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	ot_descr_short tinytext NOT NULL,
	ot_descr tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_legal_forms'
#
CREATE TABLE tx_partner_val_legal_forms (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	lf_descr_abbr tinytext NOT NULL,
	lf_descr_short tinytext NOT NULL,
	lf_descr tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_marital_status'
#
CREATE TABLE tx_partner_val_marital_status (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	ms_descr_short tinytext NOT NULL,
	ms_descr tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_religions'
#
CREATE TABLE tx_partner_val_religions (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	rl_descr_short tinytext NOT NULL,
	rl_descr tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_occupations'
#
CREATE TABLE tx_partner_val_occupations (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	oc_descr_short tinytext NOT NULL,
	oc_descr tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_hobbies'
#
CREATE TABLE tx_partner_val_hobbies (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	hb_descr_short tinytext NOT NULL,
	hb_descr tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_courses'
#
CREATE TABLE tx_partner_val_courses (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	cs_name_short tinytext NOT NULL,
	cs_name tinytext NOT NULL,
	cs_descr text NOT NULL,
	start_date int(11) DEFAULT '0' NOT NULL,
	end_date int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_val_rel_types'
#
CREATE TABLE tx_partner_val_rel_types (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	rt_descr_short tinytext NOT NULL,
	rt_descr tinytext NOT NULL,
	allowed_categories varchar(8) DEFAULT '' NOT NULL,
	primary_title tinytext NOT NULL,
	secondary_title tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_partner_reports'
#
CREATE TABLE tx_partner_reports (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	query mediumblob NOT NULL,
	field_scope blob NOT NULL,
	contact_info_scope blob NOT NULL,
	processed_values tinyint(4) unsigned DEFAULT '0' NOT NULL,
	tech_keys tinyint(4) unsigned DEFAULT '0' NOT NULL,
	blank_values tinyint(4) unsigned DEFAULT '0' NOT NULL,
	allowed_formats blob NOT NULL,
	format_options mediumblob NOT NULL,
	field_selection mediumblob NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
