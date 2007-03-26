<?php

########################################################################
# Extension Manager/Repository config file for ext: "paymentlib"
#
# Auto generated 19-03-2007 19:32
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Payment code library',
	'description' => 'This code library acts as an abstraction layer for payment methods in general. Have a look at this extension if you need any payment functionality in your TYPO3 project.',
	'category' => 'misc',
	'shy' => 0,
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'internal' => 0,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Tonni Aagesen',
	'author_email' => 't3dev@support.pil.dk',
	'author_company' => 'pil.dk',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '0.2.0',
	'_md5_values_when_last_written' => 'a:23:{s:9:"Changelog";s:4:"e8d6";s:10:"README.txt";s:4:"3e0b";s:8:"TODO.txt";s:4:"c95f";s:32:"class.tx_paymentlib_tceforms.php";s:4:"7666";s:21:"ext_conf_template.txt";s:4:"7494";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"b1d9";s:14:"ext_tables.sql";s:4:"0b4a";s:13:"locallang.php";s:4:"b58a";s:16:"locallang_db.php";s:4:"24c3";s:7:"tca.php";s:4:"d851";s:36:"lib/class.tx_paymentlib_provider.php";s:4:"c899";s:43:"lib/class.tx_paymentlib_providerfactory.php";s:4:"f117";s:41:"lib/class.tx_paymentlib_providerproxy.php";s:4:"ee0c";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"b339";s:14:"mod1/index.php";s:4:"4d2e";s:18:"mod1/locallang.php";s:4:"563c";s:22:"mod1/locallang_mod.php";s:4:"ce2c";s:19:"mod1/moduleicon.gif";s:4:"8074";s:48:"tests/tx_paymentlib_providerfactory_testcase.php";s:4:"b94b";s:46:"tests/tx_paymentlib_providerproxy_testcase.php";s:4:"66d2";s:49:"tests/fixtures/tx_paymentlib_provider_fixture.php";s:4:"76b5";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '5.0.1-0.0.0',
			'typo3' => '3.8.1-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>