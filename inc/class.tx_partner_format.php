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
* Class for formatting query-data in various formats
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/

require_once(PATH_t3lib.'class.t3lib_befunc.php');
require_once(PATH_t3lib.'class.t3lib_cs.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_lang.php');


if (t3lib_extMgm::isLoaded('fpdf'))	{
	require(t3lib_extMgm::extPath('fpdf').'class.tx_fpdf.php');
}

	// Give the script max. 10 minutes to run (has no effect if PHP is run in safe_mode)
set_time_limit(600);

/**
 * Class for formatting query-data in various formats
 *
 */
class tx_partner_format {

	/**
	 * This function returns data formatted as CSV (Comma Separated Values).
	 *
	 * @param	array		$params: Data to be formatted and parameters for formatting. Passed as reference. Details see below.
	 * @param	array		$params['data']: The data to be formatted
	 * @param	array		$params['structure']: The structure in which the data must be formatted. One structure for 'screen'-display and one for 'file'-output.
	 * @param	array		$params['formatOptions']: The options for this format
	 * @param	string		$params['allowedFormats']: Which formats are currently allowed (comma-separated string)
	 * @param	integer		$params['reportUid']: UID of the report calling this function
	 * @param	object		$ref: Current query object passed as reference.
	 * @return	string		Formatted data
	 */
	function formatAsCSV(&$params, &$ref)		{

			// Get the parameters
		$data = $params['data'];
		$structure = $params['structure'];
		$delimiter = $params['formatOptions']['delimiter'] ? $params['formatOptions']['delimiter'] : ';';
		$wrap = $params['formatOptions']['wrap'] ? $params['formatOptions']['wrap'] : '';
		$lineEnd = $params['formatOptions']['line_end'] ? $params['formatOptions']['line_end'] : '2';
		$includeFieldNames = $params['formatOptions']['include_field_names'] ? $params['formatOptions']['include_field_names'] : '0';
		$includeTableNames = $params['formatOptions']['include_table_names'] ? $params['formatOptions']['include_table_names'] : '0';

			// Init variables
		$content = '';

			// Determine the CSV Line-End
		if ($lineEnd == 0) $lineEnd = '';
		if ($lineEnd == 1) $lineEnd = "\r";
		if ($lineEnd == 2) $lineEnd = "\n";

			// If there was data and a structure, continue...
		if (is_array($data) and is_array($structure['file']))		{

			// Get an array which represents an empty partner record
			$emptyPartnerArray = array_fill(0, count($structure['file']['tx_partner_main']), '');

				// Get the title row with the table/field names
			foreach ($structure['file'] as $theTable=>$fields)		{
				if ($includeTableNames)		{
					$lines['0'][] = tx_partner_lang::getLabel($theTable);
					if ($theTable == 'tx_partner_main') $lines['0'] = $lines['0'] + $emptyPartnerArray;
				}
				if ($includeFieldNames)		{
					foreach ($fields as $k=>$v)		{
						$lines['1'][] = $v['label'];
					}
				}
			}

				// Set the line number for the data rows
			$lineNr = 1;

				// Get the data rows
			foreach ($data as $theRecord)		{

					// Start a new line and reset the contact-info counter
				$lineNr++;
				$ciCounter = 0;

					// Get the fields from the record
				foreach ($theRecord as $theTable=>$fields)		{

						// If it is the partner record (there can only be one), go through the structure and get all the according values
					if ($theTable == 'tx_partner_main')		{
						foreach ($structure['file'][$theTable] as $k=>$v)		{
							$lines[$lineNr][] = $fields[$k]['value'];
						}
					}

						// If it is a contact-info record (there can by many), get the single records first.
					if ($theTable == 'tx_partner_contact_info')		{
						foreach ($fields as $theFields)		{

								// If this is not the first contact-info record that is processed, increase the line-number and start the line with an empty partner
							if ($ciCounter != 0)		{
								$lineNr++;
								$lines[$lineNr] = $emptyPartnerArray;
							}

								// Now go through the structure and get all the according values
							foreach ($structure['file'][$theTable] as $k=>$v)		{
								$lines[$lineNr][] = $theFields[$k]['value'];
							}

								// One contact-info processed: Increase contact-info counter
							$ciCounter++;
						}
					}
				}
			}

				// Get the CSV values
			foreach ($lines as $theLine)		{
				$content.= t3lib_div::csvValues($theLine, $delimiter, $wrap).$lineEnd;
			}
		} else {
				// No Partners or Structure provided...
			$content = tx_partner_lang::getLabel('tx_partner.label.no_records_available');
		}

		return $content;
	}



