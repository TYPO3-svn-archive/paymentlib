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

define('TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZEANDTRANSFER', 200);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZE', 201);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_TRANSFER', 202);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_REAUTHORIZEANDTRANSFER', 203);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_REAUTHORIZE', 204);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_CANCELAUTHORIZED', 205);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZEREFUND', 210);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZEANDTRANSFERREFUND', 211);

define('TX_PAYMENTLIB_TRANSACTION_RESULT_APPROVED', 300);
define('TX_PAYMENTLIB_TRANSACTION_RESULT_DECLINED', 301);
define('TX_PAYMENTLIB_TRANSACTION_RESULT_FRAUD', 302);
define('TX_PAYMENTLIB_TRANSACTION_RESULT_DUPLICATE', 303);
define('TX_PAYMENTLIB_TRANSACTION_RESULT_OTHER', 310);

define('TX_PAYMENTLIB_GATEWAYMODE_FORM', 400);
define('TX_PAYMENTLIB_GATEWAYMODE_WEBSERVICE', 401);

define('TX_PAYMENTLIB_TRANSACTION_STATE_AUTHORIZED', 500);
define('TX_PAYMENTLIB_TRANSACTION_STATE_AUTHORIZE_FAILED', 501);
define('TX_PAYMENTLIB_TRANSACTION_STATE_CAPTURED', 502);
define('TX_PAYMENTLIB_TRANSACTION_STATE_CAPTURE_FAILED', 503);
define('TX_PAYMENTLIB_TRANSACTION_STATE_REVERSED', 504);
define('TX_PAYMENTLIB_TRANSACTION_STATE_REVERSAL_FAILED', 505);
define('TX_PAYMENTLIB_TRANSACTION_STATE_CREDITED', 506);
define('TX_PAYMENTLIB_TRANSACTION_STATE_CREDIT_FAILED', 507);
define('TX_PAYMENTLIB_TRANSACTION_STATE_RENEWED', 508);
define('TX_PAYMENTLIB_TRANSACTION_STATE_RENEWAL_FAILED', 509);


/**
 * Abstract class defining the interface for provider implementations.
 * 
 * All implementations must implement this interface but depending on the
 * gatway modes they support, methods like transaction_validate won't
 * do anything.
 *
 * @package 	TYPO3
 * @subpackage	tx_paymentlib
 * @version		1.0.0
 * @author		Robert Lemke <robert@typo3.org>
 */
abstract class tx_paymentlib_provider {

	/**
	 * Returns the provider key. Each provider implementation should have such
	 * a unique key.
	 *
	 * @return	array		Provider key
	 * @access	public
	 */
	abstract public function getProviderKey ();

	/**
	 * Returns an array of keys of the supported payment methods
	 *
	 * @return	array		Supported payment methods
	 * @access	public
	 */
	abstract public function getAvailablePaymentMethods ();

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
	abstract public function supportsGatewayMode ($gatewayMode);

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
	 abstract public function transaction_init ($action, $paymentMethod, $gatewayMode, $callingExtKey);

	/**
	 * Sets the payment details. Which fields can be set usually depends on the
	 * chosen / supported gateway mode. TX_PAYMENTLIB_GATEWAYMODE_FORM does not
	 * allow setting credit card data for example.
	 *
	 * @param	array		$detailsArr: The payment details array
	 * @return	boolean		Returns TRUE if all required details have been set
	 * @access	public
	 */
	 abstract public function transaction_setDetails ($detailsArr);

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
	 abstract public function transaction_validate ($level=1);

	/**
	 * Submits the prepared transaction to the payment gateway
	 *
	 * This method is not available in mode TX_PAYMENTLIB_GATEWAYMODE_FORM, you'll have
	 * to render and submit a form instead.
	 *
	 * @return	boolean		TRUE if transaction was successul, FALSE if not. The result can be accessed via transaction_getResults()
	 * @access	public
	 */
	abstract public function transaction_process ();

	/**
	 * Returns the form action URI to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * @return	string		Form action URI
	 * @access	public
	 */
	abstract public function transaction_formGetActionURI ();

    /**
     * Returns any extra parameter for the form tag to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
     *
     * @return  string      Form tag extra parameters
     * @access  public
     */
    abstract public function transaction_formGetFormParms ();
    
    /**
     * Returns any extra parameter for the form submit button to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
     *
     * @return  string      Form submit button extra parameters
     * @access  public
     */
    abstract public function transaction_formGetSubmitParms ();

	/**
	 * Returns an array of field names and values which must be included as hidden
	 * fields in the form you render use mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * @return	array		Field names and values to be rendered as hidden fields
	 * @access	public
	 */
	abstract public function transaction_formGetHiddenFields ();

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
	abstract public function transaction_formGetVisibleFields ();

	/**
	 * Sets the URI which the user should be redirected to after a successful payment/transaction
	 * If you provider/gateway implementation only supports one redirect URI, set okpage and
	 * errorpage to the same URI
	 * 
	 * @return void
	 * @access public
	 */
	abstract public function transaction_setOkPage ($uri);

	/**
	 * Sets the URI which the user should be redirected to after a failed payment/transaction
	 * If you provider/gateway implementation only supports one redirect URI, set okpage and
	 * errorpage to the same URI
	 * 
	 * @return void
	 * @access public 
	 */
	abstract public function transaction_setErrorPage ($uri);
	
	/**
	 * Returns the results of a processed transaction
	 *
	 * @param	string		$reference
	 * @return	array		Results of a processed transaction
	 * @access	public
	 */
	abstract public function transaction_getResults ($reference);


}

?>