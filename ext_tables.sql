#
# Table structure for table 'tx_wmdbdamsets_sets_categories_mm'
# 
#
CREATE TABLE tx_wmdbdamsets_sets_categories_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);




#
# Table structure for table 'tx_wmdbdamsets_sets_assets_mm'
# 
#
CREATE TABLE tx_wmdbdamsets_sets_assets_mm (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
	KEY uidlocal (uid_local),
	KEY uidforeign (uid_foreign)
);



#
# Table structure for table 'tx_wmdbdamsets_sets'
#
CREATE TABLE tx_wmdbdamsets_sets (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource text,
	fe_group varchar(100) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	categories int(11) DEFAULT '0' NOT NULL,
	language char(3) DEFAULT '' NOT NULL,
	assets int(11) DEFAULT '0' NOT NULL,
	orig_id int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);