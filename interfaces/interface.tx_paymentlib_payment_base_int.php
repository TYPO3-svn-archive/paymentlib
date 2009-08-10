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
 * The base interface for all payment methods. All payment methods
 * have to implement this interface to ensure type settings for 
 * the used payment method.
 */

interface tx_paymentlib_payment_base_int {
	
	// Payment method types
	const TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZEANDTRANSFER = 200;
	const TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZE = 201;
	const TX_PAYMENTLIB_TRANSACTION_ACTION_TRANSFER = 202;
	const TX_PAYMENTLIB_TRANSACTION_ACTION_REAUTHORIZEANDTRANSFER = 203;
	const TX_PAYMENTLIB_TRANSACTION_ACTION_REAUTHORIZE = 204;
	const TX_PAYMENTLIB_TRANSACTION_ACTION_CANCELAUTHORIZED = 205;
	const TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZEREFUND = 210;
	const TX_PAYMENTLIB_TRANSACTION_ACTION_AUTHORIZEANDTRANSFERREFUND = 211;
	
}

?>