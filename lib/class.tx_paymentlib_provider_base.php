<?php

require_once (t3lib_extMgm::extPath ( 'paymentlib' ) . 'lib/transaction/class.tx_paymentlib_transaction.php');

require_once (t3lib_extMgm::extPath ( 'paymentlib' ) . 'lib/transaction/class.tx_paymentlib_payment_base.php');

require_once (t3lib_extMgm::extPath ( 'paymentlib' ) . 'lib/interfaces/interface.tx_paymentlib_payment_base_int.php');

class tx_paymentlib_provider_base implements tx_paymentlib_provider_base_int {
	
	// TODO: Extemd table
	const DB_TRANSACTION_BASKET = "basket";
	const DB_TRANSACTION_GATEWAY_MODE = "gateway_mode";
	const DB_TRANSACTION_ADDITIONAL = "additional";
	
	// Needed configurations	
	const CONF_RETURN_SUCCESS_URL = 'return';
	const CONF_RETURN_ABORT_URL = 'cancel_return';
	const CONF_CURRENCY = "currency_code";
	
	// Static info tables
	// Used for some fixtures to the given currency
	const EXT_STATIC_INFO_TABLES = "static_info_tables";
	const EXT_STATIC_INFO_TABLES_CURRENCY_TABLE = "static_currencies";
	const EXT_STATIC_INFO_TABLES_CUE_ISO_3_FIELD = 'cu_iso_3';
	const EXT_STATIC_INFO_TABLES_SYMBOL_LEFT = "cu_symbol_left";
	const EXT_STATIC_INFO_TABLES_SYMBOL_RIGHT = "cu_symbol_right";
	
	// Error message
	const TRANSACTION_NOT_INITILIZED_MESSAGE = "Transaction is not initialized! Please verify the transaction initialization!";
	
	const TRANSACTION_NOT_INITIALIZED_ERROR_CODE = 0x999;
	
	protected $providerKey = "paymentlib"; // must be overridden
	protected $extKey = "paymentlib"; // must be overridden
	protected $supportedGatewayArray; // must be overridden 
	protected $conf;
	private $transaction;
	
	/**
	 * Some default paraemters will be set here. 
	 * 
	 * @access public
	 */
	function __construct() {
		
		$this->supportedGatewayArray = array (

		self::TX_PAYMENTLIB_GATEWAYMODE_FORM, self::TX_PAYMENTLIB_GATEWAYMODE_WEBSERVICE );
	
	}
	
