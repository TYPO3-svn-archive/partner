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
* API functions for reading/writing/processing partner-data
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/


	// Needed to make the script run under FE conditions
require_once(PATH_t3lib.'class.t3lib_befunc.php');


/**
 * This class contains functions needed to read data from the 'partner'-extension.
 * Please use only this class to access partner records, as this
 * represents the official API.
 *
 */
class tx_partner_main {

	var $countryToIso = array('country', 'po_country', 'nationality'); // Fields with country codes to be converted to ISO-codes
	var $languageToIso = array('mother_tongue', 'preferred_language'); // Fields with language codes to be converted to ISO-codes
	

	/**
	 * Constructor of the class. Loads all configurations values needed to run.
	 *
	 * @return	void
	 */
	function tx_partner_main()		{


	}



	/*********************************************
 	*
 	* READING DATA
 	*
 	*********************************************/


	/**
	 * Gets a partner and writes the data into the class variable $this->data
	 *
	 * @param	integer		$uid: UID of the partner the get
	 * @return	void
	 */
	function getPartner($uid)		{
		global $TYPO3_CONF_VARS;

		if ($uid)		{
			$rec = array();
			
				// Read database
			$where = 'tx_partner_main.uid='.$uid.t3lib_BEfunc::deleteClause('tx_partner_main');
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_main', $where);
			if ($res)		{
				$rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			}

				// Get the user-defined fields
			if (is_array($TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields']['tx_partner_main']))		{
				foreach ($TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields']['tx_partner_main'] as $field => $params)		{
					if ($params['userFunc'])		{
						$rec[$field] = t3lib_div::callUserFunction($params['userFunc'], $params, $rec, '');
					}
				}
			}
			
				// Replace internal values from static_info_tables with ISO-codes
			/*
			foreach ($rec as $k=>$v)		{
				if (in_array($k, $this->countryToIso)) $rec[$k] = tx_partner_div::convertToIso('static_countries', $v);
				if (in_array($k, $this->languageToIso)) $rec[$k] = tx_partner_div::convertToIso('static_languages', $v);
			}
			*/

				// Write the record into the class variable
			$this->data = $rec;
		}
	}


	/**
	 * Reads the contact information for the current partner. The result will be made available as an array of contact-info objects
	 * in $this->contactInfo.
	 *
	 * 1 = Only contact info marked as 'standard'
	 * 2 = All contact info
	 *
	 * @param	integer		$scope: Scope for the reading the contact info (optional, default: 2=all)
	 * @return	void
	 */
	function getContactInfo($scope=2)		{

			// Check if a partner has already been loaded
		if (!is_array($this->data)) return false;

			// Get only the standard contact info
		if ($scope == 1)		{
			$this->contactInfo = tx_partner_div::getContactInfo($this->data['uid'], 1);
		}

			// Get all the contact info
		if ($scope == 2)		{
			$this->contactInfo = tx_partner_div::getContactInfo($this->data['uid'], 2);
		}
	}


	/**
	 * Reads the relationships for the current partner. The result will be made available as an array of relationship objects,
	 * $this->relationshipsAsPrimary for relationships where the current partner is the primary partner and in
	 * $this->relationshipsAsSecondary for relationships where the current partner is the secondary partner.
	 *
	 * The $scope can be set as follows:
	 * 1 = Only relationships where the current partner is the PRIMARY partner (result in $this->relationshipsAsPrimary)
	 * 2 = Only relationships where the current partner is the SECONDARY partner (result in $this->relationshipsAsSecondary)
	 * 3 = All relationships (result in $this->relationshipsAsPrimary and $this->relationshipsAsSecondary)
	 *
	 * @param	integer		$scope: Scope for the reading the relationships (optional, default: 3=all)
	 * @param	string		$restrictToRelationshipTypes: If you want the result to be restricted to certain relationship types, you can provide a comma-separated list with all allowed relationship types here
	 * @return	void
	 */
	function getRelationships($scope=3, $restrictToRelationshipTypes='')		{

			// Check if a partner has already been loaded
		if (!is_array($this->data)) return false;

			// Get the relationships as MAIN partner
		if ($scope == 1 or $scope == 3)		{
			$this->relationshipsAsPrimary = tx_partner_div::getRelationships($this->data['uid'], 0, $restrictToRelationshipTypes);
			if ($this->relationshipsAsPrimary)	{
				foreach ($this->relationshipsAsPrimary as $theRelationship)	{
					$this->relatedPartnerAsPrimary[$theRelationship->data['uid_secondary']] = t3lib_div::makeInstance('tx_partner_main');
					$this->relatedPartnerAsPrimary[$theRelationship->data['uid_secondary']]->getPartner($theRelationship->data['uid_secondary']);
				}
			}
		}

		if ($scope == 2 or $scope == 3)		{
			$this->relationshipsAsSecondary = tx_partner_div::getRelationships($this->data['uid'], 1, $restrictToRelationshipTypes);
			if ($this->relationshipsAsSecondary)	{
				foreach ($this->relationshipsAsSecondary as $theRelationship)	{
					$this->relatedPartnerAsSecondary[$theRelationship->data['uid_primary']] = t3lib_div::makeInstance('tx_partner_main');
					$this->relatedPartnerAsSecondary[$theRelationship->data['uid_primary']]->getPartner($theRelationship->data['uid_primary']);
				}
			}
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
	function insertPartner()		{


	}


	/**
	 * @return	void
	 */
	function updatePartner()		{

	}


	/**
	 * @return	void
	 */
	function deletePartner()		{


	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_main.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_main.php']);
}

?>