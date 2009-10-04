<?php
require_once(t3lib_extMgm::extPath('paymentlib') . 'interfaces/interface.tx_paymentlib_transaction_int.php');

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
 * The transaction object for the paymentlib. This is the central
 * element of all payments. It holds informations about the
 * transaction id, the used payment method and further more.
 */

class tx_paymentlib_transaction implements tx_paymentlib_transaction_int {

	private $created;
	private $gateway;
	private $callingExtension;
	private $transactionId;
	private $state;
	private $transactionAmount;
	private $currency;
	private $paymentMethod;
	private $stateMessage;
	private $basket;
	private $additional;
	private $gatewayMode;
	private $returnSuccesUrl;
	private $returnAbortUrl;

	function __construct($callingExtension, $gateway, $createTime, $transactionId = NULL) {

		if ($callingExtension != NULL
			&& is_string($callingExtension)
			&& $gateway != NULL
			&& is_string($gateway)
			&& is_integer($createTime)
			&& $createTime > 0
			&& $transactionId != NULL
			&& is_string($transactionId)
			) {

				// These arguments are needed to create the transaction
				// object

				$this->callingExtension = $callingExtension;

				$this->gateway = $gateway;

				$this->created = $createTime;

				$this->transactionId = $transactionId;

			} else {

				// If some initialization parameters are missing throw a
				// invalid argument exception

				throw new InvalidArgumentException(self::INVALID_CONSTRUCTOR_MESSAGE, self::INVALID_CONSTRUCTOR_ARGUMENTS);

			}
	}

	function __clone() {

		$this->paymentMethod = clone $this->paymentMethod;


	}

	/**
	 * @return unknown
	 */
	public function getBasket() {
		return $this->basket;
	}

	/**
	 * @return unknown
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @return unknown
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @return unknown
	 */
	public function getCallingExtension() {
		return $this->callingExtension;
	}

	/**
	 * @return unknown
	 */
	public function getGateway() {
		return $this->gateway;
	}

	/**
	 * @return unknown
	 */
	public function getPaymentMethod() {
		return $this->paymentMethod;
	}

	/**
	 * @return unknown
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * @return unknown
	 */
	public function getStateMessage() {
		return $this->stateMessage;
	}

	/**
	 * @return unknown
	 */
	public function getTransactionAmount() {
		return $this->transactionAmount;
	}

	/**
	 * @param unknown_type $basket
	 */
	public function setBasket($basket) {
		$this->basket = $basket;
	}

	/**
	 * @param unknown_type $created
	 */
	private function setCreated($created) {
		$this->created = $created;
	}

	/**
	 * @param unknown_type $extension
	 */
	private function setCallingExtension($extension) {
		$this->callingExtension = $extension;
	}

	/**
	 * @return unknown
	 */
	private function getTransactionId() {
		return $this->transactionId;
	}

	/**
	 * @param unknown_type $gateway
	 */
	private function setGateway($gateway) {
		$this->gateway = $gateway;
	}

	/**
	 * @param unknown_type $currency
	 */
	public function setCurrency($currency) {
		$this->currency = $currency;
	}

	/**
	 * @param unknown_type $paymentMethod
	 */
	public function setPaymentMethod(tx_paymentlib_base_payment_int $paymentMethod) {
		$this->paymentMethod = $paymentMethod;
	}

	/**
	 * @param unknown_type $state
	 */
	public function setState($state) {
		$this->state = $state;
	}

	/**
	 * @param unknown_type $stateMessage
	 */
	public function setStateMessage($stateMessage) {
		$this->stateMessage = $stateMessage;
	}

	/**
	 * @param unknown_type $transactionAmount
	 */
	public function setTransactionAmount($transactionAmount) {
		$this->transactionAmount = $transactionAmount;
	}

	/**
	 * @param unknown_type $transactionId
	 */
	public function setTransactionId($transactionId) {
		$this->transactionId = $transactionId;
	}
	/**
	 * @return unknown
	 */
	public function getAdditional() {
		return $this->additional;
	}

	/**
	 * @param unknown_type $additional
	 */
	public function setAdditional($additional) {
		$this->additional = $additional;
	}
	/**
	 * @return unknown
	 */
	public function getGatewayMode() {
		return $this->gatewayMode;
	}

	/**
	 * @param unknown_type $gatewayMode
	 */
	public function setGatewayMode($gatewayMode) {
		$this->gatewayMode = $gatewayMode;
	}
	/**
	 * @return unknown
	 */
	public function getReturnAbortUrl() {
		return $this->returnAbortUrl;
	}

	/**
	 * @return unknown
	 */
	public function getReturnSuccesUrl() {
		return $this->returnSuccesUrl;
	}

	/**
	 * @param unknown_type $returnAbortUrl
	 */
	public function setReturnAbortUrl($returnAbortUrl) {
		$this->returnAbortUrl = $returnAbortUrl;
	}

	/**
	 * @param unknown_type $returnSuccesUrl
	 */
	public function setReturnSuccesUrl($returnSuccesUrl) {
		$this->returnSuccesUrl = $returnSuccesUrl;
	}
	
	public function __toArray() {
		
		$transactionData = array(
		
			self::DB_TRANSACTION_CREATED => $this->getCreated(),
			
			self::DB_TRANSACTION_GATEWAY_ID => $this->getGateway(),			// Typicall the same as the current extension key
			
			self::DB_TRANSACTION_EXT_KEY => $this->getCallingExtension(),
			
			self::DB_TRANSACTION_REFERENCE => $this->getTransactionId(),
			
			self::DB_TRANSACTION_STATE => $this->getState(),
			
			self::DB_TRANSACTION_AMOUNT => $this->getTransactionAmount(),
			
			self::DB_TRANSACTION_CURRENCY => $this->getCurrency(),
			
			self::DB_TRANSACTION_PAYMENT_METHOD_KEY => $this->getGateway(),
			
			self::DB_TRANSACTION_PAYMENT_METHOD => serialize($this->getPaymentMethod()),	
											
			self::DB_TRANSACTION_MESSAGE => $this->getStateMessage(),
			
		);
		
		return $transactionData;
		
	}
}

?>