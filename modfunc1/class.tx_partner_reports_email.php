<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2008 David Bruehlmeier (typo3@bruehlmeier.com)
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
 * Displays the 'E-Mail' Report as a sub-submodule of
 * Web>Partner>Reports
 *
 * @author David Bruehlmeier <typo3@bruehlmeier.com>
 */

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_query.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');
require_once(PATH_t3lib.'class.t3lib_iconworks.php');




/**
 * Class for displaying the 'E-Mail' Report in Web>Partner>Reports
 *
 * @author	David Bruehlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_partner
 */
class tx_partner_reports_email extends t3lib_extobjbase {

	var $occupations;	// Cached occupations

	/**
	 * Modifies parent objects internal MOD_MENU array, adding items this module needs.
	 *
	 * @return	array		Items merged with the parent objects.
	 * @see t3lib_extobjbase::init()
	 */
	function modMenu()	{
		$modMenuAdd['occupation'] = $this->readOccupations(true);
		return $modMenuAdd;
	}


	/**
	 * Creation of the report.
	 *
	 * @return	string		The content
	 */
	function main()		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');

		// Which occupation? (option)
		$occupation = $this->pObj->MOD_SETTINGS['occupation'];
		$allOccupations = $this->readOccupations();

		// Get the partner list for the selected occupation
		$partnerList = $this->getPartnerList($occupation);

		// Prepare the output
		$allEmailsList = array();
		if (!empty($partnerList)) {
			foreach ($partnerList as $currentOccupationUID=>$currentPartnerList) {
				if ($currentOccupationUID == 'any') continue;
				$currentOcupationLabel = $allOccupations[$currentOccupationUID];
				$allEmailForOccupation = '';
				$currentEmail = '';
				$emailList = array();
				$itemizedEmails = '';
				if (is_array($currentPartnerList)) {
					foreach ($partnerList[$currentOccupationUID] as $currentPartnerUID=>$currentPartner) {
						$currentEmail = $this->getStandardEmailAdress($currentPartnerUID);
						if ($currentEmail) {
							$emailList[] = $currentEmail;
							$allEmailsList[$currentEmail] = $currentEmail;
							$itemizedEmails.= $this->getMailIconLink($currentPartnerUID);
						}
						$itemizedEmails.= $currentPartner->data['label'];
						$itemizedEmails.= tx_partner_div::getEditPartnerLink($currentPartnerUID);
						$itemizedEmails.= '<br/>';
					}

				}
				$allEmailForOccupation = sprintf($LANG->getLL('tx_partner.modfunc.reports.email.send_mail_to_all_members_of_this_occupation'),$currentOcupationLabel).': '.$this->getMailIcon($emailList).'<br/>';
				$list.= $this->pObj->doc->section($currentOcupationLabel, $allEmailForOccupation.$itemizedEmails, 0, 1);
				$list.= $this->pObj->doc->spacer(20);
			}
		}
			
		else {
			$itemizedEmails.= sprintf($LANG->getLL('tx_partner.modfunc.reports.email.no_partner_found_for_this_occupation'),$allOccupations[$occupation]);
			$list.= $this->pObj->doc->section($allOccupations[$occupation], $itemizedEmails, 0, 1);
		}
			

		if (!empty($allEmailsList)) {
			$allEmail = $LANG->getLL('tx_partner.modfunc.reports.email.send_mail_to_all').': '.$this->getMailIcon($allEmailsList).'<br/>';
		}

		// Prepare the output
		$content.= $this->pObj->doc->section('', $this->pObj->doc->funcMenu($LANG->getLL('tx_partner.modfunc.reports.email.occupation').':', t3lib_BEfunc::getFuncMenu($this->pObj->id, 'SET[occupation]', $this->pObj->MOD_SETTINGS['occupation'], $this->pObj->MOD_MENU['occupation'])));
		$content.= $this->pObj->doc->spacer(20);
		$content.= $allEmail;
		$content.= $list;

		// Return the output
		return $content;

	}

	function getMailIcon($emailList) {
		global $LANG;
		$emails = implode(';', $emailList);
		$out = '<a href="mailto:'.$emails.'"><img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/newmail.gif', 'width="18" height="16"').' title="'.$LANG->getLL('tx_partner.label.send_email').'" alt="" /></a>';
		return $out;
	}

	function readOccupations($addAny = false) {
		global $LANG;
		if ($this->occupations) return $this->occupations;

		// Read all occupations
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,oc_descr',
			'tx_partner_val_occupations',
			'tx_partner_val_occupations.pid='.$this->pObj->id.t3lib_BEfunc::deleteClause('tx_partner_val_occupations'),
			'', // GROUP by,
			'oc_descr' // ORDER by,
		);

		if ($addAny) $occupations['any'] = '('.$LANG->getLL('tx_partner.modfunc.reports.email.occupation_any').')';

		if (is_resource($res))		{
			while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))		{
				$occupations[$rec['uid']] = $rec['oc_descr'];
			}
		}

		$this->occupations = $occupations;
		return $occupations;
	}

	function getPartnerList($occupation) {
		$out = array();

		if ($occupation == 'any') {
			$allOccupations = $this->readOccupations();
			foreach ($allOccupations as $k=>$v) {
				$filter = array();
				if ($k != 'any') {
					$filter[] = $k;
					$query = t3lib_div::makeInstance('tx_partner_query');
					$query->getPartnerByOccupation($filter);
					if (is_array($query->query)) {
						foreach ($query->query as $partner) {
							$out[$k][$partner->data['uid']] = $partner;
						}
					}
				}
			}
		} else {
			$filter[] = $occupation;
			$query = t3lib_div::makeInstance('tx_partner_query');
			$query->getPartnerByOccupation($filter);
			$query->getContactInfo(1);
			if (is_array($query->query)) {
				foreach ($query->query as $partner) {
					$out[$occupation][$partner->data['uid']] = $partner;
				}
			}
		}

		return $out;
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
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');
		$stdEmailAdress = $this->getStandardEmailAdress($partnerUID);
		if (!$stdEmailAdress) return;

		$mailIconLink = '<a href="mailto:'.$stdEmailAdress.'">
				<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/newmail.gif', 'width="18" height="16"').' title="'.$LANG->getLL('tx_partner.label.send_email').' ('.$stdEmailAdress.')" alt="" /></a>';

		return $mailIconLink;
	}

	function getStandardEmailAdress($partnerUID) {
		$stdContactInfo = t3lib_BEfunc::getRecordsByField(
			'tx_partner_contact_info',
			'uid_foreign',
			$partnerUID,
			'AND tx_partner_contact_info.type=3 AND tx_partner_contact_info.standard=1'
		);

		return $stdContactInfo[0]['email'];
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports_email.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports_email.php']);
}
?>