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
* Class for getting localized labels under both Frontend
* and Backend conditions. This is used throughout the extension
* in API-functions which must be usable under any conditions.
*
* Use non-instantiated, eg: tx_partner_lang::getLabel()
*
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

//require_once(t3lib_extMgm::extPath('cms').'tslib/class.tslib_pibase.php');


class tx_partner_lang {

	/**
	 * Gets a label under both Frontend and Backend conditions. Under Backend conditions,
	 * the label will be localized using the $LANG object. Under Frontend conditions, the
	 * label will be localized using the pibase-function to read the $LOCAL_LANG array.
	 *
	 * @param	string		$label: Name of the label for which to get the text
	 * @return	string		Localized text of the label
	 */
	function getLabel($label)		{

		$out = '';

		if (TYPO3_MODE == 'BE')		{
			global $LANG;
			$LANG->includeLLFile('EXT:partner/locallang_db.xml');

				// Try to read the label using the getLL function. If this doesn't return a label, try using the sL function (e.g. for use with EXT:... labels).
			$out = $LANG->getLL($label);
			if (!$out) $out = $LANG->sL($label);
		}

		if (TYPO3_MODE == 'FE')		{

				// Create a new pibase-instance
			$pibase = t3lib_div::makeInstance('tslib_pibase');

				// Load the labels
			$pibase->scriptRelPath = './';
			$pibase->extKey = 'partner';
			$pibase->pi_loadLL();

				// Return the requested label
			$out = $pibase->pi_getLL($label);

		}

			// If the input could not be transformed into a text, return false. Otherwise return the text
		if ($label == $out)		{
			return false;
		} else {
			return $out;
		}
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_lang.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/api/class.tx_partner_lang.php']);
}

?>