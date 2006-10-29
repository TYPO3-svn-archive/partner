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
* 'Assign FE Users' Tool as a sub-submodule of
* Web>Partner>Tools
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');





/**
 * Class for the 'Assign FE Users' Tool as a sub-submodule of
 * Web>Partner>Tools
 *
 * @author	David Bruehlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_partner
 */
class tx_partner_tools_assignfeusers extends t3lib_extobjbase {


	/**
	 * Modifies parent objects internal MOD_MENU array, adding items this module needs.
	 *
	 * @return	array		Items merged with the parent objects.
	 * @see t3lib_extobjbase::init()
	 */
	function modMenu()	{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Add the method to use
		$modMenuAdd = array(
			'method' => Array (
				'0' => $LANG->getLL('tx_partner.modfunc.tools.assignfeusers.email'),
				'1' => $LANG->getLL('tx_partner.modfunc.tools.assignfeusers.last_name'),
				'2' => $LANG->getLL('tx_partner.modfunc.tools.assignfeusers.check'),
				'3' => $LANG->getLL('tx_partner.modfunc.tools.assignfeusers.email_list'),
			),
		);

		return $modMenuAdd;
	}



	/**
	 * Creation of the report.
	 *
	 * @return	string		The content
	 */
	function main()	{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Check if updates need to be made (button pushed)
		if (t3lib_div::_GP('make_updates'))	{
				// Make the updates
			$updateOut = $this->updatePartnersWithFeUser(t3lib_div::_GP('fe_user'));

				// Print the protocol
			$content.= $this->pObj->doc->section($LANG->getLL('tx_partner.label.protocol'), $updateOut, 1, 1);
		}

		switch((string)$this->pObj->MOD_SETTINGS['method']) {
			case 0:
			case 1:
					// Get the mapping list
				$list = $this->getFeUserToPartnerMappingList($this->pObj->id, (string)$this->pObj->MOD_SETTINGS['method']);
				break;
			case 2:
					// Get the check report
				$list = $this->getFeUserCheckReport($this->pObj->id);
				break;
			case 3:
				$list = $this->getFeUserEmailList($this->pObj->id);
				break;
		}

			// Prepare the output
		$content.= $this->pObj->doc->section($LANG->getLL('tx_partner.modfunc.tools.assignfeusers'), '', 0, 1);
		$content.= $this->pObj->doc->section('', $this->pObj->doc->funcMenu($LANG->getLL('tx_partner.label.method').':', t3lib_BEfunc::getFuncMenu($this->pObj->id, 'SET[method]', $this->pObj->MOD_SETTINGS['method'], $this->pObj->MOD_MENU['method'])));
		$content.= $this->pObj->doc->section($this->pObj->MOD_MENU['method'][$this->pObj->MOD_SETTINGS['method']], $list, 1, 1);

		return $content;

	}

	/**
	 * Internal function to update partners with the mapped FE-users.
	 * - If the partner doesn't have a standard e-mail address, but there is an e-mail address in the mapped FE-user,
	 *   the e-mail address from the FE-user will be inserted as the new standard e-mail address of the partner
	 * - The mapped FE-user UID will be inserted into the partner-record
	 * - The result of the updates is formatted as HTML
	 *
	 * @param	array		$mapArray: Array with partner-UID's as keys and mapped FE-user UID as value
	 * @return	string		Protocol of the update, formatted as HTML
	 */
	function updatePartnersWithFeUser($mapArray)		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