	/**
	 * This function returns data formatted as PDF (Portable Data Format). The formatted data is returned as a string,
	 * which can for instance be used to download a PDF file. See EXT:partner/inc/class.tx_partner_download_report.php
	 * for an example.
	 *
	 * @param	array		$params: Data to be formatted and parameters for formatting. Passed as reference. Details see below.
	 * @param	array		$params['data']: The data to be formatted
	 * @param	array		$params['structure']: The structure in which the data must be formatted. One structure for 'screen'-display and one for 'file'-output.
	 * @param	array		$params['formatOptions']: The options for this format
	 * @param	string		$params['allowedFormats']: Which formats are currently allowed (comma-separated string)
	 * @param	integer		$params['reportUid']: UID of the report calling this function
	 * @param	object		$ref: Current query object passed as reference.
	 * @return	string		Formatted data
	 */
	function formatAsPDF(&$params, &$ref)		{
		global $TCA;

			// Check if the FPDF library is loaded (extension 'FDPF'). If not, return immediately.
		if (!t3lib_extMgm::isLoaded('fpdf')) return;

			// Get the parameters
		$data =				$params['data'];
		$structure =		$params['structure'];
		$orientation =		$params['formatOptions']['orientation'] ? $params['formatOptions']['orientation'] : 'portrait';
		$unit =				$params['formatOptions']['unit'] ? $params['formatOptions']['unit'] : 'mm';
		$format =			$params['formatOptions']['format'] ? $params['formatOptions']['format'] : 'A4';
		$marginLeft =		$params['formatOptions']['margin_left'] ? $params['formatOptions']['margin_left'] : '10';
		$marginTop =		$params['formatOptions']['margin_top'] ? $params['formatOptions']['margin_top'] : '10';
		$marginRight =		$params['formatOptions']['margin_right'] ? $params['formatOptions']['margin_right'] : '10';
		$font =				$params['formatOptions']['font'] ? $params['formatOptions']['font'] : 'courier';
		$fontSize =			$params['formatOptions']['font_size'] ? $params['formatOptions']['font_size'] : '10';
		$fillColor =		$params['formatOptions']['fill_color'] ? $params['formatOptions']['fill_color'] : '75';
		$picturePosition =	$params['formatOptions']['picture_position'] ? $params['formatOptions']['picture_position'] : 'top_right';
		$pictureWidth =		$params['formatOptions']['picture_width'] ? $params['formatOptions']['picture_width'] : '25';
		$pictureMarginTop =	$params['formatOptions']['picture_margin_top'] ? $params['formatOptions']['picture_margin_top'] : '15';
		$template =			$params['formatOptions']['template'] ? $params['formatOptions']['template'] : '';

			// Get a new instance of the FPDF library
		$pdf = new PDF($orientation, $unit, $format);

			// Get the uploadfolders for the report-template and for partner-images
		t3lib_div::loadTCA('tx_partner_main');
		t3lib_div::loadTCA('tx_partner_reports');
		$ffReports = t3lib_BEfunc::getFlexFormDS($TCA['tx_partner_reports']['columns']['format_options']['config'],'format_options','tx_partner_reports');
		$templateDir = $ffReports['sheets']['PDF']['ROOT']['el']['template']['TCEforms']['config']['uploadfolder'];
		$imgDir = $TCA['tx_partner_main']['columns']['image']['config']['uploadfolder'];

			// Determine the widths and height of cells
		$cellHeight = ($fontSize+6)/$pdf->k;
    	$cellWidth = array(13*$fontSize/$pdf->k, $pageWidth*0.75);
    	$cellWidth[] = $pdf->fw - $marginLeft - $marginRight - $cellWidth['0'];

    		// Get the position of the image
    	if ($picturePosition == 'top_left')		{
    		$imgPosX = $marginLeft;
    		$imgPosY = $pictureMarginTop;
    	}
    	if ($picturePosition == 'top_right')		{
    		$imgPosX = $pdf->fw - $marginRight - $pictureWidth;
    		$imgPosY = $pictureMarginTop;
    	}

    		// Set the template
    	if ($template)		{
    		$pdf->tx_fpdf->template = PATH_site.$templateDir.'/'.$template;
    	}

			// Set the page margins and start a new page
		$pdf->SetMargins($marginLeft, $marginTop, $marginRight);
		$pdf->AddPage();
		$pdf->SetFont($font,'',$fontSize);
		$pdf->SetFillColor($fillColor);
		
			// The data must be in iso-8859-1: Determine the charset from the database
		$fromCharset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : 'iso-8859-1';
		
			// Convert to iso-8859-1
		$cs = t3lib_div::makeInstance('t3lib_cs');
		$cs->convArray(&$data, $fromCharset, 'iso-8859-1');
		
			// If there was data and a file-structure, continue...
		if (is_array($data) and is_array($structure['file']))		{

				// Go through all records
			foreach ($data as $theRecord)		{
				foreach ($theRecord as $theTable=>$fields)		{

					if ($theTable == 'tx_partner_main')		{

							// Label
						$pdf->SetFont($font,'B',$fontSize+2);
						$pdf->Cell('',$cellHeight,$theRecord['tx_partner_main']['label']['value'],0,1,'L',1);
						$pdf->Cell('',$cellHeight, '', '', 1);

							// Name of the table
						$pdf->SetFont($font,'B',$fontSize);
						$pdf->Cell('',$cellHeight,tx_partner_lang::getLabel('tx_partner_main'),'B', 1);

					    	// Get the data according to the file-structure
					    foreach($fields as $k => $v)		{
							$pdf->SetFont($font,'',$fontSize);
					        $pdf->Cell($cellWidth[0],$cellHeight,$v['label'],'B');
					        $pdf->Cell($cellWidth[1],$cellHeight,$v['value'],'B', 1);

					        	// Image
							if ($k == 'image' and $v['value'])		{
								$pdf->Image(PATH_site.$imgDir.'/'.$v['value'], $imgPosX, $imgPosY, $pictureWidth);
							}
					    }
					}

					if ($theTable == 'tx_partner_contact_info' and is_array($theRecord['tx_partner_contact_info']))		{

							// Name of the table
						$pdf->SetFont($font,'B',$fontSize);
						$pdf->Cell('',$cellHeight, '', '', 1);
						$pdf->Cell('',$cellHeight,tx_partner_lang::getLabel('tx_partner_contact_info'),'B', 1);

							// Get the contact-infos
						foreach ($theRecord['tx_partner_contact_info'] as $theContactInfo)		{
							$pdf->SetFont($font,'',$fontSize);
							$pdf->Cell('',$cellHeight, '', '', 1);
							$pdf->Cell('',$cellHeight,$theContactInfo['label']['value'],'B', 1);

							foreach ($theContactInfo as $k => $v)		{
					        	$pdf->Cell($cellWidth[0],$cellHeight,$v['label'],'B');
					        	$pdf->Cell($cellWidth[1],$cellHeight,$v['value'],'B', 1);
							}
						}
					}
				}
				$pdf->AddPage();
			}
		} else {
				// No Partners or Structure provided...
			$pdf->Write(20, tx_partner_lang::getLabel('tx_partner.label.no_records_available'));
		}

			// Convert to PDF
		$content = $pdf->Output('', 'S');
		return $content;
	}

