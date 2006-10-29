<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 David Bruehlmeier (typo3@bruehlmeier.com)
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
* Class holding all functions to create user output
* in TCE Forms for the extension 'partner'
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_main.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_query.php');



class tx_partner_tce_user {

	/**
	 * This function is called by the ['config']['userFunc']
	 * configuration in $TCA and creates the relationships overview.
	 *
	 * @param	array		$PA: The TYPO3 standard array
	 * @param	object		$fobj: An instance of the current TCE Forms Object
	 * @return	string		HTML for the relationships overview
	 */
	function createRelationshipsOverview($PA, $fobj) {
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Get the relationships only for already saved records
		if (substr($PA['row']['uid'], 0, 3) != 'NEW') {

				// Get the relationships as PRIMARY partner
			$relationshipsList['primary'] = $this->getRelationshipsList($PA['row']['uid'], 'primary', $PA['row']['pid']);

				// Get the relationships as SECONDARY partner
			$relationshipsList['secondary'] = $this->getRelationshipsList($PA['row']['uid'], 'secondary', $PA['row']['pid']);

				// HTML-output
			$out = $this->createOutput($PA['row']['uid'],$PA['row']['type'],$PA['row']['pid'],$relationshipsList);

		} else {
			$out = '
				<table width="460px" class="typo3-TCEforms-select-checkbox">
					<tr class="">
						<td class="c-labelCell" colspan="2">'.$LANG->getLL('tx_partner.label.relationships_not_found').'</td>
					</tr>
				</table>
			';
		}

		return $out;
	}


	/**
	 * This function is called by the ['config']['userFunc']
	 * configuration in $TCA and creates the contacts information overview.
	 *
	 * @param	array		$PA: The TYPO3 standard array
	 * @param	object		$fobj: An instance of the current TCE Forms Object
	 * @return	string		HTML for the contact overview
	 */
	function createContactsInformationOverview($PA, $fobj) {
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Get all the contact infos for this partner (ordered by type / standard entry first)
		$contactInfos = t3lib_BEfunc::getRecordsByField(
			'tx_partner_contact_info',
			'uid_foreign',
			$PA['row']['uid'],
			'',
			'',
			'tx_partner_contact_info.type ASC, tx_partner_contact_info.standard DESC'
		);

			// Check if any contact-infos could be selected
		if (is_array($contactInfos))		{

				// Make list for all found contact-infos
			$rows = '';
			foreach ($contactInfos as $theContactInfo)		{

					// Get the icon for the current record
				$altText = 'alt="'.$LANG->getLL('tx_partner_contact_info.type.I.'.$theContactInfo['type']).'"';
				$icon = t3lib_iconWorks::getIconImage('tx_partner_contact_info',$theContactInfo,'',$altText);

					// Parameters for the 'edit'-link
				$editLink = '&edit[tx_partner_contact_info]['.$theContactInfo['uid'].']=edit&tx_partner_uid_foreign='.$PA['row']['uid'];

					// Get the 'edit'-icon
				$editIconTitle = $LANG->getLL('tx_partner.label.edit_contact_info');
				$editIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/edit2.gif', 'width="11" height="12"').' title="'.$editIconTitle.'" border="0" alt="" />';

					// Get the nature (private or business)
				$nature = $LANG->getLL('tx_partner_contact_info.nature.I.'.$theContactInfo['nature']);

					// Transform the data for displaying
				$displayData = $this->transformContactInfo($theContactInfo);

					// Make standard entries bold
				if ($theContactInfo['standard'])		{
					$displayData = '<b>'.$displayData.'</b>';
				}

					// Create the row with the contact-infos and the edit-links
				$rows.= '
					<tr class="">
						<td><a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($editLink, $GLOBALS['BACK_PATH'])).'">'.$editIcon.'</a></td>
						<td>'.$icon.'</td>
						<td class="c-labelCell" >'.$displayData.'</td>
						<td class="c-labelCell" >'.$nature.'</td>
						<td class="c-labelCell" >'.t3lib_div::fixed_lgd($theContactInfo['remarks'],'20').'&nbsp;</td>
					</tr>';
			}

		} else {
				// No contact-info found
		$rows.= '
					<tr class="">
						<td colspan="5">'.
						$LANG->getLL('tx_partner.label.contact_info_not_found').
					'</tr>';
		}


