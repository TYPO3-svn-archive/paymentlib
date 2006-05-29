<?php
/***************************************************************
* $Id$
*
*  Copyright notice
*
*  (c) 2005 Robert Lemke (robert@typo3.org)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */

require_once (t3lib_extMgM::extPath('paymentlib').'lib/class.tx_paymentlib_provider.php');

class tx_paymentlibfixture_provider extends tx_paymentlib_provider {
	
	public $fixtureId;
	

	/**
	 * Returns a configuration array of available payment methods.
	 * 
	 * @return	array		Supported payment methods
	 * @access	public 
	 */
	 public function getAvailablePaymentMethods () {
#	 	return t3lib_div::xml2array (t3lib_div::getUrl(t3lib_extMgm::extPath ('paymentlib_ipayment').'paymentmethods.xml'));
	 }

	/**
	 * Returns TRUE if the payment implementation supports the given gateway mode.
	 * All implementations should at least support the mode 
	 * TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * TX_PAYMENTLIB_GATEWAYMODE_WEBSERVICE usually requires your webserver and
	 * the whole application to be certified if used with certain credit cards.
	 *
	 * @param	integer		$gatewayMode: The gateway mode to check for. One of the constants TX_PAYMENTLIB_GATEWAYMODE_*
	 * @return	boolean		TRUE if the given gateway mode is supported
	 * @access	public
	 */
	public function supportsGatewayMode ($gatewayMode) {
	}

	/**
	 * Checks the connection to the payment provider. 
	 *
	 * @return	integer		
	 * @access	public 
	 */
	public function checkConnection () {
		return TRUE;
	}	

	/**
	 * Returns the provider key 
	 *
	 * @return	integer		
	 * @access	public 
	 */
	public function getProviderKey () {
		return $this->providerKey;
	}	

	/**
	 * Initializes a transaction.
	 *
	 * @param	integer		$action: Type of the transaction, one of the constants TX_PAYMENTLIB_TRANSACTION_ACTION_*
	 * @param	string		$paymentMethod: Payment method, one of the values of getSupportedMethods()
	 * @param	integer		$gatewayMode: Gateway mode for this transaction, one of the constants TX_PAYMENTLIB_GATEWAYMODE_*
	 * @param	string		$callingExtKey: Extension key of the calling script.
	 * @return	void
	 * @access	public
	 */
	 public function transaction_init ($action, $paymentMethod, $gatewayMode, $callingExtKey) {
	 }

	/**
	 *
	 * @return	array		Supported payment methods
	 * @access	public 
	 */
	 public function transaction_setDetails ($detailsArr) {
	 }

	/**
	 *
	 * @return	array		Supported payment methods
	 * @access	public 
	 */
	 public function transaction_validate ($level=1) {
	 }

	/**
	 *
	 * @return	array		Supported payment methods
	 * @access	public 
	 */
	 public function transaction_process () {
	 }

	/**
	 * Returns the form action URI to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * @return	string		Form action URI
	 * @access	public
	 */
	public function transaction_formGetActionURI () {
	}

	/**
	 * Returns an array of field names and values which must be included as hidden
	 * fields in the form you render use mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * @return	array		Field names and values to be rendered as hidden fields
	 * @access	public
	 */
	public function transaction_formGetHiddenFields () {
	}

	/**
	 * Returns an array of field names and their configuration which must be rendered
	 * for submitting credit card numbers etc.
	 *
	 * The configuration has the format of the TCA fields section and can be used for
	 * rendering the labels and fields with by the extension frontendformslib
	 *
	 * @return	array		Field names and configuration to be rendered as visible fields
	 * @access	public
	 */
	public function transaction_formGetVisibleFields () {
	}

	/**
	 *
	 * @return	array		Supported payment methods
	 * @access	public 
	 */
	 public function transaction_getResults () {
	 }
	
}

?>