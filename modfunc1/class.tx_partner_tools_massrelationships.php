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
* 'Mass Change Relationships' Tool as a sub-submodule of
* Web>Partner>Tools
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(PATH_t3lib.'class.t3lib_tceforms.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_main.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_relationship.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_query.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');



/**
 * Class for the 'Mass Change Relationships' Tool as a sub-submodule of
 * Web>Partner>Tools
 *
 * @author	David Bruehlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_partner
 */
class tx_partner_tools_massrelationships extends t3lib_extobjbase {



	/**
	 * Constructor of the class
	 *
	 * @return	[type]		...
	 */
	function tx_partner_tools_massrelationships()		{
		global $LANG;

			// Include the general language file
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Define the default styles
		$this->defaultListStyle = 'height="18px" style="border-bottom-width:1px; border-bottom-color:#C6C2BD; border-bottom-style:solid;" nowrap';
		$this->defaultHeader1Style = 'bgcolor="#CBC7C3"';

		$this->t3lib_TCEforms = t3lib_div::makeInstance('t3lib_TCEforms');
		$this->t3lib_TCEforms->backPath = $GLOBALS['BACK_PATH'];
	}



	/**
	 * Creation of the submodule.
	 *
	 * @return	string		The content
	 */
	function main()	{
		global $LANG;

			// Get the form
		$result = $this->massChangeRelationships();

			// Prepare the output
		$content.= $this->pObj->doc->section($LANG->getLL('tx_partner.modfunc.tools.massrelationships'), '', 0, 1);
		$content.= $this->pObj->doc->section($this->pObj->MOD_MENU['method'][$this->pObj->MOD_SETTINGS['method']], $result, 1, 1);
		$content.= $this->t3lib_TCEforms->JSbottom();

		return $content;
	}


