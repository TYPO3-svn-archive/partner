<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007 David Bruehlmeier (typo3@bruehlmeier.com)
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
		$modMenuAdd['link'] = '';
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
		$LANG->includeLLFile('EXT:partner/locallang.php');

		// Which occupation? (option)
		$occupation = $this->pObj->MOD_SETTINGS['occupation'];
		$allOccupations = $this->readOccupations();

		// Get the partner for the selected occupation
		if ($occupation == 'any') {
			foreach ($allOccupations as $k=>$v) {
				if ($k == 'any') continue;
				$emailAddresses = $this->getEmailAdresses($k);
				$list.= $this->pObj->doc->section($v, $emailAddresses, 0, 1);
			}
			if (!$list) $list = $LANG->getLL('tx_partner.modfunc.reports.email.no_partner_found_for_this_occupation');

		} else {
			$emailAddresses = $this->getEmailAdresses($occupation);
			$list = $this->pObj->doc->section($allOccupations[$occupation], $emailAddresses, 0, 1);
		}

		// Prepare the output
		$content.= $this->pObj->doc->section('', $this->pObj->doc->funcMenu($LANG->getLL('tx_partner.modfunc.reports.email.occupation').':', t3lib_BEfunc::getFuncMenu($this->pObj->id, 'SET[occupation]', $this->pObj->MOD_SETTINGS['occupation'], $this->pObj->MOD_MENU['occupation'])));
		$content.= $this->pObj->doc->spacer(5);
		$content.= $this->pObj->doc->section('', $this->pObj->doc->funcMenu($LANG->getLL('tx_partner.modfunc.reports.email.display_link_icon').':', t3lib_BEfunc::getFuncCheck($this->pObj->id, 'SET[link]', $this->pObj->MOD_SETTINGS['link'], $this->pObj->MOD_MENU['link'])));
		$content.= $this->pObj->doc->spacer(20);
		$content.= $list;

		// Return the output
		return $content;
	}

	function readOccupations($addAny = false) {
		global $LANG;
		if ($this->occupations) return $this->occupations;

		// Read all occupations
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid,oc_descr',
			'tx_partner_val_occupations',
			'tx_partner_val_occupations.pid'.t3lib_BEfunc::deleteClause('tx_partner_val_occupations'),
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

	function getEmailAdresses($occupation) {
		global $LANG;

		$contactInfo = t3lib_div::makeInstance('tx_partner_contact_info');
		$query = t3lib_div::makeInstance('tx_partner_query');
		$out = array();

		$filter[] = $occupation;
		$query->getPartnerByOccupation($filter);

		// Get the standard E-Mail addresses for the selected partners
		if (is_array($query->query)) {
			foreach ($query->query as $partner) {
				//$contactInfo->getContactInfo($partner->data['uid']);
				$stdContactInfo = t3lib_BEfunc::getRecordsByField(
					'tx_partner_contact_info',
					'uid_foreign',
					$partner->data['uid'],
					'AND tx_partner_contact_info.type=3 AND tx_partner_contact_info.standard=1'
				);
			if ($this->pObj->MOD_SETTINGS['link']) {
				$result[] = tx_partner_div::getMailIconLink($partner->data['uid']).$stdContactInfo['0']['email'];
			} else {
				$result[] = $stdContactInfo['0']['email'];
			}
			}
		}

		if (is_array($result)) {
			$out = implode('; ', $result);
		} else {
			$out = $LANG->getLL('tx_partner.modfunc.reports.email.no_partner_found_for_this_occupation');
		}
		return $out;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports_email.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports_email.php']);
}
?>