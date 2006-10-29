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
* API functions for reading/writing/processing contact-information
* data.
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

	// Needed to make the script run even under FE conditions
require_once(PATH_t3lib.'class.t3lib_befunc.php');


/**
 * This class contains functions needed to read contact-infos from the 'partner'-extension.
 * Please use only this class to access contact-info records, as this
 * represents the official API.
 *
 */
class tx_partner_contact_info {
	
	var $countryToIso = array('country'); // Fields with country codes to be converted to ISO-codes


	/**
	 * Constructor of the class. Loads all configurations values needed to run.
	 *
	 * @return	void
	 */
	function tx_partner_contact_info()		{


	}



	/*********************************************
 	*
 	* READING DATA
 	*
 	**********************************************


	/**
	 * Reads a contact information and writes the data in $this->data
	 *
	 * @param	integer		$uid: UID of the contact information
	 * @return	void
	 */
	function getContactInfo($uid)		{
		global $TYPO3_CONF_VARS;

		if ($uid)		{
			$rec = array();

				// Read database
			$where = 'tx_partner_contact_info.uid='.$uid.t3lib_BEfunc::deleteClause('tx_partner_contact_info');
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_contact_info', $where);
			if ($res)		{
				$rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			}
			
				// Get the user-defined fields
			if (is_array($TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields']['tx_partner_contact_info']))		{
				foreach ($TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields']['tx_partner_contact_info'] as $field => $params)		{
					if ($params['userFunc'])		{
						$rec[$field] = t3lib_div::callUserFunction($params['userFunc'], $params, $rec, '');
					}
				}
			}
			
				// Replace internal values from static_info_tables with ISO-codes
			/*
			foreach ($rec as $k=>$v)		{
				if (in_array($k, $this->countryToIso)) $rec[$k] = tx_partner_div::convertToIso('static_countries', $v);
			}
			*/

				// Write the record into the class variable
			$this->data = $rec;
			
			
		}
	}

	/*********************************************
 	*
 	* PROCESSING DATA
 	*
 	*********************************************/

	/**
	 * @return	void
	 */
	function insertContactInfo()		{


	}


	/**
	 * @return	void
	 */
	function updateContactInfo()		{


	}


	/**
	 * @return	void
	 */
	function deleteContactInfo()		{


	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_contact_info.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_contact_info.php']);
}

?>
