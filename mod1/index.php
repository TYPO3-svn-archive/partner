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
* Module 'Partner' for the 'partner' extension.
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

unset($MCONF);
require ('conf.php');
$BACK_PATH='../../../../typo3/';
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
require_once (PATH_t3lib.'class.t3lib_treeview.php');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
require_once (PATH_t3lib.'class.t3lib_tcemain.php');
require_once (PATH_t3lib.'class.t3lib_tceforms.php');
require_once (t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');

$GLOBALS['LANG']->includeLLFile ('EXT:partner/locallang.php');
$GLOBALS['BE_USER']->modAccess($MCONF, 0); // This checks permissions and exits if the users has no permission for entry.

class tx_partner_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $defaultListStyle = 'height="18px" style="border-bottom-width:1px; border-bottom-color:#C6C2BD; border-bottom-style:solid;" nowrap';
	var $defaultHeader1Style = 'bgcolor="#CBC7C3"';

	/**
	 * Initializes all global variables and calls the init() method
	 * of the parent class (t3lib_SCbase)
	 *
	 * @return	void
	 */
	function init() {
		global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
		$LANG->includeLLFile('EXT:partner/locallang.php');

		$this->tceforms = t3lib_div::makeInstance('t3lib_TCEforms');
		$this->tceforms->totalWrap = '';
		$this->tceforms->fieldTemplate='###FIELD_ITEM###';
		parent::init();
	}

	/**
	 * Configures the menus (main and submodules)
	 *
	 * @return	void
	 */
	function menuConfig() {

			// Add the Main Menu
		$this->MOD_MENU['function'] = $this->mergeExternalItems($this->MCONF['name'],'function',$this->MOD_MENU['function']);
		$this->MOD_MENU['function'] = t3lib_BEfunc::unsetMenuItems($this->modTSconfig['properties'],$this->MOD_MENU['function'],'menu.function');
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Writes the content to $this->content
	 *
	 * @return	void
	 */
	function main() {
		global $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
		$LANG->includeLLFile('EXT:partner/locallang.php');
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');

			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id)) {
				// Draw the header
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form = '<form action="" method="POST" name="editform">';

				// Add standard JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
				// Add JavaScript for the dynamic Tab Menus
			$this->doc->JScode.= $this->doc->getDynTabMenuJScode();

				// Add JavaScript to toggle fields
			$this->doc->JScode.= "
				<script language=\"javascript\" type=\"text/javascript\">
					var toggleStatus = new Array();
					function toggle(arrName, fieldName) {
						if (typeof(toggleStatus[arrName]) == 'undefined') toggleStatus[arrName] = 'checked';
						for (var i=0; i < document.forms[0].elements.length; i++) {
							if (document.forms[0].elements[i].name.search(arrName+'.*'+fieldName) != -1) {
								document.forms[0].elements[i].checked = toggleStatus[arrName];
							}
						}
						toggleStatus[arrName] = (toggleStatus[arrName] == 'checked') ? '' : 'checked';
						return true;
					}
				</script>
			";

				// Add standard JavaScript to the end
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
				</script>
			';

				// Make 'New Partner'-link, but only if creation of a new partner is allowed on the current page
			$newPartnerLabel = $LANG->getLL('tx_partner.label.create_new_partner');
			$newPartnerIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/new_el.gif', 'width="11" height="12"').' title="'.$newPartnerLabel.'" border="0" alt="" />';

			if ($tce->isTableAllowedForThisPage($this->id,'tx_partner_main'))		{
					// Allowed: Add link
				$params = '&edit[tx_partner_main]['.$this->id.']=new';
				$newPartnerLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'])).'">'.$newPartnerIcon.$newPartnerLabel.'</a>';
			} else {
					// Not allowed: No link, but help icon
				$helpIcon = t3lib_BEfunc::cshItem('_MOD_partner', 'no_sys_folder', $GLOBALS['BACK_PATH']);
				$newPartnerLink = $newPartnerIcon.$newPartnerLabel.$helpIcon;
			}


				// Check if there are partner records in the current folder
			if (!tx_partner_div::checkPartnerRecordsExist($this->id))	{
				$noPartnerIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_warning.gif', 'width="18" height="16" align="absmiddle"').' title="'.$newPartnerLabel.'" border="0" alt="" />';
				$noPartner.= '<br /><span class="typo3-red">'.$noPartnerIcon.$LANG->getLL('tx_partner.label.no_partner_in_the_current_folder').'</span>';
			}

				// Left Part of Header with path info and 'New Partner'-link
			$headerLeft = $LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'], 50).'<br />'.$newPartnerLink.$noPartner;
				// Right Part of Header with main and submenu
			$headerRight = t3lib_BEfunc::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']);
			$headerRight.= $this->doc->spacer(5);
			$headerRight.= t3lib_BEfunc::getFuncMenu($this->id,'SET['.$this->MOD_SETTINGS['function'].']',$this->MOD_SETTINGS[$this->MOD_SETTINGS['function']],$this->MOD_MENU[$this->MOD_SETTINGS['function']]);

				// Create the header
			$this->content.= $this->doc->startPage($LANG->getLL('tx_partner.label.partner_management'));
			$this->content.= $this->doc->header($LANG->getLL('tx_partner.label.partner_management'));
			$this->content.= $this->doc->spacer(5);
			$this->content.= $this->doc->section('', $this->doc->funcMenu($headerLeft, $headerRight));
			$this->content.= $this->doc->divider(5);

				// Render content
			$this->extObjContent();

				// Add shortcut
			if ($BE_USER->mayMakeShortcut()) {
				$this->content .= $this->doc->spacer(20).$this->doc->section('', $this->doc->makeShortcutIcon('id', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']));
			}

			$this->content.= $this->doc->spacer(10);
		} else {
				// If no access or if ID == zero
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

				// Make an empty header
			$this->content.= $this->doc->startPage($LANG->getLL('tx_partner.label.partner_management'));
			$this->content.= $this->doc->header($LANG->getLL('tx_partner.label.partner_management'));
			$this->content.= $this->doc->spacer(5);
			$this->content.= $this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()		{

		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/mod1/index.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_partner_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);
$SOBE->checkExtObj();	// Checking for first level external objects

// Repeat Include files! - if any files has been added by second-level extensions
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);
$SOBE->checkSubExtObj();	// Checking second level external objects

$SOBE->main();
$SOBE->printContent();

?>