	/**
	 * Returns the ts-configuration for th current paymentlib
	 *
	 * @return array		$this->conf: The current ts-configuration
	 * 
	 * @see tx_paymentlib_provider_int::getConf()
	 * @access public
	 */
	public function getConf() {
		return $this->conf;
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
	 * 
	 * @see tx_paymentlib_provider_int::getProviderKey() 
	 * @access public
	 */
	public function getProviderKey() {
		
		return $this->providerKey;
	
	}
	
	/**
	 * Returns an array of keys of the supported payment methods
	 *
	 * @return	array		Supported payment methods as objects
	 * 
	 * @see tx_paymentlib_provider_int::getAvailablePaymentMethods()
	 * @access public
	 */
	public function getAvailablePaymentMethods() {
		
	//TODO - Insert your code here
	}
	
	/**
	 * Method to check if the current paymentlib supports the selected
	 * payment-method
	 * 
	 * @param integer			$gatewayMode: The gateway mod to check
	 * 
	 * @see tx_paymentlib_provider_base_int::supportsGatewayMode
	 * @access public
	 */
	public function supportsGatewayMode($gatewayMode) {
		
		$supported = in_array ( $gatewayMode, $this->supportedGatewayArray );
		
		return $supported;
	
	}
	
	/**
	 * Returns the current transaction object
	 * 
	 * @return tx_paymentlib_transaction_int	The current transaction or null
	 * 
	 * @see tx_paymentlib_provider_base_int::getTransaction()
	 * @acess public
	 */
	public function getTransaction() {
		
		return $this->transaction;
	
	}
	
	/**
	 * Initializes a transaction.
	 *
	 * @param string								$callingExtension: The provider key of the calling extension. Required!
	 * @param tx_paymentlib_base_payment_int		$paymentMethod: The selected payment method. Required
	 * @param string								$gatewayMode: The paymentlib mode. Required
	 * @param string								$transactionId: Self generated transaction id. Optional
	 * @return	tx_paymentlib_transaction			The new transaction object.
	 * 
	 * @throws InvalidArgumentException				If one or more of the needed parameters are missing.
	 * 
	 * @see tx_paymentlib_provider_int::transaction_init() 
	 * @access	public
	 */
	public function transaction_init($callingExtension, tx_paymentlib_base_payment_int $paymentMethod, $gatewayMode, $transactionId = NULL) {
		
		if (! empty ( $paymentMethod )) {
			
			// Initialize transaction object
			

			if (empty ( $transactionId )) {
				
				$mills = strval ( microtime () );
				$rand = strval ( mt_rand ( 0, microtime () ) );
				
				$transactionId = md5 ( $callingExtension . "-" . $this->providerKey . "#" . $rand . '-' . $mills );
			
			}
			
			$transaction = null;
			
			try {
				
				// Try to create the transaction objet with the given 
				// parameters	
				// This will raise an invalid argument exception if 
				// not all parameters are set.	
				

				$transaction = new tx_paymentlib_transaction ( $callingExtension, $this->providerKey, time (), $transactionId );
				
				$transaction->setPaymentMethod ( $paymentMethod );
				
				if (empty ( $gatewayMode ) || ! in_array ( $gatewayMode, $this->supportedGatewayArray )) {
					
					$gatewayMode = TX_PAYMENTLIB_GATEWAYMODE_FORM;
				
				}
				
				$transaction->setGatewayMode ( $gatewayMode );
				
				$transaction->setState ( TRANSACTION_NOPROCESS );
				
				$transaction->setStateMessage ( TRANSACTION_NOT_PROCESSED_MSG );
				
				$this->saveTransactionInDatabase ();
				
				return clone ($this->transaction);
			
			} catch ( InvalidArgumentException $e ) {
				
				unset ( $transaction );
				
				throw $e;
			
			}
		
		} else {
			
			throw InvalidArgumentException ( self::PAYMENT_METHOD_REQUIRED, self::PAYMENT_METHOD_REQUIRED_CODE );
		
		}
	
	}
	
	/**
	 * 
	 * @param array		$detailsArr: The payment details array 
	 * @return boolean		Returns TRUE if all required details have been set 
	 *  
	 * @see tx_paymentlib_provider_int::transaction_setDetails()
	 * @access pubic
	 */
	public function transaction_setDetails(tx_paymentlib_transaction_int $transaction) {
		
		if ($this->transactionInitialized ()) {
			
			$this->transaction_setCurrency ( $transaction->getCurrency () );
			
			$this->transaction_setBasket ( $transaction->getBasket () );
			
			$this->transaction_setReturnSuccessUrl ( $transaction->getReturnSuccessUrl () );
			
			$this->transaction_setReturnAbortUrl ( $transaction->getReturnAbortUrl () );
			
			$this->saveTransactionInDatabase ();
		
		} else {
			
			$message = self::TRANSACTION_NOT_INITIALIZED;
			
			throw new invalidArgumentException ( $message, self::TRANSACTION_NOT_INITIALIZED_ERROR_CODE );
		
		}
	
	}
	
	/**
	 * Sets the currency which is used during the transaction. This currency will
	 * be send to the payment provider
	 * 
	 * @param string		$currency
	 * 
	 * @see tx_paymentlib_provider_int::setTransactionCurrency()
	 * @access public
	 */
	public function setTransactionCurrency($currency) {
		
		if ($this->transactionInitialized ()) {
			
			global $TYPO3_DB;
			
			if (empty ( $currency )) {
				
				$currency = $this->conf [self::CONF_CURRENCY];
			
			}
			
			// Lookup to static info tables to mapp country related
			// currency symbols to their cu_iso3-value e.g.
			// '€' => 'EUR'
			

			if (! empty ( $currency ) && t3lib_extMgm::isLoaded ( self::EXT_STATIC_INFO_TABLES )) {
				
				$where = self::EXT_STATIC_INFO_TABLES_SYMBOL_LEFT . "='" . $currency . "' or " . self::EXT_STATIC_INFO_TABLES_SYMBOL_RIGHT . "='" . $currency . "'";
				
				$res = $TYPO3_DB->exec_SELECTquery ( self::EXT_STATIC_INFO_TABLES_CUE_ISO_3_FIELD, self::EXT_STATIC_INFO_TABLES_CURRENCY_TABLE, $where );
				
				if ($res) {
					
					$data = $TYPO3_DB->sql_fetch_assoc ( $res );
					
					$currency = $data [self::EXT_STATIC_INFO_TABLES_CUE_ISO_3_FIELD];
				
				}
			
			}
			
			if (! empty ( $currency ) && is_object ( $this->transaction )) {
				
				// TODO: Check currency if it is in "cu_iso_3"-format from static info tables
				

				$this->transaction->setCurrency ( $currency );
			
			}
			
			$this->saveTransactionInDatabase ();
		
		} else {
			
			$message = self::TRANSACTION_NOT_INITIALIZED;
			
			throw new invalidArgumentException ( $message, self::TRANSACTION_NOT_INITIALIZED_ERROR_CODE );
		
		}
	
	}
	
	/**
	 * Sets the basket which will be send to the payment provider.
	 * 
	 * @param tx_paymentlib_basket_int $basket
	 * 
	 * @see tx_paymentlib_provider_int::setTransactionBaske()
	 * @access public
	 */
	public function setTransactionBasket(tx_paymentlib_basket_int $basket) {
		
		if ($this->transactionInitialized () && ! empty ( $basket ) && is_object ( $basket )) {
			
			$this->transaction->setBasket ( $basket );
			
			$this->saveTransactionInDatabase ();
		
		} else {
			
			$message = self::TRANSACTION_NOT_INITIALIZED;
			
			throw new invalidArgumentException ( $message, self::TRANSACTION_NOT_INITIALIZED_ERROR_CODE );
		
		}
	
	}
	
	/**
	 * Sets the return url to redirct to when the transaction is aborted or if 
	 * an error occurs during the submitting of the transaction
	 * 
	 * @param string 		$returnAbortUrl: The url to redirect to redirect to
	 * 
	 * @see tx_paymentlib_provider_int::setTransactionReturnUrl()
	 * @access public
	 */
	public function setTransactionReturnSuccessUrl($returnSuccessUrl) {
		
		if ($this->transactionInitialized ()) {
			
			if (! empty ( $returnSuccessUrl ) && is_object ( $this->transaction )) {
				
				$this->transaction->setReturnAbortUrl ( $returnSuccessUrl );
			
			} else {
				
				$this->transaction->setReturnAbortUrl ( $this->conf [self::CONF_RETURN_ABORT_URL] );
			
			}
		
		} else {
			
			$message = self::TRANSACTION_NOT_INITIALIZED;
			
			throw new invalidArgumentException ( $message, self::TRANSACTION_NOT_INITIALIZED_ERROR_CODE );
		
		}
	
	}
	
	/**
	 * Sets the currency which is used during the transaction. This currency will
	 * be send to the payment provider
	 * 
	 * @param string		$currency
	 * 
	 * @see tx_paymentlib_provider_int::setTransactionReturnAbortUrl()
	 * @access public
	 */
	public function setTransactionReturnAbortUrl($returnAbortUrl) {
		
		if ($this->transactionInitialized ()) {
			
			if (! empty ( $returnAbortUrl ) && is_object ( $this->transaction )) {
				
				$this->transaction->setReturnAbortUrl ( $returnAbortUrl );
			
			} else {
				
				$this->transaction->setReturnSuccessUrl ( $this->conf [self::CONF_RETURN_SUCCESS_URL] );
			
			}
		
		} else {
			
			$message = self::TRANSACTION_NOT_INITIALIZED;
			
			throw new invalidArgumentException ( $message, self::TRANSACTION_NOT_INITIALIZED_ERROR_CODE );
		
		}
	
	}
	
	/**
	 * Saves the current transaction information in the database.
	 *
	 * @access private
	 */
	
	private function saveTransactionInDatabase() {
		
		// Clean all not processed transactions from the past!
		// Workaround to "clean" aborted transactions
		

		$res = $TYPO3_DB->exec_DELETEquery ( 'tx_paymentlib_transactions', 'gatewayid =' . $TYPO3_DB->fullQuoteStr ( $this->getProviderKey (), 'tx_paymentlib_transactions' ) . ' AND amount LIKE "0.00" AND message LIKE "s:25:\"Transaction not processed\";"' );
		
		try {
			
			$savedTransaction = $this->getTransactionFromDatabase ( $this->transaction->getTransactionId () );
			
			$transactionDataArray = $this->transaction->__toArray ();
			
			if (is_null ( $savedTransaction )) {
				
				$res = $TYPO3_DB->exec_INSERTquery ( 'tx_paymentlib_transactions', $transactionDataArray );
			
			} else {
				
				$res = $TYPO3_DB->exec_UPDATEquery ( 'tx_paymentlib_transactions', 'reference = ' . $TYPO3_DB->fullQuoteStr ( $this->transactionId, 'tx_paymentlib_transactions' ), $transactionDataArray );
			
			}
			
			return $res;
		
		} catch ( InvalidArgumentException $e ) {
			
		// Should never occur
		

		}
	
	}
	
	/**
	 * Returns the transaction with the given transactionid from the database
	 *
	 * @param string 			 					$transactionId: The transaction id to search 
	 * 												for
	 * @return tx_paymentlib_transaction_int		The transaction object or null
	 * @throws InvalidArgumentException 			if an error occured when initializint the transaction
	 * 												object;
	 */
	final public function getTransactionFromDatabase($transactionId) {
		
		global $TYPO3_DB;
		
		$res = $TYPO3_DB->exec_SELECTquery ( '*', 'tx_paymentlib_transactions', 'reference = "' . $transactionId . '"' );
		
		if (! empty ( $transactionId ) && $res) {
			
			$data = $TYPO3_DB->sql_fetch_assoc ( $res );
			
			$transaction = null;
			
			try {
				
				$transaction = new tx_paymentlib_transaction ( $data ['ext_key'], $data ['paymethod_key'], $data ['crdate'], $data ['reference'] );
				
				$transaction->setTransactionAmount ( $data ['amount'] );
				
				$transaction->setCurrency ( $data ['currency'] );
				
				$transaction->setState ( $data ['state'] );
				
				$transaction->setStateMessage ( $data ['message'] );
				
				$transaction->setPaymentMethod ( unserialize ( $data ['payment_method'] ) );
				
				return $transaction;
			
			} catch ( InvalidArgumentException $e ) {
				
				unset ( $transaction );
				
				throw $e;
			
			}
		} else {
			
			return null;
		
		}
	
	}
	
	protected function transactionInitialized() {
		
		$transaction = $this->transaction;
		
		if (! is_null ( $transaction )) {
			
			$transactionId = $transaction->getTransactionId ();
			
			if (! is_null ( $transactionId ) && ! empty ( $transactionId )) {
				
				return true;
			
			} else {
				
				return false;
			
			}
		
		} else {
			
			return false;
		
		}
	
	}
	
	/**
	 * 
	 * @param array		results from transaction_getResults 
	 * @return boolean		TRUE if the transaction went wrong 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_failed()
	 */
	public function transaction_failed($resultsArr) {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @return string		Form action URI 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_formGetActionURI()
	 */
	public function transaction_formGetActionURI() {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @return string      Form tag extra parameters 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_formGetFormParms()
	 */
	public function transaction_formGetFormParms() {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @return array		Field names and values to be rendered as hidden fields 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_formGetHiddenFields()
	 */
	public function transaction_formGetHiddenFields() {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @return string      Form submit button extra parameters 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_formGetSubmitParms()
	 */
	public function transaction_formGetSubmitParms() {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @return array		Field names and configuration to be rendered as visible fields 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_formGetVisibleFields()
	 */
	public function transaction_formGetVisibleFields() {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @param string		$reference 
	 * @return array		Results of a processed transaction 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_getResults()
	 */
	public function transaction_getResults($reference) {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @param array		results from transaction_getResults 
	 * @return boolean		TRUE if the transaction went wrong 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_message()
	 */
	public function transaction_message($resultsArr) {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @return boolean		TRUE if transaction was successul, FALSE if not. The result can be accessed via transaction_getResults() 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_process()
	 */
	public function transaction_process() {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @return void 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_setErrorPage()
	 */
	public function transaction_setErrorPage($uri) {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @return void 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_setOkPage()
	 */
	public function transaction_setOkPage($uri) {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @param array		results from transaction_getResults 
	 * @return boolean		TRUE if the transaction went fine 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_succeded()
	 */
	public function transaction_succeded($resultsArr) {
		
	//TODO - Insert your code here
	}
	
	/**
	 * 
	 * @param integer		$level: Level of validation, depends on implementation 
	 * @return boolean		Returns TRUE if validation was successful, FALSE if not 
	 * @access public 
	 * @see tx_paymentlib_provider_int::transaction_validate()
	 */
	public function transaction_validate($level = 1) {
		
	//TODO - Insert your code here
	}
}

?>