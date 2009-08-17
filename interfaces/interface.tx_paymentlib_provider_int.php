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

// TODO: Place this constants in their respective classes

require_once (t3lib_extMgM::extPath('paymentlib') . 'interfaces/interface.tx_paymentlib_provider_base_int.php');

// Payment types
define('TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZEANDTRANSFER', 200);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZE', 201);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_TRANSFER', 202);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_REAUTHORIZEANDTRANSFER', 203);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_REAUTHORIZE', 204);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_CANCELAUTHORIZED', 205);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZEREFUND', 210);
define('TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZEANDTRANSFERREFUND', 211);

// Transaction results
define('TX_PAYMENTLIB_TRANSACTION_RESULT_APPROVED', 300);
define('TX_PAYMENTLIB_TRANSACTION_RESULT_DECLINED', 301);
define('TX_PAYMENTLIB_TRANSACTION_RESULT_FRAUD', 302);
define('TX_PAYMENTLIB_TRANSACTION_RESULT_DUPLICATE', 303);
define('TX_PAYMENTLIB_TRANSACTION_RESULT_OTHER', 310);

// Transaction states
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
 * @version		1.1.0
 * @author		Robert Lemke <robert@typo3.org>
 *
 * @deprecated 	since 01.08.2009 because of introducing a transaction object
 *             	to handle transactions in future.
 *             	Please use the new class <pre>tx_paymentlib_object_provider</pre>
 * 			 	as base for new paymentlibs in future
 */
interface tx_paymentlib_provider_int extends tx_paymentlib_provider_base_int {



	public function createUniqueID ($orderuid, $callingExtension);

	public function clearErrors ();

	public function addError ($error);

	public function hasErrors ();

	public function getErrors ();

	public function usesBasket ();

}

?>