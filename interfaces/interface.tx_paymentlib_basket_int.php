<?php

require_once(t3lib_extMgm::extPath('paymentlib') . 'interfaces/interface.tx_paymentlib_basket_item_int.php');

/***************************************************************
* $Id$
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
interface tx_paymentlib_basket_int extends Iterator {
		
	public function getTotal();
	
	public function setTotal(tx_paymentlib_price_info_int $totalAmount);
	
	public function getBasketItemByArticleNumber($articleNumber);
	
	public function setBasketItem(tx_paymentlib_basket_item_int $item);
	
	public function getBasketItemsByCategory($category);
	
	public function getAllBasketItems();
	
	public function __toString();
	
	public function __toArray();
	
}

?>