<?php
/***************************************************************
* $Id$
*
*  Copyright notice
*
*  (c) 2005 Robert Lemke (robert@typo3.org), Tonni Aagesen (t3dev@support.pil.dk)
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
 * @author	Robert Lemke <robert@typo3.org>
 */

require_once (t3lib_extMgm::extPath ('paymentlib').'lib/class.tx_paymentlib_providerfactory.php');

class tx_paymentlib_tceforms {

	function itemsProcFunc_paymentMethods (&$params, &$pObj)	{
		global $LANG;

			// Create an instance of the provider factory and traverse all available providers:
		$providerFactoryObj = tx_paymentlib_providerfactory::getInstance();
		$providerObjectsArr = $providerFactoryObj->getProviderObjects();		

		if (is_array ($providerObjectsArr)) {
			foreach ($providerObjectsArr as $providerObj) {
					// Get available payment methods for this provider and add it to the selection box:
				$paymentMethodsArr = $providerObj->getAvailablePaymentMethods();
				if (is_array ($paymentMethodsArr)) {
					foreach ($paymentMethodsArr as $methodKey => $methodConf) {
						$params['items'][] = array (
							htmlspecialchars($providerObj->gatewayName . ': ' . $LANG->sl($methodConf['label'])), 
							$methodKey,
							$methodConf['iconpath'],
						);
					}
				}		
			}
		}
	}

	function itemsProcFunc_paymentDetails ($PA, $fobj)	{
		global $LANG;

		$providerFactoryObj = tx_paymentlib_providerfactory::getInstance();
		$trx = $providerFactoryObj->getTransactionByUid(intval($PA['row'][$PA['field']]));
		
		if (!is_array($trx)) {
		    return '<p>No payment details available</p>';
		}
		
		$commonCols = array('uid', 'gatewayid', 'reference', 'amount', 'currency', 'state', 'message', 'paymethod_key');
		$tmp = array();
		$out = array();
		$pad_len = 16;
				
		foreach ($commonCols as $col) {
		        $tmp['common'][$col] = empty($trx[$col]) ? 'N/A' : $trx[$col];
		        $pad_len = strlen($col) > $pad_len ? strlen($col) : $pad_len;		    
		}

		foreach ($trx['user'] as $name => $details) {
		    if (is_array($details)) {
		        foreach ($details as $k => $v) {
		            $tmp[$name][$k] = empty($v) ? 'N/A' : $v;
		            $pad_len = strlen($k) > $pad_len ? strlen($k) : $pad_len;
		        }
		    }
		}
		
		foreach ($tmp as $header => $details) {
		    $out[] = '<h3>' . ucfirst($header) . '</h3>';
		    foreach ($details as $k => $v) {
		        $out[] = '<div style="padding: 0 5px 5px 15px; font-family: monospace, curier">' . str_pad($k, ($pad_len + 3), '.', STR_PAD_RIGHT) . ': ' . $v . '</div>';
		    }
		}

		return '<div class="formField1" style="border: 0px solid #000000;">' . implode('', $out) . '</div>'; 
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/paymentlib/class.tx_paymentlib_tceforms.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/paymentlib/class.tx_paymentlib_tceforms.php']);
}

?>