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
 * Class for advanced SELECT lookups for display in TCE Forms
 *
 * @author David Bruehlmeier <typo3@bruehlmeier.com>
 */


require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');


class tx_partner_select {


	/**
	 * Advanced select for relationship types.
	 * Only the types which are allowed for the current relationship category
	 * (as defined in tx_partner_val_rel_types.allowed_categories) must be displayed.
	 *
	 * @param	array		&$params: A reference to the current $params of the field
	 * @param	object		&$pObj: A reference to the current parent object
	 * @return	void		The parameters are directly changed, since they are passed by reference
	 */
	function types(&$params, &$pObj) {
		global $TCA;

			// Is the current partner primary or secondary?
		$GPvar = t3lib_div::GPvar('tx_partner');
		$primaryOrSecondary = ($GPvar['relPrimSec'] == 'primary') ? 0 : 1;

			// Get the allowed relationship types and add them to the selection list
		$allowedRelationshipTypes = tx_partner_div::getAllowedRelationshipTypes($params['row']['pid'], $GPvar['partnerType'], $primaryOrSecondary);
		if (is_array($allowedRelationshipTypes))		{
			foreach ($allowedRelationshipTypes as $k=>$v)		{
				$params['items'][] = array($v['rt_descr'], $k, '');
			}

		}
	}


	/**
	 * Advanced select for status. Only if a status is allowed for a table
	 * (as defined in tx_partner_val_status.allowed_tables), it must be displayed.
	 *
	 * @param	array		&$params: A reference to the current $params of the field
	 * @param	object		&$pObj: A reference to the current parent object
	 * @return	void		The parameters are directly changed, since they are passed by reference
	 * @todo	Possible improvement: Use tx_partner_div::getAllowedStatus
	 */
	function status(&$params, &$pObj) {

			// Check each status if it is allowed for the current table and unset if necessary
		foreach ($params['items'] as $itemCounter => $theItem) {
			$allowedTables = t3lib_BEfunc::getRecord('tx_partner_val_status', $theItem['1'], 'allowed_tables');
			if (!t3lib_div::inList($allowedTables['allowed_tables'], $params['table'])) unset($params['items'][$itemCounter]);
		}
	}


	/**
	 * Advanced select for allowed formats. Displays all formats currently available.
	 *
	 * @param	array		&$params: A reference to the current $params of the field
	 * @param	object		&$pObj: A reference to the current parent object
	 * @return	void		The parameters are directly changed, since they are passed by reference
	 */
	function allowed_formats(&$params, &$pObj) {
		global $LANG, $TYPO3_CONF_VARS;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Create the items-array with all possible formats
		foreach ($TYPO3_CONF_VARS['EXTCONF']['partner']['formats'] as $theFormat=>$formatOptions)		{
			if ($formatOptions['external'])		{
				$params['items'][] = array($LANG->sL($formatOptions['label']), $theFormat, $formatOptions['icon']);
			}
		}
	}



	/**
	 * Advanced select for available fonts. Displays all fonts configured for the extension FPDF.
	 *
	 * @param	array		&$params: A reference to the current $params of the field
	 * @param	object		&$pObj: A reference to the current parent object
	 * @return	void		The parameters are directly changed, since they are passed by reference
	 */
	function fonts(&$params, &$pObj) {
		global $TYPO3_CONF_VARS;

		if (is_array($TYPO3_CONF_VARS['EXTCONF']['fpdf']['fonts']))		{
			foreach ($TYPO3_CONF_VARS['EXTCONF']['fpdf']['fonts'] as $theFont)		{
				$extFonts[$theFont[0]] = $theFont[0];
			}
		}

		$params['items'][] = array($theFont[0], $theFont[0], '');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_select.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_select.php']);
}

?>