	/**
	 * This function returns data formatted as XLS (Microsoft Excel). The data is returned in the Excel-XML
	 * format, so Excel 2003 or newer is required to view the resulting file.
	 *
	 * @param	array		$params: Data to be formatted and parameters for formatting. Passed as reference. Details see below.
	 * @param	array		$params['data']: The data to be formatted
	 * @param	array		$params['structure']: The structure in which the data must be formatted. One structure for 'screen'-display and one for 'file'-output.
	 * @param	array		$params['formatOptions']: The options for this format
	 * @param	string		$params['allowedFormats']: Which formats are currently allowed (comma-separated string)
	 * @param	integer		$params['reportUid']: UID of the report calling this function
	 * @param	object		$ref: Current query object passed as reference.
	 * @return	string		Formatted data
	 */
	function formatAsXLS(&$params, &$ref)		{
		global $LANG, $BE_USER;
		$LANG->includeLLFile('EXT:partner/locallang.php');
		
			// Define the date fields which need to be converted to UTC (this should probably be in a more central function)
		$dateFields = array('tstamp'=>'unixDateTime', 'crdate'=>'unixDateTime', 'formation_date'=>'unixDate', 'closure_date'=>'unixDate', 'join_date'=>'unixDate', 'leave_date'=>'unixDate', 'birth_date'=>'yyyymmdd', 'death_date'=>'yyyymmdd');

			// Fill misc variables
		$data = $params['data'];
		$structure = $params['structure'];
		$report = t3lib_BEfunc::getRecord('tx_partner_reports',$params['reportUid']);
		$noOfColumns = count($structure['file']['tx_partner_main']) + count($structure['file']['tx_partner_contact_info']);
		$emptyCells = $this->xlsGetEmptyCells($structure);
		$timestamp = $this->convertDateToIso(mktime(), 'unixDateTime');
		
			// Get format options and set defaults if no option was provided
		$fgColorFieldNames = $params['formatOptions']['fg_color_field_names'] ? $params['formatOptions']['fg_color_field_names'] : '#000000';
		$bgColorFieldNames = $params['formatOptions']['bg_color_field_names'] ? $params['formatOptions']['bg_color_field_names'] : '#C0C0C0';
		$includeTechFieldNames = $params['formatOptions']['include_tech_field_names'] ? $params['formatOptions']['include_tech_field_names'] : '0';
		$fgColorTechFieldNames = $params['formatOptions']['fg_color_tech_field_names'] ? $params['formatOptions']['fg_color_tech_field_names'] : '#000000';
		$bgColorTechFieldNames = $params['formatOptions']['bg_color_tech_field_names'] ? $params['formatOptions']['bg_color_tech_field_names'] : '#C0C0C0';
		$includeTableNames = $params['formatOptions']['include_table_names'] ? $params['formatOptions']['include_table_names'] : '0';
		$fgColorTableNames = $params['formatOptions']['fg_color_table_names'] ? $params['formatOptions']['fg_color_table_names'] : '#FFFFFF';
		$bgColorTableNames = $params['formatOptions']['bg_color_table_names'] ? $params['formatOptions']['bg_color_table_names'] : '#333333';
		$dateFormat = 'dd/mm/yyyy';
		if ($params['formatOptions']['date_format'] == 1) $dateFormat = 'mm\-dd\-yyyy';
		
			// Init counters
		$rowCount = 1;
		$contactInfoCounter = 0;

			// Check if data and a file-structure were provided
		if (!is_array($data) or !is_array($structure['file'])) return false;
		
			// *** HEADER ***
		$rows.= $this->xlsRowStart($rowCount);
		$rows.= $this->xlsCell($report['title'], 's23', 'String');
		for ($i = 1; $i < $noOfColumns-1; $i++) {
			$rows.= $this->xlsCell('', 's22');
		}
		$rows.= $this->xlsCell($timestamp, 's22', 'DateTime');
		$rows.= $this->xlsRowEnd();
		$rowCount++;
		
			// *** EMPTY ROWS ***
		$rows.= $this->xlsRowStart($rowCount);
		for ($i = 0; $i < $noOfColumns; $i++) {
			$rows.= $this->xlsCell();
		}
		$rows.= $this->xlsRowEnd();
		$rowCount++;
		
			// *** LABELS ***
		if ($includeTableNames)		{
			$rows.= $this->xlsRowStart($rowCount);
			$rows.= $this->xlsCell($LANG->getLL('tx_partner_main'), 's24', 'String');
			for ($i = 1; $i < count($structure['file']['tx_partner_main']); $i++) {
				$rows.= $this->xlsCell('', 's24');
			}
			if (is_array($structure['file']['tx_partner_contact_info']))		{
				$rows.= $this->xlsCell($LANG->getLL('tx_partner_contact_info'), 's24', 'String');
				for ($i = 1; $i < count($structure['file']['tx_partner_contact_info']); $i++) {
					$rows.= $this->xlsCell('', 's24');
				}
			}
			$rows.= $this->xlsRowEnd();
			$rowCount++;
		}

		if ($includeTechFieldNames)		{
			$rows.= $this->xlsRowStart($rowCount);
			foreach ($structure['file'] as $theTable => $fieldList)		{
				foreach ($fieldList as $k=>$v) {
					$rows.= $this->xlsCell($k, 's25', 'String');
				}
			}
			$rows.= $this->xlsRowEnd();
			$rowCount++;
		}
		$rows.= $this->xlsRowStart($rowCount);
		foreach ($structure['file'] as $theTable => $fieldList)		{
			foreach ($fieldList as $v) {
				$rows.= $this->xlsCell($v['label'], 's21', 'String');
			}
		}
		$rows.= $this->xlsRowEnd();
		$rowCount++;
		
			// *** DATA ***
		foreach ($data as $partnerUid=>$thePartner)		{
			
			$rows.= $this->xlsRowStart($rowCount);

				// Get the partner fields
			foreach ($structure['file']['tx_partner_main'] as $k => $v)		{
				$val = $thePartner['tx_partner_main'][$k]['value'];
				$dataType = 'String';
				
					// Convert date fields to ISO format
				if ($dateFields[$k])		{
					$val = $this->convertDateToIso($thePartner['tx_partner_main'][$k]['rawValue'], $dateFields[$k]);
					$dataType = 'DateTime';
				}
				
					// Fill in the cell
				$rows.= $this->xlsCell($val, 's22', $dataType);
			}
			
				// Get the contact-info fields
			$contactInfoCounter = 0;
			if (is_array($thePartner['tx_partner_contact_info']) && is_array($structure['file']['tx_partner_contact_info']))		{
				foreach ($thePartner['tx_partner_contact_info'] as $theContactInfoField)		{
					$contactInfoCounter++;
					
						// The first row of contact-infos is added to the same row as the partnre
					if ($contactInfoCounter > 1)		{
						$rows.= $this->xlsRowStart($rowCount);
						$rows.= $emptyCells['tx_partner_main'];
					}
					
						// Get all fields from the contact-info
					foreach ($structure['file']['tx_partner_contact_info'] as $k => $v)		{
						$val = $theContactInfoField[$k]['value'];
						$dataType = 'String';
						
							// Convert date fields to ISO format
						if ($dateFields[$k])		{
							$val = $this->convertDateToIso($theContactInfoField[$k]['rawValue'], $dateFields[$k]);
							$dataType = 'DateTime';
						}
						
							// Fill in the cell
						$rows.= $this->xlsCell($val, 's22', $dataType);
					}					
					
						// End the contact-info row
					$rows.= $this->xlsRowEnd();
					$rowCount++;
				}
			}
			
				// If there were no contact-infos, fill the row with the empty contact-info fields and close the row
			if ($contactInfoCounter == 0)		{
				$rows.= $emptyCells['tx_partner_contact_info'];
				$rows.= $this->xlsRowEnd();
				$rowCount++;				
			}
		}
		
		$content = 
'<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>'.$BE_USER->user['realName'].'</Author>
  <Description>'.$LANG->getLL('tx_partner.label.downloaded_from_partner_ext').'</Description>
  <LastAuthor>'.$BE_USER->user['realName'].'</LastAuthor>
  <Created>'.$timestamp.'</Created>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s21">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2"/>
   </Borders>
   <Font ss:Color="'.$fgColorFieldNames.'" ss:Bold="1"/>
   <Interior ss:Color="'.$bgColorFieldNames.'" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s22">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <NumberFormat ss:Format="'.$dateFormat.'"/>
  </Style>
  <Style ss:ID="s23">
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
   <Font ss:Bold="1"/>
  </Style>
  <Style ss:ID="s24">
   <Borders/>
   <Font ss:Color="'.$fgColorTableNames.'" ss:Bold="1"/>
   <Interior ss:Color="'.$bgColorTableNames.'" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="s25">
   <Borders/>
   <Font ss:Color="'.$fgColorTechFieldNames.'" ss:Bold="1"/>
   <Interior ss:Color="'.$bgColorTechFieldNames.'" ss:Pattern="Solid"/>
  </Style>
 </Styles>
<Worksheet ss:Name="'.substr($report['title'],0,30).'">
<Table>'."\n"
.$rows.
'</Table>
</Worksheet>
</Workbook>';

			// The output must be in utf-8: Determine the charset from the database
		$fromCharset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : 'iso-8859-1';
		
			// Convert to utf-8
		$cs = t3lib_div::makeInstance('t3lib_cs');
		$content = $cs->conv($content, $fromCharset, 'utf-8');

		return $content;
	}


