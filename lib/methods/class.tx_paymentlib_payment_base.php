<?php

require_once(t3lib_extMgm::extPath('paymentlib') . 'interfaces/interface.tx_paymentlib_payment_base_int.php');

/***************************************************************
* $Id: class.tx_paymentlib_base_payment.php 23190 2009-08-08 17:47:48Z franzholz $
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
* 
*  This class is the base class for all payment methods of the 
*  paymentlib.
***************************************************************/

abstract class tx_paymentlib_payment_base implements tx_paymentlib_payment_base_int {
	
	private $type; 	// Holds the type of payment; One of the type parameters
					// from interface tx_paymentlib_base_payment_int
	

	/**
	 * Returns the type one of the payment method types defined
	 * in interface tx_paymentlib_base_payment_int.
	 * 
	 * @return unknown
	 * 
	 * @see tx_paymentlib_base_payment_int
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Sets the type of the payment method defined in interface
	 * tx_paymentlib_base_payment_int
	 * 
	 * @param unknown_type $type
	 * 
	 * @see tx_paymentlib_base_payment_int
	 */
	public function setType($type) {
		$this->type = $type;
	}


}

?>