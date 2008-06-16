<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2005 David Bruehlmeier (typo3@bruehlmeier.com)
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
* Class with helper functions used in various places within this
* extension. It is encouraged to use these functions in extensions
* of this extension as well!
*
* Note: Some functions cannot be used under FE-conditions. Please
* refer to the documentation of the functions.
*
* Use non-instantiated, e.g. tx_partner_div::getMergedFieldVisibilities()
*
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/


require_once(PATH_t3lib.'class.t3lib_iconworks.php');
require_once(PATH_t3lib.'class.t3lib_befunc.php');
require_once(PATH_t3lib.'class.t3lib_tcemain.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_lang.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_contact_info.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_relationship.php');


class tx_partner_div {

	/*********************************************
 	*
 	* READING DATA
 	*
 	*********************************************/
 	
 

	/**
	 * Get the number of partners that are assigned to a specific fe_user.
	 *
	 * @param	integer		$feUserUid: UID of the fe_user for which the number of partners shall be counted
	 * @return	integer		Number of partners found for the fe_user
	 */
	function getPartnerCountByFeUser($feUserUid)		{

		if ($feUserUid)		{
				// Get the number of partners for which the current FE user is assigned
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*)', 'tx_partner_main', 'fe_user='.$feUserUid);
			if ($res) $count = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}

