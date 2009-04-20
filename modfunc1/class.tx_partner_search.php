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
* Contains a class to add the 'Search' section to the main menu of
* the Web>Partner module.
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_query.php');
$GLOBALS['LANG']->includeLLFile('EXT:partner/locallang_db.xml');






/**
 * The Search function in the Web>Partner module
 * Creates a framework for adding report sub-sub-modules under the Search function in Web>Partner
 *
 * @author	David Bruehlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_partner
 */
class tx_partner_search extends t3lib_extobjbase {
	var $function_key = 'tx_partner_search';

	/**
	 * Initialize.
	 * Calls parent init function and then the handleExternalFunctionValue() function from the parent class
	 *
	 * @param	object		A reference to the parent (calling) object (which is probably an instance of an extension class to t3lib_SCbase)
	 * @param	array		The configuration set for this module - from global array TBE_MODULES_EXT
	 * @return	void
	 * @see t3lib_extobjbase::handleExternalFunctionValue(), t3lib_extobjbase::init()
	 */
	function init(&$pObj,$conf)	{
			// OK, handles ordinary init. This includes setting up the menu array with ->modMenu
		parent::init($pObj,$conf);

			// Making sure that any further external classes are added to the include_once array. Notice that inclusion happens twice in the main script because of this!!!
		$this->handleExternalFunctionValue();
	}


	/**
	 * Creation of the main content. Calling extObjContent() to trigger content generation from the sub-sub modules
	 *
	 * @return	string		The content
	 * @see t3lib_extobjbase::extObjContent()
	 */
	function main()	{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');
		
			// Define the fields by which a user can search partner-data
		$queryFields = array(
			'first_name',
			'last_name',
			'org_name',
			'street',
			'postal_code',
			'locality',
		);

			// Define the fields to display in the list
		$fieldSelection['tx_partner_main'] = array(
			'_name' => array(
				'screen' => '1',
				'length' => '30',
			),
			'street' => array(
				'screen' => '1',
				'length' => '15',
			),
			'postal_code' => array(
				'screen' => '1',
				'length' => '5',
			),
			'locality' => array(
				'screen' => '1',
				'length' => '15',
			),
		);

			// Define the format options for the list
		$formatOptions['formatOptions'] = array(
			'editReport' => '0',
			'editPartner' => '1',
			'mailLink' => '1',
		);

			// Check for incoming data and save it
		$data = t3lib_div::_POST('data');
		if (is_array($data))		{
			foreach ($queryFields as $fieldName)		{
				$searchValues['data'][$fieldName] = $data[$fieldName];
			}
			$searchValues['exact_search'] = t3lib_div::_POST('exact_search');
			$searchValues['max'] = t3lib_div::_POST('max');
			$GLOBALS['BE_USER']->pushModuleData($this->function_key, $searchValues);
		}

			// Get the current search values from the current BE-session data and the begin from the GET-value
		$searchValues = $GLOBALS['BE_USER']->getModuleData($this->function_key, 'ses');
		$searchValues['begin'] = t3lib_div::_GP('begin');

			// If no maximum number of partners to display was entered, set it to a default value
		$searchValues['max'] = $searchValues['max'] ? $searchValues['max'] : '99';

			// Query fields
		foreach ($queryFields as $i => $theField)		{
			$code[$i][] = $LANG->getLL('tx_partner_main.'.$theField);
			$code[$i][] = '<input type="text" name="data['.$theField.']" value="'.$searchValues['data'][$theField].'"'.$GLOBALS['TBE_TEMPLATE']->formWidth(30).' maxlength="50" />';
		}
			// Exact Search
		$helpIcon = t3lib_BEfunc::cshItem('_MOD_partner', 'exact_search', $GLOBALS['BACK_PATH']);
		$code[$i+1][] = $LANG->getLL('tx_partner.modfunc.search.exact_search').$helpIcon;
		$code[$i+1][] = '<input type="checkbox" name="exact_search"'.($searchValues['exact_search']?' checked="checked"':'').' />';

			// Max. number of results
		$code[$i+2][] = $LANG->getLL('tx_partner.modfunc.search.max_no_of_results');
		$code[$i+2][] = '<input type="text" name="max" value="'.$searchValues['max'].'"'.$GLOBALS['TBE_TEMPLATE']->formWidth(2).' maxlength="2" />';

			// Create the table
		$this->pObj->doc->tableLayout = Array (
			'defRow' => Array (
				'0' => Array('<td align="left" valign="top" height="22" width="150">','</td>'),
				'defCol' => Array('<td valign="top" height="22">','</td>')
			)
		);
		$content.= $this->pObj->doc->section($LANG->getLL('tx_partner.modfunc.search'), $this->pObj->doc->table($code),0,1);

			// Submit Button
		$content.= '<input type="hidden" name="begin" value="0">';
		$content.= $this->pObj->doc->spacer(5);
		$content.= $this->pObj->doc->section('','<input type="submit" name="submit" value="'.$LANG->getLL('tx_partner.modfunc.search.search_partner').'" />');
		$content.= $this->pObj->doc->spacer(15);

		$query = t3lib_div::makeInstance('tx_partner_query');
		if ($this->pObj->id) $totalNo = $query->getPartnerBySearchStrings($searchValues['data'], $this->pObj->id, $searchValues['exact_search'], $searchValues['begin'], $searchValues['max']);

		if (is_array($query->query))		{

				// Partner found
			$list = $query->getFormattedDataByQuery('BE_module', 'field_selection', '0', $fieldSelection, TRUE, FALSE, TRUE, $formatOptions);
			$to = ($searchValues['begin']+$searchValues['max'] >= $totalNo) ? $totalNo : $searchValues['begin']+$searchValues['max'];
			$partnerCount = sprintf($LANG->getLL('tx_partner.modfunc.search.partner_FROM_to_TO_of_TOTAL_partners'), $searchValues['begin']+1, $to, $totalNo);
			$title = strtoupper($LANG->getLL('tx_partner.modfunc.search.search_result')).' ('.$partnerCount.')';

				// If not all partners are displayed, add navigation bar to top and end of the list
			if ($totalNo > count($query->query))		{
				$navBar = $this->getNavigationBar($searchValues['begin'], $totalNo, $searchValues['max'], $this->pObj->id);
				$list = $navBar.$this->pObj->doc->spacer(5).$list.$this->pObj->doc->spacer(10).$navBar;
			}
			$content.= $this->pObj->doc->section($title, $list, 1, 1);
		} else {

				// No partner found
			$content.= $this->pObj->doc->section($LANG->getLL('tx_partner.modfunc.search.search_result'), $LANG->getLL('tx_partner.modfunc.search.no_partner_found'),0,1);
		}


		return $content;
	}


