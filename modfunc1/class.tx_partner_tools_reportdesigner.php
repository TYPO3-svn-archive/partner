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
* Displays the 'Report Designer' Tool as a sub-submodule of
* Web>Partner>Tools
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(PATH_t3lib.'class.t3lib_iconworks.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_query.php');




/**
 * Class for displaying the 'Report Designer' Tool in Web>Partner>Tools
 *
 * @author	David Bruehlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_partner
 */
class tx_partner_tools_reportdesigner extends t3lib_extobjbase {


	/**
	 * Creation of the report.
	 *
	 * @return	string		The content
	 */
	function main()	{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');

			// Make 'New Report'-link, but only if creation of a new report is allowed on the current page
		$newReportLabel = $LANG->getLL('tx_partner.label.create_new_report');
		$newReportIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/new_el.gif', 'width="11" height="12"').' title="'.$newPartnerLabel.'" border="0" alt="" />';

		if ($tce->isTableAllowedForThisPage($this->pObj->id,'tx_partner_reports'))		{
				// Allowed: Add link
			$params = '&edit[tx_partner_reports]['.$this->pObj->id.']=new';
			$newReportLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'])).'">'.$newReportIcon.$newReportLabel.'</a>';
		} else {
				// Not allowed: No link, but help icon
			$helpIcon = t3lib_BEfunc::cshItem('_MOD_partner', 'no_sys_folder', $GLOBALS['BACK_PATH']);
			$newReportLink = $newReportIcon.$newReportLabel.$helpIcon;
		}


		$reports = t3lib_BEfunc::getRecordsByField('tx_partner_reports', 'pid', $this->pObj->id);

		if (is_array($reports))		{

				// Header
			$list.= '
				<tr '.$this->pObj->defaultHeader1Style.'>
					<td width="15">&nbsp;</td>
					<td>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tx_partner_reports', 'title')).'</td>
					<td>&nbsp;</td>
				</tr>';

				// List
			foreach ($reports as $theReport)		{

					// Download Buttons
				$buttons = tx_partner_div::getFormatIcons($theReport['uid'], '', 'horizontal', TRUE, FALSE, TRUE, $GLOBALS['BACK_PATH']);

				$list.= '
					<tr>
						<td width="15">'.tx_partner_div::getEditReportLink($theReport['uid']).'</td>
						<td '.$this->pObj->defaultListStyle.'>'.t3lib_div::fixed_lgd($theReport['title'],'40').'&nbsp;</td>
						<td align="right" '.$this->pObj->defaultListStyle.'>'.$buttons.'</td>
					</tr>';
			}

			$list = '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$list.'</table>';

		}

			// Prepare the output
		$content.= $this->pObj->doc->section($LANG->getLL('tx_partner.modfunc.tools.reportdesigner'), '', 0, 1);
		$content.= $this->pObj->doc->section('', $newReportLink);
		$content.= $this->pObj->doc->spacer('15');
		$content.= $this->pObj->doc->section($title, $list, 1, 1);

			// Return the output
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_tools_reportdesigner.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_tools_reportdesigner.php']);
}
?>