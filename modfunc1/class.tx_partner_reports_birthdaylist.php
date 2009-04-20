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
* Displays the 'Birthday List' Report as a sub-submodule of
* Web>Partner>Reports
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_query.php');




/**
 * Class for displaying the 'Birthday List' Report in Web>Partner>Reports
 *
 * @author	David Bruehlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_partner
 */
class tx_partner_reports_birthdaylist extends t3lib_extobjbase {


	/**
	 * Modifies parent objects internal MOD_MENU array, adding items this module needs.
	 *
	 * @return	array		Items merged with the parent objects.
	 * @see t3lib_extobjbase::init()
	 */
	function modMenu()	{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');

			// Add number of months to be displays
		$labelMonths = $LANG->getLL('tx_partner.label.months');
		$modMenuAdd = array(
			'no_of_months' => Array (
				'01' => '1 '.$LANG->getLL('tx_partner.label.month'),
				'02' => '2 '.$labelMonths,
				'03' => '3 '.$labelMonths,
				'04' => '4 '.$labelMonths,
				'05' => '5 '.$labelMonths,
				'06' => '6 '.$labelMonths,
				'07' => '7 '.$labelMonths,
				'08' => '8 '.$labelMonths,
				'09' => '9 '.$labelMonths,
				'10' => '10 '.$labelMonths,
				'11' => '11 '.$labelMonths,
				'12' => '1 '.$LANG->getLL('tx_partner.label.year'),
			),
		);

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

			// Get a query instance
		$query = t3lib_div::makeInstance('tx_partner_query');

			// Define the fields to display in the list
		$fieldSelection['tx_partner_main'] = array(
			'_name' => array(
				'screen' => '1',
				'length' => '35',
			),
			'birth_date' => array(
				'screen' => '1',
				'length' => '15',
			),
			'_age' => array(
				'screen' => '1',
				'length' => '10',
			),
		);

			// Define the format options for the list
		$formatOptions['formatOptions'] = array(
			'editReport' => '0',
			'editPartner' => '1',
			'mailLink' => '1',
		);

			// How many months in advance (option)
		$noOfMonths = (int)$this->pObj->MOD_SETTINGS['no_of_months'];
		$currentMonth = date('n')-1;
		$i = 0;

			// Get the list for all requested months
		while ($i < $noOfMonths) {

				// Determine the month and the year for which to get the list
			$listMonth = fmod($currentMonth+$i, 12)+1;
			$listMonth = str_pad((string)$listMonth, 2, '0', STR_PAD_LEFT);
			$listYear = ($currentMonth+$i < 12) ? date('Y') : date('Y')+1;

				// Get the list
			$query->getPartnerByBirthday('', $listMonth, '01', '', $listMonth, '31', $this->pObj->id);
			if (is_array($query->query))		{
				$res = $query->getFormattedDataByQuery('BE_module', 'field_selection', '0', $fieldSelection, TRUE, FALSE, TRUE, $formatOptions);
				unset ($query->query);
				$list.= $this->pObj->doc->section($LANG->getLL('tx_partner.label.month').' '.$LANG->getLL('tx_partner.label.month.'.$listMonth).' '.$listYear, $res, 1, 1);
				$list.= $this->pObj->doc->spacer(25);
			}

			$i++;
		}

			// Prepare the output
		$content.= $this->pObj->doc->section('', $this->pObj->doc->funcMenu($LANG->getLL('tx_partner.modfunc.reports.birthdaylist.reporting_period').':', t3lib_BEfunc::getFuncMenu($this->pObj->id, 'SET[no_of_months]', $this->pObj->MOD_SETTINGS['no_of_months'], $this->pObj->MOD_MENU['no_of_months'])));
		$content.= $this->pObj->doc->section($LANG->getLL('tx_partner.modfunc.reports.birthdaylist'), $list, 0, 1);

			// Return the output
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports_birthdaylist.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports_birthdaylist.php']);
}
?>