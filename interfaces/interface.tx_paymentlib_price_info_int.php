<?php

/***************************************************************
* $Id: interface.tx_paymentlib_basket_int.php 23190 2009-08-08 17:47:48Z franzholz $
*
*  Copyright notice
*
*  (c) 2009 Franz Holzinger (franz@ttproducts.de)
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
 * Abstract class defining the interface for basket implementations.
 *
 * All implementations which want to use a basket must implement this interface
 *
 * @package 	TYPO3
 * @subpackage	tx_paymentlib
 * @version		1.1.0
 * @author		Udo Gerhards, Udo.Gerhards@gerhards.eu
 */

interface tx_paymentlib_price_info_int extends Serializable {
	
	public function getPriceShippingHandlingIncluded();
	
	public function setPriceShippingHandlingIncluded($priceShippingHandlingIncluded);
	
	public function getPrice();
	
	public function setPrice($price);
	
	public function getTaxValue();
	
	public function setTaxValue($taxValue);
	
	public function getTaxPercentage();
	
	public function setTaxPercentage($taxPercentage);
	
	public function getHandlingValue();
	
	public function setHandlingValue($handlingValue);
	
	public function getHandlingPercentage();
	
	public function setHandlingPercentage($handlingPercentage);
	
	public function getNumericalCalculationBase();
	
	public function setNumericalCalculationBase($numericalCalculationBase);
	
	public function __toString();
	
	public function __toArray();
}

?>