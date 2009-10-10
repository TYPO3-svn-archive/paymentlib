<?php
/***************************************************************
* $Id$
*
*  Copyright notice
*
*  (c) 2009 Robert Lemke (robert@typo3.org)
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

require_once(t3lib_extMgm::extPath('paymentlib') . 'interfaces/interface.tx_paymentlib_provider_int.php');


/**
 * Abstract class defining the interface for provider implementations.
 *
 * All implementations must implement this interface but depending on the
 * gatway modes they support, methods like transaction_validate won't
 * do anything.
 *
 * @package 	TYPO3
 * @subpackage	tx_paymentlib
 * @version		1.1.0
 * @author		Robert Lemke <robert@typo3.org>
 */
abstract class tx_paymentlib_provider implements tx_paymentlib_provider_int {
	protected $providerKey = "paymentlib";	// must be overridden
	protected $extKey = "paymentlib";		// must be overridden
	protected $supportedGatewayArray = array();	// must be overridden
	protected $conf;
	protected $bSendBasket;
	private $errorStack;
	private	$action;
	private	$paymentMethod;
	private	$gatewayMode;
	private	$callingExtension;
	private $detailsArr;
	private $transactionId;
	private $referenceId;
	private $config = array();
	private $cookieArray = array();


	/**
	 * Constructor. Pass the class name of a provider implementation.
	 *
	 * @param	string		$providerClass: Class name of a provider implementation acting as the "Real Subject"
	 * @return	void
	 * @access	public
	 */
	public function __construct () {
		global $TSFE;

		$this->clearErrors();
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['paymentlib']);
		$extManagerConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);

		if (is_array($this->conf) && is_array($extManagerConf))	{
			$this->conf = array_merge($this->conf, $extManagerConf);
		}

		$this->setCookieArray(
			array('fe_typo_user' => $_COOKIE['fe_typo_user'])
		);
	}


	public function getProviderKey ()	{
			return $this->providerKey;
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
	 public function getAvailablePaymentMethods () {

		$filename = t3lib_extMgm::extPath($this->extKey) . 'paymentmethods.xml';
		$filenamepath = t3lib_div::getUrl(t3lib_extMgm::extPath($this->extKey) . 'paymentmethods.xml');

		if ($filenamepath)	{
			$rc = t3lib_div::xml2array($filenamepath);
			$errorIndices = $filenamepath;
		} else {
			$errorIndices = $filename . ' not found';
		}

		if (!is_array($rc))	{
			$this->addError('tx_paymentlib_provider::getAvailablePaymentMethods "' . $this->extKey . ':' . $errorIndices . ':' .  $rc . '"');
			$rc = FALSE;
		}
		return $rc;
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
	public function supportsGatewayMode ($gatewayMode)	{

		$rc = in_array($gatewayMode, $this->supportedGatewayArray);
		return $rc;
	}


	/**
	 * Initializes a transaction.
	 *
	 * @param	integer		$action: Type of the transaction, one of the constants TX_PAYMENTLIB_TRANSACTION_ACTION_*
	 * @param	string		$paymentMethod: Payment method, one of the values of getSupportedMethods()
	 * @param	integer		$gatewayMode: Gateway mode for this transaction, one of the constants TX_PAYMENTLIB_GATEWAYMODE_*
	 * @param	string		$callingExtKey: Extension key of the calling script.
	 * @param	array		$conf: configuration. This will override former configuration from the exension manager.
	 * @return	void
	 * @access	public
	 */
	public function transaction_init ($action, $paymentMethod, $gatewayMode, $callingExtKey, $conf=array())	{
		if ($this->supportsGatewayMode($gatewayMode))	{
			$this->action = $action;
			$this->paymentMethod = $paymentMethod;
			$this->gatewayMode = $gatewayMode;
			$this->callingExtension = $callingExtKey;
			if (is_array($this->conf) && is_array($conf))	{
				$this->conf = array_merge($this->conf, $conf);
			}
			$rc = TRUE;
		} else {
			$rc = FALSE;
		}
		return $rc;
	}


	public function getConf ()	{
		return $this->conf;
	}


	public function getConfig ()	{
		return $this->config;
	}


	public function setCookieArray ($cookieArray)	{
		if (is_array($cookieArray))	{
			$this->cookieArray = array_merge($this->cookieArray, $cookieArray);
		}
	}


	public function getCookies ()	{
		$rc = '';
		if (count($this->cookieArray))	{
			$tmpArray = array();
			foreach ($this->cookieArray as $k => $v)	{
				$tmpArray[] = $k . '=' . $v;
			}
			$rc = implode('; ',$tmpArray);
		}
		return $rc;
	}


	public function getLanguage ()	{
		global $TSFE;

		$rc = (isset($TSFE->config['config']) && is_array($TSFE->config['config']) && $TSFE->config['config']['language'] ? $TSFE->config['config']['language'] : 'en');
		return $rc;
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
	public function transaction_setDetails ($detailsArr)	{
		global $TYPO3_DB;

		$rc = TRUE;
		$this->detailsArr = $detailsArr;

		$referenceId =
			$this->createReferenceUid(strval($detailsArr['transaction']['orderuid']), $this->getCallingExtension());

		$this->setTransactionUid($transactionId);
		$this->config = array();
		$this->config['currency_code'] = $detailsArr['transaction']['currency'];
		if (ord($this->config['currency_code']) == 128)	{ // 'euro symbol'
			$this->config['currency_code'] = 'EUR';
		}
		$this->config['return'] = ($detailsArr['transaction']['successlink'] ? $detailsArr['transaction']['successlink'] : $this->conf['return']);
		$this->config['cancel_return'] = ($detailsArr['transaction']['returi'] ? $detailsArr['transaction']['returi'] : $this->conf['cancel_return']);

		// Store order id in database
		$dataArr = array(
			'crdate' => time(),
			'gatewayid' => $this->providerKey,
			'ext_key' => $this->callingExtension,
			'reference' => $transactionId,
			'state' => TRANSACTION_NOPROCESS,
			'amount' => $detailsArr['transaction']['amount'],
			'currency' => $detailsArr['transaction']['currency'],
			'paymethod_key' => $this->providerKey,
			'paymethod_method' => $this->paymentMethod,
			'message' => TRANSACTION_NOT_PROCESSED_MSG
		);

		$res = $TYPO3_DB->exec_DELETEquery('tx_paymentlib_transactions', 'gatewayid =' . $TYPO3_DB->fullQuoteStr($this->getProviderKey(), 'tx_paymentlib_transactions') . ' AND amount LIKE "0.00" AND message LIKE "s:25:\"Transaction not processed\";"');

		if ($this->getTransaction($transactionId) === FALSE)	{
			$res = $TYPO3_DB->exec_INSERTquery('tx_paymentlib_transactions', $dataArr);
		} else {
			$res = $TYPO3_DB->exec_UPDATEquery('tx_paymentlib_transactions', 'reference = ' . $TYPO3_DB->fullQuoteStr($transactionId, 'tx_paymentlib_transactions'), $dataArr);
		}
		if (!$res)	{
			$rc = FALSE;
		}

		return $rc;
	}


	public function getDetails ()	{
		return $this->detailsArr;
	}


	public function getGatewayMode ()	{
		return $this->gatewayMode;
	}


	public function getPaymentMethod ()	{
		return $this->paymentMethod;
	}


	public function getCallingExtension ()	{
		return $this->callingExtension;
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
	public function transaction_validate ($level=1) 	{
		return FALSE;
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
	public function transaction_process ()	{
		return FALSE;
	}


	/**
	 * Returns the form action URI to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	 *
	 * @return	string		Form action URI
	 * @access	public
	 */
	public function transaction_formGetActionURI ()	{

		if ($this->gatewayMode == TX_PAYMENTLIB_GATEWAYMODE_FORM)	{
			$rc = $this->conf['formActionURI'];
		} else {
			$rc = FALSE;
		}
		return $rc;
	}


	/**
	* Returns any extra parameter for the form tag to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	*
	* @return  string      Form tag extra parameters
	* @access  public
	*/
	public function transaction_formGetFormParms ()	{
		return '';
	}


	/**
	* Returns any extra parameter for the form submit button to be used in mode TX_PAYMENTLIB_GATEWAYMODE_FORM.
	*
	* @return  string      Form submit button extra parameters
	* @access  public
	*/
	public function transaction_formGetSubmitParms ()	{
		return '';
	}


	public function transaction_succeded ($resultsArr)	{
		if ($resultsArr['status'] == TRANSACTION_SUCCESS)	{
			$rc = TRUE;
		} else {
			$rc = FALSE;
		}

		return $rc;
	}


	public function transaction_failed ($resultsArr)	{

		if ($resultsArr['status'] == TRANSACTION_FAILED)	{
			$rc = TRUE;
		} else {
			$rc = FALSE;
		}

		return $rc;
	}


	public function transaction_message ($resultsArr)	{
		return $resultsArr['errmsg'];
	}


	public function clearErrors ()	{
		$this->errorStack = array();
	}


	public function addError ($error)	{
		$this->errorStack[] = $error;
	}


	public function hasErrors ()	{
		$rc = (count($this->errorStack) > 0);
	}


	public function getErrors ()	{
		return $this->errorStack;
	}


	public function usesBasket ()	{
		$rc = (intval($this->bSendBasket) > 0) || isset($this->detailsArr['basket']) && is_array($this->detailsArr['basket']) && count($this->detailsArr['basket']);

		return $rc;
	}


	// *****************************************************************************
	// Helpers
	// *****************************************************************************

	public function getTransaction ($transactionId)	{
		global $TYPO3_DB;

		$rc = FALSE;
		$res = $TYPO3_DB->exec_SELECTquery('*', 'tx_paymentlib_transactions', 'reference = "'.$transactionId.'"');

		if ($transactionId !='' && $res)	{
			$rc = $TYPO3_DB->sql_fetch_assoc($res);
		}
		return $rc;
	}


	public function createReferenceUid ($orderuid, $callingExtension)	{
		$rc = $this->providerKey . '#' . md5($callingExtension . '-' . $orderuid);
		return $rc;
	}


	/**
	 * Sets the reference of the transaction table
	 *
	 * @param	integer		unique transaction id
	 * @return	void
	 * @access	public
	 */
	public function setReferenceUid ($reference)	{
		$this->referenceId = $reference;
	}


	/**
	 * Fetches the reference of the transaction table, which is the reference
	 *
	 * @return	void		unique reference
	 * @access	public
	 */
	public function getReferenceUid ()	{
		return $this->referenceId;
	}


	/**
	 * Sets the uid of the transaction table
	 *
	 * @param	integer		unique transaction id
	 * @return	void
	 * @access	public
	 */
	public function setTransactionUid ($transUid)	{
		$this->transactionId = $transUid;
	}


	/**
	 * Fetches the uid of the transaction table, which is the reference
	 *
	 * @return	void		unique transaction id
	 * @access	public
	 */
	public function getTransactionUid ()	{
		return $this->transactionId;
	}
}

?>