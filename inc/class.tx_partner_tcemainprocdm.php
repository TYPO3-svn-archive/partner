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
* Class with methods called as hooks from TCE Main
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');

class tx_partner_tcemainprocdm {

	/**
	 * This method is called when the 'processDatamap'-hooks are acivated in ext_localconf.php.
	 * It is called in tcemain right before the database operations (INSERT/UPDATE) are
	 * carried out.
	 *
	 * The following tasks are accomplished
	 * - Update the field visibility settings for tx_partner_main
	 * - Create/Change label fields for tx_partner_main and tx_partner_contact_info
	 * - Add uid_foreign for the table tx_partner_contact_info
	 * - Update the 'hotlist' for country and language fields (from tx_staticinfotables)
	 * - Convert the field_selection field from tx_partner_reports to XML
	 *
	 * @param	string		$status: The status of the current record
	 * @param	string		$table: The table of the current record
	 * @param	string		$id: The current record UID
	 * @param	array		$fieldArray: The currently processed fieldArray, passed by reference
	 * @param	string		$thisRef: The current cObj, passed by reference
	 * @return	void		Nothing returned. The fieldArray is directly changed, as it is passed by reference
	 */
	function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$thisRef) {

		// ********************************************************************
		// UPDATE FIELD VISIBILITY FOR tx_partner_main
		// ********************************************************************

		if ($table == 'tx_partner_main' && $fieldArray['field_visibility'])		{

			if (is_array($fieldArray['field_visibility']))		{

					// Get the merged field visibilities for the current partner
				$mergedFieldVisibilities = tx_partner_div::getMergedFieldVisibilities($id);

					// Prepare the array of elements for submission to the database
				foreach ($fieldArray['field_visibility'] as $theFieldName=>$theFieldValue)		{

						// Handling of default values. This is necessary, because all changes are submitted
						// in the same field. Therefore, we cannot be sure that if a value is in the fieldArray,
						// that it was really changed. It might also be just displayed from the default setup
						// in TSconfig. If we make no difference here, then all values (including the default)
						// setup would be submitted as user-defined values!
					if (substr($theFieldValue,0,7) == 'DEFAULT')		{

							// Compare which values were changed compared to the default setup
						if (strtoupper(substr($theFieldValue,8)) != strtoupper($mergedFieldVisibilities[$theFieldName]['value']))	{
							$tf = t3lib_div::trimExplode('-', $theFieldName);
							$newValues[$theFieldName]['value'] = strtoupper(substr($theFieldValue,8));
							$newValues[$theFieldName]['table'] = $tf['0'];
							$newValues[$theFieldName]['field'] = $tf['1'];
							$newValues[$theFieldName]['default'] = FALSE;
						}

					} else {
						if (strtoupper($theFieldValue) != 'RESET')		{
							$tf = t3lib_div::trimExplode('-', $theFieldName);
							$newValues[$theFieldName]['value'] = strtoupper($theFieldValue);
							$newValues[$theFieldName]['table'] = $tf['0'];
							$newValues[$theFieldName]['field'] = $tf['1'];
							$newValues[$theFieldName]['default'] = FALSE;
						}
					}
				}

				if (is_array($newValues))		{
						// If there were any changes to user-defined field visibilites, convert the array to XML for storage
					$fieldArray['field_visibility'] = t3lib_div::array2xml($newValues);
				} else {
						// Otherwise emtpy the whole fieldArray-part (unsetting is not allowed)
					$fieldArray['field_visibility'] = '';
				}
			}
		}


		// ********************************************************************
		// LABELS FOR partner AND contact_info
		// ********************************************************************

		if ($table == 'tx_partner_main') {
			$label = tx_partner_div::createLabel('tx_partner_main', '', $thisRef->datamap['tx_partner_main'][$id]);
		}
		if ($table == 'tx_partner_contact_info') {
			$label = tx_partner_div::createLabel('tx_partner_contact_info', '', $thisRef->datamap['tx_partner_contact_info'][$id]);
		}
		if ($label) $fieldArray['label'] = $label;


		// ********************************************************************
		// ADD uid_foreign FOR THE TABLE tx_partner_contact_info
		// ********************************************************************

		if ($table == 'tx_partner_contact_info')		{
			$uid_foreign = t3lib_div::_GET('tx_partner_uid_foreign');
			if ($uid_foreign) {
				$fieldArray['uid_foreign'] = $uid_foreign;
			}
		}

		// ********************************************************************
		// CHECK birth_date AND death_date FOR VALID FORMAT
		// ********************************************************************

		if ($table == 'tx_partner_main' && $fieldArray['birth_date'])	{
			if (!tx_partner_div::getDateArray($fieldArray['birth_date'])) $fieldArray['birth_date'] = '';
		}
		if ($table == 'tx_partner_main' && $fieldArray['death_date'])	{
			if (!tx_partner_div::getDateArray($fieldArray['death_date'])) $fieldArray['death_date'] = '';
		}


		// ********************************************************************
		// UPDATE THE HOTLIST FOR FIELDS FROM static_info_tables
		// ********************************************************************

		$hotlistFields = array(
			'tx_partner_main' => array(
				array(
					'field' => 'country',
					'table' => 'static_countries',
				),
				array(
					'field' => 'po_country',
					'table' => 'static_countries',
				),
				array(
					'field' => 'nationality',
					'table' => 'static_countries',
				),
				array(
					'field' => 'mother_tongue',
					'table' => 'static_languages',
				),
				array(
					'field' => 'preferred_language',
					'table' => 'static_languages',
				),
			),
			'tx_partner_contact_info' => array(
				array(
					'field' => 'country',
					'table' => 'static_countries',
				),
			),
		);

		foreach ($hotlistFields as $theTable => $v)		{
			foreach ($v as $info)		{
				if ($table == $theTable AND $fieldArray[$info['field']])	{
					tx_staticinfotables_div::updateHotlist($info['table'], $fieldArray[$info['field']], 'uid', 'tx_partner');
				}
			}
		}

		// ********************************************************************
		// PREPARE THE field_selection FIELD FROM tx_partner_reports
		// ********************************************************************
		if ($table == 'tx_partner_reports' AND $fieldArray['field_selection'])		{

				// Save only values which are selected for either file or screen output
			foreach ($fieldArray['field_selection'] as $table => $fieldValues)		{
				foreach ($fieldValues as $fieldName => $v)		{
					if (!($v['file'] or $v['screen'])) unset($fieldArray['field_selection'][$table][$fieldName]);
				}
				if (!$fieldArray['field_selection'][$table]) unset($fieldArray['field_selection'][$table]);
			}

				// Convert the array to XML for storage
			if ($fieldArray['field_selection'])		{
				$fieldArray['field_selection'] = t3lib_div::array2xml($fieldArray['field_selection']);
			} else {
				$fieldArray['field_selection'] = '';		// unsetting is not allowed at this stage in TCE-main
			}
		}
	}


	/**
	 * This method is called when the 'processDatamap'-hooks are acivated in ext_localconf.php.
	 * It is called in tcemain right AFTER the database operations (INSERT/UPDATE) are
	 * carried out.
	 *
	 * The following tasks are accomplished
	 * - Syncronize the redundant partner data with fe_users
	 * - Syncronize the redundant contact-info data with fe_users
	 * - Check and set the standard-flag on tx_partner_contact_info
	 *
	 * @param	string		$status: The status of the current record
	 * @param	string		$table: The table of the current record
	 * @param	string		$id: The current record UID
	 * @param	array		$fieldArray: The currently processed fieldArray, passed by reference
	 * @param	string		$thisRef: The current cObj, passed by reference
	 * @return	void		Nothing returned. The method directly makes the necessary db-updates if possible.
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$thisRef) {

			// Get the current record from the database
		if ($status == 'new')		{
			$id = $thisRef->substNEWwithIDs[$id];
		}
		$currentRecord = t3lib_BEfunc::getRecord($table, $id);

			// Get the foreign_uid of the current record from the GET parameter of the calling partner
			// (not available in datamap, since it is a user processed field)
		$currentUIDforeign = t3lib_div::_GET('tx_partner_uid_foreign');


		// ********************************************************************
		// CHECK AND SET THE standard FLAG on tx_partner_contact_info
		// ********************************************************************

		if ($table == 'tx_partner_contact_info')	{

				// Proceed only if $currentUIDforeign is available. Otherwise, the records that need
				// to be updated cannot be selected. This may be the case if the record is called directly
				// from Web>List instead of calling it from the single-view of a partner...
			if ($currentUIDforeign and $id)		{

				$recordsWithStdFlag = t3lib_BEfunc::getRecordsByField('tx_partner_contact_info', 'standard', '1', ' AND uid_foreign='.$currentUIDforeign.' AND type='.$currentRecord['type']);
				$count = count($recordsWithStdFlag);

					// No contact-infos with std-flag set. Make the current record the standard record
				if ($count == 0)		{

						// Set the standard-flag to 1
					$data['tx_partner_contact_info'][$id]['standard'] = '1';

						// Set the new label
					$changeRec = $currentRecord;
					$changeRec['standard'] = '1';
					$data['tx_partner_contact_info'][$id]['label'] = tx_partner_div::createLabel('tx_partner_contact_info', '', $changeRec);
				}

					// More than one records have the standard-flag set. Remove it from all other records and leave it only for the current record.
				if ($count > 1)		{

					foreach ($recordsWithStdFlag as $theRecord)		{

							// Set the standard-flag to 0
						$data['tx_partner_contact_info'][$theRecord['uid']]['standard'] = '0';

							// Set the new label
						$changeRec = t3lib_BEfunc::getRecord('tx_partner_contact_info', $theRecord['uid']);
						$changeRec['standard'] = '0';
						$data['tx_partner_contact_info'][$theRecord['uid']]['label'] = tx_partner_div::createLabel('tx_partner_contact_info', '', $changeRec);
					}

						// Current Record: Set the standard-flag to 1
					$data['tx_partner_contact_info'][$id]['standard'] = '1';

						// Current Record: Set the new label
					$changeRec = $currentRecord;
					$changeRec['standard'] = '1';
					$data['tx_partner_contact_info'][$id]['label'] = tx_partner_div::createLabel('tx_partner_contact_info', '', $changeRec);

				}

					// Make the updates (cannot use tce-main, might create an endless loop)
				if (is_array($data['tx_partner_contact_info']) and $id)		{

					foreach ($data['tx_partner_contact_info'] as $updateUid => $updateFields)		{
						$query = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
							'tx_partner_contact_info',
							'uid='.$updateUid,
							$updateFields
						);
					}
				}
			}
		}


		// ********************************************************************
		// SYNC DATA WITH fe_users
		// ********************************************************************

		if ($table == 'tx_partner_main')		{
				// Sync the redundant partner fields with fe_users
			tx_partner_div::syncPartnerWithFeUser($id);
		}

		if ($table == 'tx_partner_contact_info')		{
				// Sync the e-mail address with fe_users
			if ($fieldArray['email'] or $fieldArray['standard'] == 1)		{
				tx_partner_div::syncPartnerWithFeUser($currentUIDforeign);
			}
		}

	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_tcemainprocdm.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_tcemainprocdm.php']);
}

?>