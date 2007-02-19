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
* Class for getting partner-data (queries) in various ways
* and output formats. Recommended for use in your own extensions.
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/


require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_main.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_lang.php');
require_once(t3lib_extMgm::extPath('static_info_tables').'class.tx_staticinfotables_div.php');


	// Needed to make the script run under FE conditions
require_once(PATH_t3lib.'class.t3lib_befunc.php');



/**
 * Class for getting partner-data (queries) in various ways
 * and output formats. Recommended for use in your own extensions.
 *
 */
class tx_partner_query {


	/*********************************************
 	*
 	* GETTING DATA
 	*
 	*********************************************/


	/**
	 * Gets partner data based on the selection defined in a saved report.
	 * The selected data is made available in $this->query.
	 *
	 * @param	integer		$reportUid: UID of the report which defines the partners to be selected
	 * @return	void
	 */
	function getPartnerByReport($reportUid)		{

		$report = tx_partner_div::getReport($reportUid);

		if (is_array($report))		{
			$this->getPartnerByList($report['selected_partners']);
		}
	}


	/**
	 * Gets partner data based on an array of partner-UIDs.
	 * The selected data is made available in $this->query.
	 *
	 * @param	array		$partnerList: Array with partner-UIDs which will be selected for the query
	 * @return	void
	 */
	function getPartnerByList($partnerList)		{

		if (is_array($partnerList))		{
			foreach ($partnerList as $thePartner)	{
				$this->query[$thePartner] = t3lib_div::makeInstance('tx_partner_main');
				$this->query[$thePartner]->getPartner($thePartner);
			}
		}
	}
	
