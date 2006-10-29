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
* API functions for reading/writing/processing relationships between partners.
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_main.php');

	// Needed to make the script run under FE conditions
require_once(PATH_t3lib.'class.t3lib_befunc.php');


/**
 * This class contains functions needed to work with relationship records from the 'partner'-extension.
 * Please use only this class to access partner records, as this
 * represents the official API.
 *
 */
class tx_partner_relationship {

	/**
	 * Constructor of the class. Loads all configurations values needed to run.
	 *
	 * @return	void
	 */
	function tx_partner_relationship()		{


	}



	/*********************************************
 	*
 	* READING DATA
 	*
 	*********************************************/


	/**
	 * Reads a relationship and writes the data in $this->data
	 *
	 * @param	integer		$uid: UID of relationship record
	 * @return	void
	 */
	function getRelationship($uid)		{

		if ($uid)		{

				// Make WHERE clause
			$where = 'uid='.$uid;
			$where.= t3lib_BEfunc::deleteClause('tx_partner_relationships');

				// Read database
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_relationships', $where);

				// Get data and write it into $this->data
			if ($res)		{
				$this->data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			}
		}
	}


	/*********************************************
 	*
 	* PROCESSING DATA
 	*
 	*********************************************/

	/**
	 * Creates a new relationship. This is the data that must be provided as an array in $insertArray
	 *
	 * $insertArray = array(
	 * 		'pid' => '',				// Mandatory: PID where to create the record
	 * 		'hidden' => '',				// Optional: Set to '1' if you want to create a hidden record
	 * 		'type' => '',				// Mandatory: Valid UID from tx_partner_val_rel_types
	 * 		'uid_primary' => '',		// Mandatory: UID of the primary partner record
	 * 		'uid_secondary' => '',		// Mandatory: UID of the secondary partner record
	 * 		'status' => '',				// Mandatory: Valid UID from tx_partner_val_status
	 * 		'established_date' => '',	// Optional: Date when the relationship was established
	 * 		'lapsed_date' => '',		// Optional: Date when the relationship lapsed
	 * 		'lapsed_reason' => '',		// Optional: Reason (freetext) why the relationship lapsed
	 * );
	 *
	 * @param	array		$insertArray: Array with all fields to create the new relationship. Structure as above.
	 * @return	integer		UID of the new relationship
	 */
	function insertRelationship($insertArray)		{
		$out = '';

			// Formal valudation (check all mandatory fields and check for formally correct values)
		if (empty($insertArray['pid'])) return false;
		if (!is_array(tx_partner_div::getValRecord('tx_partner_val_rel_types', $insertArray['type']))) return false;
		if (empty($insertArray['uid_primary'])) return false;
		if (empty($insertArray['uid_secondary'])) return false;
		if (!is_array(tx_partner_div::getValRecord('tx_partner_val_status', $insertArray['status']))) return false;

			// Material validation of the relationship type. If the relationship is not allowed, don't insert anything and return false
		if (!tx_partner_div::checkRelationship($insertArray['uid_primary'], $insertArray['uid_secondary'], $insertArray['type'])) return false;

			// Material validation of the dates. If the lapsed date is before the established date, unset both date fields
		if (isset($insertArray['established_date']) and isset($insertArray['lapsed_date']))		{
			if ($insertArray['lapsed_date'] < $insertArray['established_date'])		{
				unset ($insertArray['established_date']);
				unset ($insertArray['lapsed_date']);
			}
		}

			// Add technical data
		$insertArray['tstamp'] = time();
		$insertArray['crdate'] = time();
		$insertArray['cruser_id'] = $GLOBALS['BE_USER']->user['uid'];

		$data['tx_partner_relationships']['NEW'] = $insertArray;

		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->stripslashes_values=0;
		$tce->start($data,Array());
		$tce->process_datamap();

		$out = $tce->substNEWwithIDs['NEW'];

		return $out;

	}


