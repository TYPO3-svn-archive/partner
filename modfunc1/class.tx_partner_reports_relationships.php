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
* Displays the 'Overview Relationships' Report as a sub-submodule of
* Web>Partner>Reports
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');





/**
 * Class for displaying the 'Overview Relationships' Report in Web>Partner>Reports
 *
 * @author	David Bruehlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_partner
 */
class tx_partner_reports_relationships extends t3lib_extobjbase {


	/**
	 * Modifies parent objects internal MOD_MENU array, adding items this module needs.
	 *
	 * @return	array		Items merged with the parent objects.
	 * @see t3lib_extobjbase::init()
	 */
	function modMenu()	{

		$modMenuAdd = array();

			// Add items for the relationship types
		$relationshipTypes = t3lib_BEfunc::getRecordsByField('tx_partner_val_rel_types', 'pid', $this->pObj->id, '', 'uid');
		if (is_array($relationshipTypes)) {
			foreach ($relationshipTypes as $theRelationshipType) {
				$modMenuAdd['relationship_type'][$theRelationshipType['uid']] = $theRelationshipType['rt_descr'];
			}
		}

		return $modMenuAdd;
	}



	/**
	 * Creation of the report.
	 *
	 * @return	string		The content
	 */
	function main()	{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');

			// Get the relationship overview for the selected relationship type
		$overview = $this->getRelationshipOverview($this->pObj->MOD_SETTINGS['relationship_type']);

			// Prepare the output
		$content.= $this->pObj->doc->section($LANG->getLL('tx_partner.modfunc.reports.relationships'), '', 0, 1);
		$content.= $this->pObj->doc->section('', $this->pObj->doc->funcMenu($LANG->getLL('tx_partner.label.relationship_type').':', t3lib_BEfunc::getFuncMenu($this->pObj->id, 'SET[relationship_type]', $this->pObj->MOD_SETTINGS['relationship_type'], $this->pObj->MOD_MENU['relationship_type'])));
		$content.= $this->pObj->doc->spacer(10);
		$content.= $this->pObj->doc->section('', $overview, 1, 1);

			// Return the output
		return $content;
	}


	/**
	 * Generates the relationship overview
	 * This method automatically finds all the starting points (highest in hierachy)
	 * for the requested types and the makes a tree-view of all related records
	 *
	 * @param	string		$type: Relationship type for which the overview must be created
	 * @return	string		HTML to display the tree-view of the relationship overview
	 */
	function getRelationshipOverview($type)		{
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');

			// Get all relationships for the current relationship type
		$relationships = t3lib_BEfunc::getRecordsByField('tx_partner_relationships', 'type', $type, 'AND tx_partner_relationships.pid='.$this->pObj->id);

			// Find starting points within these relationships
		if (is_array($relationships))		{
			foreach ($relationships as $theRelationship)		{
				if (is_array($startingPoints))		{
					if (in_array($theRelationship['uid_primary'], $startingPoints))		{
						// Do nothing... this uid was already identified as starting point
					} else {
						$secondaryRelationships = t3lib_BEfunc::getRecordsByField('tx_partner_relationships', 'uid_secondary', $theRelationship['uid_primary'], 'AND tx_partner_relationships.type='.$type);
						if (!is_array($secondaryRelationships))		{
								// Identified as starting point!
							$startingPoints[] = $theRelationship['uid_primary'];
						}
					}
				} else {
					$secondaryRelationships = t3lib_BEfunc::getRecordsByField('tx_partner_relationships', 'uid_secondary', $theRelationship['uid_primary'], 'AND tx_partner_relationships.type='.$type);
					if (!is_array($secondaryRelationships))		{
							// Identified as starting point!
						$startingPoints[] = $theRelationship['uid_primary'];
					}
				}
			}

				// From each starting point, collect the data to create a tree
			if (is_array($startingPoints))		{
				foreach ($startingPoints as $theStartingPoint)		{
					$d = $this->getTreeDataArray($theStartingPoint, $type);
					$treeDataArray[$theStartingPoint] = $d[$theStartingPoint];
				}
			}

				// Make instance of tree
			$tree = t3lib_div::makeInstance('t3lib_treeView');

				// Initialize tree
			$tree->init();
			$tree->table = 'tx_partner_main';
			$tree->expandAll = 1;

				// Import data
			$tree->setDataFromArray($treeDataArray);

				// Create the tree
			$tree->getTree(0);

				// Put together the tree HTML
			$content = '
				<tr '.$this->pObj->defaultHeader1Style.'>
				<td>Partner</td>
				<td>'.$LANG->getLL('tx_partner.label.edit_partner').'</td>
				</tr>';
			foreach($tree->tree as $data)		{
				$content .= '
					<tr>
					<td nowrap="nowrap">'.$data['HTML'].htmlspecialchars($data['row']['title']).'</td>
					<td>'.tx_partner_div::getEditPartnerLink($data['row']['uid']).'</td>
					</tr>';
			}
			$content = '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$content.'</table>';

			return $content;
		} else {
				// No records available
			return $LANG->getLL('tx_partner.label.no_records_available');
		}
	}

	/**
	 * Internal method to get the data array to display the tree for the
	 * relationship overview
	 *
	 * @param	string		$partnerUID: UID of the partner for which the data array must be selected
	 * @param	string		$type: Relationship type for which the data array must be selected
	 * @param	string		$recursive: Internal: If the method is called recersively, this must be set to true
	 * @return	array		Tree Data Array
	 */
	function getTreeDataArray($partnerUID, $type, $recursive = false)		{
		static $treeDataArray;

			// Make instance of t3lib_treeView (only needed for the constant of subLevelID)
		$tree = t3lib_div::makeInstance('t3lib_treeView');

			// Get the partner and relationship data for the current partner
		$partner = t3lib_BEfunc::getRecord('tx_partner_main', $partnerUID, '*', '');
		$relationships = t3lib_BEfunc::getRecordsByField('tx_partner_relationships', 'uid_primary', $partnerUID, 'AND tx_partner_relationships.type='.$type);

			// Create the data array
		$tempDataArray[$partnerUID] = array(
			'title' => t3lib_div::fixed_lgd($partner['label'],50),
			'type' => $partner['type'],
		);

			// If relationships are available for this partner, then create subpages by calling the function for each relationship recursively
		if (is_array($relationships))		{
			foreach ($relationships as $theRelationship)		{
				$tempDataArray[$partnerUID][$tree->subLevelID][$theRelationship['uid_secondary']] = $this->getTreeDataArray($theRelationship['uid_secondary'], $type, true);
			}
		}

		if ($recursive)		{
				// The function was called recursively to create subpages, return only the temporary array
			return $tempDataArray[$partnerUID];
		} else {
				// Add up the treeDataArray
			$treeDataArray = $tempDataArray;
		}

			// Return the treeDataArray for the current partnerUID
		return $treeDataArray;
	}


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports_relationships.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/modfunc1/class.tx_partner_reports_relationships.php']);
}
?>