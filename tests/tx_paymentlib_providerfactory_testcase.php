<?php
/***************************************************************
* $Id$
*
*  Copyright notice
*
*  (c) 2005 Robert Lemke (robert@typo3.org)
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
 * @author	Robert Lemke <robert@typo3.org>
 */

require_once (t3lib_extMgm::extPath ('paymentlib').'/lib/class.tx_paymentlib_providerfactory.php');
require_once (t3lib_extMgm::extPath ('paymentlib').'/tests/fixtures/tx_paymentlib_provider_fixture.php');
require_once 'PHPUnit2/Framework/TestCase.php';

class tx_paymentlib_providerfactory_testcase extends PHPUnit2_Framework_TestCase {
	
	public function __construct ($name) {
		parent::__construct ($name);
	}

	public function test_registerAndGetProviderClass() {
		$fixtureProviderClassName = 'tx_paymentlibfixture_provider';
		$providerFactoryObj = tx_paymentlib_providerfactory::getInstance();
		$fixtureProviderObj = $providerFactoryObj->registerProviderClass ($fixtureProviderClassName); 
		self::assertTrue ($fixtureProviderObj !== FALSE, 'Registering fixture provider class failed');
		
		$providerObjectsArr = $providerFactoryObj->getProviderObjects();
		self::assertTrue (is_array($providerObjectsArr), "getProviderObjects() should deliver an array but it didn't");
		
		self::assertSame ($fixtureProviderObj, $providerObjectsArr[$fixtureProviderClassName], "returned instance of $fixtureProviderClassName is not the same as the instance returne while registering the provider class");
	}

	public function test_malEinTest()  {
		self::assertTrue (1==2, 'Eins ist nicht gleich eins');	
	}
}

?>