	/**
	 * Updates the current relationship record. The relationship must already be available in $this->data (load it with
	 * $this->getRelationship() first).
	 *
	 * This is the data which can be updated by populating $changeArray:
	 * 	$changeArray = array(
	 * 		'hidden' => '',					// Boolean (0 = not hidden, 1 = hidden)
	 * 		'type' => '',					// Valid UID from tx_partner_val_rel_type. The type must be allowed for the selected type.
	 * 		'uid_primary' => '',			// Valid UID from tx_partner_main. This is the primary partner in the relationship. Must be of a type which fits the relationship type.
	 * 		'uid_secondary' => '',			// Valid UID from tx_partner_main. This is the secondary partner in the relationship. Must be of a type which fits the relationship type.
	 * 		'status' => '',					// Valid UID from tx_partner_val_status.
	 * 		'established_date' => '',		// Unix-Timestamp
	 * 		'lapsed_date' => '',			// Unix-Timestamp
	 * 		'lapsed_reason' => '',			// String
	 * 	);
	 *
	 * All data is validated. If a field contains an invalid value, the correctly filled fields are updated nevertheless.
	 * Exceptions are the type and the type. If the type does not match the types of the primary and the secondary partner
	 * Or if the primary and the secondary partner cannot be related by the chosen type, the function will not update
	 * anything and will return false.
	 *
	 * @param	array		$changeArray: Array with all fields to update. Structure as above.
	 * @return	boolean		True if update was succesful.
	 */
	function updateRelationship($changeArray)		{

			// The relationship must already be loaded
		if (!is_array($this->data)) return false;

			// Formal checks of the data to be changed
		if (is_array($changeArray))		{
			if ($changeArray['hidden'] === 0) $f['hidden'] = 0;
			if ($changeArray['hidden'] === 1) $f['hidden'] = 1;
			if (strpos('0123', $changeArray['type']) !== false) $f['type'] = $changeArray['type'];
			if (isset($changeArray['type']) and is_array(tx_partner_div::getValRecord('tx_partner_val_rel_types', $changeArray['type']))) $f['type'] = $changeArray['type'];
			if ($changeArray['uid_primary'])		{
				$primaryPartner = t3lib_div::makeInstance('tx_partner_main');
				$primaryPartner->getPartner($changeArray['uid_primary']);
				if (is_array($primaryPartner->data)) $f['uid_primary'] = $changeArray['uid_primary'];
			}
			if ($changeArray['uid_secondary'])		{
				$secondaryPartner = t3lib_div::makeInstance('tx_partner_main');
				$secondaryPartner->getPartner($changeArray['uid_secondary']);
				if (is_array($secondaryPartner->data)) $f['uid_secondary'] = $changeArray['uid_secondary'];
			}
			if (isset($changeArray['status']) and is_array(tx_partner_div::getValRecord('tx_partner_val_status', $changeArray['status']))) $f['status'] = $changeArray['status'];
			if (isset($changeArray['established_date']) and $changeArray['established_date'] == 0) $f['established_date'] = 0;
			if ($changeArray['established_date'] > 0 and $changeArray['established_date'] <= 2147483647) $f['established_date'] = $changeArray['established_date'];
			if (isset($changeArray['lapsed_date']) and $changeArray['lapsed_date'] == 0) $f['lapsed_date'] = 0;
			if ($changeArray['lapsed_date'] > 0 and $changeArray['lapsed_date'] <= 2147483647) $f['lapsed_date'] = $changeArray['lapsed_date'];
			if (isset($changeArray['lapsed_reason'])) $f['lapsed_reason'] = $changeArray['lapsed_reason'];
		}

		if (is_array($f))		{

				// Material validation of the relationship type
			if (isset($f['type']))		{

					// If the partner needs to be changed, take the new partner, otherwise take the saved partner
				$uidPrimaryPartner = $f['uid_primary'] ? $f['uid_primary'] : $this->data['uid_primary'];
				$uidSecondaryPartner = $f['uid_secondary'] ? $f['uid_secondary'] : $this->data['uid_secondary'];

					// If the relationship is not allowed, don't update anything and return false
				if (!tx_partner_div::checkRelationship($uidPrimaryPartner, $uidSecondaryPartner, $f['type'])) return false;
			}

				// Material validation of the date fields (lapsed must be greater than established
			if (isset($f['established_date']) or isset($f['lapsed_date']))		{

					// If the date needs to be changed, take the new dates, otherwise take the saved dates
				$establishedDate = isset($f['established_date']) ? $f['established_date'] : $this->data['established_date'];
				$lapsedDate = isset($f['lapsed_date']) ? $f['lapsed_date'] : $this->data['lapsed_date'];
				if ($lapsedDate == 0) $lapsedDate = 2147483647;

					// If the lapsed date is before the established date, unset both date fields
				if ($lapsedDate < $establishedDate)		{
					if (isset($f['established_date'])) unset ($f['established_date']);
					if (isset($f['lapsed_date'])) unset ($f['lapsed_date']);
				}
			}

				// Add technical fields
			$f['uid'] = $this->data['uid'];
			$f['tstamp'] = time();

				// Update database
			$data['tx_partner_relationships'][$this->data['uid']] = $f;
			$tce = t3lib_div::makeInstance('t3lib_TCEmain');
			$tce->stripslashes_values=0;
			$tce->start($data,Array());
			$tce->process_datamap();

				// Update $this->data
			$this->getRelationship($this->data['uid']);
		}
	}


	/**
	 * Deletes the current relationship. The relationship must already be available in $this->data (load it with
	 * $this->getRelationship() first).
	 *
	 * @return	void
	 */
	function deleteRelationship()		{

			// The relationship must already be loaded
		if (!is_array($this->data)) return false;

			// Update database
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->stripslashes_values=0;
		$tce->start(array(),array());
		$tce->deleteRecord('tx_partner_relationships', $this->data['uid'], false);

			// Update $this->data
		$this->getRelationship($this->data['uid']);
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_relationship.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_relationship.php']);
}

?>