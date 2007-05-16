<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 David Bruehlmeier (typo3@bruehlmeier.com)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Class for updating tx_partner_val_types from tx_partner_val_rel_types
 *
 * @author  David Bruehlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_partner
 */
class ext_update {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main() {
		$out = '';
		$records = $this->getRecords();

		if (!t3lib_div::GPvar('do_update')) {
			$onClick = "document.location='".t3lib_div::linkThisScript(array('do_update' => 1))."'; return false;";
			$out = 'There are '.count($records['tx_partner_val_rel_subtypes']).' relationship subtype records which must be migrated to the new relationship type table (tx_partner_val_rel_types).<br />
			        There are '.count($records['tx_partner_relationships']).' relationship records where the \'subtype\' field must be migrated into the \'type\' field.';
			$out.= $GLOBALS['TBE_TEMPLATE']->spacer(10);
			$out.= '<strong><span class="typo3-red">BEWARE!</span></strong><br />';
			$out.= 'This action <span class="typo3-red">will change</span> your old data in the tx_partner_relationships table. It will <span class="typo3-red">remove</span> the table tx_partner_val_rel_subtypes and it will <span class="typo3-red">remove</span> the field \'subtype\' from tx_partner_relationships!<br /><br />';
			$out.= 'Make sure you have made a <span class="typo3-red"><strong>COMPLETE BACKUP</strong></span> of your data before proceeding!';
			$out.= $GLOBALS['TBE_TEMPLATE']->spacer(10);
			$out.= 'Do you want to perform the action now?<br />';
			$out.= '<form action=""><input type="submit" value="UPDATE" onclick="'.htmlspecialchars($onClick).'"></form>';
		} else {

			foreach ($records['tx_partner_val_rel_subtypes'] as $theRecord)		{
				$theRecord['rt_descr_short'] = $theRecord['rs_descr_short'];
				unset ($theRecord['rs_descr_short']);
				$theRecord['rt_descr'] = $theRecord['rs_descr'];
				unset ($theRecord['rs_descr']);
				$theRecord['allowed_categories'] = $theRecord['allowed_types'];
				unset ($theRecord['allowed_types']);
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_partner_val_rel_types',$theRecord);
				$GLOBALS['TYPO3_DB']->debug('ext_update->main');
				$out.= 'Relationship type record '.$theRecord['rt_descr_short'].' (UID '.$theRecord['uid'].') successfully migrated.<br />';
			}

			foreach ($records['tx_partner_relationships'] as $theRelationship)		{
				$update['type'] = $theRelationship['subtype'];
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_partner_relationships','uid='.$theRelationship['uid'],$update);
				$GLOBALS['TYPO3_DB']->debug('ext_update->main');
				$out.= 'Relationship record with UID '.$theRelationship['uid'].' successfully migrated.<br />';
			}

			$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE `tx_partner_relationships` DROP `subtype`');
			$GLOBALS['TYPO3_DB']->debug('ext_update->main');
			$out.= 'Field \'subtype\' successfully removed from table tx_partner_relationships.<br />';
			$GLOBALS['TYPO3_DB']->admin_query('DROP TABLE `tx_partner_val_rel_subtypes`');
			$GLOBALS['TYPO3_DB']->debug('ext_update->main');
			$out.= 'Table tx_partner_val_rel_subtypes succesfully removed.<br />';

			$out.= '<br />Done.';
		}

		return $out;
	}

	/**
	 * Checks if the update function needs to be available at all. It will only be available if there are records
	 * in the old table tx_partner_val_rel_subtypes and no records in the new table tx_partner_val_rel_types.
	 *
	 * @return	boolean
	 */
	function access() {
		$out = 0;

		$records = $this->getRecords();
		if ($records['all_tables']['tx_partner_val_rel_subtypes'] && $records['all_tables']['tx_partner_val_rel_types'])		{
			if (count($records['tx_partner_val_rel_subtypes']) > 0 and count($records['tx_partner_val_rel_types']) == 0) $out = 1;
		}

		return $out;
	}


	/**
	 * Gets all records from tx_partner_val_rel_subtypes and tx_partner_val_rel_types.
	 *
	 * @return	array		All records from tx_partner_val_rel_subtypes and tx_partner_val_rel_types
	 */
	function getRecords()		{
		$out = '';

			// Get all tables in the current DB
		$out['all_tables'] = $GLOBALS['TYPO3_DB']->admin_get_tables();
		if ($out['all_tables'])		{

			if ($out['all_tables']['tx_partner_val_rel_subtypes'])		{
				$resOld = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_val_rel_subtypes', '');
				if ($resOld)		{
					while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resOld))		{
						$out['tx_partner_val_rel_subtypes'][$rec['uid']] = $rec;
					}
				}
			}

			if ($out['all_tables']['tx_partner_val_rel_types'])		{
				$resNew = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_val_rel_types', '');
				if ($resNew)		{
					while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resNew))		{
						$out['tx_partner_val_rel_types'][$rec['uid']] = $rec;
					}
				}
			}

			if ($out['all_tables']['tx_partner_relationships'])		{
				$resRel = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_partner_relationships', '');
				if ($resRel)		{
					while ($rec = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resRel))		{
						$out['tx_partner_relationships'][$rec['uid']] = $rec;
					}
				}
			}
		}

		return $out;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/class.ext_update.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/class.ext_update.php']);
}

?>