	/**
	 * Main function for mass changing relationships.
	 *
	 * @return	[type]		...
	 */
	function massChangeRelationships()		{
		global $LANG;
		$content = '';

			// Get the primary partner from the hidden field and save it as class var
		if (t3lib_div::_GP('hdn_primary_partner'))		{
			$po = t3lib_div::makeInstance('tx_partner_main');
			$po->getPartner(t3lib_div::_GP('hdn_primary_partner'));
			$this->primaryPartner = $po->data;
		}

			// Get the already selected default values from the BE-user session
		$this->defaults = $GLOBALS['BE_USER']->getModuleData('tx_partner_tools_massrelationships', 'ses');

			// Get the currently selected secondary partners
		if (is_array(t3lib_div::_GP('chk_sec_partner')))		{
			$this->secPartners = t3lib_div::_GP('chk_sec_partner');
		}

			// Decide what to do...
		$cmd = t3lib_div::_GP('cmd');
		if (is_array($cmd))		{
			switch (key($cmd)) {

				case 'search_primary_partner':

						// Search partner
					$totalNo = 0;
					$searchStrings['label'] = t3lib_div::_GP('sel_primary_partner');
					$query = t3lib_div::makeInstance('tx_partner_query');
					$totalNo = $query->getPartnerBySearchStrings($searchStrings, $this->pObj->id, true);

					switch ($totalNo)		{
						case 0: // No partners found

								// Display messsage and empty search form
							$content.= sprintf($LANG->getLL('tx_partner.modfunc.tools.massrelationships.no_partner_found'), t3lib_div::_GP('sel_primary_partner'));
							$content.= $GLOBALS['TBE_TEMPLATE']->spacer(3);
							$content.= $this->printInputField('sel_primary_partner', '', 20);

								// Search Button
							$content.= $this->printButton('cmd[search_primary_partner]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.search'));
						break;

						case 1:	// Exactly one partner found

								// Save the partner
							$partner = reset($query->query);
							$this->primaryPartner = $partner->data;

								// Print the selected primary partner
							$content.= $this->printPrimaryPartner();

								// Print the change form
							$content.= $this->printChangeForm();
						break;


						default:	// More than one partner found

								// Display messsage and selection list
							$content.= $LANG->getLL('tx_partner.modfunc.tools.massrelationships.more_than_one_partner_found_please_select').'<br />';
							$content.= $this->printSelectionList($query->query, 'radio', 'rdo_primary_partner');

								// Button
							$content.= $this->printButton('cmd[select_primary_partner]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.select'));
						break;
					}

				break; // search_primary_partner

				case 'select_primary_partner';

						// Save the primary partner
					$po = t3lib_div::makeInstance('tx_partner_main');
					$po->getPartner(t3lib_div::_GP('rdo_primary_partner'));
					$this->primaryPartner = $po->data;

						// Print the selected partner
					$content.= $this->printPrimaryPartner();

						// Print the change form
					$content.= $this->printChangeForm();

				break; //select_primary_partner

				case 'change_relationships';

						// Get the GP-vars of the current fields and the saved values (for comparison)
					$existingRel = t3lib_div::_GP('sel_existing_rel');
					$hdnExistingRel = t3lib_div::_GP('hdn_sel_existing_rel');

					if (is_array($existingRel) and is_array($this->primaryPartner))		{

						foreach ($existingRel as $k=>$v)		{
							$changeArray = '';

								// Check if the field was actually changed
							if ($hdnExistingRel[$k]['status'] != $v['status']) $changeArray['status'] = $v['status'];
							if ($hdnExistingRel[$k]['established_date'] != $v['established_date']) $changeArray['established_date'] = $v['established_date'];
							if ($hdnExistingRel[$k]['lapsed_date'] != $v['lapsed_date']) $changeArray['lapsed_date'] = $v['lapsed_date'];
							if ($hdnExistingRel[$k]['lapsed_reason'] != $v['lapsed_reason']) $changeArray['lapsed_reason'] = $v['lapsed_reason'];

								// If at least one field has changed, update the relationship
							if (is_array($changeArray))		{
								$relObj = t3lib_div::makeInstance('tx_partner_relationship');
								$relObj->getRelationship($k);
								$relObj->updateRelationship($changeArray);
							}
						}
					}

						// Print the selected primary partner
					$content.= $this->printPrimaryPartner();

						// Print the change form
					$content.= $this->printChangeForm();

				break; //change_relationships

				case 'delete_relationships':

					$selExistingRel = t3lib_div::_GP('sel_existing_rel');
					if (is_array($selExistingRel))		{
						foreach ($selExistingRel as $k=>$v)		{

								// Delete all checked relationships
							if ($v['checked'])		{
								$relObj = t3lib_div::makeInstance('tx_partner_relationship');
								$relObj->getRelationship($k);
								$relObj->deleteRelationship();
							}
						}
					}

						// Print the selected primary partner
					$content.= $this->printPrimaryPartner();

						// Print the change form
					$content.= $this->printChangeForm();

				break; //delete_relationships

				case 'set_defaults';
				case 'force_defaults';

						// Save the default values in the class vars
					if (is_array(t3lib_div::_GP('sel_defaults')))		{
						foreach (t3lib_div::_GP('sel_defaults') as $k=>$v)		{
							if (isset($v)) $this->defaults[$k] = $v;
						}
					}

						// Set the force defaults flag
					if (key($cmd) == 'force_defaults') $this->forceDefaults = true;

						// Print the selected primary partner
					$content.= $this->printPrimaryPartner();

						// Print the change form
					$content.= $this->printChangeForm();

				break; //set_defaults, force_defaults

				case 'search_sec_partner':

						// Print the selected primary partner
					$content.= $this->printPrimaryPartner();

						// Print the change form
					$content.= $this->printChangeForm();

				break; // search_sec_partner

				case 'save':
					if (is_array($this->secPartners))		{
						foreach ($this->secPartners as $k=>$v)		{

								// Prepare all checked records for the insert
							if ($v['checked'])		{
								$insertArray = array(
									'pid' => $this->pObj->id,
									'type' => $this->defaults['type'],
									'uid_primary' => $this->primaryPartner['uid'],
									'uid_secondary' => $v['uid_secondary'],
									'status' => $v['status'],
									'established_date' => $v['established_date'],
									'lapsed_date' => $v['lapsed_date'],
									'lapsed_reason' => $v['lapsed_reason'],
								);

									// Insert the record
								$relObj = t3lib_div::makeInstance('tx_partner_relationship');
								$newUid = $relObj->insertRelationship($insertArray);

									// If the insert was successful, unset it from the selection list
								if ($newUid)		{
									unset ($this->secPartners[$k]);
								}
							}
						}
					}

						// Print the selected primary partner
					$content.= $this->printPrimaryPartner();

						// Print the change form
					$content.= $this->printChangeForm();

				break; // save
				default:
					$content.= $this->printEmptyForm();
				break; // default
			}
		} else {
				// No button pushed yet: Display empty search form.
			$content.= $this->printEmptyForm();
		}

			// Save primary partner as hidden field
		if (is_array($this->primaryPartner))		{
			$content.= '<input type="hidden" name="hdn_primary_partner" value="'.$this->primaryPartner['uid'].'" />';
		}
			// Save defaults in BE-user session
		if (is_array($this->defaults))		{
			$GLOBALS['BE_USER']->pushModuleData('tx_partner_tools_massrelationships', $this->defaults);
		}


		return $content;
	}

	/**
	 * Creates HTML for the an empty search form.
	 *
	 * @return	string		HTML for output in the backend
	 */
	function printEmptyForm()		{
		global $LANG;
		$out = '';

		$out.= $this->pObj->doc->section($LANG->getLL('tx_partner.modfunc.tools.massrelationships.select_primary_partner'), '', 1, 1);
		$out.= $LANG->getLL('tx_partner.modfunc.tools.enter_search_strings');
		$out.= $GLOBALS['TBE_TEMPLATE']->spacer(3);
		$out.= $this->printInputField('sel_primary_partner', '', 20);
		$out.= $GLOBALS['TBE_TEMPLATE']->spacer(3);
		$out.= $this->printButton('cmd[search_primary_partner]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.search'));

		return $out;
	}


	/**
	 * Creates HTML for the main form where all changes can be made. The selected primary partner must already be available in $this->primaryPartner.
	 *
	 * @return	string		HTML for output in the backend
	 */
	function printChangeForm()		{
		global $LANG;
		$out = '';

		if (is_array($this->primaryPartner))		{

			$this->setDefaults();

				// Check if there are allowed relationship types for the primary partner and allowed status records for relationships
			if (is_array($this->allowedRelationshipTypes) and is_array($this->allowedStatus))		{

					// Build dynamic tab menu
				$menuParts[] = array(
					'label' => $LANG->getLL('tx_partner.modfunc.tools.massrelationships.existing_relationships'),
					'description' => $LANG->getLL('tx_partner.modfunc.tools.massrelationships.existing_relationships.description'),
					'content' => $GLOBALS['TBE_TEMPLATE']->spacer(10).$this->printExistingRelationships(),
				);
				$menuParts[] = array(
					'label' => $LANG->getLL('tx_partner.modfunc.tools.massrelationships.new_relationships'),
					'description' => $LANG->getLL('tx_partner.modfunc.tools.massrelationships.new_relationships.description'),
					'content' => $GLOBALS['TBE_TEMPLATE']->spacer(10).$this->printNewRelationships(),
				);
				$menuParts[] = array(
					'label' => $LANG->getLL('tx_partner.modfunc.tools.massrelationships.default_values'),
					'description' => $LANG->getLL('tx_partner.modfunc.tools.massrelationships.default_values.description'),
					'content' => $GLOBALS['TBE_TEMPLATE']->spacer(10).$this->printDefaults(),
				);

					// Get dynamic tab menu
				$out.= $this->pObj->doc->getDynTabMenu($menuParts, 'main');

			} else {
				$out.= $GLOBALS['TBE_TEMPLATE']->spacer(3);

					// No relationship types allowed:
				if (!is_array($this->allowedRelationshipTypes))		{
					$out.= $LANG->getLL('tx_partner.modfunc.tools.massrelationships.no_allowed_rel_types_found');
					$out.= t3lib_BEfunc::cshItem('_MOD_partner', 'no_allowed_rel_types_found', $GLOBALS['BACK_PATH']);
					$out.= $GLOBALS['TBE_TEMPLATE']->spacer(1);
				}

					// No status for relationships
				if (!is_array($this->allowedStatus))		{
					$out.= $LANG->getLL('tx_partner.modfunc.tools.massrelationships.no_rel_status_found');
					$out.= t3lib_BEfunc::cshItem('_MOD_partner', 'no_rel_status_found', $GLOBALS['BACK_PATH']);
					$out.= $GLOBALS['TBE_TEMPLATE']->spacer(1);
				}

			}
		}

		return $out;
	}


	/**
	 * Sets all default values.
	 *
	 * @return	void
	 */
	function setDefaults()		{

			// Get allowed relationship types and allowed status
		if (isset($this->pObj->id) && isset($this->primaryPartner['type']))		{
			$this->allowedRelationshipTypes = tx_partner_div::getAllowedRelationshipTypes($this->pObj->id, $this->primaryPartner['type'], 0);
		}
		if (isset($this->pObj->id))		{
			$this->allowedStatus = tx_partner_div::getAllowedStatus($this->pObj->id, 'tx_partner_relationships');
		}

			// Set standard default values if no other values were selected yet
		if (!isset($this->defaults['type']) and is_array($this->allowedRelationshipTypes))		{
			$relType = reset($this->allowedRelationshipTypes);
			$this->defaults['type'] = $relType['uid'];
		}
		if (!isset($this->defaults['status']) and is_array($this->allowedStatus))		{
			$status = reset($this->allowedStatus);
			$this->defaults['status'] = $status['uid'];
		}
		if (!isset($this->defaults['established_date'])) $this->defaults['established_date'] = mktime(0,0,0,date('m'),date('d'),date('Y'));;
		if (!isset($this->defaults['no_of_search_fields'])) $this->defaults['no_of_search_fields'] = 5;
	}




	/**
	 * Creates HTML to output the existing relationships for the current primary partner.
	 *
	 * @return	string		HTML for output in the backend
	 */
	function printExistingRelationships()		{
		global $LANG;
		$out = '';

			// Get the existing relationships
		$po = t3lib_div::makeInstance('tx_partner_main');
		$po->getPartner($this->primaryPartner['uid']);
		$po->getRelationships();

			// Existing relationships found
		if (is_array($po->relationshipsAsPrimary))		{

				// List
			$deleteTitle = $LANG->getLL('tx_partner.modfunc.tools.massrelationships.delete_relationship');
			$deleteIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/garbage.gif', 'width="11" height="12"').' title="'.$deleteTitle.'" border="0" alt="" onClick="toggle(\'sel_existing_rel\', \'checked\')"/>';
			foreach ($po->relationshipsAsPrimary as $k=>$v)		{
				$relArray[$k] = $v->data;
			}
			$out.= $this->printRelationshipList($relArray, 'sel_existing_rel', '', $deleteIcon);

				// Add the buttons
			$out.= $GLOBALS['TBE_TEMPLATE']->spacer(10);
			$out.= $this->printButton('cmd[change_relationships]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.change'));
			$out.= '&nbsp;'.$this->printButton('cmd[delete_relationships]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.delete'));


		} else {

				// No existing relationships found
			$out.= $LANG->getLL('tx_partner.modfunc.tools.massrelationships.no_existing_relationships_found');
		}

			// Log
		if (is_array($this->log['existing_rel']))		{
			$out.= $GLOBALS['TBE_TEMPLATE']->spacer(20);
			$out.= tx_partner_div::getMessageOutput($this->log['existing_rel']);
		}

		return $out;
	}


	/**
	 * Creates HTML to output the fields to enter default values for new relationship records.
	 *
	 * @return	string		HTML for output in the backend
	 */
	function printDefaults()		{
		global $LANG;
		$out = '';

			// Build selection list for the allowed relationship types
		if (is_array($this->allowedRelationshipTypes))		{
			foreach ($this->allowedRelationshipTypes as $uid => $theRelType)		{
				$selected = '';
				if ($uid == $this->defaults['type']) $selected = ' selected="selected"';
				$types.= '<option value="'.$uid.'"'.$selected.'>'.$theRelType['primary_title'].'</option>'."\n";
			}
		}
		$types = '<select name="sel_defaults[type]" size="1">'.$types.'</select>';

			// Build selection list for the status
		if (is_array($this->allowedStatus))		{
			foreach ($this->allowedStatus as $uid => $theStatus)		{
				$selected = '';
				if ($uid == $this->defaults['status']) $selected = ' selected="selected"';
				$status.= '<option value="'.$uid.'"'.$selected.'>'.$theStatus['st_descr'].'</option>'."\n";
			}
		}
		$status = '<select name="sel_defaults[status]" size="1">'.$status.'</select>';

			// Build selection list for the number of search fields
		$i = 0;
		while ($i < 10) {
			$i++;
			$selected = '';
			if ($this->defaults['no_of_search_fields'] == $i) $selected = ' selected="selected"';
			$noOfSearchFields.= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		$noOfSearchFields = '<select name="sel_defaults[no_of_search_fields]" id="sel_defaults[no_of_search_fields]">'.$noOfSearchFields.'</select>';

			// Rows
		$rows.= '
			<tr>
				<td>'.$LANG->getLL('tx_partner_relationships.type').'</td>
				<td>'.$types.'</td>
			</tr>
			<tr>
				<td>'.$LANG->getLL('tx_partner_relationships.status').'</td>
				<td>'.$status.'</td>
			</tr>
			<tr>
				<td>'.$LANG->getLL('tx_partner_relationships.established_date').'</td>
				<td>'.$this->printDateInputField('sel_defaults[established_date]', $this->defaults['established_date'], 8).'</td>
			</tr>
			<tr>
				<td>'.$LANG->getLL('tx_partner_relationships.lapsed_date').'</td>
				<td>'.$this->printDateInputField('sel_defaults[lapsed_date]', $this->defaults['lapsed_date'], 8).'</td>
			</tr>
			<tr>
				<td>'.$LANG->getLL('tx_partner_relationships.lapsed_reason').'</td>
				<td>'.$this->printInputField('sel_defaults[lapsed_reason]', $this->defaults['lapsed_reason'], 15).'</td>
			</tr>
			<tr>
				<td>'.$LANG->getLL('tx_partner.modfunc.tools.massrelationships.no_of_search_fields').'</td>
				<td>'.$noOfSearchFields.'</td>
			</tr>
		';

			// Add table tags
		$out.= '<table width="100%"  border="0" cellspacing="0" cellpadding="0">'.$rows.'</table>';

			// Add the command-buttons
		$out.= $GLOBALS['TBE_TEMPLATE']->spacer(10);
		$out.= $this->printButton('cmd[set_defaults]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.set_defaults'));
		$out.= '&nbsp;'.$this->printButton('cmd[force_defaults]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.force_defaults'));

			// Log
		if (is_array($this->log['defaults']))		{
			$out.= $GLOBALS['TBE_TEMPLATE']->spacer(20);
			$out.= tx_partner_div::getMessageOutput($this->log['defaults']);
		}

		return $out;
	}



	/**
	 * Creates HTML to output the selected new relationships.
	 *
	 * @return	string		HTML for output in the backend
	 */
	function printNewRelationships()		{
		global $LANG;
		$out = '';
		$saveButton = false;

			// Search all entered partners
		if (is_array(t3lib_div::_GP('sel_sec_partner')))		{
			foreach (t3lib_div::_GP('sel_sec_partner') as $theSecPartner)		{
				if ($theSecPartner['label'])		{

						// Get the partners for each allowed partner type
					$allowedPartnerTypes = tx_partner_div::getAllowedPartnerTypes($this->defaults['type'], 0);
					if (is_array($allowedPartnerTypes))		{
						$totalNo = 0;
						foreach ($allowedPartnerTypes as $thePartnerType)		{

								// Get the partner(s)
							$query = t3lib_div::makeInstance('tx_partner_query');
							$searchStrings['type'] = $thePartnerType;
							$searchStrings['label'] = $theSecPartner['label'];
							$totalNo = $query->getPartnerBySearchStrings($searchStrings, $this->pObj->id, true);

								// Build result array, exclude the current primary partner from the result (cannot create relationship with itself)
							if (is_array($query->query))		{
								foreach ($query->query as $k => $v)		{
									if ($this->primaryPartner['uid'] != $v->data['uid'])		{
										$relArray[$v->data['uid']] = array		(
											'checked' => ($totalNo == 1) ? 1 : 0,
											'type' => $this->defaults['type'],
											'status' => !empty($theSecPartner['status']) ? $theSecPartner['status'] : $this->defaults['status'],
											'uid_secondary' => $v->data['uid'],
											'established_date' => !empty($theSecPartner['established_date']) ? $theSecPartner['established_date'] : $this->defaults['established_date'],
											'lapsed_date' => !empty($theSecPartner['lapsed_date']) ? $theSecPartner['lapsed_date'] : $this->defaults['lapsed_date'],
											'lapsed_reason' => !empty($theSecPartner['lapsed_reason']) ? $theSecPartner['lapsed_reason'] : $this->defaults['lapsed_reason'],
										);
										$saveButton = true;
									} else {

											// Partner excluded, because it is the same as the currently selected primary partner
											$this->log['new_rel'][]['info'] = sprintf($LANG->getLL('tx_partner.modfunc.tools.massrelationships.sec_partner_excluded'), $v->data['label']).t3lib_BEfunc::cshItem('_MOD_partner', 'sec_partner_excluded_because_same_as_primary', $GLOBALS['BACK_PATH']);
									}
								}
							} else {

									// No partner found for the enterd search string
								$this->log['new_rel'][]['info'] = sprintf($LANG->getLL('tx_partner.modfunc.tools.massrelationships.no_partner_found'), $theSecPartner['label']).t3lib_BEfunc::cshItem('_MOD_partner', 'no_partner_found', $GLOBALS['BACK_PATH']);
							}
						}
					} else {

							// No partner types allowed for the current relationship type
						$valRelType = tx_partner_div::getValRecord('tx_partner_val_rel_types', $this->defaults['type']);
						$this->log['new_rel'][]['info'] = sprintf($LANG->getLL('tx_partner.modfunc.tools.massrelationships.no_partner_types_allowed_for_rel_type'), $valRelType['rt_descr_short']);
					}
				}
			}
		}

			// Add already selected partners
		if (is_array($this->secPartners))		{
			foreach ($this->secPartners as $v)		{
				if ($v['checked'])		{

						// Check if the relationship is still allowed (relationship type might have changed)
					if (tx_partner_div::checkRelationship($this->primaryPartner['uid'], $v['uid_secondary'], $this->defaults['type']) == true)		{
						$relArray[$v['uid_secondary']] = array		(
							'checked' => 1,
							'type' => $this->defaults['type'],
							'status' => $this->forceDefaults ? $this->defaults['status'] : $v['status'],
							'uid_secondary' => $v['uid_secondary'],
							'established_date' => $this->forceDefaults ? $this->defaults['established_date'] : $v['established_date'],
							'lapsed_date' => $this->forceDefaults ? $this->defaults['lapsed_date'] : $v['lapsed_date'],
							'lapsed_reason' => $this->forceDefaults ? $this->defaults['lapsed_reason'] : $v['lapsed_reason'],
						);
						$saveButton = true;
					} else {

							// Log: Partner was removed, because it was not allowed for the current relationship type
						$po = t3lib_div::makeInstance('tx_partner_main');
						$po->getPartner($v['uid_secondary']);
						$this->log['new_rel'][]['info'] = sprintf($LANG->getLL('tx_partner.modfunc.tools.massrelationships.sec_partner_removed'), $po->data['label']).t3lib_BEfunc::cshItem('_MOD_partner', 'sec_partner_removed_because_not_allowed', $GLOBALS['BACK_PATH']);
					}
				}
			}
		}

			// Add empty search fields
		while ($cnt++ < $this->defaults['no_of_search_fields']) {
			$relArray['NEW'.$cnt] = array		(
				'checked' => 0,
				'type' => $this->defaults['type'],
				'status' => $this->defaults['status'],
				'uid_secondary' => 'NEW',
				'established_date' => $this->defaults['established_date'],
				'lapsed_date' => $this->defaults['lapsed_date'],
				'lapsed_reason' => $this->defaults['lapsed_reason'],
			);
		}

		if (is_array($relArray))		{

				// Select Icon
			$selectTitle = $LANG->getLL('tx_partner.modfunc.tools.massrelationships.select_relationship');
			$selectIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/clip_select.gif', 'width="12" height="12"').' title="'.$selectTitle.'" border="0" alt="" onClick="toggle(\'sel_sec_partner\', \'checked\')" />';

				// Print the list
			$out.= $this->printRelationshipList($relArray, 'chk_sec_partner', 'sel_sec_partner', $selectIcon);
		}

			// Add buttons (save button only if secondary partners are ready to be saved)
		$out.= $GLOBALS['TBE_TEMPLATE']->spacer(10);
		$out.= $this->printButton('cmd[search_sec_partner]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.search'));
		if ($saveButton)		{
			$out.= '&nbsp;'.$this->printButton('cmd[save]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.save'));
		}

			// Log
		if (is_array($this->log['new_rel']))		{
			$out.= $GLOBALS['TBE_TEMPLATE']->spacer(20);
			$out.= tx_partner_div::getMessageOutput($this->log['new_rel']);
		}

		return $out;
	}


	/**
	 * Creates HTML for a list of relationships.
	 *
	 * @param	array		$relationships: Array with relationship objects to list
	 * @param	string		$selectedFieldName: Name of of the fields in the list (will be appended by the uid of the relationship)
	 * @param	string		$searchFieldName: Name of the search fields in the list (will be appended by the 'fake' uid of the relationship)
	 * @param	string		$selectIcon: Optional. You can provide an icon for the select-column in the title row.
	 * @return	string		HTML for output in the backend
	 */
	function printRelationshipList($relationships, $selectedFieldName, $searchFieldName, $selectIcon='')		{
		global $LANG;
		$out = '';

		if (is_array($relationships))		{

				// Title rows
			$rows.= '
				<tr>
				    <td '.$this->defaultHeader1Style.' width="20" align="center">'.$selectIcon.'</td>
				    <td '.$this->defaultHeader1Style.'>'.$LANG->getLL('tx_partner_relationships.type').'</td>
				    <td '.$this->defaultHeader1Style.' colspan="3">'.$LANG->getLL('tx_partner_relationships.uid_secondary').'</td>
				</tr>
				<tr>
					<td '.$this->defaultHeader1Style.'>&nbsp;</td>
				    <td '.$this->defaultHeader1Style.'>'.$LANG->getLL('tx_partner_relationships.status').'</td>
				    <td '.$this->defaultHeader1Style.'>'.$LANG->getLL('tx_partner_relationships.established_date').'</td>
				    <td '.$this->defaultHeader1Style.'>'.$LANG->getLL('tx_partner_relationships.lapsed_date').'</td>
				    <td '.$this->defaultHeader1Style.'>'.$LANG->getLL('tx_partner_relationships.lapsed_reason').'</td>
				</tr>
			';

				// Build rows
			foreach ($relationships as $k=>$v)		{

					// If the secondary partner is provided, get the partner object, otherwise display an empty search field
				if ($v['uid_secondary'] == 'NEW')		{
					$theFieldName = $searchFieldName;
					$secPartner = $this->printInputField($theFieldName.'['.$k.'][label]', '', 20);
				} else {
					$theFieldName = $selectedFieldName;
					$spo = t3lib_div::makeInstance('tx_partner_main');
					$spo->getPartner($v['uid_secondary']);
					$secPartner = $spo->data['label'];
				}

					// Get the relationship type
				$type = tx_partner_div::getValRecord('tx_partner_val_rel_types', $v['type']);

					// Build selection list for the status
				//$allowedStatus = tx_partner_div::getAllowedStatus('tx_partner_relationships');
				$status = '';
				if (is_array($this->allowedStatus))		{
					foreach ($this->allowedStatus as $uid => $theStatus)		{
						$selected = '';
						if ($uid == $v['status']) $selected = ' selected="selected"';
						$status.= '<option value="'.$uid.'"'.$selected.'>'.$theStatus['st_descr'].'</option>'."\n";
					}
				}
				$status = '<select name="'.$theFieldName.'['.$k.'][status]" size="1">'.$status.'</select>';

				$rows.= '
					<tr>
					    <td width="20">'.$this->printCheckboxField($theFieldName.'['.$k.'][checked]', $v['checked'], $k).'</td>
					    <td>'.$type['primary_title'].'</td>
					    <td colspan="3">'.$secPartner.'</td>
					</tr>
					<tr>
						<td '.$this->defaultListStyle.'>&nbsp;</td>
					    <td '.$this->defaultListStyle.'>'.$status.'</td>
					    <td '.$this->defaultListStyle.'>'.$this->printDateInputField($theFieldName.'['.$k.'][established_date]', $v['established_date'], 8).'</td>
					    <td '.$this->defaultListStyle.'>'.$this->printDateInputField($theFieldName.'['.$k.'][lapsed_date]', $v['lapsed_date'], 8).'</td>
					    <td '.$this->defaultListStyle.'>'.$this->printInputField($theFieldName.'['.$k.'][lapsed_reason]', $v['lapsed_reason'], 15).'</td>
					</tr>
				';

					// Add additional hidden fields
				if ($v['uid_secondary'] != 'NEW')		{
					$out.= '<input type="hidden" name="'.$theFieldName.'['.$k.'][uid_secondary]" value="'.$v['uid_secondary'].'" />';
					$out.= '<input type="hidden" name="'.$theFieldName.'['.$k.'][status]" value="'.$v['status'].'" />';
				}
			}

				// Add table tags
			$out.= '<table width="100%" border="0" cellspacing="0" cellpadding="0">'.$rows.'</table>';
		}

		return $out;
	}


	/**
	 * Creates HTML for a submit button.
	 *
	 * @param	string		$name: Name of the button
	 * @param	string		$label: Label of the button
	 * @return	string		HTML for output in the backend
	 */
	function printButton($name, $label)		{
		return '<input type="submit" name="'.$name.'" value="'.$label.'" />';
	}


	/**
	 * Creates HTML for a checkbox field
	 *
	 * @param	string		$name: Name of the field
	 * @param	string		$checked: If set to true, the checkbox will be marked as checked.
	 * @param	[type]		$value: ...
	 * @return	string		HTML for output in the backend
	 */
	function printCheckboxField($name, $checked, $value)		{
		$chk = $checked ? ' checked="checked"' : '';
		return '<input type="checkbox" value="'.$value.'" name="'.$name.'"'.$chk.' />';
	}


	/**
	 * Creates HTML for a regular input field
	 *
	 * @param	string		$name: Name of the field
	 * @param	string		$value: Value of the field (optional)
	 * @param	string		$length: Length of the field (optional, default = 10)
	 * @return	string		HTML for output in the backend
	 */
	function printInputField($name, $value='', $length=10)		{
		$out = '';

			// Input field
		$out.= '<input type="text" name="'.$name.'" value="'.$value.'" '.$this->pObj->doc->formWidth($length).' />';

			// Hidden field for value comparison
		$out.= '<input type="hidden" value="'.$value.'" name="hdn_'.$name.'" />';

		return $out;
	}


	/**
	 * Creates HTML for an input field for date values
	 *
	 * @param	string		$name: Name of the field
	 * @param	string		$value: Value of the field (optional)
	 * @param	string		$length: Length of the field (optional, default = 10)
	 * @return	string		HTML for output in the backend
	 */
	function printDateInputField($name, $value='', $length=10)		{
		$out = '';

			// Input field with onChange event for the date evaluation
		$out.= '<input type="text" name="'.$name.'_hr" value="'.$value.'" onChange="typo3FormFieldGet(\''.$name.'\', \'date\', \'\', 0,0);"'.$this->pObj->doc->formWidth($length).' />';

			// Hidden field for the date evaluation by JavaScript
		$out.= '<input type="hidden" value="'.$value.'" name="'.$name.'" />';

			// Hidden field for value comparison
		$out.= '<input type="hidden" value="'.$value.'" name="hdn_'.$name.'" />';

			// Add the field to extended Java-Script of the TCE-forms instance
		$this->t3lib_TCEforms->extJSCODE.= 'typo3FormFieldSet("'.$name.'", \'date\', "", 0,0);';

		return $out;
	}



	/**
	 * Creates HTML for printing the selected primary partner. The primary partner must already be available in $this->primaryPartner.
	 *
	 * @return	string		HTML for output in the backend
	 */
	function printPrimaryPartner()		{
		global $LANG;
		$out = '';

		if (is_array($this->primaryPartner))		{

				// Title
			$out.= $this->pObj->doc->section($LANG->getLL('tx_partner.modfunc.tools.massrelationships.selected_primary_partner'), '', 1, 1);

				// Restart button
			$restartButton = $this->printButton('cmd[restart]', $LANG->getLL('tx_partner.modfunc.tools.massrelationships.restart'));

				// Primary partner with type-icon
			$icon = t3lib_iconworks::getIconImage('tx_partner_main',array('type'=>$this->primaryPartner['type']),$GLOBALS['BACK_PATH'],'title="'.$LANG->getLL('tx_partner_main.type.I.'.$this->primaryPartner['type']).'"');

				// Assemble table
			$rows = '<tr valign="middle"><td width="20">'.$icon.'</td><td><strong>'.$this->primaryPartner['label'].'</strong></td><td align="right">'.$restartButton.'</td></tr>';
			$out.= '<table width="100%" border="0" cellspacing="0" cellpadding="0">'.$rows.'</table>';
		}

		return $out;
	}

	/**
	 * Creates HTML for a selection list
	 *
	 * @param	array		$partnerObjects: Array with partner objects to select from
	 * @param	string		$type: Type of selection fields. Can be either 'radio' or 'checkbox'.
	 * @param	string		$name: Name of the input fields
	 * @param	boolean		$checkAll: If set to true, all items will be marked as checked.
	 * @return	string		HTML for output in the backend
	 */
	function printSelectionList($partnerObjects, $type='radio', $name, $checkAll=false)		{
		global $LANG;
		$out = '';

		if (is_array($partnerObjects))		{

				// If there is only one partner in the array, directly mark it as checked
			if (count($partnerObjects) == 1 or $checkAll) $checked = 'checked="checked" ';
			foreach ($partnerObjects as $v)		{
				$out.= '
					<tr>
						<td width="20"><input type="'.$type.'" name="'.$name.'" value="'.$v->data['uid'].'" '.$checked.'/></td>
						<td>'.$v->data['label'].'</td>
					</tr>
				';
			}

				// Add table tags
			$out = '<table width="100%" border="0" cellspacing="0" cellpadding="0">'.$out.'</table>';
		}

		return $out;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_tools_massrelationships.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_tools_massrelationships.php']);
}
?>