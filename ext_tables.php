<?php
//$Id$

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE=='BE')	{
	
	require_once (t3lib_extMgm::extPath($_EXTKEY).'class.tx_paymentlib_tceforms.php');

	t3lib_div::loadTCA('tt_content');
	
	$TCA['tx_paymentlib_transactions'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:paymentlib/locallang_db.php:tx_paymentlib_transactions',
			'label' => 'name',
			'crdate' => 'crdate',
			'default_sortby' => 'ORDER BY crdate',
			'dividers2tabs' => TRUE,		
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_paymentlib_transactions.gif',
		),
	);	
}
?>