	/**
	 * Gets a navigation bar for browsing the resulting partner-list. It consists of max. two elements, one for
	 * going backwards and one for going forward.
	 *
	 * @param	integer		$begin: Indicates where the list currently begins
	 * @param	integer		$total: Total number of selectable partners
	 * @param	integer		$max: Number of max. records to be displayed at once
	 * @param	integer		$pid: PID of the current page
	 * @return	string		HTML with the navigation bar (wrapped in a table)
	 */
	function getNavigationBar($begin, $total, $max, $pid)		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');


			// Get the left link (go back)
		if ($begin > 0)		{
			$label = sprintf($LANG->getLL('tx_partner.modfunc.search.display_previous'), $max);
			$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/button_left.gif', 'width="11" height="10"').' title="'.$label.'" border="0" alt="" />';
			$from = $begin-$max;
			$leftLink = $icon.'&nbsp;<a href="?id='.$pid.'&begin='.$from.'" title="'.$label.'">'.$label.'</a>';
		}

			// Get the right link (go forward)
		if ($total-$max > $begin)		{
			$next = ($begin+$max*2 > $total) ? $total-($begin+$max) : $max;
			$label = sprintf($LANG->getLL('tx_partner.modfunc.search.display_next'), $next);
			$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/button_right.gif', 'width="11" height="10"').' title="'.$label.'" border="0" alt="" />';
			$from = $begin+$max;
			$rightLink = '<a href="?id='.$pid.'&begin='.$from.'" title="'.$label.'">'.$label.'</a>&nbsp;'.$icon;
		}

			// Create HTML-Table
		$out = '
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" valign="top">'.$leftLink.'</td>
					<td align="right" valign="top">'.$rightLink.'</td>
				</tr>
			</table>
		';

		return $out;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_search.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_search.php']);
}
?>
