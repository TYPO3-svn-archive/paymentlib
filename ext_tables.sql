#
# $Id$
# Table structure for table 'tx_paymentlib_transactions'
#
CREATE TABLE tx_paymentlib_transactions (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	type int(11) unsigned DEFAULT '0' NOT NULL,	
	extkey varchar(100) DEFAULT '' NOT NULL,
	extreference text NOT NULL,
	status varchar(20) DEFAULT '' NOT NULL,
	amount varchar(10) DEFAULT '' NOT NULL,
	currency varchar(10) DEFAULT '' NOT NULL,
	invoicetext varchar(30) DEFAULT '' NOT NULL,
	remotebookingnr varchar(50) DEFAULT '' NOT NULL,
	remoteauthcode varchar(50) DEFAULT '' NOT NULL,
	remotetimestamp int(11) unsigned DEFAULT '0' NOT NULL,
	remoteerrorcode varchar(20) DEFAULT '' NOT NULL,
	remotemessages text NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