			// Parameters and title for the 'new'-link (only if this is not a newly created partner)
		if (substr($PA['row']['uid'], 0, 3) != 'NEW') {

				// Hack: REQUEST_URI points to a new record, even after first save. Only after second save, it's ok.
				// Make sure it is pointing to EDIT the current record
			$requestUri = t3lib_div::getIndpEnv('REQUEST_URI');
			if (strtoupper(substr($requestUri, -3)) == 'NEW')		{
				$pos = strpos($requestUri, 'edit[tx_partner_main]')+strlen('edit[tx_partner_main]');
				$requestUri = substr_replace($requestUri, '['.$PA['row']['uid'].']=edit', $pos);
			};

			$params = '&edit[tx_partner_contact_info]['.$PA['row']['pid'].']=new&tx_partner_uid_foreign='.$PA['row']['uid'];
			$linkTitle = $LANG->getLL('tx_partner.label.create_new_contact_info');
			$linkIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/new_el.gif', 'width="11" height="12"').'title="'.$linkTitle.'" border="0" alt="" />';

				// Add the 'new'-link
			$rows.= '
					<tr class="">
						<td colspan="5">'.
						'<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'], $requestUri)).'">'.$linkIcon.'<img src="clear.gif" width="5" height="12" border="0" alt="" />'.$LANG->getLL('tx_partner.label.create_new_contact_info').'</a>'.
					'</tr>';
		}


			// Wrap the table with the <table> tags
		$output = '
				<table border="0" cellpadding="0" cellspacing="0" class="typo3-TCEforms-select-checkbox">'
					.$rows.'
				</table>';

			// Return the accumulated HTML
		return $output;
	}

	/**
	 * Internal method which is called to transform a contact-information record
	 * to display in the contact-information overview
	 *
	 * @param	array		$contactInformationRecord: Array with a contact information record as selected from the database
	 * @return	array		Array with transformed data ready for output
	 */
	function transformContactInfo($contactInformationRecord) {
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Transform according to the contact-info type
		switch ($contactInformationRecord['type'])		{
			case 0:		// Phone
			case 1:		// Mobile Phone
			case 2:		// Fax

					// Get the international phone prefix
				if ($contactInformationRecord['country'])		{
					if (t3lib_extMgm::isLoaded('static_info_tables')) {
						$cn_phone = t3lib_BEfunc::getRecord('static_countries',$contactInformationRecord['country'],'cn_phone');
						if (is_array($cn_phone)) {
							$intlPrefix = ' +'.$cn_phone['cn_phone'];
						}
					}
				}

					// Get the area code
				if ($contactInformationRecord['area_code'])		{
					$areaCode = ' ('.$contactInformationRecord['area_code'].')';
				}

					// Get the extension
				if ($contactInformationRecord['extension'])		{
					$extension = ' - '.$contactInformationRecord['extension'];
				}

					// Format the number like this: +[International Prefix] (Area Code) [Phone Number] - [Extension]
				$out = $intlPrefix.$areaCode.' '.$contactInformationRecord['number'].$extension;

			break;
			case 3:		// E-Mail
				if (t3lib_div::validEmail($contactInformationRecord['email']) )		{
						// Valid e-mail address
					$out = '<a href="mailto:'.$contactInformationRecord['email'].'">'.$contactInformationRecord['email'].'</a>';
				} else {
						// Invalid e-mail address
					$warnTitle = $LANG->getLL('tx_partner.label.email_not_valid');
					$warnIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_warning.gif', 'width="18" height="16"').' title="'.$warnTitle.'" border="0" alt="" />';
					$out = $contactInformationRecord['email'].$warnIcon;
				}
			break;
			case 4:		// URL
					// Make a link with the url
				$out = '<a href="http://'.$contactInformationRecord['url'].'" target="_blank">'.$contactInformationRecord['url'].'</a>';
			break;
			default:
					// This shouldn't happen, but just in case... just take the label
				$out = $contactInformationRecord['label'];
			break;
		}

		return $out;
	}


	/**
	 * Internal method which is called to get an array of relationships for the current
	 * partner, either as primary or as secondary partner.
	 *
	 * @param	string		$partnerUID: UID of the partner for which the relationships must be selected
	 * @param	string		$primaryOrSecondary: Defines if the relationships as primary or secondary partner should be selected. Must be either 'primary' or 'secondary'
	 * @param	integer		$pid: Current PID
	 * @return	array		Array with all selected relationships
	 */
	function getRelationshipsList($partnerUID, $primaryOrSecondary, $pid) {
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Get the list of relationships
		$selectField = 'uid_'.$primaryOrSecondary;
		$relationshipsList = t3lib_BEfunc::getRecordsByField('tx_partner_relationships', $selectField, $partnerUID);

		// Get additional data only if relationships could be found
		if (is_array($relationshipsList)) {
			$currentRelationship = 0;
			foreach ($relationshipsList as $theRelationship) {

				// For ALL Partners: UID of the relationship
				$returnList[$currentRelationship]['relationship_uid'] = $theRelationship['uid'];

				// For PRIMARY partners
				if ($primaryOrSecondary == 'primary') {

					// Title of relationship
					$titleArray = t3lib_BEfunc::getRecord('tx_partner_val_rel_types', $theRelationship['type'], 'primary_title', ' AND pid='.$pid);

					// Record found or not available
					if (is_array($titleArray)) {
						$returnList[$currentRelationship]['title'] = $titleArray['primary_title'];
					} else {
						$returnList[$currentRelationship]['title'] = $LANG->getLL('tx_partner.label.not_available.abbreviation').' ('.($theRelationship['type']).')';
					}

					// Label of the related partner
					$labelArray = t3lib_BEfunc::getRecord('tx_partner_main', $theRelationship['uid_secondary'], 'label');
					// Record found or not available
					if (is_array($labelArray)) {
						$returnList[$currentRelationship]['label'] = t3lib_div::fixed_lgd($labelArray['label'],50);
					} else {
						$returnList[$currentRelationship]['label'] = $LANG->getLL('tx_partner.label.not_available.abbreviation').' ('.$theRelationship['uid_secondary'].')';
					}

					// UID of the related partner
					$returnList[$currentRelationship]['related_partner_uid'] = $theRelationship['uid_secondary'];
				}

				// For SECONDARY partners
				if ($primaryOrSecondary == 'secondary') {
					// Title of relationship
					$titleArray = t3lib_BEfunc::getRecord('tx_partner_val_rel_types', $theRelationship['type'], 'secondary_title', ' AND pid='.$pid);
					// Record found or not available
					if (is_array($titleArray)) {
						$returnList[$currentRelationship]['title'] = $titleArray['secondary_title'];
					} else {
						$returnList[$currentRelationship]['title'] = $LANG->getLL('tx_partner.label.not_available.abbreviation').' ('.($theRelationship['type']).')';
					}

					// Label of the related partner
					$labelArray = t3lib_BEfunc::getRecord('tx_partner_main', $theRelationship['uid_primary'], 'label');
					// Record found or not available
					if (is_array($labelArray)) {
						$returnList[$currentRelationship]['label'] = t3lib_div::fixed_lgd($labelArray['label'],50);
					} else {
						$returnList[$currentRelationship]['label'] = $LANG->getLL('tx_partner.label.not_available.abbreviation').' ('.$theRelationship['uid_secondary'].')';
					}

					// UID of the related partner
					$returnList[$currentRelationship]['related_partner_uid'] = $theRelationship['uid_primary'];
				}
				// Increase Array-Counter
				$currentRelationship++;
			}
		} else {
			$returnList['0']['noRelationships'] = true;
		}

			// Sort the array by labels
		usort($returnList,'sortByLabel');

			// Return the result
		return $returnList;
	}

	/**
	 * Internal method called to form the HTML to display relationships.
	 *
	 * @param	string		$partnerUID: UID of the partner
	 * @param	string		$newRecordsPID: PID to which the 'new records' link should be pointing
	 * @param	array		$relationshipsList: Array with relationships, usually created by getRelationshipsList
	 * @param	[type]		$relationshipsList: ...
	 * @return	string		HTML output to display relationships
	 */
	function createOutput($partnerUID, $partnerType, $newRecordsPID, $relationshipsList) {
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			//Relationships to display
		while ($theEntry = current($relationshipsList)) {
				// Make Header
			$rows.= '
				<tr class="">
					<td style="border-bottom: 0px none; padding: 0px; margin: 0px" colspan="2" height="0px"><img src="clear.gif" width="1" height="1" border="0" alt="" /></td>
				</tr>
				<tr bgcolor="#CBC7C3">
					<td style="border-bottom: 0px none; padding-top: 3px; padding-bottom: 3px;" colspan="2"><b>'.$LANG->getLL('tx_partner.label.related_as_'.key($relationshipsList)).'</b></td>
				</tr>
			';

				// List of relationships
			foreach ($theEntry as $theRelationship) {
				if (!$theRelationship['noRelationships']) {
						// Parameters for the edit-link of relationships
					$paramsRelationship = '&edit[tx_partner_relationships]['.$theRelationship['relationship_uid'].']=edit&tx_partner[relPrimSec]='.key($relationshipsList).'&tx_partner[partnerType]='.$partnerType;

						// Edit-link of related partners
					if ($theRelationship['related_partner_uid'] == 0)		{
							// No related partner, no link...
						$relatedPartnerLink = $theRelationship['label'];
					} else {
							// Make edit-link to the related partner
						$paramsRelatedPartner = '&edit[tx_partner_main]['.$theRelationship['related_partner_uid'].']=edit';
						$relatedPartnerLink = '<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($paramsRelatedPartner, $GLOBALS['BACK_PATH'])).'">'.$theRelationship['label'].'</a>';
					}
					$rows.= '
						<tr class="">
							<td class="c-labelCell" nowrap width="160px">'.'<a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($paramsRelationship, $GLOBALS['BACK_PATH'])).'">'.$theRelationship['title'].'</a>'.'</td>
							<td class="c-labelCell" nowrap>'.$relatedPartnerLink.'</td>
						</tr>';
				} else {
					$rows.= '
						<tr class="">
							<td class="c-labelCell" colspan="2">'.$LANG->getLL('tx_partner.label.relationships_not_found').'</td>
						</tr>';
				}
			}

				// Make new relationship link

				// Hack: REQUEST_URI points to a new record, even after first save. Only after second save, it's ok.
				// Make sure it is pointing to EDIT the current record
			$requestUri = t3lib_div::getIndpEnv('REQUEST_URI');
			if (strtoupper(substr($requestUri, -3)) == 'NEW')		{
				$pos = strpos($requestUri, 'edit[tx_partner_main]')+strlen('edit[tx_partner_main]');
				$requestUri = substr_replace($requestUri, '['.$PA['row']['uid'].']=edit', $pos);
			};

			$params = '&edit[tx_partner_relationships]['.$newRecordsPID.']=new&defVals[tx_partner_relationships][uid_'.key($relationshipsList).']='.$partnerUID.'&tx_partner[relPrimSec]='.key($relationshipsList).'&tx_partner[partnerType]='.$partnerType;
			$linkTitle = $LANG->getLL('tx_partner.label.new_relationships_as_'.key($relationshipsList));
			$linkIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/new_el.gif', 'width="11" height="12"').' title="'.$linkTitle.'" border="0" alt="" />';

			$rows.= '
				<tr class="">
					<td class="c-labelCell" colspan="2"><a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'], $requestUri)).'">'.$linkIcon.'<img src="clear.gif" width="5" height="12" border="0" alt="" />'.$linkTitle.'</a></td>
				</tr>
			';

				// Get next entry
			next ($relationshipsList);
		}

			// Make table
		$output = '
			<table width="460px" class="typo3-TCEforms-select-checkbox">
		'.$rows.'
			</table>
		';

		return $output;
	}

	/**
	 * This function is called by the ['config']['userFunc']
	 * configuration in $TCA and creates the overview and edit field
	 * of the field visibilities
	 *
	 * @param	array		$PA: The TYPO3 standard array
	 * @param	object		$fobj: An instance of the current TCE Forms Object
	 * @return	string		HTML for the field visibilities
	 */
	function fieldVisibility($PA, $fobj) {
		global $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Get the merged field visibilities
		$mergedFieldVisibilities = tx_partner_div::getMergedFieldVisibilities($PA['row']['uid']);

			// If there were values, create the list with all field visibilities
		if (is_array($mergedFieldVisibilities))		{
			foreach ($mergedFieldVisibilities as $fieldName=>$fieldValue)		{

					// Assemble the rows-array
				$rows[$fieldName]['tableName'] = $fieldValue['table'];
				$rows[$fieldName]['tableIcon'] = t3lib_iconWorks::getIconImage($fieldValue['table'],'','','title="'.$LANG->getLL($fieldValue['table'].'').'"');
				$rows[$fieldName]['fieldName'] = $fieldValue['field'];
				$rows[$fieldName]['fieldLabel'] = $LANG->getLL($fieldValue['table'].'.'.$fieldValue['field']);
				$rows[$fieldName]['checked'] = $fieldValue['value'];

					// Get the icons for the default or the user-defined values
				if ($fieldValue['default'])		{
					$title = $LANG->getLL('tx_partner.label.field_visibility.default');
					$rows[$fieldName]['default'] = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_ok2.gif', 'width="18" height="16"').' title="'.$title.'" border="0" alt="" />';
					$rows[$fieldName]['value'] = 'DEFAULT.';
				} else {
					$title = $LANG->getLL('tx_partner.label.field_visibility.user_defined');
					$rows[$fieldName]['default'] = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/i/fe_users.gif', 'width="18" height="16"').' title="'.$title.'" border="0" alt="" />';
					$rows[$fieldName]['value'] = '';
				}

					// Show a CSH-icon in case an error was detected
				if ($fieldValue['error'])		{
					$rows[$fieldName]['default'] = t3lib_BEfunc::helpTextIcon('tx_partner_main','error_'.$fieldValue['error'],'',1);
				}
			}

				// Headers
			$out = '
					<tr class="">
						<td class="c-labelCell" >&nbsp;</td>
						<td class="c-labelCell" >&nbsp;</td>
						<td class="c-labelCell" align="center">'.t3lib_div::fixed_lgd($LANG->getLL('tx_partner.label.field_visibility.reset'),'14').'</td>
						<td class="c-labelCell" align="center">'.t3lib_div::fixed_lgd($LANG->getLL('tx_partner.label.field_visibility.private'),'14').'</td>
						<td class="c-labelCell" align="center">'.t3lib_div::fixed_lgd($LANG->getLL('tx_partner.label.field_visibility.restricted'),'14').'</td>
						<td class="c-labelCell" align="center">'.t3lib_div::fixed_lgd($LANG->getLL('tx_partner.label.field_visibility.public'),'14').'</td>
					</tr>
			';

				// Compile Rows
			foreach ($rows as $theRow)		{
				unset ($checked);
				$checked[$theRow['checked']] = ' checked';

				if ($theRow['value'] == 'DEFAULT.')		{
					$defaultRow = '-';
				} else {
					$defaultRow = '<input type="radio" name="data[tx_partner_main]['.$PA['row']['uid'].'][field_visibility]['.$theRow['tableName'].'-'.$theRow['fieldName'].']" value="'.$theRow['value'].'reset">';
				}

				$out.='
					<tr class="">
						<td width="250" class="c-labelCell">'.$theRow['tableIcon'].$theRow['fieldLabel'].'</td>
						<td class="c-labelCell">'.$theRow['default'].'</td>
 						<td class="c-labelCell" align="center">'.$defaultRow.'</td>
 						<td class="c-labelCell" align="center"><input type="radio" name="data[tx_partner_main]['.$PA['row']['uid'].'][field_visibility]['.$theRow['tableName'].'-'.$theRow['fieldName'].']" value="'.$theRow['value'].'private"'.$checked['PRIVATE'].'></td>
 						<td class="c-labelCell" align="center"><input type="radio" name="data[tx_partner_main]['.$PA['row']['uid'].'][field_visibility]['.$theRow['tableName'].'-'.$theRow['fieldName'].']" value="'.$theRow['value'].'restricted"'.$checked['RESTRICTED'].'></td>
 						<td class="c-labelCell" align="center"><input type="radio" name="data[tx_partner_main]['.$PA['row']['uid'].'][field_visibility]['.$theRow['tableName'].'-'.$theRow['fieldName'].']" value="'.$theRow['value'].'public"'.$checked['PUBLIC'].'></td>
					</tr>
				';
			}

				// Make Table
			$out = '
				<table width="460px" class="typo3-TCEforms-select-checkbox">
			'.$out.'
				</table>
			';

		} else {
				// No default values set in TSconfig
			$helpTextIcon = t3lib_BEfunc::helpTextIcon('tx_partner_main','field_visibility_no_values','',1);
			$out = '</br>'.$LANG->getLL('tx_partner.label.field_visibility.no_values').'&nbsp;'.$helpTextIcon;
		}

		return $out;
	}


	/**
	 * This function is called by the ['config']['userFunc']
	 * configuration in $TCA and creates the preview of a report.
	 *
	 * @param	array		$PA: The TYPO3 standard array
	 * @param	object		$fobj: An instance of the current TCE Forms Object
	 * @return	string		HTML for the relationships overview
	 */
	function getReportPreview($PA, $fobj) {
		global $TYPO3_CONF_VARS, $LANG;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Make a new query instance for getting the partner data
		$query = t3lib_div::makeInstance('tx_partner_query');

			// Download Buttons
		$buttons = tx_partner_div::getFormatIcons($PA['row']['uid'], '', 'vertical');

			// Get the CSV preview
		$formattedData['CSV'] = $query->getFormattedDataByReport($PA['row']['uid'], 'CSV', 3);

			// Build the HTML with the preview textareas
		$content = '
			<table>
				<tr>
					<td>'.$buttons.'</td>
				</tr>
				<tr>
					<td>'.$LANG->getLL('tx_partner_reports.preview_csv_first_three_rows').'</td>
				</tr>
				<tr>
					<td><textarea rows="5" wrap="off" '.$GLOBALS['TBE_TEMPLATE']->formWidthText(46,"","off").'>'.$formattedData['CSV'].'</textarea></td>
				</tr>
			</table>
		';

		return $content;
	}


	/**
	 * This function is called by the ['config']['userFunc']
	 * configuration in $TCA and creates the field selection of a report.
	 *
	 * @param	array		$PA: The TYPO3 standard array
	 * @param	object		$fobj: An instance of the current TCE Forms Object
	 * @return	string		HTML for the relationships overview
	 */
	function getReportFieldSelection($PA, $fobj) {
		global $LANG, $TCA;
		$LANG->includeLLFile('EXT:partner/locallang.php');

		$fieldSelection = t3lib_div::xml2array($PA['row']['field_selection']);
		$tables = array('tx_partner_main', 'tx_partner_contact_info');

		foreach ($tables as $theTable)		{
			$fieldNames = array();
			$rows = array();

				// Get all field names
			t3lib_div::loadTCA($theTable);
			foreach ($TCA[$theTable]['columns'] as $fieldName=>$conf)		{
				$fieldNames[$fieldName] = $LANG->sL($conf['label']);
			}

				// TODO: Sort the field names according to their language labels, depending on the user's option
			//asort ($fieldNames);

			$icon = t3lib_iconWorks::getIconImage($theTable,'','');
			$content.= '<tr class=""><td colspan="4" valign="bottom" height="25"><strong>'.$icon.'&nbsp;'.$LANG->getLL($theTable).'</strong></td></tr>';

				// Title Row
			$rows['_titlerow']['fieldName'] = '<strong>'.$LANG->getLL('tx_partner.label.field_label').' ('.$LANG->getLL('tx_partner.label.field_tech_name').')</strong>';
			$rows['_titlerow']['screen'] = '<strong>'.$LANG->getLL('tx_partner.label.screen').'</strong>';
			$rows['_titlerow']['length'] = '<strong>'.$LANG->getLL('tx_partner.label.length').'</strong>';
			$rows['_titlerow']['file'] = '<strong>'.$LANG->getLL('tx_partner.label.file').'</strong>';

				// Fields
			foreach ($fieldNames as $field=>$fieldName)		{

					// Get the values from the field-selection array (explicit is_array check required for PHP5)
				if (is_array($fieldSelection))		{
					$fsLength = $fieldSelection[$theTable][$field]['length'];
					$fsScreen = $fieldSelection[$theTable][$field]['screen'];
					$fsFile = $fieldSelection[$theTable][$field]['file'];
				}

					// Get the current values for the field
				$length = $fsLength ? $fsLength : $TCA[$theTable]['columns'][$field]['config']['size'];
				$checkedScreen = $fsScreen ? ' checked="checked"' : '';
				$checkedFile = $fsFile ? ' checked="checked"' : '';

					// Get the field's HTML
				$rows[$field]['fieldName'] = $fieldName.' ('.$field.')';
				$rows[$field]['screen'] = '<input type="checkbox" name="data[tx_partner_reports]['.$PA['row']['uid'].'][field_selection]['.$theTable.']['.$field.'][screen]" value="1"'.$checkedScreen.'>';
				$rows[$field]['length'] = '<input type="text" name="data[tx_partner_reports]['.$PA['row']['uid'].'][field_selection]['.$theTable.']['.$field.'][length]" size="3" maxlength="3" '.$GLOBALS['TBE_TEMPLATE']->formWidth(3).' value="'.$length.'">';
				$rows[$field]['file'] = '<input type="checkbox" name="data[tx_partner_reports]['.$PA['row']['uid'].'][field_selection]['.$theTable.']['.$field.'][file]" value="1"'.$checkedFile.'>';
			}

				// Compile the rows
			foreach ($rows as $theRow)		{
				$content.='
						<tr class="">
							<td class="c-labelCell">'.$theRow['fieldName'].'</td>
							<td class="c-labelCell" width="30">'.$theRow['screen'].'</td>
							<td class="c-labelCell" width="30">'.$theRow['length'].'</td>
							<td class="c-labelCell" width="30">'.$theRow['file'].'</td>
						</tr>
				';
			}

		}

		$content = '
			<table width="460px" class="typo3-TCEforms-select-checkbox">
		'.$content.'
			</table>
		';

		return $content;
	}
}

/**
* Static comparison functions
* (cannot be a method)
*/

/**
 * Sorting by Labels
 *
 * @param	string		$a: First string to compare
 * @param	string		$b: Second string to compare
 * @return	string		Result of comparison
 */
function sortByLabel($a, $b) {
		// Compare labels
	return strcmp(strtolower($a['label']), strtolower($b['label']));
}

	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_tce_user.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_tce_user.php']);
	}

?>