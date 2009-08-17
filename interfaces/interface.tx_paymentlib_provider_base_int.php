<?php

/***************************************************************
* $Id$
*
*  Copyright notice
*
*  (c) 2009 Udo Gerhards (Udo@gerhards.eu)
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
 * The paymentlib provider interface. Used to implement new paymentlibs
 *
 */
interface tx_paymentlib_provider_base_int {

	// Provider modes
	const TX_PAYMENTLIB_GATEWAYMODE_FORM = 400;
	const TX_PAYMENTLIB_GATEWAYMODE_WEBSERVICE = 401;

	const PAYMENT_METHOD_REQUIRED_CODE = 0x998;
	const PAYMENT_METHOD_REQUIRED = "No valid payment method given!";

	/**
	 * Returns the provider key. Each provider implementation should have such
	 * a unique key.
	 *
	 * @return	array		Provider key
	 * @access	public
	 */
	public function getProviderKey ();

	/**
	 * Returns an array of keys of the supported payment methods
	 *
	 * @return	array		Supported payment methods
	 * @access	public
	 */
	public function getAvailablePaymentMethods ();

	/**
	 * Returns the current transaction object
	 *
	 * @return tx_paymentlib_transaction_int	The current transaction or null
	 *
	 * @acess public
	 */
	public function getTransaction();

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
	public function supportsGatewayMode ($gatewayMode);

	/**
	 * Initializes a transaction.
	 *
	 * @param string								$callingExtension: The provider key of the calling extension. Required!
	 * @param tx_paymentlib_base_payment_int		$paymentMethod: The selected payment method. Required
	 * @param string								$gatewayMode: The paymentlib mode. Required
	 * @param string								$transactionId: Self generated transaction id. Optional
	 * @return	tx_paymentlib_transaction_int		The new transaction object.
	 *
	 * @throws InvalidArgumentException				If one or more of the needed parameters are missing.
	 * @access	public
	 */
	 public function transaction_init ($callingExtension, tx_paymentlib_base_payment_int $paymentMethod, $gatewayMode, $transactionId = NULL);


	/**
	 * Sets the payment details. Which fields can be set usually depends on the
	 * chosen / supported gateway mode. TX_PAYMENTLIB_GATEWAYMODE_FORM does not
	 * allow setting credit card data for example.
	 *
	 * @param	array		$detailsArr: The payment details array
	 * @return	boolean		Returns TRUE if all required details have been set
	 * @access	public
	 */
	 public function transaction_setDetails ($detailsArr);

	/**
	 * Sets the return url to redirct to when the transaction is successfull
	 * submitted
	 *
	 * @param string 		$returnSuccesUrl: The url to redirect to
	 * @access public
	 */
	public function setTransactionReturnSuccessUrl($returnSucessUrl);

	/**
	 * Sets the return url to redirct to when the transaction is aborted or if
	 * an error occurs during the submitting of the transaction
	 *
	 * @param string 		$returnAbortUrl: The url to redirect to redirect to
	 * @access public
	 */
	public function setTransactionReturnAbortUrl($returnAbortUrl);

	/**
	 * Sets the currency which is used during the transaction. This currency will
	 * be send to the payment provider
	 *
	 * @param string		$currency
	 * @access public
	 */
	public function setTransactionCurrency($currency);

	/**
	 * Sets the basket which will be send to the payment provider.
	 *
	 * @param tx_paymentlib_basket_int $basket
	 * @access public
	 */
	public function setTransactionBasket(tx_paymentlib_basket_int $basket);


	/**
	 * Returns the current transaction informations as array
	 *
	 * @return array		$transactionData: The informations of the current transaction
	 * @access public
	 */
	public function getTransactionAsArray();

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
	 public function transaction_validate ($level=1);

	/**
	 * Submits the prepared transaction to the payment gateway
	 *
	 * This method is not available in mode TX_PAYMENTLIB_GATEWAYMODE_FORM, you'll have
	 * to render and submit a form instead.
	 *
	 * @return	boolean		TRUE if transaction was successul, FALSE if not. The result can be accessed via transaction_getResults()
	 * @access	public
	 */
	public function transaction_process ();

	/**
	 * Returns the form action URI to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * @return	string		Form action URI
	 * @access	public
	 */
	public function transaction_formGetActionURI ();

    /**
     * Returns any extra parameter for the form tag to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
     *
     * @return  string      Form tag extra parameters
     * @access  public
     */
    public function transaction_formGetFormParms ();

    /**
     * Returns any extra parameter for the form submit button to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
     *
     * @return  string      Form submit button extra parameters
     * @access  public
     */
    public function transaction_formGetSubmitParms ();

	/**
	 * Returns an array of field names and values which must be included as hidden
	 * fields in the form you render use mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * @return	array		Field names and values to be rendered as hidden fields
	 * @access	public
	 */
	public function transaction_formGetHiddenFields ();

	/**
	 * Returns an array of field names and their configuration which must be rendered
	 * for submitting credit card numbers etc.
	 *
	 * The configuration has the format of the TCA fields section and can be used for
	 * rendering th	e labels and fields with by the extension frontendformslib
	 *
	 * @return	array		Field names and configuration to be rendered as visible fields
	 * @access	public
	 */
	public function transaction_formGetVisibleFields ();

	/**
	 * Sets the URI which the user should be redirected to after a successful payment/transaction
	 * If your provider/gateway implementation only supports one redirect URI, set okpage and
	 * errorpage to the same URI
	 *
	 * @return void
	 * @access public
	 */
	public function transaction_setOkPage ($uri);

	/**
	 * Sets the URI which the user should be redirected to after a failed payment/transaction
	 * If your provider/gateway implementation only supports one redirect URI, set okpage and
	 * errorpage to the same URI
	 *
	 * @return void
	 * @access public
	 */
	public function transaction_setErrorPage ($uri);

	/**
	 * Returns the results of a processed transaction
	 *
	 * @param	string		$reference
	 * @return	array		Results of a processed transaction
	 * @access	public
	 */
	public function transaction_getResults ($reference);

	/**
	 * Returns if the transaction has been successfull
	 *
	 * @param	array		results from transaction_getResults
	 * @return	boolean		TRUE if the transaction went fine
	 * @access	public
	 */
	public function transaction_succeded ($resultsArr);

	/**
	 * Returns if the transaction has been unsuccessfull
	 *
	 * @param	array		results from transaction_getResults
	 * @return	boolean		TRUE if the transaction went wrong
	 * @access	public
	 */
	public function transaction_failed ($resultsArr);

	/**
	 * Returns if the message of the transaction
	 *
	 * @param	array		results from transaction_getResults
	 * @return	boolean		TRUE if the transaction went wrong
	 * @access	public
	 */
	public function transaction_message ($resultsArr);


}

?>