	/**
	 * This function returns data formatted as HTML for use in the TYPO3 Backend as a module output.
	 *
	 * For use in Backend only.
	 *
	 * @param	array		$params: Data to be formatted and parameters for formatting. Passed as reference. Details see below.
	 * @param	array		$params['data']: The data to be formatted
	 * @param	array		$params['structure']: The structure in which the data must be formatted. One structure for 'screen'-display and one for 'file'-output.
	 * @param	array		$params['formatOptions']: The options for this format
	 * @param	string		$params['allowedFormats']: Which formats are currently allowed (comma-separated string)
	 * @param	integer		$params['reportUid']: UID of the report calling this function
	 * @param	object		$ref: Current query object passed as reference.
	 * @return	string		Formatted data
	 */
	function formatAsBEModule(&$params, &$ref)		{
		global $LANG, $TCA, $TYPO3_CONF_VARS;
		$LANG->includeLLFile('EXT:partner/locallang.php');

			// Get the parameters
		$data = $params['data'];
		$structure = $params['structure'];
		$allowedFormats = $params['allowedFormats'];
		$reportUid = $params['reportUid'];
		$editReport = $params['formatOptions']['editReport'];
		$editPartner = $params['formatOptions']['editPartner'];
		$mailLink = $params['formatOptions']['mailLink'];

			// Define the styles
		$defaultListStyle = 'height="18px" style="border-bottom-width:1px; border-bottom-color:#C6C2BD; border-bottom-style:solid;" nowrap';
		$defaultHeader1Style = 'bgcolor="#CBC7C3"';

			// Only go on if data and structure was provided
		if (is_array($data) and is_array($structure['screen']))		{

				// Edit report-link
			if ($editReport)		{
				$params = '&edit[tx_partner_reports]['.$reportUid.']=edit';
				$linkTitle = $LANG->getLL('tx_partner.label.edit_report');
				$linkIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/edit2.gif', 'width="11" height="12"').' title="'.$linkTitle.'" border="0" alt="" />';
				$headerRows.= '<tr><td colspan="2"><a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'])).'">'.$linkIcon.$linkTitle.'</a></td></tr>';
			}

				// Download Buttons
			if ($allowedFormats)		{
				$labelDownload = $LANG->getLL('tx_partner.label.download');
				$headerRows.= '<tr><td>'.'<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/rel_file.gif', 'width="13" height="12"').' title="'.$labelDownload.'" border="0" alt="" />'.$labelDownload.'</td>';
				$headerRows.= '<td align="right">'.tx_partner_div::getFormatIcons($reportUid, $allowedFormats, 'horizontal', TRUE, FALSE, TRUE, $GLOBALS['BACK_PATH']).'</td></tr>';
			}

				// Create Header in a table
			$headerRows.= '<tr><td colspan="2">&nbsp;</td></tr>';
			$content.= '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$headerRows.'</table>';

				// Get the fieldCount (max. count of fields for a table)
			$countPartner = count($structure['screen']['tx_partner_main']);
			$countContactInfo = count($structure['screen']['tx_partner_contact_info']);
			$fieldCount = ($countPartner > $countContactInfo) ? $countPartner : $countContactInfo;
			if ($mailLink) $fieldCount++;

				// Get the title row with the field names
			foreach ($structure['screen'] as $theTable => $fieldList)		{

					// Start row
				$i = 0;
				$list.= '<tr '. $defaultHeader1Style.'>';

					// Get the icon
				$list.= '<td>'.t3lib_iconworks::getIconImage($theTable,'',$GLOBALS['BACK_PATH'],'title="'.$LANG->getLL($theTable).'"').'</td>';
				$list.= '<td>&nbsp;</td>';

					// Get the labels
				foreach ($fieldList as $k => $v) {
					$list.= '<td>'.t3lib_div::fixed_lgd($v['label'], $v['length']).'</td>';
					$i++;
				}

					// Fill remaining space to fit the max. count of fields
				if ($i < $fieldCount)		{
					$colspan = $fieldCount - $i;
					$list.= '<td colspan="'.$colspan.'">&nbsp;</td>';
				}

			}

				// Data
			foreach ($data as $partnerUid=>$thePartner)		{

					// Start row
				$i = 0;
				$list.= '<tr>';

					// Get the icon
				$list.= '<td>'.t3lib_iconworks::getIconImage('tx_partner_main','',$GLOBALS['BACK_PATH'],'title="'.$LANG->getLL('tx_partner_main').'"').'</td>';

					// Get the edit-link for the partner
				if ($editPartner)		{
					$list.= '<td>'.tx_partner_div::getEditPartnerLink($partnerUid).'</td>';
				} else {
					$list.= '<td>&nbsp;</td>';
				}

				foreach ($structure['screen']['tx_partner_main'] as $k => $v)		{
					$list.= '<td '.$defaultListStyle.'>'.t3lib_div::fixed_lgd($thePartner['tx_partner_main'][$k]['value'], $v['length']).'&nbsp;</td>';
					$i++;
				}

				if ($mailLink)		{
					$list.= '<td '.$defaultListStyle.'>'.tx_partner_div::getMailIconLink($partnerUid).'&nbsp;</td>';
				}

					// Fill remaining space to fit the max. count of fields
				if ($i < $fieldCount)		{
					$colspan = $fieldCount - $i;
					$list.= '<td '.$defaultListStyle.' colspan="'.$colspan.'">&nbsp;</td>';
				}

				$list.= '</tr>';
				if (is_array($structure['screen']['tx_partner_contact_info']) and is_array($thePartner['tx_partner_contact_info']))		{
					foreach ($thePartner['tx_partner_contact_info'] as $contactInfoUid=>$theContactInfo)		{

							// Get the complete contact-info record
						$ci = t3lib_BEfunc::getRecord('tx_partner_contact_info', $contactInfoUid);

							// Start row
						$list.= '<tr><td>&nbsp;</td>';

							// Get the typeicon of the contact info
						$icon = t3lib_iconworks::getIconImage('tx_partner_contact_info',array('type'=>$ci['type']),$GLOBALS['BACK_PATH'],'title="'.$LANG->getLL('tx_partner_contact_info.type.I.'.$ci['type']).'"');
						$list.= '<td>'.$icon.'</td>';

							// Get the fields for one row
						$i = 0;
						foreach ($structure['screen']['tx_partner_contact_info'] as $k => $v)		{
							$list.= '<td '.$defaultListStyle.'>'.t3lib_div::fixed_lgd($thePartner['tx_partner_contact_info'][$contactInfoUid][$k]['value'], $v['length']).'&nbsp;</td>';
							$i++;
						}

							// Fill remaining space to fit the max. count of fields
						if ($i < $fieldCount)		{
							$colspan = $fieldCount - $i;
							$list.= '<td '.$defaultListStyle.' colspan="'.$colspan.'">&nbsp;</td>';
						}
						$list.= '</tr>';
					}
				}
			}

				// Make table
			$content.= '<table width="100%" border="0" cellpadding="0" cellspacing="0">'.$list.'</table>';
		} else {
				// No Partners or Structure provided...
			$content = $LANG->getLL('tx_partner.label.no_records_available');
		}

		return $content;
	}

	/**
	 * This function returns data formatted as HTML for general use (e.g. in the Frontend).
	 *
	 * @param	array		$params: Data to be formatted and parameters for formatting. Passed as reference. Details see below.
	 * @param	array		$params['data']: The data to be formatted
	 * @param	object		$ref: Current query object passed as reference.
	 * @return	string		Formatted data
	 */
	function formatAsHTML(&$params, &$ref)		{

			// Get the parameters
		$data = $params['data'];
		$structure = $params['structure'];

			// Only go on if data was provided
		if (is_array($data))		{
			foreach ($data as $partnerUid => $theRecord)		{
				foreach ($structure['screen'] as $theTable => $theEntry)		{
					if ($theTable == 'tx_partner_main')		{
						foreach ($theEntry as $k => $v)		{

								// Get the values for 'special' fields (e.g. image)
							$svp = $this->getHTMLValue($theTable, $partnerUid, $k, $theRecord[$theTable][$k]['value']);

								// Assign the values
							$content[$partnerUid][$theTable][$k]['value'] = $svp ? $svp : $theRecord[$theTable][$k]['value'];
							$content[$partnerUid][$theTable][$k]['label'] = $v['label'];
						}
						
							// Get the related partner (as primary)
						if ($theRecord['related_as_primary'])	{
							foreach ($theRecord['related_as_primary'] as $relatedPartnerUid => $theRelatedPartner)	{
								foreach ($theRelatedPartner as $k => $v)	{
									
										// Get the values for 'special' fields (e.g. image)
									$svp = $this->getHTMLValue($theTable, $relatedPartnerUid, $k, $v['value']);
		
										// Assign the values
									$content[$partnerUid]['related_as_primary'][$relatedPartnerUid][$k]['value'] = $svp ? $svp : $v['value'];
									$content[$partnerUid]['related_as_primary'][$relatedPartnerUid][$k]['label'] = $v['label'];
								}								
							}
						}
					} else {
						if (is_array($theRecord['tx_partner_contact_info']))		{
							foreach ($theRecord['tx_partner_contact_info'] as $contactInfoUid => $theContactInfo)		{
								foreach ($theContactInfo as $k => $v)		{

										// Get the values for 'special' fields (e.g. url)
									$svci = $this->getHTMLValue($theTable, $partnerUid, $k, $theRecord[$theTable][$contactInfoUid][$k]['value']);

										// Assign the values
									$content[$partnerUid][$theTable][$contactInfoUid][$k]['value'] = $svci ? $svci : $theRecord[$theTable][$contactInfoUid][$k]['value'];
									$content[$partnerUid][$theTable][$contactInfoUid][$k]['label'] = $v['label'];
								}
							}
						}
					}
				}
			}
		} else {
				// No Partners provided...
			$content = tx_partner_lang::getLabel('tx_partner.label.no_records_available');
		}
		return $content;
	}



	/*********************************************
 	*
 	* INTERNAL HELPER METHODS
 	*
 	*********************************************/
 	
	/**
	 * Returns the start of a new row in an XML-based MS-Excel file.
	 *
	 * @param	integer		$rowId: Id of the row to start
	 * @return	string		XML-Tag for the start of a new row
	 */
	function xlsRowStart($rowId)		{
		return ' <Row ss:Index="'.$rowId.'">'."\n";
	}
	
	
	/**
	 * Returns the end of a new row in an XML-based MS-Excel file.
	 *
	 * @return	string		XML-Tag for the end of a new row
	 */
	function xlsRowEnd()		{
		return " </Row>\n";
	}
	
	
	/**
	 * Returns the XML-tag of a new cell in an XML-based MS-Excel file.
	 *
	 * @param	string		$content: Content to wrap in the tag (optional)
	 * @param	string		$styleId: Id of the style for the cell (optional). Must be available in the <Styles> section of the file.
	 * @param	string		$dataType: Type of the data in the cell (optional). Must be a value known to MS Excel (such as 'String' or 'DateTime').
	 * @return	string		XML-Tag for one cell
	 */
	function xlsCell($content='', $styleId='', $dataType='')		{
		if (!empty($styleId)) $styleId = ' ss:StyleID="'.$styleId.'"';
		if (!empty($dataType)) $dataType = ' ss:Type="'.$dataType.'"';
		
		if (!empty($content))		{
			$cell = '  <Cell'.$styleId.'><Data'.$dataType.'>'.$content.'</Data></Cell>'."\n";
		} else {
			$cell = '  <Cell'.$styleId.'/>'."\n";
		}
		return $cell;
	}
	
	
	/**
	 * Returns empty cell-tags for both a row of a partner or a contact-info. Can be used for spacers.
	 *
	 * @param	array		$structure: Structure for which to build the empty cells
	 * @return	array		Array with XML-tags for an empty partner (tx_partner_main) or an empty contact-info (tx_partner_contact_info)
	 */
	function xlsGetEmptyCells($structure)		{
		foreach ($structure['file'] as $k=>$v)		{
			if ($k == 'tx_partner_main') $styleId = '';
			if ($k == 'tx_partner_contact_info') $styleId = 's22';
			$total = count($v);
			for ($i = 0; $i < $total; $i++) {
				$emptyCells[$k].= $this->xlsCell('', $styleId);
			}
		}
		return $emptyCells;
	}
	
	/**
	 * Converts a timestamp to the ISO-format (YYYY-MM-DDThh:mm:ssZ). The source format can be:
	 * - 'unixDateTime': a UNIX timestamp, will be fully converted
	 * - 'unixDate': a UNIX timestamp, the time part will be set to 00:00:00Z
	 * - 'yyyymmdd': internal format used by the partner-extension for birth- and death date
	 *
	 * @param	integer		$timestamp: Timestamp to convert
	 * @param	string		$sourceFormat: Format of the timestamp
	 * @return	string		ISO timestamp
	 */
	function convertDateToIso($timestamp, $sourceFormat)		{
		$out = '';
		
		if (intval($timestamp) == 0) return false;
		
		switch ($sourceFormat) {
			case 'unixDateTime':
				$out = gmdate("Y-m-d\TH:i:s\Z", $timestamp);
			break;
			case 'unixDate':
				$out = gmdate("Y-m-d\T00:00:00\Z", $timestamp);
			break;
			case 'yyyymmdd':
				$dateArray = tx_partner_div::getDateArray($timestamp);
				if (is_array($dateArray))	{
					$out = sprintf('%04d-%02d-%02d', $dateArray['year'], $dateArray['month'], $dateArray['day']);
					$out.= 'T00:00:00Z';
				}
			break;
		}
		
		return $out;
	}
	

	/**
	 * Returns HTML for an input value. It can handle images, links and lists. The image and link-fields
	 * are explicitely configured in this function. Lists are found by looking at the $TCA-config section.
	 * If the config-type is 'select' and 'maxitems' is greater than one, the values will be rendered as a list.
	 *
	 * @param	string		$table: Table of the field with the value to be processed. May only be 'tx_partner_main' or 'tx_partner_contact_info', else the method will return nothing.
	 * @param	integer		$uid: UID of the record where the value comes from
	 * @param	string		$field: Field name where the value comes from
	 * @param	string		$value: Value to be processed
	 * @return	string		An HTML string ready for output
	 */
	function getHTMLValue($table, $uid, $field, $value)		{
		global $TCA;

			// Define which field is treated by which function
		$procFunc = array(
			'tx_partner_main' => array(
				'image' => 'getHTMLImage',
			),
			'tx_partner_contact_info' => array(
				'email' => 'getHTMLLink',
				'url' => 'getHTMLLink',
			),
		);

			// Check if the current field needs to be treated as a list
		if ($TCA[$table]['columns'][$field]['config']['type'] = 'select' && $TCA[$table]['columns'][$field]['config']['maxitems'] > 1)		{
			$procFunc[$table][$field] = 'getHTMLList';
		}

			// If a function was defined for the current field, send it there and get back the proper value
		if ($procFunc[$table][$field])		{

				// Build the params array
			$params['table'] = $table;
			$params['uid'] = $uid;
			$params['field'] = $field;
			$params['value'] = $value;

				// Call the function
			$out = $this->$procFunc[$table][$field]($params);
		}

		return $out;
	}


	/**
	 * Returns HTML for an image. It is using the cObj IMAGE to do the formatting.
	 * Have a look at tx_partner_format::getHTMLValue to see what's inside the $params array.
	 *
	 * @param	array		$params: Parameters needed for the formatting.
	 * @return	string		An HTML string ready for output
	 * @see tx_partner_format::getHTMLValue
	 */
	function getHTMLImage($params)		{
		global $TCA;

			// Get the label of the record
		$rec = t3lib_BEfunc::getRecord($params['table'], $params['uid'], 'label');

			// TODO: Additional values for the image (e.g. size, altText, etc.)
		$imgConf = array('altText' => $rec['label']);

			// Override the file-property with the current $file
		$imgConf['file'] = $TCA['tx_partner_main']['columns']['image']['config']['uploadfolder'].'/'.$params['value'];

			// Get the image
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$image = $cObj->IMAGE($imgConf);

		return $image;
	}


	/**
	 * Returns HTML for an unstructured list.
	 * Have a look at tx_partner_format::getHTMLValue to see what's inside the $params array.
	 *
	 * @param	array		$params: Parameters needed for the formatting.
	 * @return	string		An HTML string ready for output
	 * @see tx_partner_format::getHTMLValue
	 */
	function getHTMLList($params)		{
		global $TCA;

			// Get TCA configuration
		$tcaConfig = $TCA[$params['table']]['columns'][$params['field']]['config'];

			// Read all entries for this select-field
		$dbAnalysis = t3lib_div::makeInstance('t3lib_loadDBGroup');
		$dbAnalysis->start($key, $tcaConfig['foreign_table'], $tcaConfig['MM'], $params['uid']);
		$dbAnalysis->getFromDB();

			// Make <ul>-list
		if (is_array($dbAnalysis->results)) {

				// Get label field of the current foreign_table
			$labelField = $TCA[$tcaConfig['foreign_table']]['ctrl']['label'];

				// Get the processed values (label-fields) and wrap them in <li>-tags
			foreach ($dbAnalysis->results[$tcaConfig['foreign_table']] as $theListValue) {
				$out .= '<li>'.$theListValue[$labelField].'</li>';
			}

				// Get listWrap from TS
			$ulStart = $this->conf['listWrap']?'<ul '.$this->conf['listWrap'].'>':'<ul>';

				// Wrap list with <ul>-tags
			$out = $ulStart.$out.'</ul>';
		}

		return $out;
	}


	/**
	 * Returns HTML for a link. It is using the cObj function typoLink to do the formatting.
	 * Have a look at tx_partner_format::getHTMLValue to see what's inside the $params array.
	 *
	 * @param	array		$params: Parameters needed for the formatting.
	 * @return	string		An HTML string ready for output
	 * @see tx_partner_format::getHTMLValue
	 */
	function getHTMLLink($params)		{

			// TODO: Get TS-config for the link
		$linkConf = array();

			// Assign the link
		$linkConf['parameter'] = $params['value'];

			// Make a temporary cObj and use the typoLink function
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$out = $cObj->typoLink($link, $linkConf);

		return $out;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_format.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_format.php']);
}

?>