		if (is_array($mapArray))		{
			foreach ($mapArray as $partnerUid=> $feUserUid)		{

					// Get the partner record
				$partner = t3lib_BEfunc::getRecord('tx_partner_main',$partnerUid);

					// Get the standard e-mail address
				$stdEmail = tx_partner_div::getContactInfo($partnerUid, 1, 3);

					// Get the fe_user record
				$feUser = t3lib_BEfunc::getRecord('fe_users', $feUserUid);

					// If the partner doesn't have a standard e-mail address, but there is an e-mail address in the fe_user,
					// take it from there and INSERT it as a new standard e-mail address
				if (!is_array($stdEmail) and $feUser['email'])		{
					$where = 'uid='.$partnerUid;
					$fields_values = array(
						'pid' => $this->pObj->id,
						'tstamp' => time(),
						'crdate' => time(),
						'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
						'uid_foreign' => $partnerUid,
						'type' => '3',
						'standard' => '1',
						'email' => $feUser['email'],
					);
					$fields_values['label'] = tx_partner_div::createLabel('tx_partner_contact_info', '', $fields_values);
					$result = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_partner_contact_info',$fields_values);
					$GLOBALS['TYPO3_DB']->debug('updatePartnersWithFeUser');
				}

					// Update the partner record
				$data['tx_partner_main'][$partnerUid]['fe_user'] = $feUserUid;

				$tce = t3lib_div::makeInstance('t3lib_TCEmain');
				$tce->stripslashes_values=0;
				$tce->start($data,Array());
				$tce->process_datamap();

					// Sync the redundant partner data
				tx_partner_div::syncPartnerWithFeUser($partnerUid);

					// Make row in protocol
				$msgArray[]['success'] = $partner['last_name'].', '.$partner['first_name'].' ('.$partner['uid'].') '.$LANG->getLL('tx_partner.label.update_successful');
			}
		}

		if (!is_array($msgArray))		{
				// Make row in protocol
			$msgArray[]['info'] = $LANG->getLL('tx_partner.label.no_updates');
		}

