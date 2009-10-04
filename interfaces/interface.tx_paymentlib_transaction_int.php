<?php


interface tx_paymentlib_transaction_int {

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

	// Error codes / messages
	const INVALID_CONSTRUCTOR_ARGUMENTS = 0x999;
	const INVALID_CONSTRUCTOR_MESSAGE = "Transaction initalizing arguments are missing!";

	// Transaction results
	const TX_PAYMENTLIB_TRANSACTION_RESULT_APPROVED = 300;
	const TX_PAYMENTLIB_TRANSACTION_RESULT_DECLINED = 301;
	const TX_PAYMENTLIB_TRANSACTION_RESULT_FRAUD = 302;
	const TX_PAYMENTLIB_TRANSACTION_RESULT_DUPLICATE = 303;
	const TX_PAYMENTLIB_TRANSACTION_RESULT_OTHER = 310;

	// Transaction states
	const TX_PAYMENTLIB_TRANSACTION_STATE_AUTHORIZED = 500;
	const TX_PAYMENTLIB_TRANSACTION_STATE_AUTHORIZE_FAILED = 501;
	const TX_PAYMENTLIB_TRANSACTION_STATE_CAPTURED = 502;
	const TX_PAYMENTLIB_TRANSACTION_STATE_CAPTURE_FAILED = 503;
	const TX_PAYMENTLIB_TRANSACTION_STATE_REVERSED = 04;
	const TX_PAYMENTLIB_TRANSACTION_STATE_REVERSAL_FAILED = 505;
	const TX_PAYMENTLIB_TRANSACTION_STATE_CREDITED = 506;
	const TX_PAYMENTLIB_TRANSACTION_STATE_CREDIT_FAILED = 507;
	const TX_PAYMENTLIB_TRANSACTION_STATE_RENEWED = 508;
	const TX_PAYMENTLIB_TRANSACTION_STATE_RENEWAL_FAILED = 509;
	
	const DB_TRANSACTION_CREATED = 'crdate';
	const DB_TRANSACTION_GATEWAY_ID = 'gatewayid';
	const DB_TRANSACTION_EXT_KEY = 'ext_key';
	const DB_TRANSACTION_REFERENCE = 'reference';
	const DB_TRANSACTION_STATE = 'state';
	const DB_TRANSACTION_AMOUNT = 'amount';
	const DB_TRANSACTION_CURRENCY = "currency";
	const DB_TRANSACTION_PAYMENT_METHOD_KEY = 'paymethod_key';
	const DB_TRANSACTION_PAYMENT_METHOD = "paymethod_method";
	const DB_TRANSACTION_MESSAGE = "message";

	/**
	 * @return unknown
	 */
	public function getBasket() ;

	/**
	 * @return unknown
	 */
	public function getCreated() ;

	/**
	 * @return unknown
	 */
	public function getCurrency() ;

	/**
	 * @return unknown
	 */
	public function getCallingExtension() ;

	/**
	 * @return unknown
	 */
	public function getGateway() ;

	/**
	 * @return unknown
	 */
	public function getPaymentMethod() ;

	/**
	 * @return unknown
	 */
	public function getState() ;

	/**
	 * @return unknown
	 */
	public function getStateMessage() ;

	/**
	 * @return unknown
	 */
	public function getTransactionAmount() ;

	/**
	 * @param unknown_type $basket
	 */
	public function setBasket($basket) ;

	/**
	 * @param unknown_type $currency
	 */
	public function setCurrency($currency) ;

	/**
	 * @param unknown_type $paymentMethod
	 */
	public function setPaymentMethod(tx_paymentlib_base_payment_int $paymentMethod) ;

	/**
	 * @param unknown_type $state
	 */
	public function setState($state) ;

	/**
	 * @param unknown_type $stateMessage
	 */
	public function setStateMessage($stateMessage) ;

	/**
	 * @param unknown_type $transactionAmount
	 */
	public function setTransactionAmount($transactionAmount) ;

	/**
	 * @param unknown_type $transactionId
	 */
	public function setTransactionId($transactionId) ;
	/**
	 * @return unknown
	 */
	public function getAdditional() ;

	/**
	 * @param unknown_type $additional
	 */
	public function setAdditional($additional) ;
	/**
	 * @return unknown
	 */
	public function getGatewayMode() ;

	/**
	 * @param unknown_type $gatewayMode
	 */
	public function setGatewayMode($gatewayMode) ;

	/**
	 * @return unknown
	 */
	public function getReturnAbortUrl();

	/**
	 * @return unknown
	 */
	public function getReturnSuccesUrl();

	/**
	 * @param unknown_type $returnAbortUrl
	 */
	public function setReturnAbortUrl($returnAbortUrl);

}

?>


}

?>