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
* Contains a class to add the 'Reports' section to the main menu of
* the Web>Partner module.
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_query.php');






/**
 * The Reports function in the Web>Partner module
 * Creates a framework for adding report sub-sub-modules under the Reports function in Web>Partner
 *
 * @author	David Bruehlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_partner
 */
class tx_partner_reports extends t3lib_extobjbase {
	var $function_key = 'tx_partner_reports';		// The function key
	var $pointer = 0;								// The current pointer from the element browser
	var $interval = 10;								// Interval for one "page" for the element browser

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
		
			// Get the current pointer from the element browser
		$this->pointer = intval(t3lib_div::_GP('pointer'));
		
			// Get the interval (can be configured in the Extension Manager)
		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['partner']);
		$this->interval = $confArr['maxNoOfPartner'] ? $confArr['maxNoOfPartner'] : 10;
		
			// OK, handles ordinary init. This includes setting up the menu array with ->modMenu
		parent::init($pObj,$conf);

			// Making sure that any further external classes are added to the include_once array. Notice that inclusion happens twice in the main script because of this!!!
		$this->handleExternalFunctionValue();
	}

	/**
	 * Modifies parent objects internal MOD_MENU array, adding items this module needs.
	 *
	 * @return	array		Items merged with the parent objects.
	 * @see t3lib_extobjbase::init()
	 */
	function modMenu()	{

		$modMenuAdd = array();

			// Get all saved reports for the current pid
		$reports = t3lib_BEfunc::getRecordsByField('tx_partner_reports','pid',$this->pObj->id);

		if (is_array($reports))		{
			foreach ($reports as $theReport)		{
				 $modMenuAdd[$this->function_key][$theReport['uid']] = $theReport['title'];
			}
		}

			// Add the sub-submenus for the current submenu
		$modMenuAdd[$this->function_key] = $this->pObj->mergeExternalItems($this->pObj->MCONF['name'],$this->function_key,$modMenuAdd[$this->function_key]);
		$modMenuAdd[$this->function_key] = t3lib_BEfunc::unsetMenuItems($this->pObj->modTSconfig['properties'],$modMenuAdd[$this->function_key],'menu.'.$this->function_key);
		return $modMenuAdd;
	}

	/**
	 * Creation of the main content. Calling extObjContent() to trigger content generation from the sub-sub modules
	 *
	 * @return	string		The content
	 * @see t3lib_extobjbase::extObjContent()
	 */
	function main()	{
		$content = '';

			// If the current module is numeric, it is assumed to be a saved report
		if (is_numeric($this->pObj->MOD_SETTINGS[$this->function_key]))		{
			
				// Just a quick-fix: Show an element browser to make sure the report can also be used
				// when working with large amounts of partners.
			$content.= $this->getElementBrowser($this->pObj->MOD_SETTINGS[$this->function_key]);

				// Get the data formatted as a backend module
			$query = t3lib_div::makeInstance('tx_partner_query');
			$limit = (string)$this->pointer.','.(string)($this->interval);
			$content.= $query->getFormattedDataByReport($this->pObj->MOD_SETTINGS[$this->function_key], 'BE_module', $limit);

		} else {

				// Not a saved report but a report configured as a submodule
			$content.= $this->extObjContent();
		}

		return $content;
	}
	
	
	
	function getElementBrowser($uid)	{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');
		$out = '';
		$content = '';
		
			// Get the total count of partners (max)
		$count = tx_partner_div::getCountFromReport($uid);
		
			// If the pointer is not 0 (initial), enable the "previous" link
		$label = $LANG->getLL('tx_partner.modfunc.reports.previous_page');
		if ($this->pointer > 0)	{
			$img = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pilleft_n.gif','width="14" height="14"').' alt="'.$label.'" title="'.$label.'" />';
			$href = htmlspecialchars(t3lib_div::linkThisScript(array('pointer'=>$this->pointer-$this->interval)));
			$left = '<a href="'.$href.'">'.$img.'</a>'.'&nbsp;<a href="'.$href.'">'.$label.'</a>';
		} else {
			$img = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pilleft_d.gif','width="14" height="14"').' alt="'.$label.'" title="'.$label.'" />';
			$left = $img.'&nbsp;'.$label;		
		}
		
			// If the current pointer + the interval is less than the total number of partner, enable the "next" link
		$label = $LANG->getLL('tx_partner.modfunc.reports.next_page');
		if ($this->pointer+$this->interval < $count)	{
			$img = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pilright_n.gif','width="14" height="14"').' alt="'.$label.'" title="'.$label.'" />';
			$href = htmlspecialchars(t3lib_div::linkThisScript(array('pointer'=>$this->pointer+$this->interval)));
			$right = '<a href="'.$href.'">'.$label.'</a>&nbsp;<a href="'.$href.'">'.$img.'</a>';
		} else {
			$img = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/pilright_d.gif','width="14" height="14"').' alt="'.$label.'" title="'.$label.'" />';
			$right = $label.'&nbsp;'.$img;		
		}
		
			// Wrap in a table
		$content.= '<tr height="30"><td align="left">'.$left.'</td><td align="right">'.$right.'</td></tr>';
		$out = '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$content.'</table>';
		return $out;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports.php']);
}
?>