			// Return the result
		if (is_array($count))		{
			return current($count);
		} else {
			return 0;
		}

	}

	/**
	 * Get all allowed relationship types for the partner type in the requested PID.
	 * The allowed relationship types are returned as an array.
	 *
	 * This is the 'sister'-function to tx_partner_div::getAllowedPartnerTypes().
	 *
	 * @param	integer		$pid: PID of the folder with the relationship-types records
	 * @param	integer		$partnerType: Type of the partner (0 = Person, 1 = Organization)
	 * @param	integer		$primaryOrSecondary: Indicates if the partner type comes from the primary partner (=0) or from the secondary partner (=1)
	 * @return	array		Array with all possible relationship types
	 * @see tx_partner_div::getAllowedPartnerTypes()
	 */
	function getAllowedRelationshipTypes($pid, $partnerType, $primaryOrSecondary=0)		{

			// Determine the allowed categories
		if ($primaryOrSecondary == 0)		{
			if ($partnerType == 0) $allowedCategories = array(0,1);		// Person-Person and Person-Organization
			if ($partnerType == 1) $allowedCategories = array(2,3);		// Organization-Person and Organization-Organization
		} else {
			if ($partnerType == 0) $allowedCategories = array(0,2);		// Person-Person and Organization-Person
			if ($partnerType == 1) $allowedCategories = array(1,3);		// Person-Organisation and Organization-Organization
		}

			// Read the allowed relationship types from the database
		$listQuery = ' AND ('.$GLOBALS['TYPO3_DB']->listQuery('allowed_categories', $allowedCategories[0], 'tx_partner_val_rel_types');
		$listQuery.= ' OR '.$GLOBALS['TYPO3_DB']->listQuery('allowed_categories', $allowedCategories[1], 'tx_partner_val_rel_types').')';
		$confArr = unserialize($GLOBALS['_EXTCONF']);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_val_rel_types', ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'pid='.$pid : '1=1').$listQuery);

		if (is_resource($res))		{
			while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))		{
				$allowedRelationshipTypes[$rec['uid']] = $rec;
			}
		}

		return $allowedRelationshipTypes;
	}

	/**
	 * Get the allowed partner types for a relationship type. This is the 'sister'-function to tx_partner_div::getAllowedRelationshipTypes().
	 *
	 * @param	integer		$uid: UID of relationship type record
	 * @param	integer		$mainOrSecondary: Indicates if the partner will be main (=0) or secondary (=1) in the relationship
	 * @return	array		Array with the allowed partner types (0 = Person, 1 = Organization)
	 * @see tx_partner_div::getAllowedRelationshipTypes()
	 */
	function getAllowedPartnerTypes($uid, $mainOrSecondary=0)		{

		if ($uid)		{
				// Get the relationship type record
			$rec = tx_partner_div::getValRecord('tx_partner_val_rel_types', $uid);

				// Determine the allowed partner-types
			if (is_array($rec))		{
				if ($mainOrSecondary == 0)		{
					if (strpos($rec['allowed_categories'], '0') !== false) $allowedPartnerType[0] = 0;		// Person-Person
					if (strpos($rec['allowed_categories'], '1') !== false) $allowedPartnerType[1] = 1;		// Person-Organization
					if (strpos($rec['allowed_categories'], '2') !== false) $allowedPartnerType[0] = 0;		// Organization-Person
					if (strpos($rec['allowed_categories'], '3') !== false) $allowedPartnerType[1] = 1;		// Organization-Organization
				} else {
					if (strpos($rec['allowed_categories'], '0') !== false) $allowedPartnerType[0] = 0;		// Person-Person
					if (strpos($rec['allowed_categories'], '1') !== false) $allowedPartnerType[0] = 0;		// Person-Organization
					if (strpos($rec['allowed_categories'], '2') !== false) $allowedPartnerType[1] = 1;		// Organization-Person
					if (strpos($rec['allowed_categories'], '3') !== false) $allowedPartnerType[1] = 1;		// Organization-Organization
				}
			}
		}

		return $allowedPartnerType;
	}

	/**
	 * Checks if it is allowed to join to partners by a certain relationship type.
	 *
	 * @param	integer		$uidPrimaryPartner: UID of the primary partner
	 * @param	integer		$uidSecondaryPartner: UID of the secondary partner
	 * @param	integer		$uidRelationshipType: UID of the relationship type record
	 * @return	boolean		Returns true if the partners are allowed for the relationship, otherwise false
	 */
	function checkRelationship($uidPrimaryPartner, $uidSecondaryPartner, $uidRelationshipType)		{

			// Get the partners
		$primaryPartner = t3lib_div::makeInstance('tx_partner_main');
		$primaryPartner->getPartner($uidPrimaryPartner);
		$secondaryPartner = t3lib_div::makeInstance('tx_partner_main');
		$secondaryPartner->getPartner($uidSecondaryPartner);

			// Get the allowed partner types
		$allowedPartnerTypesForPrimary = tx_partner_div::getAllowedPartnerTypes($uidRelationshipType, 1);
		$allowedPartnerTypesForSecondary = tx_partner_div::getAllowedPartnerTypes($uidRelationshipType, 0);

			// Check if the primary partner is of an allowed partner-type for this relationship
		if (is_array($allowedPartnerTypesForPrimary))		{
			if (!in_array($primaryPartner->data['type'], $allowedPartnerTypesForPrimary)) return false;
		}

			// Check if the secondary partner is of an allowed partner-type for this relationship
		if (is_array($allowedPartnerTypesForSecondary))		{
			if (!in_array($secondaryPartner->data['type'], $allowedPartnerTypesForSecondary)) return false;
		}

			// If we got this far, the relationship is allowed for both partner types
		return true;
	}

	/**
	 * Get the contact info for a partner. The partner must be identified with its UID. You can also determine if you
	 * want only the standard contact info ($scope = 1) or all contact info ($scope = 2). The found contact info will
	 * be returned as an array of contact-info objects.
	 *
	 * @param	integer		$uid: UID of the partner
	 * @param	integer		$scope: Only standard (=1) or all contact info (=2, default)
	 * @param	integer		$type: Only get the contact info of a certain type (optional). 0=Phone, 1=Mobile Phone, 2=Fax, 3=E-Mail, 4=URL
	 * @return	array		Array of all found contact-info objects
	 */
	function getContactInfo($uid, $scope=2, $type='')		{
		$out = '';
		$where = '';
		
			// Make the WHERE clause
		$where.= 'uid_foreign = '.intval($uid);
		if ($scope == 1) $where.= ' AND tx_partner_contact_info.standard';
		if ($type !== '') $where.= ' AND tx_partner_contact_info.type='.intval($type);
		$where.= t3lib_BEfunc::deleteClause('tx_partner_contact_info');

			// Read database
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_partner_contact_info', $where);

			// Get data
		if (is_resource($res))		{
			while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))		{
				$out[$rec['uid']] = t3lib_div::makeInstance('tx_partner_contact_info');
				$out[$rec['uid']]->getContactInfo($rec['uid']);
			}
		}
		
			// If only the standard contact-info from a certain type is requested, check if there really is only one contact-info found.
			// If more than one is found (should technically not happen...), return false
		if (is_array($out) and $scope == 1 and $type !== '')		{
			if (count($out) != 1) return false;
		}

		return $out;
	}

	/**
	 * Get the relationships for a partner. The partner must be identified with its UID and you must determine whether
	 * the partner is the PRIMARY or the SECONDARY partner in the relationship. The found relationships will be returned
	 * as an array of relationship objects.
	 *
	 * @param	integer		$uid: UID of the partner
	 * @param	integer		$primaryOrSecondary: Indicates if the partner is the main (=0) or the secondary (=1) partner in the relationship
	 * @param	string		$restrictToRelationshipTypes: If you want the result to be restricted to certain relationship types, you can provide a comma-separated list with all allowed relationship types here
	 * @return	array		Array of all found relationships objects
	 */
	function getRelationships($uid, $primaryOrSecondary, $restrictToRelationshipTypes='')		{
		$out = '';
		$where = '';

			// Make the WHERE clause
		$where = 'uid_primary = '.$uid;
		if ($primaryOrSecondary == 1) $where = 'uid_secondary = '.$uid;
		if ($restrictToRelationshipTypes) $where.= ' AND type IN ('.$restrictToRelationshipTypes.')';
		$where.= t3lib_BEfunc::deleteClause('tx_partner_relationships');

			// Read database
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_partner_relationships', $where);

			// Get data
		if ($res)		{
			while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))		{
				$out[$rec['uid']] = t3lib_div::makeInstance('tx_partner_relationship');
				$out[$rec['uid']]->getRelationship($rec['uid']);
			}
		}

		return $out;
	}

	/**
	 * Get the allowed status records for a specific table
	 *
	 * @param	integer		$pid: PID where the status record(s) are located
	 * @param	string		$table: Name of the table for which to get the status record(s)
	 * @return	array		Array with all allowed status records for the table
	 * @todo	Possible improvement: The relevant records should be directly selected by a proper SQL statement
	 */
	function getAllowedStatus($pid, $table)		{
		$out = '';

			// Get all status records
		$confArr = unserialize($GLOBALS['_EXTCONF']);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_val_status', ($confArr['lookupsFromCurrentPageOnly'] != 0 ? 'pid='.$pid : ''));
		if ($res)		{
			while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))		{
				$out[$rec['uid']] = $rec;
			}
		}

			// Sort out the status records which are allowed for the requested table
		foreach ($out as $k=>$v)		{
			if (!t3lib_div::inList($v['allowed_tables'], $table)) unset($out[$k]);
		}

			// Check if the final result is empty
		if (empty($out)) $out = '';

		return $out;
	}


	/**
	 * Checks if there exists at least one partner record for the requested PID.
	 *
	 * @param	integer		$pid: PID where to check for partner records
	 * @return	boolean		False if there are none, true if there is at least one partner record
	 */
	function checkPartnerRecordsExist($pid)		{
		$out = false;
		$deleteClause = t3lib_BEfunc::deleteClause('tx_partner_main');

			// Get max. one partner record. If found, return true.
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_main', 'pid='.$pid.$deleteClause, '', '', '1');
		if ($res) $rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (is_array($rec)) $out = true;

		return $out;
	}


	/**
	 * Get any tx_partner_val* record (such as tx_partner_val_status)
	 *
	 * @param	string		$table: Name of the tx_partner_val* table
	 * @param	integer		$uid: UID of the record to read from the table
	 * @return	array		Array with the record found
	 */
	function getValRecord($table, $uid)		{

		$allowedTables = array(
			'tx_partner_val_contact_permissions',
			'tx_partner_val_courses',
			'tx_partner_val_hobbies',
			'tx_partner_val_legal_forms',
			'tx_partner_val_marital_status',
			'tx_partner_val_occupations',
			'tx_partner_val_org_types',
			'tx_partner_val_rel_types',
			'tx_partner_val_religions',
			'tx_partner_val_status',
			'tx_partner_val_titles',
		);

		if (in_array($table, $allowedTables) && $uid) 		{

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, 'uid='.$uid);

			if ($res)		{
				$rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			}
		}

		return $rec;
	}


	/**
	 * Gets the merged field visibility settings for a partner-record.
	 * The function will get the default and the user-defined field visibility settings and then
	 * merge the values (i.e. the default values will be overwritten by user-defined values).
	 *
	 * The resulting array has the same structure as the stored array in
	 * tx_partner_main.field_visibilities and is sorted according to the
	 * list in $TCA[..table..]['interface']['showRecordFieldList']
	 *
	 * @param	integer		$partnerUid: The UID of the partner for which to get the merged field visibilities
	 * @return	array		Merged field visibilities, sorted according to showRecordFieldList in TCA
	 */
	function getMergedFieldVisibilities($partnerUid) {

			// Read the partner
		$partner = t3lib_BEfunc::getRecord('tx_partner_main',$partnerUid);

			// Get the default field visibility settings
		$defaultValues = tx_partner_div::getDefaultFieldVisibilities($partner['pid']);

			// Get user-defined field visibility settings
		if ($partner['field_visibility'])		{
			$userValues = t3lib_div::xml2array($partner['field_visibility']);
		}

		if (is_array($defaultValues) and is_array($userValues))		{
				// Merge default values and user values
			$out = array_merge($defaultValues,$userValues);
		} elseif (is_array($defaultValues))		{
				// No user values... return the default values
			$out = $defaultValues;
		}

		return $out;
	}


	/**
	 * Gets the default field visibility settings from TSconfig. The page from which the
	 * TSconfig parameters are fetched from is defined by the pid (which must be the sys-folder
	 * where the partner records are stored).
	 *
	 * The system will check if the settings in TSconfig are valid (i.e. configured fields in $TCA
	 * from tx_partner_main or tx_partner_contact_info and only allowed keywords). The resulting
	 * array will be in the order defined in TCA>interface>showRecordFieldList.
	 *
	 * @param	integer		$pid: The PID from which to fetch the default field visibility settings
	 * @return	array		Default field visibility settings
	 */
	function getDefaultFieldVisibilities($pid) {
		global $TCA;

			// Define the tables for which field visibilities can be defined in TypoScript
		$tables = array('tx_partner_main','tx_partner_contact_info');

			// Define the keywords which can be used to describe the field visibility setting
			// (e.g. tx_partner.fieldVisibility.default.tx_partner_main.last_name = PUBLIC)
		$keywords = array('PRIVATE'=>'PRIVATE','RESTRICTED'=>'RESTRICTED','PUBLIC'=>'PUBLIC');

			// Load the showRecordFieldList part (from the TCA>interface section) to define the order
			// by which the merged field visibilities will be sorted by
		foreach ($tables as $theTable)		{
			t3lib_div::loadTCA($theTable);
			$fieldList[$theTable] = explode(',', $TCA[$theTable]['interface']['showRecordFieldList']);
		}

			// Get TSconfig for the current PID
		$TSconfig = t3lib_BEfunc::getPagesTSconfig($pid);

			// Get default field visibilities from TypoScript
		$tscDef['tx_partner_main'] = $TSconfig['tx_partner.']['fieldVisibility.']['default.']['tx_partner_main.'];
		$tscDef['tx_partner_contact_info'] = $TSconfig['tx_partner.']['fieldVisibility.']['default.']['tx_partner_contact_info.'];

			// Get the default values for each field configured BOTH in TCA>interface>showRecordFieldList AND TypoScript
		foreach ($tscDef as $theTable=>$theDefaultField)		{
				// Get the field list for the current table
			$tcaFields = $fieldList[$theTable];

				// Run through all fields from TCA>interface>showRecordFieldList
			foreach ($tcaFields as $theTCAfield)		{
					// If a value corresponds to a field configured in TypoScript, it's a proper default value
				if ($theDefaultField[$theTCAfield])		{

					$defaultValues[$theTable.'-'.$theTCAfield]['table'] = $theTable;
					$defaultValues[$theTable.'-'.$theTCAfield]['field'] = $theTCAfield;
					$defaultValues[$theTable.'-'.$theTCAfield]['value'] = $theDefaultField[$theTCAfield];
					$defaultValues[$theTable.'-'.$theTCAfield]['default'] = TRUE;

						// Check for a proper keyword
					if (!array_search($theDefaultField[$theTCAfield],$keywords))		{
						$defaultValues[$theTable.'-'.$theTCAfield]['error'] = 'invalid_keyword';
					}

						// Check if the userChange property is set
					if (isset($theDefaultField[$theTCAfield.'.']['userChange']))		{
						$defaultValues[$theTable.'-'.$theTCAfield]['userChange'] =	$theDefaultField[$theTCAfield.'.']['userChange'];
					}
				}
			}
		}

		return $defaultValues;
	}

	/**
	 * Gets the details of a report, including the UID's and the number of the
	 * partners that must be selected
	 *
	 * @param	integer		$uid: UID of the report for which to get the details
	 * @param	integer		$limit: Limit the result to a certain number of partners
	 * @return	array		Details for the requested report
	 */
	function getReport($uid, $limit='')		{

			// Get the requested report
		$report = t3lib_BEfunc::getRecord('tx_partner_reports', $uid);

			// If the report was found, get the details
		if (is_array($report))		{

				// Get the current selection of partners
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_main', $report['query'], '', '', $limit);

			if ($res)		{
				while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))		{
					$report['selected_partners'][] = $rec['uid'];
				}
			}

				// Get the number of partners selected
			$report['partners_count'] = count($report['selected_partners']);

				// Convert the stored XML-values from the field_selection field back to an array
			$report['field_selection'] = t3lib_div::xml2array($report['field_selection']);
		}

		return $report;
	}
	
	
	
	/**
	 * Gets the total number of partners which are determined by the definition of a report.
	 *
	 * @param	integer		$uid: UID of the report for which to get the total number
	 * @return	integer		Total number of partners selected by the report
	 */
	function getCountFromReport($uid)		{
		$out = 0;

			// Get the requested report
		$report = t3lib_BEfunc::getRecord('tx_partner_reports', $uid);

			// If the report was found, get the count
		if (is_array($report))		{

				// Get the count of the current selection of partners
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)', 'tx_partner_main', $report['query']);
			if ($res) $rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$out = $rec['count(*)'];			
		}

		return $out;
	}


	/**
	 * Gets all fields according to the 'types'-configuration in $TCA for the record
	 * specified. It is based on t3lib_BEfunc::getTCAtypes, but it also includes
	 * all fields in palettes.
	 *
	 * @param	string		$table: Table name (present in TCA)
	 * @param	integer		$type: Type of the record
	 * @return	array		Array with all fields according to the 'types'-configuration in $TCA
	 */
	function getAllTypeFields($table, $type)		{
		global $TCA;

			// Load full $TCA
		t3lib_div::loadTCA($table);

			// Get the types configuration for the requested record
		$typesConfig = t3lib_BEfunc::getTCAtypes($table, $type, 1);

			// Unset the dividers
		unset ($typesConfig['--div--']);

			// Get the values
		foreach ($typesConfig as $theTypesField)		{

				// Get the palette values
			if ($theTypesField['palette'])		{
				$p = $TCA['tx_partner_main']['palettes'][$theTypesField['palette']];
				$palette = explode(',',$TCA['tx_partner_main']['palettes'][$theTypesField['palette']]['showitem']);
				foreach ($palette as $v)		{
					$out[$v] = $v;
				}
			}

				// Add the current value to the out-array
			$out[$theTypesField['field']] = $theTypesField['field'];
		}

		return $out;
	}


	/*********************************************
 	*
 	* PROCESSING DATA
 	*
 	*********************************************/


	/**
	 * Syncronize redundant partner / contact-info data with the fe_user assigned to the partner.
	 * If the fe_user is assigned to more than one partner, the function will not make any changes to
	 * the fe_user.
	 *
	 * @param	integer		$partnerUid: UID of the partner for which the fe_user shall be sync'd
	 * @return	boolean		True if sync was successful, false if not successful
	 */
	function syncPartnerWithFeUser($partnerUid)		{
		$result = false;

		if (substr($partnerUid, 0, 3) == 'NEW')		{

				// New record, nothing to be syncronized.
			$result = true;

		} else {

				// Read the partner-record from the database
			$partnerRecord = array();
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_partner_main','uid='.$partnerUid);
			if ($res) $partner = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

				// Get the contact-info for the standard e-mail address
			$stdMail = tx_partner_div::getContactInfo($partnerUid, 1, 3);
			if (is_array($stdMail)) $stdMail = reset($stdMail);

				// Get the number of partners for which the current FE user is assigned
			$count = tx_partner_div::getPartnerCountByFeUser($partner['fe_user'], $partner['uid']);

				// Only go on if exactly one partner was found for the current fe-user
			if ($count == 1)		{

					// Get the name
				if ($partner['type'] == 0) {	// Person
					if ($partner['first_name']) $name = $partner['first_name'].' ';
					$name.= $partner['last_name'];
				}
				if ($partner['type'] == 1) {	// Organisation
					$name = $partner['org_name'];
				}

					// Update the fe_users record
				$fields_values['name'] = $name;
				$fields_values['email'] = $stdMail->data['email'];

					// Write to database (cannot use TCEmain here to make this work under FE-conditions as well)
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid='.$partner['fe_user'],$fields_values);

				$result = true;
			}
		}

		return $result;
	}


	/**
	 * Create value for the 'label'-field. The label field is built upon several parameters and
	 * depends on the the record (Partner or Contact-Info) and the type of the record.
	 *
	 * @param	string		$table: Table of the record for which the label must be created (either tx_partner_main or tx_partner_contact_info)
	 * @param	string		$uid: UID of the record for which the label must be created. If $record is provided, this parameter is not needed and will be disregarded.
	 * @param	string		$record: Record for which the label must be created. Optional alternative to providing a UID.
	 * @return	string		Value for the 'label'-field of the record
	 */
	function createLabel($table, $uid, $record='')		{
		$label = '';

			// If no record has been provided, but a uid, read it from the database
		if (!is_array($record) and $uid)		{
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$table,'uid='.$uid);
			if ($res) $record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}

			// If a record has been found/provided, go on...
		if (is_array($record))		{
			switch ($table)		{

				// Make label for a Partner record
				// **************************************************
			case 'tx_partner_main':
				if ($record['type'] == 0)		{
						// Label for a person
					$label = $record['last_name'];
					if ($record['first_name']) $label.= ', '.$record['first_name'];
				} else {
						// Label for an organization
					$label = $record['org_name'];
				}

					// Add locality if filled
				if ($record['locality']) $label .= ' - '.$record['locality'];
			break;

				// Make label for a Contact-Information record
				// **************************************************
			case 'tx_partner_contact_info':

					// Get the 'standard' flag
				if ($record['standard']) {
					$standard = '* ';
				}

		
				// Get the international phone prefix
			if ($record['country'] != '') {
				if (t3lib_extMgm::isLoaded('static_info_tables')) {
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('cn_phone','static_countries','uid='.$record['country']);
					if ($res) $cn_phone_array = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					if (is_array($cn_phone_array)) {
						$cn_phone = ' +'.$cn_phone_array['cn_phone'];
					}
				}
			}
		
				// Get the area code
			if ($record['area_code'] != '') {
				$area_code = ' ('.$record['area_code'].')';
			}
		
				// Get the number
			if ($record['number'] != '') {
				$number = ' '.$record['number'];
			}

				// Get the extension
			if ($record['extension'] != '') {
				$extension = '-'.$record['extension'];
			}

					// Get the localized labels for the nature of the contact
				if ($record['nature'] == '0') {
					$labelNature = ' '.tx_partner_lang::getLabel('tx_partner.label.private.1char').':';
				}
				if ($record['nature'] == '1') {
					$labelNature = ' '.tx_partner_lang::getLabel('tx_partner.label.business.1char').':';
				}

					// Assemble the label according to the type of the contact-info record
				$typeLabel = tx_partner_lang::getLabel('tx_partner_contact_info.type.I.'.$record['type']);
				switch ($record['type']) {
					case 0: // Phone
					$label = $standard.$typeLabel.$labelNature.$cn_phone.$area_code.$number.$extension;
					break;

					case 1: // Mobile
					$label = $standard.$typeLabel.$labelNature.$cn_phone.$area_code.$number;
					break;

					case 2: // Fax
					$label = $standard.$typeLabel.$labelNature.$cn_phone.$area_code.$number.$extension;
					break;

					case 3: // E-Mail
					$label = $standard.$typeLabel.$labelNature.' '.$record['email'];
					break;

					case 4: // URL
					$label = $standard.$typeLabel.$labelNature.' '.$record['url'];
					break;
			}
			break;
			}
		}
		return $label;
	}
	
	/**
	 * Format a contact-information
	 *
	 * @param	object		$contactInfo: Contact-information object
	 * @return	string		Formatted contact-info
	 */
	function formatContactInfo($contactInfo)		{
		$out = '';
		
		if (!is_object($contactInfo)) return false;
		
		switch ($contactInfo->data['type'])		{
			case 0: // Phone
			case 1: // Mobile
			case 2: // Fax
				if ($contactInfo->data['_prefix'])   $out.= '+'.$contactInfo->data['_prefix'].' ';
				if ($contactInfo->data['area_code']) $out.= '('.$contactInfo->data['area_code'].') ';
				if ($contactInfo->data['number'])    $out.= $contactInfo->data['number'];
			break;
			
			case 3: // E-Mail
				$out.= $contactInfo->data['email'];
			break;
			
			case 4: // URL
				$out.= $contactInfo->data['url'];
			break;			
		}		
		
		return $out;
	}

	/*********************************************
 	*
 	* OUTPUT RELATED (BACKEND)
 	*
 	*********************************************/

	/**
	 * Function to create an 'edit partner' link.
	 *
	 * For use in Backend only.
	 *
	 * @param	string		$partnerUID: UID of the partner which the link must point to
	 * @return	string		HTML with the 'edit partner' link
	 */
	function getEditPartnerLink($partnerUID)		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Edit parameters
		$params = '&edit[tx_partner_main]['.$partnerUID.']=edit';

			// Title for the edit-link
		$linkTitle = $LANG->getLL('tx_partner.label.edit_partner');

			// Icon for the edit-link
		$linkIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/edit2.gif', 'width="11" height="12"').' title="'.$linkTitle.'" border="0" alt="" />';

			// Assemble the link
		$editLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'])).'">'.$linkIcon.'</a>';

		return $editLink;
	}

	/**
	 * Function to create the 'edit fe_user' link.
	 *
	 * For use in Backend only.
	 *
	 * @param	integer		$feUserUid: UID of the fe_user which the link must point to
	 * @return	string		HTML with the 'edit partner' link
	 */
	function getEditFeUserLink($feUserUid)		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Edit parameters
		$params = '&edit[fe_users]['.$feUserUid.']=edit';

			// Title for the edit-link
		$linkTitle = $LANG->getLL('tx_partner.label.edit_fe_user');

			// Icon for the edit-link
		$linkIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/edit2.gif', 'width="11" height="12"').' title="'.$linkTitle.'" border="0" alt="" />';

			// Assemble the link
		$editLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'])).'">'.$linkIcon.'</a>';

		return $editLink;
	}


	/**
	 * Function to create an 'edit report' link.
	 *
	 * For use in Backend only.
	 *
	 * @param	string		$reportUID: UID of the report which the link must point to
	 * @return	string		HTML with the 'edit report' link
	 */
	function getEditReportLink($reportUID)		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Edit parameters
		$params = '&edit[tx_partner_reports]['.$reportUID.']=edit';

			// Title for the edit-link
		$linkTitle = $LANG->getLL('tx_partner.label.edit_report');

			// Icon for the edit-link
		$linkIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/edit2.gif', 'width="11" height="12"').' title="'.$linkTitle.'" border="0" alt="" />';

			// Assemble the link
		$editLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'])).'">'.$linkIcon.'</a>';

		return $editLink;
	}


	/**
	 * Creates the mail icon with a link to the standard e-mail address of the partner.
	 * If no standard e-mail address can be found, the function returns nothing.
	 *
	 * For use in Backend only.
	 *
	 * @param	string		$partnerUID: UID of the partner for which the linked icon must be created
	 * @return	string		HTML with the linked mail icon
	 */
	function getMailIconLink($partnerUID)		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');
		$mailIconLink = '';

			// Get the standard e-mail address
		$stdContactInfo = t3lib_BEfunc::getRecordsByField(
			'tx_partner_contact_info',
			'uid_foreign',
			$partnerUID,
			'AND tx_partner_contact_info.type=3 AND tx_partner_contact_info.standard=1'
		);

			// Get the mail icon if exactly one standard-entry could be found
		if ((is_array($stdContactInfo['0'])) and (!is_array($stdContactInfo['1']))) {
			$mailIconLink = '<a href="mailto:'.$stdContactInfo['0']['email'].'">
				<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/newmail.gif', 'width="18" height="16"').' title="'.$LANG->getLL('tx_partner.label.send_email').' ('.$stdContactInfo['0']['email'].')" alt="" /></a>';
		}

		return $mailIconLink;
	}

	/**
	 * Creates a list of format-icons for downloading data from a report in specific formats.
	 *
	 * For use in Backend only.
	 *
	 * @param	integer		$reportUid: UID of the report for which to download the data. Must be provided if $linked is set to TRUE, otherwise the icon will not be linked
	 * @param	string		$requestedFormats: Comma-separated list of formats which must be returned. If not provided, the formats configured as 'external' in $TYPO3_CONF_VARS['EXTCONF']['partner']['formats'] will be taken
	 * @param	string		$orientation: Orientation of the output. Can bei either 'horizontal' or 'vertical'
	 * @param	boolean		$linked: If set, the icons/labels will be linked to the download-function
	 * @param	boolean		$withLabels: If set, the icons will be returned with a proper label
	 * @param	boolean		$tableWrap: If set, the result will be wrapped in <table> tags (otherwise just <tr><td> tags)
	 * @param	string		$backPath: The back-path to the calling script
	 * @return	string		HTML for use in the Backend
	 */
	function getFormatIcons($reportUid, $requestedFormats='', $orientation='horizontal', $linked=TRUE, $withLabels=TRUE, $tableWrap=TRUE, $backPath = '')		{
		global $LANG, $TYPO3_CONF_VARS;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Determine which formats to display. If specific formats were requested, take those, otherwise take all formats defined in the global conf-vars
		if ($requestedFormats)		{
			$formats = explode(',', $requestedFormats);
		} else {
			$formats = array_keys($TYPO3_CONF_VARS['EXTCONF']['partner']['formats']);
		}
			// Label
		$labelDownload = $LANG->getLL('tx_partner.label.download');

			// Get icons (possibly with labels and/or linked) for each requested format
		foreach ($formats as $theFormat)		{
			if ($TYPO3_CONF_VARS['EXTCONF']['partner']['formats'][$theFormat]['external'])		{
				$item = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/'.$TYPO3_CONF_VARS['EXTCONF']['partner']['formats'][$theFormat]['icon'], 'width="18" height="16"').' title="'.$labelDownload.' ('.$theFormat.')" border="0" alt="" />';
				if ($withLabels) $item = $item.$labelDownload.' ('.strtoupper($theFormat).')';
				if ($linked and $reportUid)		{
					$id = 'ID'.rand(1,9999999);
					$item = '<a href="#"onclick="this.blur();vHWin=window.open(\''.$backPath.t3lib_extMgm::extRelPath('partner').'inc/class.tx_partner_download_report.php?report='.$reportUid.'&dbruehlmeier=ja&format='.$theFormat.'\',\'popUp'.$id.'\',\'height=250,width=400,status=0,menubar=0,scrollbars=1\');vHWin.focus();return false;">'.$item.'</a>';
				}
				$items[] = $item;
			}
		}

			// If formats were found, create a table with the proper orientation
		if (is_array($items))		{
			switch (strtolower($orientation)) {
				case 'horizontal':

					foreach ($items as $theItem)		{
						$content.= '<td>'.$theItem.'</td>';
					}
					$content = '<tr>'.$content.'</tr>';
					if ($tableWrap) $content = '<table>'.$content.'</table>';

				break;

				case 'vertical':

					foreach ($items as $theItem)		{
						$content.= '<tr><td>'.$theItem.'</td></tr>';
					}
					if ($tableWrap) $content = '<table>'.$content.'</table>';

				break;
			}
		}

		return $content;
	}


	/*********************************************
 	*
 	* MISCELLANEOUS
 	*
 	*********************************************/

	/**
	 * Validates a date inputted in the format YYYYMMDD
	 * and returns the date as an array with the
	 * parts ['year'], ['month'] and ['day'].
	 *
	 * If the input string is not a valid date, the function returns false.
	 *
	 * @param	string		$date: Date in the format YYYYMMDD
	 * @return	array		Date splitted into the parts ['year'], ['month'] and ['day'] or false if date is not valid
	 */
	function getDateArray($date)		{
		$dateArray = array();

			// Get the parts from the string supplied
		$dateArray['year'] = substr($date, 0, 4);
		$dateArray['month'] = substr($date, 4, 2);
		$dateArray['day'] = substr($date, 6, 2);

		if (checkdate($dateArray['month'], $dateArray['day'], $dateArray['year']))		{
				// The date is valid: Return the date as an array.
			$out = $dateArray;
		} else {
				// The date is NOT valid: Return false
			$out = false;
		}

		return $out;
	}

	/**
	 * Gets the HTML-output for a message array. The message array needs to be in the following format:
	 * $msgArray = array(
	 * 		array('info' => 'This is just an information'),
	 * 		array('success' => 'This is a success message'),
	 * 		array('warning' => 'This is a warning'),
	 * 		array('error' => 'This is an error!'),
	 * 		array('asdf' => 'This is the default message'),
	 * );
	 *
	 * @param	array		$msgArray: Array with the accumulated messages
	 * @param	string		$tableStyle: Style-attributes for the <table>-tag
	 * @param	string		$trStyle: Style-attributes for the <tr>-tags
	 * @param	string		$tdStyle: Style-attributes for the <td>-tags
	 * @return	string		HTML-output for the messages (wrapped in a table)
	 */
	function getMessageOutput($msgArray, $tableStyle='', $trStyle='', $tdStyle='')		{

			// Define the default TABLE style
		if (!$tableStyle)		{
			$tableStyle = 'width="100%" border="0" cellspacing="0" cellpadding="0"';
		}

			// Define the default TR style
		if (!$trStyle)		{
			$trStyle = '';
		}

			// Define the default TD style
		if (!$tdStyle)		{
			$tdStyle = 'height="16px" align="left" valign="top" style="border-bottom-width:1px; border-bottom-color:#C6C2BD; border-bottom-style:solid;"';
		}

		if (is_array($msgArray))		{
			foreach ($msgArray as $theMessage)		{
				
					// Get the proper icon
				switch (key($theMessage))		{
					case 'success':
						$img = 't3lib/gfx/icon_ok2.gif';
					break;
					case 'warning':
						$img = 't3lib/gfx/icon_warning2.gif';
					break;
					case 'error':
						$img = 't3lib/gfx/icon_fatalerror.gif';
					break;
					case 'info':
					default:
						$img = 't3lib/gfx/icon_note.gif';
					break;
				}

					// Label
				$label = tx_partner_lang::getLabel('tx_partner.label.'.(key($theMessage)));

				$icon = '<img '.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $img).' width="18" height="16" alt="'.$label.'">';

					// Create the row
				$rows.= '
					<tr '.$trStyle.'>
						<td '.$tdStyle.' width="20" >'.$icon.'</td>
						<td '.$tdStyle.'>'.current($theMessage).'</td>
					</tr>
				';
			}

				// Create the table
			$out = '
				<table '.$tableStyle.'>'.$rows.'
				</table>
			';
		}
		return $out;
	}

	/**
	 * Returns 'this.blur();' string, if supported.
	 *
	 * @return	string		If the current browser supports styles, the string 'this.blur();' is returned.
	 */
	function blur()	{
		return $GLOBALS['CLIENT']['FORMSTYLE'] ? 'this.blur();':'';
	}
	
	
	/**
	 * Converts an internal code from static_info_tables to the 2-letter ISO code
	 *
	 * @param	string		$table: Name of the table (either 'static_countries' or 'static_languages')
	 * @param	integer		$uid: UID of the record for which to get the ISO code	 
	 * @return	string		2-letter ISO code
	 */
	function convertToIso($table, $uid)	{
		$out = '';
		
			// Check if an allowed table was supplied
		$allowedTables = array('static_countries', 'static_languages');
		if (!in_array($table, $allowedTables)) return false;
		
			// Get the ISO-code
		$rec = t3lib_BEfunc::getRecord($table, $uid);
		if ($table == 'static_countries') $out = $rec['cn_iso_2'];
		if ($table == 'static_languages') $out = $rec['lg_iso_2'];
		
		return $out;
	}
	
	/**
	 * Build as WHERE clause by an array of search operators. The searchArray must be built like this:
	 *	$searchArray = array(
	 *		'tx_partnersync_tt_address' => array(
	 *			'op' => 'NE',
	 *			'val' => '',
	 *		),
	 *	);
	 *
	 * The operators can be:
	 * 'EQ'	EQuals
	 * 'NE'	does Not Equal
	 * 'GT'	is Greater Than
	 * 'GE'	is Greater or Equal than
	 * 'LT'	is Less Than
	 * 'LE'	is Less or Eqal than
	 * 'BG'	BeGins with
	 * 'NB'	does Not Begin with
	 * 'ND'	eNDs with
	 * 'NN'	does Not eNd with
	 *
	 * @param	string		$table: Name of the table for which the build the WHERE clause
	 * @param	array		$searchArray: Array with the search operators
	 * @return	string		WHERE clause
	 */
	function buildWhereClauseByOperators($table, $searchArray)		{
		global $TCA;
		$out = '';
		$where = array();

			// Check if a search-array was provided
		if (!is_array($searchArray)) return false;
		
			// Load TCA
		t3lib_div::loadTCA('tx_partner_main');	

			// Build the where statement
		foreach ($searchArray as $fieldName=>$opValArray)		{
			
				// Check if the field is in TCA
			if (is_array($opValArray) and $TCA[$table]['columns'][$fieldName])		{
				
					// Format the parameters
				$field = $table.'.'.$fieldName;
				$operator = strtoupper($opValArray['op']);
				$value = $GLOBALS['TYPO3_DB']->quoteStr($opValArray['val'], $table);
				
					// Build the proper where clause
				switch ($operator) {
					case 'EQ':	// EQuals
						$where[] = $field.' = \''.$value.'\'';
					break;
					case 'NE':	// does Not Equal
						$where[] = $field.' <> \''.$value.'\'';
					break;
					case 'GT':	// is Greater Than
						$where[] = $field.' < \''.$value.'\'';
					break;
					case 'GE':	// is Greater or Equal than
						$where[] = $field.' <= \''.$value.'\'';
					break;
					case 'LT':	// is Less Than
						$where[] = $field.' > \''.$value.'\'';
					break;
					case 'LE':	// is Less or Equal than
						$where[] = $field.' >= \''.$value.'\'';
					break;
					case 'BG':	// BeGins with
						$where[] = $field.' LIKE \''.$value.'%\'';
					break;
					case 'NB':	// does Not Begin with
						$where[] = $field.' NOT LIKE \''.$value.'%\'';
					break;
					case 'ND':	// eNDs with
						$where[] = $field.' LIKE \'%'.$value.'\'';
					break;
					case 'NN':	// does Not eNd with
						$where[] = $field.' NOT LIKE \'%'.$value.'\'';
					break;
					
				}
			}
		}
		
			// Join the where-clause with 'AND's
		if (is_array($where)) $out = t3lib_div::csvValues($where,' AND ','');

		return $out;
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_div.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_div.php']);
}

?>
