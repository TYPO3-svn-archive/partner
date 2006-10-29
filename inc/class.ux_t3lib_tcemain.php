<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 David Bruehlmeier (typo3@bruehlmeier.com)
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
* Temporary XCLASS of TCEmain as a workaround until the hook is
* provided in the core.
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(t3lib_extMgm::extPath('partner').'inc/class.tx_partner_tcemainprocdm.php');

class ux_t3lib_tcemain extends t3lib_TCEmain {

	/**
	 * Processing the data-array
	 * Call this function to process the data-array set by start()
	 *
	 * @return	void
	 */
	function process_datamap() {
		global $TCA, $TYPO3_CONF_VARS;

			// First prepare user defined objects (if any) for hooks which extend this function:
		$hookObjectsArr = array();
		if (is_array ($TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'])) {
			foreach ($TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'] as $classRef) {
				$hookObjectsArr[] = &t3lib_div::getUserObj($classRef);
			}
		}

			// Organize tables so that the pages-table are always processed first. This is required if you want to make sure that content pointing to a new page will be created.
		$orderOfTables = Array();
		if (isset($this->datamap['pages']))	{		// Set pages first.
			$orderOfTables[]='pages';
		}
		reset($this->datamap);
		while (list($table,) = each($this->datamap))	{
			if ($table!='pages')	{
				$orderOfTables[]=$table;
			}
		}

			// Process the tables...
		foreach($orderOfTables as $table)	{
				/* Check if
					- table is set in $TCA,
					- table is NOT readOnly,
					- the table is set with content in the data-array (if not, there's nothing to process...)
					- permissions for tableaccess OK
				*/
			$modifyAccessList = $this->checkModifyAccessList($table);
			if (!$modifyAccessList)	{
				$this->log($table,$id,2,0,1,"Attempt to modify table '%s' without permission",1,array($table));
			}
			if (isset($TCA[$table]) && !$this->tableReadOnly($table) && is_array($this->datamap[$table]) && $modifyAccessList)	{
				if ($this->reverseOrder)	{
					$this->datamap[$table] = array_reverse($this->datamap[$table], 1);
				}

					// For each record from the table, do:
					// $id is the record uid, may be a string if new records...
					// $incomingFieldArray is the array of fields
				foreach($this->datamap[$table] as $id => $incomingFieldArray)	{
					if (is_array($incomingFieldArray))	{

							// Hook: processDatamap_preProcessIncomingFieldArray
						foreach($hookObjectsArr as $hookObj)	{

							if (method_exists($hookObj, 'processDatamap_preProcessIncomingFieldArray')) {
								$hookObj->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $this);
							}
						}

							// ******************************
							// Checking access to the record
							// ******************************
						$recordAccess = 0;
						$old_pid_value = '';
						if (!t3lib_div::testInt($id)) {               // Is it a new record? (Then Id is a string)
							$fieldArray = $this->newFieldArray($table);	// Get a fieldArray with default values
							if (isset($incomingFieldArray['pid']))	{	// A pid must be set for new records.
									// $value = the pid
		 						$pid_value = $incomingFieldArray['pid'];

									// Checking and finding numerical pid, it may be a string-reference to another value
								$OK = 1;
								if (strstr($pid_value,'NEW'))	{	// If a NEW... id
									if (substr($pid_value,0,1)=='-') {$negFlag=-1;$pid_value=substr($pid_value,1);} else {$negFlag=1;}
									if (isset($this->substNEWwithIDs[$pid_value]))	{	// Trying to find the correct numerical value as it should be mapped by earlier processing of another new record.
										$old_pid_value = $pid_value;
										$pid_value=intval($negFlag*$this->substNEWwithIDs[$pid_value]);
									} else {$OK = 0;}	// If not found in the substArray we must stop the proces...
								}
								$pid_value = intval($pid_value);

									// The $pid_value is now the numerical pid at this point
								if ($OK)	{
									$sortRow = $TCA[$table]['ctrl']['sortby'];
									if ($pid_value>=0)	{	// Points to a page on which to insert the element, possibly in the top of the page
										if ($sortRow)	{	// If this table is sorted we better find the top sorting number
											$fieldArray[$sortRow] = $this->getSortNumber($table,0,$pid_value);
										}
										$fieldArray['pid'] = $pid_value;	// The numerical pid is inserted in the data array
									} else {	// points to another record before ifself
										if ($sortRow)	{	// If this table is sorted we better find the top sorting number
											$tempArray=$this->getSortNumber($table,0,$pid_value);	// Because $pid_value is < 0, getSortNumber returns an array
											$fieldArray['pid'] = $tempArray['pid'];
											$fieldArray[$sortRow] = $tempArray['sortNumber'];
										} else {	// Here we fetch the PID of the record that we point to...
											$tempdata = $this->recordInfo($table,abs($pid_value),'pid');
											$fieldArray['pid']=$tempdata['pid'];
										}
									}
								}
							}
							$theRealPid = $fieldArray['pid'];
								// Now, check if we may insert records on this pid.
							if ($theRealPid>=0)	{
								$recordAccess = $this->checkRecordInsertAccess($table,$theRealPid);	// Checks if records can be inserted on this $pid.
							} else {
								debug('Internal ERROR: pid should not be less than zero!');
							}
							$status = 'new';						// Yes new record, change $record_status to 'insert'
						} else {	// Nope... $id is a number
							$fieldArray = Array();
							$recordAccess = $this->checkRecordUpdateAccess($table,$id);
							if (!$recordAccess)		{
								$propArr = $this->getRecordProperties($table,$id);
								$this->log($table,$id,2,0,1,"Attempt to modify record '%s' (%s) without permission. Or non-existing page.",2,array($propArr['header'],$table.':'.$id),$propArr['event_pid']);
							} else {	// Next check of the record permissions (internals)
								$recordAccess = $this->BE_USER->recordEditAccessInternals($table,$id);
								if (!$recordAccess)		{
									$propArr = $this->getRecordProperties($table,$id);
									$this->log($table,$id,2,0,1,"recordEditAccessInternals() check failed. [".$this->BE_USER->errorMsg."]",2,array($propArr['header'],$table.':'.$id),$propArr['event_pid']);
								} else {	// Here we fetch the PID of the record that we point to...
									$tempdata = $this->recordInfo($table,$id,'pid');
									$theRealPid = $tempdata['pid'];
								}
							}
							$status = 'update';	// the default is 'update'
						}

							// **************************************
							// If access was granted above, proceed:
							// **************************************
						if ($recordAccess)	{

							list($tscPID) = t3lib_BEfunc::getTSCpid($table,$id,$old_pid_value ? $old_pid_value : $fieldArray['pid']);	// Here the "pid" is sent IF NOT the old pid was a string pointing to a place in the subst-id array.
							$TSConfig = $this->getTCEMAIN_TSconfig($tscPID);
							if ($status=='new' && $table=='pages' && is_array($TSConfig['permissions.']))	{
								$fieldArray = $this->setTSconfigPermissions($fieldArray,$TSConfig['permissions.']);
							}

							$fieldArray = $this->fillInFieldArray($table,$id,$fieldArray,$incomingFieldArray,$theRealPid,$status,$tscPID);

								// NOTICE! All manipulation beyond this point bypasses both "excludeFields" AND possible "MM" relations / file uploads to field!

							$fieldArray = $this->overrideFieldArray($table,$fieldArray);	// NOTICE: This overriding is potentially dangerous; permissions per field is not checked!!!

								// Setting system fields
							if ($status=='new')	{
								if ($TCA[$table]['ctrl']['crdate'])	{
									$fieldArray[$TCA[$table]['ctrl']['crdate']]=time();
								}
								if ($TCA[$table]['ctrl']['cruser_id'])	{
									$fieldArray[$TCA[$table]['ctrl']['cruser_id']]=$this->userid;
								}
							} elseif ($this->checkSimilar) {	// Removing fields which are equal to the current value:
								$fieldArray = $this->compareFieldArrayWithCurrentAndUnset($table,$id,$fieldArray);
							}
							if ($TCA[$table]['ctrl']['tstamp'])	{
								$fieldArray[$TCA[$table]['ctrl']['tstamp']]=time();
							}

								// Hook: processDatamap_postProcessFieldArray
							foreach($hookObjectsArr as $hookObj)	{
								if (method_exists($hookObj, 'processDatamap_postProcessFieldArray')) {
									$hookObj->processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $this);
								}
							}

								// Performing insert/update. If fieldArray has been unset by some userfunction (see hook above), don't do anything
								// Kasper: Unsetting the fieldArray is dangerous; MM relations might be saved already and files could have been uploaded that are now "lost"
							if (is_array($fieldArray)) {
								if ($status=='new')	{
	//								if ($pid_value<0)	{$fieldArray = $this->fixCopyAfterDuplFields($table,$id,abs($pid_value),0,$fieldArray);}	// Out-commented 02-05-02: I couldn't understand WHY this is needed for NEW records. Obviously to proces records being copied? Problem is that the fields are not set anyways and the copying function should basically take care of this!
									$this->insertDB($table,$id,$fieldArray);
								} else {
									$this->updateDB($table,$id,$fieldArray);
								}
							}

								// Inserted by dbruehlmeier (2004-12-30)
								// Hook: processDatamap_afterDatabaseOperations
							foreach($hookObjectsArr as $hookObj)	{
								if (method_exists($hookObj, 'processDatamap_afterDatabaseOperations')) {
									$hookObj->processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $this);
								}
							}
						}	// if ($recordAccess)	{
					}	// if (is_array($incomingFieldArray))	{
				}
			}
		}
		$this->dbAnalysisStoreExec();
		$this->removeRegisteredFiles();
	}


}
?>