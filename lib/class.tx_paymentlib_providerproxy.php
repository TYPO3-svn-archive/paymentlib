<?php
/***************************************************************
* $Id$
*
*  Copyright notice
*
*  (c) 2009 Robert Lemke (robert@typo3.org), Tonni Aagesen (t3dev@support.pil.dk)
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

require_once (t3lib_extMgM::extPath('paymentlib').'lib/class.tx_paymentlib_provider.php');

/**
 * Proxy class implementing the interface for provider implementations. This
 * class hangs between the real provider implementation and the application
 * using it.
 *
 * @package 	TYPO3
 * @subpackage	tx_paymentlib
 * @author		Robert Lemke <robert@typo3.org>
 */
class tx_paymentlib_providerproxy implements tx_paymentlib_provider_int {
	private $providerObj;
	protected $extensionManagerConf;


	/**
	 * Constructor. Pass the class name of a provider implementation.
	 *
	 * @param	string		$providerClass: Class name of a provider implementation acting as the "Real Subject"
	 * @return	void
	 * @access	public
	 */
	public function __construct ($providerClass) {
		$this->extensionManagerConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['paymentlib']);
		$this->providerObj = new $providerClass;
	}


	/**
	 * Returns the provider key. Each provider implementation should have such
	 * a unique key.
	 *
	 * @return	array		Provider key
	 * @access	public
	 */
	public function getProviderKey () {
		return $this->providerObj->getProviderKey ();
	}


	/**
	 * Returns an array of keys of the supported payment methods
	 *
	 * @return	array		Supported payment methods
	 * @access	public
	 */
	public function getAvailablePaymentMethods () {
		return $this->providerObj->getAvailablePaymentMethods();
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
		return $this->providerObj->supportsGatewayMode($gatewayMode);
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
	public function transaction_init ($action, $method, $gatewaymode, $callingExtKey) {
		$this->providerObj->setTransactionUid(0);
		return $this->providerObj->transaction_init($action, $method, $gatewaymode, $callingExtKey);
	}


	/**
	 * Sets the payment details. Which fields can be set usually depends on the
	 * chosen / supported gateway mode. TX_PAYMENTLIB_GATEWAYMODE_FORM does not
	 * allow setting credit card data for example.
	 *
	 * @param	array		$detailsArr: The payment details array
	 * @return	boolean		Returns TRUE if all required details have been set
	 * @access	public
	 */
	public function transaction_setDetails ($detailsArr) {
		return $this->providerObj->transaction_setDetails($detailsArr);
	}


	/**
	 * Validates the transaction data which was set by transaction_setDetails().
	 * $level determines how strong the check is, 1 only checks if the data is
	 * formally correct while level 2 checks if the credit card or bank account
	 * really exists.
	 *
	 * This method is not available in mode TX_PAYMENTLIB_GATEWAYMODE_FORM!
	 *
	 * @param	integer		$level: Level of validation, depends on implementation
	 * @return	boolean		Returns TRUE if validation was successful, FALSE if not
	 * @access	public
	 */
	public function transaction_validate ($level=1) {
		return $this->providerObj->transaction_validate($level);
	}


	/**
	 * Returns if the transaction has been successfull
	 *
	 * @param	array		results from transaction_getResults
	 * @return	boolean		TRUE if the transaction went fine
	 * @access	public
	 */
	public function transaction_succeded ($resultsArr)	{
		return $this->providerObj->transaction_succeded($resultsArr);
	}


	/**
	 * Returns if the transaction has been unsuccessfull
	 *
	 * @param	array		results from transaction_getResults
	 * @return	boolean		TRUE if the transaction went wrong
	 * @access	public
	 */
	public function transaction_failed ($resultsArr)	{
		return $this->providerObj->transaction_failed($resultsArr);
	}


	/**
	 * Returns if the message of the transaction
	 *
	 * @param	array		results from transaction_getResults
	 * @return	boolean		TRUE if the transaction went wrong
	 * @access	public
	 */
	public function transaction_message ($resultsArr)	{
		return $this->providerObj->transaction_message($resultsArr);
	}


	/**
	 * Submits the prepared transaction to the payment gateway
	 *
	 * This method is not available in mode TX_PAYMENTLIB_GATEWAYMODE_FORM, you'll have
	 * to render and submit a form instead.
	 *
	 * @return	boolean		TRUE if transaction was successul, FALSE if not. The result can be accessed via transaction_getResults()
	 * @access	public
	 */
	public function transaction_process () {
		global $TYPO3_DB;

		$processResult = $this->providerObj->transaction_process ();
		$resultsArr = $this->providerObj->transaction_getResults();
		if (is_array ($resultsArr)) {
			$fields = $resultsArr;
			$fields['crdate'] = time();
			$fields['pid'] = intval($this->extensionManagerConf['pid']);
			$fields['remotemessages'] = (is_array ($fields['remotemessages'])) ? serialize($fields['remotemessages']) : $fields['remotemessages'];
			$dbResult = $TYPO3_DB->exec_INSERTquery (
				'tx_paymentlib_transactions',
				$fields
			);
			$dbTransactionUid = $TYPO3_DB->sql_insert_id();
			$this->providerObj->setTransactionId($dbTransactionUid);
		}
		return $processResult;
	}


	/**
	 * Returns the form action URI to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * @return	string		Form action URI
	 * @access	public
	 */
	public function transaction_formGetActionURI () {
		return $this->providerObj->transaction_formGetActionURI();
	}


    /**
     * Returns any extra parameter for the form tag to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
     *
     * @return  string      Form tag extra parameters
     * @access  public
     */
    public function transaction_formGetFormParms () {
        if ($this->gatewayMode != TX_PAYMENTLIB_GATEWAYMODE_FORM) return '';

        return $this->providerObj->transaction_formGetFormParms();
    }


    /**
     * Returns any extra parameter for the form submit button to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
     *
     * @return  string      Form submit button extra parameters
     * @access  public
     */
    public function transaction_formGetSubmitParms () {
        if ($this->gatewayMode != TX_PAYMENTLIB_GATEWAYMODE_FORM) return '';

        return $this->providerObj->transaction_formGetSubmitParms();
    }


	/**
	 * Returns an array of field names and values which must be included as hidden
	 * fields in the form you render use mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * @return	array		Field names and values to be rendered as hidden fields
	 * @access	public
	 */
	public function transaction_formGetHiddenFields () {
		return $this->providerObj->transaction_formGetHiddenFields();
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
		return $this->providerObj->transaction_formGetVisibleFields();
	}


	/**
	 * Sets the URI which the user should be redirected to after a successful payment/transaction
	 *
	 * @return void
	 * @access public
	 */
	public function transaction_setOkPage ($uri) {
	    $this->providerObj->transaction_setOkPage($uri);
	}


	/**
	 * Sets the URI which the user should be redirected to after a failed payment/transaction
	 *
	 * @return void
	 * @access public
	 */
	public function transaction_setErrorPage ($uri) {
	    $this->providerObj->transaction_setErrorPage($uri);
	}


	/**
	 * Returns the results of a processed transaction
	 *
	 * @param	string		$orderid
	 * @return	array		Results of a processed transaction
	 * @access	public
	 */
	public function transaction_getResults ($reference) {
		global $TYPO3_DB;

		$resultsArr = $this->providerObj->transaction_getResults($reference);

		if (is_array ($resultsArr)) {
			$dbTransactionUid = $this->providerObj->getTransactionId();
			$dbResult = $TYPO3_DB->exec_SELECTquery (
				'gatewayid',
				'tx_paymentlib_transactions',
				'uid='.intval($dbTransactionUid)
			);

			if ($dbResult) {
				$row = $TYPO3_DB->sql_fetch_assoc($dbResult);
				if (is_array ($row) && $row['gatewayid'] === $resultsArr['gatewayid']) {
					$resultsArr['internaltransactionuid'] = $dbTransactionUid;
				} else {
						// If the transaction doesn't exist yet in the database, create a transaction record.
						// Usually the case with unsuccessful orders with gateway mode FORM.
					$fields = $resultsArr;
					$fields['crdate'] = time();
					$fields['pid'] = $this->extensionManagerConf['pid'];

					//$fields['remotemessages'] = serialize ($fields['remotemessages']);
					$dbResult =  $TYPO3_DB->exec_INSERTquery(
						'tx_paymentlib_transactions',
						$fields
					);
				}
			}
		}
		return $resultsArr;
	}


	/**
	 * Methods of the provider implementation which this proxy class does not know
	 * are just passed to the provider object. This should be mainly used for testing
	 * purposes, for other cases you should stick to the official interface which is
	 * also supported by the provider proxy.
	 *
	 * @param	string		$method:	Method name
	 * @param	array		$params:	Parameters
	 * @return	mixed		Result
	 * @access	public
	 */
	public function __call ($method, $params) {
		if (method_exists($this, $method))	{
			$rc = call_user_func_array(array($this->providerObj, $method), $params);
		} else {
			debug ('ERROR: unkown method "' . $method . '" in call of tx_paymentlib_providerproxy object');
			$rc = FALSE;
		}
		return $rc;
	}


	/**
	 * Returns the property of the real subject (provider object).
	 *
	 * @param	string		$property: Name of the variable
	 * @return	mixed		The value.
	 * @access	public
	 */
	public function __get ($property) {
		return $this->providerObj->$property;
	}


	/**
	 * Sets the property of the real subject (provider object)
	 *
	 * @param	string		$property: Name of the variable
	 * @param	mixed		$value: The new value
	 * @return	void
	 * @access	public
	 */
	public function __set ($property, $value) {
		$this->providerObj->$property = $value;
	}


	public function clearErrors ()	{
		$this->providerObj->clearErrors();
	}


	public function addError ($error)	{
		$this->providerObj->addError($error);
	}


	public function hasErrors ()	{
		$this->providerObj->hasErrors();
	}


	public function getErrors ()	{
		$this->providerObj->getErrors();
	}


	public function usesBasket ()	{
		$this->providerObj->usesBasket();
	}


	public function createReferenceUid ($orderuid, $callingExtension)	{
		$rc = $this->providerObj->createReferenceUid($orderuid, $callingExtension);
		return $rc;
	}


	/**
	 * Sets the uid of the transaction table
	 *
	 * @param	integer		unique transaction id
	 * @return	void
	 * @access	public
	 */
	public function setTransactionUid ($transUid)	{
		$this->providerObj->setTransactionUid($transUid);
	}


	/**
	 * Fetches the uid of the transaction table, which is the reference
	 *
	 * @return	void		unique transaction id
	 * @access	public
	 */
	public function getTransactionUid ()	{
		$this->providerObj->getTransactionUid();
	}


// 	public function getRealInstance () {
// 		return $this->providerObj;
// 	}

}


?>