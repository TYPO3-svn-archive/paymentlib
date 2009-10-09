<?php
// $Id: tca.php 23190 2009-08-08 17:47:48Z franzholz $

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_paymentlib_transactions'] = Array (
	'ctrl' => $TCA['tx_paymentlib_transactions']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'uid'
	),
	'columns' => Array (
		'uid' => Array (		
			'label' => 'LLL:EXT:paymentlib/locallang_db.php:tx_paymentlib_transactions_uid',
			'config' => Array (
				'type' => 'none',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => '')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);

?>