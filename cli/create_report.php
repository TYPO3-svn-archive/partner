#!/usr/bin/env php
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 David Bruehlmeier (typo3@bruehlmeier.com)
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
* Create a file for a report in a specified format.
* For use IN THE BACKGROUND!
*
* IMPORTANT: There must be a backend user called '_cli_partner'
* with sufficient rights in order to use this script. Otherwise,
* it cannot call the TYPO3 Backend in command-line mode.
*
* Have a look at /inc/class.tx_partner_download_report.php for
* an example how to use this script.
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/


// *****************************************
// Standard initialization of a CLI module:
// *****************************************

	// Defining circumstances for CLI mode:
define('TYPO3_cliMode', TRUE);

	// Defining PATH_thisScript here: Must be the ABSOLUTE path of this script in the right context:
	// This will work as long as the script is called by it's absolute path!
	// 
	// Example from "Inside TYPO3" did not work on Windows.
	// define(PATH_thisScript, $_ENV['_'] ? $_ENV['_'] : $_SERVER['_']);
	//
	// This worked on windows, but not on TYPO3 live (KDE)
	// define(PATH_ts, $_ENV['_'] ? $_ENV['_'] : $_SERVER['argv'][0]);
	//
	// This worked fine on windows AND on TYPO3 live (KDE)
define(PATH_ts, $_SERVER['argv'][0]);

	// Include configuration file:
require(dirname(PATH_ts).'/conf.php');

	// Include init and template file:
require(dirname(PATH_ts).'/'.$BACK_PATH.'init.php');
require(dirname(PATH_ts).'/'.$BACK_PATH.'template.php');

	// Get the partner-API
require_once(t3lib_extMgm::extPath('partner').'api/class.tx_partner_div.php');

	// Get and check the parameters
$reportUid = substr($_SERVER['argv'][1], 1);
if (!$reportUid) die ('You must supply the UID of the report as the first argument');
$format = substr($_SERVER['argv'][2], 1);
if (!$format) die ('You must supply the format in which you wish to create the file as the second argument');
$fileName = substr($_SERVER['argv'][3], 1);
if (!$fileName) die ('You must supply the full download path and the file name as the third argument');

	// Create the content in the requested format
$query = t3lib_div::makeInstance('tx_partner_query');
$content = $query->getFormattedDataByReport($reportUid, $format);

	// Write to file
$file = fopen($fileName, 'w');
fwrite($file, $content);
fclose($file);

?>