	/**
	 * Gets all partners from certain PID(s).
	 * The selected data is made available in $this->query.
	 *
	 * @param	string		$pidList: Comma-separated list with PIDs from which to get the partners
	 * @param	string		$orgTypes: Optional comma-separated list of organization-types to select
	 * @param	string		$postalCode: Optional. If set, only partners with postal codes starting with this string will be selected
	 * @param	string		$orderBy: Optional. SQL order-by clause.
	 * @return	void
	 */
	function getPartnerByPid($pidList, $orgTypes='', $postalCode='', $orderBy='')		{

		if ($pidList)		{
			
				// Read all partner for the requested PIDs
			$where = 'tx_partner_main.pid IN('.$pidList.')';
			if ($orgTypes) $where.=' AND tx_partner_main.org_type IN('.$orgTypes.')';
			if ($postalCode != '') $where.=' AND tx_partner_main.postal_code LIKE "'.$postalCode.'%"';
			$where.= t3lib_BEfunc::deleteClause('tx_partner_main');
			
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_partner_main', $where, '', $orderBy);
			if ($res)		{	
				while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$this->query[$rec['uid']] = t3lib_div::makeInstance('tx_partner_main');
					$this->query[$rec['uid']]->getPartner($rec['uid']);
				}
			}
		}
	}

	/**
	 * Gets partner data based on an array of occupation-UIDs (key of tx_partner_val_occupations)
	 * The selected data is made available in $this->query.
	 *
	 * @param	array		$occupations: Array with occupation-UIDs. All partners with at least one of these occupations will be selected in the query.
	 * @param	integer		$pid: Page-ID (Optional). If not provided, the function will look for partners in all PID's.
	 * @param	string		$orderBy: Optional. SQL order-by clause.
	 * @return	void
	 */
	function getPartnerByOccupation($occupations, $pid='', $orderBy='')		{

			// Restrict to certain occupations
		if (is_array($occupations)) {

				// Get a comma-separated list of the occupations-array, as needed to build an SQL-statement
			$occupations = t3lib_div::csvValues($occupations,',','');
			$occupations = t3lib_div::uniqueList($occupations);

				// Restric to PID?
			$wherePid = ($pid or $pid == '0') ? ' AND pid='.$pid : '';

				// Get all partner-UIDs which have the requested occupations (mm-relation)
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
				'tx_partner_main.uid',
				'tx_partner_main',
				'tx_partner_main_occupations_mm',
				'tx_partner_val_occupations',
				'AND tx_partner_main_occupations_mm.uid_foreign IN ('.$occupations.')'.$wherePid.t3lib_BEfunc::deleteClause('tx_partner_main'),
				'',
				$orderBy);
			while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$partnerUIDs.= $rec['uid'].',';
			}

				// If partners could be selected, remove duplicates and get the partners for the query
			if ($partnerUIDs)		{
				$partnerUIDs = t3lib_div::uniqueList($partnerUIDs);
				$this->getPartnerByList(explode(',',$partnerUIDs));
			}
		}
	}


	/**
	 * Gets partner data based on an array of partners and relationship types.
	 * The function will select all partners which are connected to the array of requested partners by
	 * at least one of the requested relationship types.
	 *
	 * The selected data is made available in $this->query.
	 *
	 * @param	array		$partnerListArray: Array with partner-UIDs from which the function will look for related partners
	 * @param	array		$relationshipTypesArray: Array with relationship types (key of tx_partner_val_rel_types). This defines the kind of relationship the partners must have to be selected.
	 * @param	integer		$pid: Page-ID (Optional). If not provided, the function will look for partners in all PID's.
	 * @return	void
	 */
	function getPartnerByRelationshipType($partnerListArray, $relationshipTypesArray, $pid='')		{

			// Both arrays must be provided to get a result
		if (is_array($relationshipTypesArray) and is_array($partnerListArray)) {

				// Get a comma-separated list of the relationship-types-array, as needed to build an SQL-statement
			$relationshipTypes = t3lib_div::csvValues($relationshipTypesArray,',','');
			$relationshipTypes = t3lib_div::uniqueList($relationshipTypes);

				// Get a comma-separated list of the partner-array, as needed to build an SQL-statement
			$partnerList = t3lib_div::csvValues($partnerListArray,',','');
			$partnerList = t3lib_div::uniqueList($partnerList);

				// Restric to PID?
			$wherePid = ($pid or $pid == '0') ? ' AND pid='.$pid : '';

				// Get all partner-UIDs which are related to the requested partners by the requested relationship type
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'tx_partner_relationships.uid_secondary',
				'tx_partner_relationships',
				'tx_partner_relationships.uid_primary IN ('.$partnerList.')'.' AND tx_partner_relationships.type IN ('.$relationshipTypes.')'.$wherePid.t3lib_BEfunc::deleteClause('tx_partner_relationships')
			);
			while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$partnerUIDs .= $rec['uid_secondary'].',';
			}

				// If partners could be selected, remove duplicates and get the partners for the query
			if ($partnerUIDs)		{
				$partnerUIDs = t3lib_div::uniqueList($partnerUIDs);
				$this->getPartnerByList(explode(',',$partnerUIDs));
			}
		}
	}
	
	
 	/**
	 * Gets partners by field comparison. This is basically the same as $this->getPartnerBySearchOperators
	 * but with an easier interface. The $searchArray is simply built like this:
	 *
	 *	$searchArray = array(
	 *		'first_name' => 'Test',
	 *		'last_name' => 'Whatever*',
	 *		$fieldName => $value,
	 *	);
	 *
	 * This also means that you cannot directly influence how each field is compared with the values from the database.
	 * But you can use '*' as a wilcard. If there is a '*' at the beginning of search string, the search operator 'ND' (ends with)
	 * is chosen, and if there is a '*' at the end of a value, the search operator 'BG' (begins with) is chosen. '*'-signs
	 * in the middle of the search string are NOT reagarded as wilcards, but simply as part of the search string. If there
	 * is no wildcard used, the comparison is done with 'EQ' (equals).
	 *
	 * The selected data is made available in $this->query.
	 *
	 * @param	array		$searchStrings: Array of field-name/search-string pairs
	 * @param	integer		$pid: Only search in this PID (optional)
	 * @param	boolean		$exactSearch: If set to false, a wildcard is appended to each search value (default = true)
	 * @param	integer		$limitBegin: Limit the search from here (optional)
	 * @param	integer		$limitMax: End the search-limit here (optional)
	 * @return	integer		Total number of partners selectable by the current options (not regarding the limits)
	 * @see $this->getPartnerBySearchOperators
	 */
	function getPartnerBySearchStrings($searchStrings, $pid='', $exactSearch=true, $limitBegin='', $limitMax='')		{
		
			// Check if a search strings array was provided
		if (!is_array($searchStrings)) return false;
		
			// Build the search array
		foreach ($searchStrings as $theField => $theValue)		{
			if (!empty($theValue))		{
				if (($exactSearch == false) && (substr($theValue, -1) != '*')) $theValue.='*';
				$op = 'EQ';
				if (substr($theValue, 0, 1) == '*')		{
					$op = 'ND';
					$theValue = substr($theValue, 1);
				}
				if (substr($theValue, -1) == '*')		{
					$op = 'BG';
					$theValue = substr($theValue, 0, strlen($theValue)-1);
				}
				$searchArray[$theField] = array(
					'op' => $op,
					'val' => $theValue,
				);
			}
		}
		
			// If requested, add PID-restriction
		if (!empty($pid))		{
			$searchArray['pid'] = array(
				'op' => 'EQ',
				'val' => intval($pid),
			);	
		}
		
			// Call the query function and return the result
		return $this->getPartnerBySearchOperators($searchArray, $limitBegin, $limitMax);		
	}
	
 	/**
	 * Gets partners by complex search operators. The $searchArray must be built like this
	 *
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
	 * The selected data is made available in $this->query.
	 *
	 * @param	array		$searchArray: Array of search operators/value pairs
	 * @param	integer		$limitBegin: Limit the search from here (optional)
	 * @param	integer		$limitMax: End the search-limit here (optional)
	 * @return	integer		Total number of partners selectable by the current options (not regarding the limits)
	 */
	function getPartnerBySearchOperators($searchArray, $limitBegin='', $limitMax='')		{

		if (is_array($searchArray))		{
			$whereClause = tx_partner_div::buildWhereClauseByOperators('tx_partner_main', $searchArray);
		}

			// Check if the WHERE clause could be built
		if (empty($whereClause)) return false;

			// Limit to a certain number?
		if (!empty($limitMax))		{
			$limitBegin = $limitBegin ? $limitBegin : '0';
			$limit = $limitBegin.','.$limitMax;
		}

			// Get the partner UID's and the total number
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_partner_main', $whereClause.t3lib_BEfunc::deleteClause('tx_partner_main'), '', 'label', $limit);
		$resTotal = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(*)', 'tx_partner_main', $whereClause.t3lib_BEfunc::deleteClause('tx_partner_main'));
		if (is_resource($res))		{
			while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))		{
				$partnerUIDs.= $rec['uid'].',';;
			}
			$totalNo = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resTotal);
		}

			// If partners could be selected, remove duplicates and get the partners for the query. Return the total number of partners selectable
		if ($partnerUIDs)		{
			$partnerUIDs = t3lib_div::uniqueList($partnerUIDs);
			$this->getPartnerByList(explode(',',$partnerUIDs));
			return intval($totalNo['COUNT(*)']);
		}
	}

	/**
	 * Gets all partners with their birthday within a certain range.
	 *
	 * The selected data is made available in $this->query.
	 *
	 * @param	string		$fromYear (YYYY): Start of the search period (Year). If left empty, the year will be disregarded in the search.
	 * @param	string		$fromMonth (MM): Start of the search period (Month). Must be supplied.
	 * @param	string		$fromDay (DD): Start of the search period (Day). Must be supplied.
	 * @param	string		$toYear (YYYY): End of the search period (Year). Optional
	 * @param	string		$toMonth (MM): End of the search period (Month). Optional.
	 * @param	string		$toDay (DD): End of the search period (Day). Optional.
	 * @param	integer		$pid: Page-ID (Optional). If not provided, the function will look for partners in all PID's.
	 * @return	void
	 */
	function getPartnerByBirthday($fromYear='0000', $fromMonth='01', $fromDay='01', $toYear='9999', $toMonth='12', $toDay='31', $pid='')		{

			// Convert the input values to string of the proper length, with leading zeros
		$fromYear = str_pad((string)$fromYear, 4, '0', STR_PAD_LEFT);
		$fromMonth = str_pad((string)$fromMonth, 2, '0', STR_PAD_LEFT);
		$fromDay = str_pad((string)$fromDay, 2, '0', STR_PAD_LEFT);
		$toYear = str_pad((string)$toYear, 4, '0', STR_PAD_LEFT);
		$toMonth = str_pad((string)$toMonth, 2, '0', STR_PAD_LEFT);
		$toDay = str_pad((string)$toDay, 2, '0', STR_PAD_LEFT);

			// Check if the selection includes the year
		if ($fromYear != '0000')		{
			$toYear = ($toYear>=$fromYear) ? $toYear : '9999';
			$whereClause = "birth_date > '$fromYear$fromMonth$fromDay' AND birth_date < '$toYear$toMonth$toDay'";
			$orderBy = "birth_date ASC";
		} else {
			$whereClause = "RIGHT(birth_date,4) > '$fromMonth$fromDay' AND RIGHT(birth_date,4) < '$toMonth$toDay'";
			$orderBy = "RIGHT(birth_date,4) ASC";
		}

			// Restric to PID?
		$wherePid = ($pid or $pid == '0') ? ' AND pid='.$pid : '';

			// Get the partner UID's
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'tx_partner_main', $whereClause.$wherePid.t3lib_BEfunc::deleteClause('tx_partner_main'), '', $orderBy);
		if ($res)		{
			while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))		{
				$partnerUIDs.= $rec['uid'].',';;
			}
		}

			// If partners could be selected, remove duplicates and get the partners for the query
		if ($partnerUIDs)		{
			$partnerUIDs = t3lib_div::uniqueList($partnerUIDs);
			$this->getPartnerByList(explode(',',$partnerUIDs));
		}
	}


	/**
	 * Gets the contact-infos in the requested scope. The partner data must already be loaded and be available
	 * in $this->query, otherwise the function will do nothing. The contact-infos will also be stored in the
	 * class variable $this->query.
	 *
	 * @param	integer		$scope: Scope for the contact info (0 = None, 1 = Only standard entries, 2 = All entries)
	 * @return	void
	 */
	function getContactInfo($scope='2')		{
		if (is_array($this->query))		{
			foreach ($this->query as $uid=>$partner)		{
				$this->query[$uid]->getContactInfo($scope);
			}
		}
	}


	/**
	 * Gets the relationships of the partner. The partner data must already be loaded and be available
	 * in $this->query, otherwise the function will do nothing. The relationships will also be stored in the
	 * class variable $this->query.
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
		if (is_array($this->query))		{
			foreach ($this->query as $uid=>$partner)		{
				$this->query[$uid]->getRelationships($scope, $restrictToRelationshipTypes);
			}
		}
	}


	/*********************************************
 	*
 	* GETTING FORMATTED DATA
 	*
 	*********************************************/

 	/**
 * Returns the data currently selected by the query in the required format.
 * The query must be available in $this->query, so you must call at least
 * one getPartner* function first (e.g. $this->getPartnerByList)
 *
 * @param	string		$format: Determines the format for the output
 * @param	string		$fieldScope: Determines the scope of how many fields will be in the result. Can be either 'all' (all fields in $TCA) or 'field_selection' (all fields in the fieldSelection array)
 * @param	integer		$contactInfoScope: Determines the scope of how much contact information will be in the result. Can be 0 (no contact-info), 1 (only standard contact-info) or 2 (all contact-info)
 * @param	array		$fieldSelection: A field selection array as stored in tx_partner_reports-field_selection
 * @param	boolean		$processedValues: If set, the values will be processed (e.g. a UNIX-timestamp will be converted to a human-readable format)
 * @param	boolean		$techValues: If set, the the technical values will be preserved if a value is processed
 * @param	boolean		$blankValues: If set, the result will also contain empty fields
 * @param	array		$additionalParams: These parameters will be appended when calling the formatting-function
 * @return	string		Formatted data
 */
	function getFormattedDataByQuery($format='CSV', $fieldScope='all', $contactInfoScope='2', $fieldSelection=array(), $processedValues=TRUE, $techValues=TRUE, $blankValues=TRUE, $additionalParams=array())		{
		global $TYPO3_CONF_VARS;

			// Get the selected values from the current query (processed if requested)
		$data = $this->getSelectedValuesFromCurrentQuery($fieldScope, $contactInfoScope, $fieldSelection, $processedValues, $techValues, $blankValues);

			// Get the structure
		$structure = $this->getStructure($fieldScope, $contactInfoScope, $fieldSelection);

			// Form the params to call the formatting function
		$params = array(
			'data' => $data,
			'structure' => $structure,
		);
		$params = $params + $additionalParams;

			// Format the data using the formatting function
		$content = t3lib_div::callUserFunction($TYPO3_CONF_VARS['EXTCONF']['partner']['formats'][$format]['formatFunc'], $params, $this, '', 0);

		return $content;
	}


	/**
	 * Gets formatted data as requested by the selection criteria and options saved in
	 * a report. The report must be identified by its UID.
	 *
	 * @param	integer		$uid: UID of the report for which to build the query
	 * @param	string		$format: Determines the format for the output
	 * @param	integer		$limit: Limit to a certain number of partners
	 * @return	string		Formatted data
	 */
	function getFormattedDataByReport($uid, $format='CSV', $limit='')		{
		global $TYPO3_CONF_VARS;

			// Get the requested report
		$report = tx_partner_div::getReport($uid, $limit);

			// If the report was found, get the details
		if (is_array($report))		{

				// Get the selected partners
			$this->getPartnerByList($report['selected_partners']);

				// Get the requested contact information
			$this->getContactInfo($report['contact_info_scope']);

				// Get the format options from the flex-form values from the report
			$ffOptionsArray = t3lib_div::xml2array($report['format_options']);
			foreach ($ffOptionsArray['data'] as $theFormat => $options)		{
				foreach ($options['lDEF'] as $theOption => $value)		{
					$formatOptions[$theFormat][$theOption] = $value['vDEF'];
				}
			}

				// Get the selected values from the current query (processed if requested)
			$data = $this->getSelectedValuesFromCurrentQuery($report['field_scope'], $report['contact_info_scope'], $report['field_selection'], $report['processed_values'], $report['tech_keys'], $report['blank_values']);

				// Get the structure
			$structure = $this->getStructure($report['field_scope'], $report['contact_info_scope'], $report['field_selection']);

				// Form the params to call the formatting function
			$params = array(
				'data' => $data,
				'structure' => $structure,
				'formatOptions' => $formatOptions[$format],
				'allowedFormats' => $report['allowed_formats'],
				'reportUid' => $uid,
			);
			

				// Format the data using the formatting function
			$content = t3lib_div::callUserFunction($TYPO3_CONF_VARS['EXTCONF']['partner']['formats'][$format]['formatFunc'], $params, $this, '', 0);

			return $content;
		}
	}



	/*********************************************
 	*
 	* INTERNAL HELPER METHODS
 	*
 	*********************************************/


	/**
	 * This function builds a structure based on the fieldScope ('all' or 'field_selection') and the contactInfoScope.
	 * If the fieldScope is 'field_selection', a fieldSelection array must be provided.
	 *
	 * The output is an array with the structures for 'file' and 'screen' output.
	 *
	 * @param	string		$fieldScope: Determines the scope of how many fields must be included in the structure. Can be either 'all' (all fields in $TCA) or 'field_selection' (all fields in the fieldSelection array)
	 * @param	integer		$contactInfoScope: Determines the scope of how much contact information must be included in the structure. Can be 0 (no contact-info), 1 (only standard contact-info) or 2 (all contact-info)
	 * @param	array		$fieldSelection: A field selection array as stored in tx_partner_reports-field_selection
	 * @return	array		Structure with two parts ('file' and 'screen')
	 */
	function getStructure($fieldScope, $contactInfoScope, $fieldSelection)		{
		global $TCA, $TYPO3_CONF_VARS;

			// Determine the tables for which to get the structure
		$tables = array('tx_partner_main');
		if ($contactInfoScope != 0) $tables[] = 'tx_partner_contact_info';

			// Determine the media
		$media = array('file', 'screen');

		switch ($fieldScope) {
			case 'all':

					// Go through all TCA-fields in all requested tables and build the structure
				foreach ($tables as $theTable)		{

						// Get the TCA-fields
					t3lib_div::loadTCA($theTable);
					foreach ($TCA[$theTable]['columns'] as $field=>$value)		{
						$s[$field]['label'] = tx_partner_lang::getLabel($theTable.'.'.$field);
						$s[$field]['length'] = $value['config']['size'];
					}

						// Build the structure
					$structure['file'][$theTable] = $s;
					$structure['screen'][$theTable] = $s;
				}


			break;

			case 'field_selection':

					// The structure can only be built if a fieldSelection array was provided
				if (is_array($fieldSelection))		{

						// Go through the fieldSelection array and build the structure
					foreach ($fieldSelection as $theTable=>$fields)		{
						//debug ($fieldSelection);
						foreach ($fields as $theField=>$theValues)		{
							$s = array();
							if ($theValues['file'] or $theValues['screen'])		{
								if (substr($theField, 0, 1) == '_')		{
										// User-defined field
									$s['label'] = tx_partner_lang::getLabel($TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields'][$theTable][$theField]['label']);
								} else {
										// Regular field
									$s['label'] = tx_partner_lang::getLabel($theTable.'.'.$theField);
								}
								$s['length'] = $theValues['length'];
							}

								// The field was requested for file-output
							if ($theValues['file'])		{
								$structure['file'][$theTable][$theField] = $s;
							}

								// The field was requested for screen-output
							if ($theValues['screen'])		{
								$structure['screen'][$theTable][$theField] = $s;
							}
						}
					}
				}

			break;
		}

			// Make sure partner is first, then contact-info
		$tmp = $structure;
		unset ($structure);
		foreach ($media as $theMedia)		{
			if ($tmp[$theMedia]['tx_partner_main']) $structure[$theMedia]['tx_partner_main'] = $tmp[$theMedia]['tx_partner_main'];
			if ($tmp[$theMedia]['tx_partner_contact_info']) $structure[$theMedia]['tx_partner_contact_info'] = $tmp[$theMedia]['tx_partner_contact_info'];
		}

		return $structure;
	}



	/**
	 * Gets the fields selected by the parameters from the current query.
	 *
	 * @param	string		$fieldScope: Determines the scope of how many fields must be selected from the query. Can be either 'all' (all fields in $TCA) or 'field_selection' (all fields in the fieldSelection array)
	 * @param	integer		$contactInfoScope: Determines the scope of how much contact information must be selected from the query. Can be 0 (no contact-info), 1 (only standard contact-info) or 2 (all contact-info)
	 * @param	array		$fieldSelection: A field selection array as stored in tx_partner_reports-field_selection
	 * @param	boolean		$processedValues: If set, the values will be processed (e.g. a UNIX-timestamp will be converted to a human-readable format)
	 * @param	boolean		$techValues: If set, the the technical values will be preserved if a value is processed
	 * @param	boolean		$blankValues: If set, the result will also contain empty fields
	 * @return	array		Selected values from the current query
	 */
	function getSelectedValuesFromCurrentQuery($fieldScope, $contactInfoScope, $fieldSelection, $processedValues=TRUE, $techValues=FALSE, $blankValues=FALSE)		{
		global $TYPO3_CONF_VARS;

			// If no contact-infos are requested, unset them
		if ($contactInfoScope == '0' && is_array($fieldNames['tx_partner_contact_info']))		{
			unset ($fieldNamesArray['tx_partner_contact_info']);
		}

			// Determine the sys-fields
		$sysFields = array('uid'=>'uid', 'pid'=>'pid', 'tstamp'=>'tstamp', 'crdate'=>'crdate', 'cruser_id'=>'cruser_id', 'deleted'=>'deleted', 'hidden'=>'hidden');

			// Determine the user-defined fields
		foreach ($TYPO3_CONF_VARS['EXTCONF']['partner']['user_fields']['tx_partner_main'] as $k => $v)		{
			$userFields[$k] = $k;
		}

			// If a query can be found, continue...
		if (is_array($this->query))		{

				// Get the selected values for the partner record
			foreach ($this->query as $partnerUid=>$partnerObj)		{

					// Get the fields that are allowed for the current type (including sys-fields and palette-fields)
				$partnerTypeFields = $sysFields + $userFields + tx_partner_div::getAllTypeFields('tx_partner_main', $partnerObj->data['type']);

					// Get the values for each requested field and process it if requested
				foreach ($partnerTypeFields as $theField)		{
					$fv = tx_partner_query::getFieldData('tx_partner_main', $theField, $partnerObj->data[$theField], $fieldScope, $fieldSelection, $processedValues, $techValues, $blankValues);
					if (is_array($fv)) $data[$partnerUid]['tx_partner_main'][$theField] = $fv;
				}

					// If there are contact-infos, get them as well
				if (is_array($partnerObj->contactInfo))		{
					foreach ($partnerObj->contactInfo as $contactInfoUid=>$theContactInfo)		{

							// Get the fields that are allowed for the current type (including sys-fields and palette-fields)
						$contactInfoTypeFields = $sysFields + tx_partner_div::getAllTypeFields('tx_partner_contact_info', $theContactInfo->data['type']);

						foreach ($contactInfoTypeFields as $theField)		{
							$fv = tx_partner_query::getFieldData('tx_partner_contact_info', $theField, $theContactInfo->data[$theField], $fieldScope, $fieldSelection, $processedValues, $techValues, $blankValues);
							if (is_array($fv)) $data[$partnerUid]['tx_partner_contact_info'][$contactInfoUid][$theField] = $fv;
						}
					}
				}
				

					// If there are related partners as primary, get them as well
				if (is_array($partnerObj->relatedPartnerAsPrimary))		{
					foreach ($partnerObj->relatedPartnerAsPrimary as $relatedPartnerUid=>$theRelatedPartner)		{
						
							// Get the fields that are allowed for the current type (including sys-fields and palette-fields)
						$partnerTypeFields = $sysFields + $userFields + tx_partner_div::getAllTypeFields('tx_partner_main', $theRelatedPartner->data['type']);
		
							// Get the values for each requested field and process it if requested
						foreach ($partnerTypeFields as $theField)		{
							$fv = tx_partner_query::getFieldData('tx_partner_main', $theField, $theRelatedPartner->data[$theField], $fieldScope, $fieldSelection, $processedValues, $techValues, $blankValues);
							if (is_array($fv)) $data[$partnerUid]['related_as_primary'][$relatedPartnerUid][$theField] = $fv;
						}
					}
				}
				
					// If there are related partners as secondary, get them as well
				if (is_array($partnerObj->relatedPartnerAsSecondary))		{
					foreach ($partnerObj->relatedPartnerAsSecondary as $relatedPartnerUid=>$theRelatedPartner)		{
						
							// Get the fields that are allowed for the current type (including sys-fields and palette-fields)
						$partnerTypeFields = $sysFields + $userFields + tx_partner_div::getAllTypeFields('tx_partner_main', $theRelatedPartner->data['type']);
		
							// Get the values for each requested field and process it if requested
						foreach ($partnerTypeFields as $theField)		{
							$fv = tx_partner_query::getFieldData('tx_partner_main', $theField, $theRelatedPartner->data[$theField], $fieldScope, $fieldSelection, $processedValues, $techValues, $blankValues);
							if (is_array($fv)) $data[$partnerUid]['related_as_secondary'][$relatedPartnerUid][$theField] = $fv;
						}
					}
				}
			}
		}
		return $data;
	}


	/**
	 * Internal function to get the proper value for one field.
	 *
	 * @param	string		$table: Name of the table from which the value comes from
	 * @param	string		$field: Name of the field from which the value comes from
	 * @param	string		$value: Value to be processed
	 * @param	string		$fieldScope: Determines the scope of how many fields must be selected from the query. Can be either 'all' (all fields in $TCA) or 'field_selection' (all fields in the fieldSelection array)
	 * @param	array		$fieldSelection: A field selection array as stored in tx_partner_reports-field_selection
	 * @param	boolean		$processedValues: If set, the values will be processed (e.g. a UNIX-timestamp will be converted to a human-readable format)
	 * @param	boolean		$techValues: If set, the the technical values will be preserved if a value is processed
	 * @param	boolean		$blankValues: If set, the result will also contain empty fields
	 * @return	array		Selected values from the current query
	 */
	function getFieldData($table, $field, $value, $fieldScope, $fieldSelection, $processedValues, $techValues, $blankValues)		{

			// If all fields are requested and no field-selection is provided, mark all fields as requested (file and screen)
		if ($fieldScope == 'all' && !is_array($fieldSelection))		{
			$allFields = TRUE;
		}
			// Only include the field if blank values are generally allowed or if the field actually contains a value
		if ($blankValues or (!$blankValues and $value!=''))		{

				// Only include the field if EITHER one of the following is true:
				// - Field scope is 'all' which means we must include all fields unconditionally
				// - Field scope is 'field_selection' AND the field is requested either as 'file' or as 'screen' output
			if ($fieldScope == 'all' or ($fieldScope == 'field_selection' and ($fieldSelection[$table][$field]['file']) or $fieldSelection[$table][$field]['screen']))		{

					// Get processed or raw values
				$data['rawValue'] = $value;
				if ($processedValues)		{
					$data['value'] = $this->getProcessedValue($table, $field, $value, $techValues);
				} else {
					$data['value'] = $value;
				}

					// Get the remaining field attributes
				$data['label'] = tx_partner_lang::getLabel($table.'.'.$field);
				$data['file'] = $fieldSelection[$table][$field]['file'];
				$data['screen'] = $fieldSelection[$table][$field]['screen'];
				$data['length'] = $fieldSelection[$table][$field]['length'];

					// If all fields are requested (without providing a field-selection array) mark the field as requested for all formats
				if ($allFields)		{
					$data['file'] = '1';
					$data['screen'] = '1';
				}
			}
		}
		return $data;
	}


	/**
	 * Internal function to get a processed value for a single field. Mainly uses
	 * t3lib_BEfunc::getProcessedValue, but not all values can be processed by this
	 * function, so a little add-on is needed.
	 *
	 * @param	string		$table: Name of the table from which the value comes from
	 * @param	string		$field: Name of the field from which the value comes from
	 * @param	string		$value: Value to be processed
	 * @param	boolean		$techValues: If set, the the technical values will be preserved if a value is processed
	 * @return	string		Processed value (htmlspecialchar'd)
	 */
	function getProcessedValue($table, $field, $value, $techValues=FALSE)		{
		global $TCA;

			// t3lib_BEfunc::getProcessedValueExtra produces errors when used under FE-conditions. This is a quick-fix.
			// If one day there is a decent FE-editing solution, hopefully there will be an equivalent function to use
			// under FE-conditions.
		if (TYPO3_MODE == 'BE')		{
			$pV = t3lib_BEfunc::getProcessedValueExtra($table, $field, $value);
		} else {
			$pV = $value;
		}

		if ($field == 'tstamp' || $field == 'crdate') $pV = strftime('%c', $value);

		if ($field == 'birth_date' || $field == 'death_date')		{
			$dateArray = tx_partner_div::getDateArray($value);
			if (is_array($dateArray)) $pV = sprintf('%02d.%02d.%04d', $dateArray['day'], $dateArray['month'], $dateArray['year']);
		}

			// Get values from static_info_tables
		$staticInfoTableFields = array(
			'country'  => 'static_countries',
			'po_country' => 'static_countries',
			'nationality' => 'static_countries',
			'mother_tongue' => 'static_languages',
			'preferred_language' => 'static_languages',
		);

		foreach ($staticInfoTableFields as $staticField=>$staticTable)		{
			if ($staticField == $field)	{

				$staticLabelField = reset(tx_staticinfotables_div::getTCAlabelField($staticTable, true));

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($staticLabelField, $staticTable, 'uid='.$value);
				if ($res)	{
					$rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					$pV = $rec[$staticLabelField];
				}
			}
		}

		if ($techValues && $pV!=$value)		{
			$pV.= ' ('.$value.')';
		}

		return htmlspecialchars($pV);
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_query.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_query.php']);
}

?>