			// Build and return the protocol
		$out = tx_partner_div::getMessageOutput($msgArray);
		return $out;
	}

	/**
	 * Gets a list of all partners for the current PID which are assigned to a FE-user
	 *
	 * @param	integer		$pid: PID for which to get the list
	 * @return	string		List, formatted as HTML
	 */
	function getFeUserEmailList($pid)		{
		$emails = '';

			// Get all partners for the current PID which have a fe_user assigned
		 $partner = t3lib_BEfunc::getRecordsByField('tx_partner_main','pid',intval($pid),' AND fe_user');

		 	// Get the standard e-mail address of each found partner
		 if (is_array($partner))		{
		 	foreach ($partner as $thePartner)		{
		 		$stdEmail = tx_partner_div::getContactInfo($thePartner['uid'], 1, 3);
		 		if (is_array($stdEmail))		{
		 			$stdEmail = reset($stdEmail);
		 			$emails.= $stdEmail->data['email'].';<br />';
		 		}
		 	}
		 }

		 return $emails;
	}

	/**
	 * Checks if all FE-users from a given pid are assigned to a partner and returns the result as an HTML protocol
	 *
	 * @param	integer		$pid: PID for which to check the assignment
	 * @return	string		Protocol, formatted as HTML
	 */
	function getFeUserCheckReport($pid)		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Get all fe_user for the current pid
		$feUser = t3lib_BEfunc::getRecordsByField('fe_users','pid',$pid);

		if (is_array($feUser))		{
			foreach ($feUser as $theUser)		{

					// If the FE-user is not assigned to a partner, write an entry in the message array
				if (!t3lib_BEfunc::getRecordsByField('tx_partner_main','fe_user',$theUser['uid']))		{
					if ($theUser['disable'] == 1)		{
							// FE-user is disabled: Only information
						$msgArray[]['info'] = $LANG->getLL('tx_partner.label.not_assigned_disabled').'<br>'.tx_partner_div::getEditFeUserLink($theUser['uid']).$theUser['name'].': '.$theUser['username'].' ('.$theUser['uid'].')';
					} else {
							// FE-user is active: Warning
						$msgArray[]['warning'] = $LANG->getLL('tx_partner.label.not_assigned_active').'<br>'.tx_partner_div::getEditFeUserLink($theUser['uid']).$theUser['name'].': '.$theUser['username'].' ('.$theUser['uid'].')';
					}
				}
			}
		}

			// If no records were found
		if (!$msgArray)		{
			$msgArray[]['info'] = $LANG->getLL('tx_partner.label.no_records_available');
		}

			// Make output
		$content = tx_partner_div::getMessageOutput($msgArray);
		return $content;
	}


	/**
	 * Gets an HTML page with a mapping proposal Partner -> FE-User for a given PID. The mapping proposal
	 * can be made by matching the e-mail address (method == 1) or matching the last name (method == 2)
	 *
	 * @param	integer		$pid: PID for which to generate the mapping proposal
	 * @param	integer		$method: How to create the proposal (1 = Based on the e-mail address, 2 = Based on the last name)
	 * @return	string		HTML page for output in the Backend
	 */
	function getFeUserToPartnerMappingList($pid, $method)		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Get all fe_user for the current pid
		$feUser = t3lib_BEfunc::getRecordsByField('fe_users','pid',$pid);

		if (is_array($feUser))		{
			foreach ($feUser as $theUser)		{

					// Check if the fe_user is not already assigned to a partner
				if (!t3lib_BEfunc::getRecordsByField('tx_partner_main','fe_user',$theUser['uid']))		{

						// Match by e-mail address
					if ($method == 0)		{

							// Check if the fe_user has an e-mail address (needed for mapping to tx_partner_main)
						if ($theUser['email'])		{
							$contactInfo = t3lib_BEfunc::getRecordsByField('tx_partner_contact_info','email',$theUser['email']);

								// If one or several equal e-mail addresses were found, write the user into the $map array
							if (is_array($contactInfo))		{
								$map[$theUser['uid']]['fe_user'] = $theUser;

									// Get the partner-record for the e-mail address found
									// and check if the partner found does not already have a fe_user assigned
								foreach ($contactInfo as $theContact) {
										// Get the partner record for the contact-UID
									$partner = t3lib_BEfunc::getRecord('tx_partner_main',$theContact['uid_foreign']);

									if (!$partner['fe_user'])		{
										$map[$theUser['uid']]['partner'][] = $partner;
									}
								}
							}
						}
					}

						// Match by last name
					if ($method == 1)		{

							// Explode the name into parts by space. This is ok as a quick solution, as there is no field for last_name in fe_users
						$nameParts = explode(' ', $theUser['name']);

							// If there was a name and it was exploded, write the user into the $map array
						if (is_array($nameParts))		{
							$map[$theUser['uid']]['fe_user'] = $theUser;

								// Get all partners with a name-part in the field last_name
							foreach ($nameParts as $theNamePart)		{
								$partner = t3lib_BEfunc::getRecordsByField('tx_partner_main','last_name',$theNamePart);

									// If one or more partners were found, check if the partner is not already assigned to an fe_user
									// and write the result into the $map array
								if (is_array($partner))		{
									foreach ($partner as $thePartner)		{
										if (!$thePartner['fe_user'])		{
											$map[$theUser['uid']]['partner'][] = $thePartner;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		//debug ($map);

			// Make the rows
		if (is_array($map))		{
			foreach ($map as $theEntry)		{
				$i = 0;
				$checked = '';

				$rowspan = count($theEntry['partner']);
				if ($rowspan == 1) $checked = ' checked="checked"';

				while ($theEntry['partner'][$i]) {
					$rows.= '
						<tr>';
					if ($i == 0)		{
						$rows.= '
							<td '.$this->pObj->defaultListStyle.' rowspan="'.$rowspan.'">'.tx_partner_div::getEditFeUserLink($theEntry['fe_user']['uid']).'</td>
							<td '.$this->pObj->defaultListStyle.' rowspan="'.$rowspan.'">'.$theEntry['fe_user']['name'].'<br><i>'.$theEntry['fe_user']['username'].' ('.$theEntry['fe_user']['uid'].')</i></td>';
					}
					$rows.= '
							<td '.$this->pObj->defaultListStyle.'>'.tx_partner_div::getEditPartnerLink($theEntry['partner'][$i]['uid']).'</td>
							<td '.$this->pObj->defaultListStyle.'>'.$theEntry['partner'][$i]['last_name'].', '.$theEntry['partner'][$i]['first_name'].' ('.$theEntry['partner'][$i]['uid'].')</td>
							<td '.$this->pObj->defaultListStyle.'><input type="checkbox" name="fe_user['.$theEntry['partner'][$i]['uid'].']" value="'.$theEntry['fe_user']['uid'].'"'.$checked.'></td>
						</tr>
					';
					$i++;
				}
			}

				// Add the button
			$button = '<br /><input type="submit" name="make_updates" value="'.$LANG->getLL('tx_partner.label.make_updates').'" />';
		} else {
				// Output no records found...
			$rows = '<tr><td>'.$LANG->getLL('tx_partner.label.no_records_available').'</td></tr>';
		}

			// Make the table and add the button
		$content = '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$rows.'</table>'.$button;

		return $content;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_tools_assignfeusers.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_tools_assignfeusers.php']);
}
?>