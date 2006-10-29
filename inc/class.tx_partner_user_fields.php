<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 David Bruehlmeier (typo3@bruehlmeier.com)
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
* Class for getting values for user-defined fields in reports.
* These fields are configured in $TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields']
* and are called as userFunc's.
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');


class tx_partner_user_fields {


	/**
	 * Gets the value for the field tx_partner_main._name
	 *
	 * The value is based on the partner type (person/organisation):
	 * - Person: Last Name + First Name
	 * - Organisation: Org Name
	 *
	 * @param	array		$params: Parameters configured for the user-field in $TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields']
	 * @param	object		$partner: Current Partner
	 * @return	string		Value for the field
	 */
	function name($params, $partner)		{

		if ($partner['type'] == 0)		{
				// Person
			$value = $partner['last_name'].' '.$partner['first_name'];
		} else {
				// Organisation
			$value = $partner['org_name'];
		}

		return $value;
	}


	/**
	 * Gets the value for the field tx_partner_main._age
	 *
	 * The value is calculated as a difference from the date in the field tx_partner_main.birth_date
	 * and the current date. It depends on the value stored in tx_partner_main.birth_date being in the format YYYYMMDD
	 *
	 * @param	array		$params: Parameters configured for the user-field in $TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields']
	 * @param	object		$partner: Current Partner
	 * @return	string		Value for the field
	 */
	function age($params, $partner)		{

			// Get the birth date as an array
		$birthDate = tx_partner_div::getDateArray($partner['birth_date']);

			// Calculate the age
		if (is_array($birthDate))		{
			$value = date('Y') - $birthDate['year'];
		}

		return $value;
	}
	
	
	/**
	 * Gets the value for the field tx_partner_contact_info._prefix
	 *
	 * The value is determined from static_countries
	 *
	 * @param	array		$params: Parameters configured for the user-field in $TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields']
	 * @param	object		$contactInfo: Current ContactInfo
	 * @return	string		Value for the field
	 */
	function prefix($params, $contactInfo)		{
		$out = '';

			// Check if there is a country in the contact-info
		if (!$contactInfo['country']) return false;
		
			// Read database
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('cn_phone','static_countries','uid='.$contactInfo['country']);
		if ($res) $rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (is_array($rec)) $out = $rec['cn_phone'];
		
		return $out;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_user_fields.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_user_fields.php']);
}

?>