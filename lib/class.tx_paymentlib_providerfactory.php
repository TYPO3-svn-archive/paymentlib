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

require_once(t3lib_extMgm::extPath('paymentlib').'lib/class.tx_paymentlib_providerproxy.php');

final class tx_paymentlib_providerfactory {

	private static $instance = FALSE;				// Holds an instance of this class
	private $providerProxyObjects = array();		// An array of proxy objects, each pointing to a registered provider object

	/**
	 * This constructor is private because you may only instantiate this class by calling
	 * the function getInstance() which returns a unique instance of this class (Singleton).
	 * 
	 * @return		void
	 * @access		private
	 */
	private function __construct() {
	}

	/**
	 * Returns a unique instance of this class. Call this function instead of creating a new
	 * instance manually!
	 * 
	 * @return		object		Unique instance of tx_paymentlib_factory
	 * @access		public
	 */
	public function getInstance() {
		if (self::$instance === FALSE) {
			self::$instance = new tx_paymentlib_providerfactory;	
		}
		return self::$instance;	
	} 
	
	/**
	 * Registers the given class as a payment provider (concrete product). This method will
	 * be called by the provider implementation itself, usually from  ext_tables.php.
	 *
	 * @param		string		$className: Class name of the payment implementation.
	 * @return		mixed		Proxied instance of the given class or FALSE if an error occurred.
	 * @access		public
	 */
	public function registerProviderClass ($className) {
		if (class_exists ($className)) {		
			$this->providerProxyObjects[$className] = new tx_paymentlib_providerproxy ($className);
			return $this->providerProxyObjects[$className];
		}
		return FALSE;
	}

	/**
	 * Returns an array of instantiated payment implementations wrapped by a proxy
	 * object. We use this proxy as a smart reference: All function calls and access
	 * to variables are redirected to the real provider object but in some cases
	 * some additional operation is done. 
	 * 
	 * @return		array		Array of payment implementations (objects)
	 * @access		public
	 */
	public function getProviderObjects() {
		return $this->providerProxyObjects;
	}
	
	/**
	 * Returns instance of the payment implementations (wrapped by a proxy
	 * object) which offers the specified payment method. 
	 * 
	 * @param		string		$paymentMethod: Payment method key
	 * @return		mixed		Reference to payment proxy object or FALSE if no matching object was found
	 * @access		public
	 */
	public function getProviderObjectByPaymentMethod($paymentMethod) {
		if (is_array ($this->providerProxyObjects)) {
			foreach ($this->providerProxyObjects as $providerClass => $providerProxyObject) {
				$paymentMethodsArr = $providerProxyObject->getAvailablePaymentMethods();
				if (array_key_exists ($paymentMethod, $paymentMethodsArr)) {
					return $providerProxyObject;
				}
			}	
		}
		return FALSE;
	}

	/**
	 * Returns an array of transaction records which match the given extension key
	 * and optionally the given extension reference string and or booking status. 
	 * Use this function instead accessing the transaction records directly.
	 * 
	 * @param		string		$extKey: Extension key
	 * @param		string		$extRef: (optional) Filter by extension reference string 
	 * @param		string		$status: (optional) Filter by status of the transaction ('booked' or 'failed')
	 * @return		array		Array of transaction records, FALSE if no records where found or an error occurred.
	 * @access		public
	 */
	public function getTransactionsByExtKey ($extKey, $extRef=NULL, $status=NULL) {
		global $TYPO3_DB;
		
		$transactionsArr = FALSE;
		$additionalWhere = ''; 

		$additionalWhere .= (isset ($extRef)) ? ' AND extreference="'.$extRef.'"' : '';
		$additionalWhere .= (isset ($status)) ? ' AND status="'.$status.'"' : '';

		$res = $TYPO3_DB->exec_SELECTquery (
			'*',
			'tx_paymentlib_transactions',
			'extkey="'.$extKey.'"'.$additionalWhere,
			'',
			'crdate DESC'
		);	

		if ($res && $TYPO3_DB->sql_num_rows ($res)) {
			$transactionsArr = array();
			while ($row = $TYPO3_DB->sql_fetch_assoc ($res)) {
				$row['remotemessages'] = unserialize ($row['remotemessages']);
				$transactionsArr[$row['uid']] = $row;
			}
		}
		return $transactionsArr;
	}	

	/**
	 * Returns a single transaction record which matches the given uid
	 * 
	 * @param		integer		$uid: UID of the transaction
	 * @access		public
	 */
	public function getTransactionByUid ($uid) {
		global $TYPO3_DB;
		
		$res = $TYPO3_DB->exec_SELECTquery (
			'*',
			'tx_paymentlib_transactions',
			'uid='.$uid
		);	

		if (!$res || !$TYPO3_DB->sql_num_rows ($res)) return FALSE;

		$row = $TYPO3_DB->sql_fetch_assoc ($res);
		$row['remotemessages'] = unserialize ($row['remotemessages']);
		return $row;
	}	
}

?>