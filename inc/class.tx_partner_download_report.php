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
* Script Class to download files as defined in reports
*
* @author David Bruehlmeier <typo3@bruehlmeier.com>
*/


	// Initialization
define('TYPO3_MOD_PATH', '../typo3conf/ext/partner/inc/');
$BACK_PATH = '../../../../typo3/';
require($BACK_PATH.'init.php');
require($BACK_PATH.'template.php');
require_once(PATH_t3lib.'class.t3lib_iconworks.php');

$GLOBALS['LANG']->includeLLFile ('EXT:partner/locallang_db.xml');


/**
 * Script Class to download files as defined in reports
 *
 */
class tx_partner_download_report {


		// Internal, dynamic:
	var $content;					// Content accumulation for the module.
	var $include_once = array();	// List of files to include.

		// Internal, static: GPvars
	var $reportUid;					// The UID of the report for which the file must be downloaded
	var $format;					// Format in whicht the file must be downloaded
	var $downloadPath;				// Absolute path (including file-name) where to download the file
	
		// Cache settings
	var $cachePath;					// Absolute path of the folder where to download the file (folder is created in ext_localconf.php)
	var $cacheFilePrefix;			// Prefix for downloaded files
	var $cacheFileName;				// Name of the file to download
	var $cacheExpiration;			// Number of seconds after which the cached report files will be deleted when a new report is created



	/**
	 * Initializes the class variables needed to run the script. Takes the GET/POST variables and makes them
	 * available as class variables.
	 *
	 * @return	void
	 */
	function init()		{
		global $LANG;
		
			// Check the GP-Vars and save them as class-vars
		$this->reportUid = intval(t3lib_div::_GP('report'));
		
		$format = t3lib_div::_GP('format');
		if (array_key_exists($format, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['partner']['formats']))	{
			$this->format = $format;
		}
		
		$this->downloadPath = t3lib_div::_GP('downloadPath');
		$this->rerun = t3lib_div::_GP('rerun');
		
			// Cache settings for the report files
		$this->cachePath = PATH_site.'typo3temp/tx_partner/';
		$this->cacheFilePrefix = 'partner_';
		$this->cacheFileName = $this->cacheFilePrefix.date('Y-m-d_H-i-s').'.'.strtolower($this->format);
		$this->cacheExpiration = 86400;
		
			// Make an instance of the backend template
		$this->doc = t3lib_div::makeInstance('smallDoc');
		$this->doc->backPath = $GLOBALS['BACK_PATH'];
		
			// Language handling
		$LANG->includeLLFile('EXT:partner/locallang_db.xml');
	}
	
	
	
	/**
	 * Cleans up old cached report files. $this->cachePath is searched for files starting with $this->cacheFileName.
	 * If a file is found and if it's older than specified by $this->cacheExpiration, it is deleted.
	 *
	 * @return	void
	 */
	function cleanUpFiles()		{
		$d = dir($this->cachePath);
		while (false !== ($entry = $d->read())) {
			if (strstr($entry, $this->cacheFilePrefix))		{
				if (filemtime($this->cachePath.$entry) < mktime()-$this->cacheExpiration)		{
					unlink($this->cachePath.$entry);
				}				
			}
		}
		$d->close();
	}



	/**
	 * This function calls the CLI (Command Line Interface) version of the report-generation script and
	 * executes it in the background. This allows the script to run for more than 30 seconds, which is necessary
	 * if the report holds a lot of data.
	 *
	 * @return	void
	 */
	function createReportInBackground()		{
		global $LANG;
		
			// If this is the first time the script runs, start the creation of the report as a background task
			// to make sure there is no timeout when creating large files. Then call the script again, but
			// this time with the $downloadPath set.
		if (!$this->rerun)		{
			$cliPath = t3lib_extMgm::extPath('partner').'cli/';
			$path = $this->cachePath.$this->cacheFileName;
			
				// Check if there is a BE-user for the CLI-script called "_cli_partner"
			$cliUser = t3lib_BEfunc::getRecordsByField('be_users','username','_cli_partner',t3lib_BEfunc::deleteClause('be_users').t3lib_BEfunc::BEenableFields('be_users'));
			if (is_array($cliUser))		{
			
					// Call the script which will create the file in the background. Different for *nix-systems than for windows
				$this->execInBackground($cliPath.'create_report.php -'.escapeshellarg($this->reportUid).' -'.escapeshellarg($this->format).' -'.escapeshellarg($path));
				
					// Call this script again, this time with rerun and downloadPath set
				$url = t3lib_div::linkThisScript(array('rerun' => 'true', 'downloadPath' => $path, 'format' => $this->format));
				$url = t3lib_div::locationHeaderUrl($url);
				header ('Location: '.$url);
			} else {
				$this->noCliUser = true;
			}
		}
		
			// If we get here, it's because this script was called the second time, after having started the background
			// task which creates the file. The full path of the file is available as $this->downloadPath.
		echo $this->getOutput();
		exit;
	}
	
	/**
	 * This function gets the data from the requested report in the requested format, saves it as a file and
	 * creates a page with a link to download the file. All is done online, so the process will be terminated by
	 * time-out on most servers after 30 seconds.
	 *
	 * @return	void
	 */
	function createReportOnline()		{
		global $LANG;

			// Get the file content in the requested format
		$query = t3lib_div::makeInstance('tx_partner_query');
		$report = $query->getFormattedDataByReport($this->reportUid, $this->format);

			// Get download path
		$this->downloadPath = $this->cachePath.$this->cacheFileName;

			// Write to file
		$file = fopen($this->downloadPath, 'w');
		fwrite($file, $report);
		fclose($file);
		
			// Create the download page
		echo $this->getOutput();
		exit;
	}
	
	

	/**
	 * Calls a php-function in the background. This is very useful for scripts which must be allowed to run for more
	 * than 30 seconds, as this is the case here when creating a report for a large number of data.
	 *
	 * The call of the script is different for *nix systems than for windows. On *nix systems, the function depends
	 * on PHP being installed in /usr/bin/php. On windows systems, the path to php.exe must be set in the system
	 * path-variable. For PHP 4.3, the necessary CLI-version is in the directory /cli/php.exe and starting from 
	 * PHP5 it is the main php.exe in the root directory of PHP. Please refer to the PHP documentation for details.
	 *
	 * @params	string		$args: Arguments which must be passed to the script. Must already be passed through escapeshellarg()!
	 * @return	void		The script is started in the background, no ouput from the function
	 */
	function execInBackground($args='')		{
		if (TYPO3_OS == 'WIN')		{
			@pclose(popen('start /B php.exe '.$args, 'r'));
		} else {
			@exec('php '.$args.' > /dev/null &');
		}
	}
	
	
	
	/**
	 * Creates the HTML for output, which is different as long as the file is still being created.
	 *
	 * @return	string		HTML for output
	 */
	function getOutput()		{
		global $LANG, $TYPO3_CONF_VARS;
		
		$content = '';
		$fileImg = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/'.$TYPO3_CONF_VARS['EXTCONF']['partner']['formats'][strtoupper($this->format)]['icon'], 'width="18" height="16"').' title="'.$this->format.'" border="0" alt="" />';
		$fileInfo = pathinfo($this->downloadPath);
		
			// If $this->noCliUser is set, the download should have happend in the background, but no CLI BE-User was found.
		if ($this->noCliUser)		{
			$content.= $this->doc->startPage($LANG->getLL('tx_partner.label.error'));
			$content.= $this->doc->header($LANG->getLL('tx_partner.label.error_no_cli_user_found'));
			$content.= '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/icon_fatalerror.gif" width="18" height="16"').' title="'.$LANG->getLL('tx_partner.label.error').'" border="0" alt="" />';
			$content.= $LANG->getLL('tx_partner.label.error_no_cli_user_found.explanation');
		} else {
			if (!is_file($this->downloadPath))		{
				
					// The file is still being created, so set meta-refresh to 1 sec and print the proper explanations
				$inProgress = true;
				$this->doc->JScode = '<meta http-equiv="refresh" content="1"; />';
				$content.= $this->doc->startPage($LANG->getLL('tx_partner.label.creating_report'));
				$content.= $this->doc->header($LANG->getLL('tx_partner.label.creating_report'));
				$content.= $fileImg;
				$content.= $LANG->getLL('tx_partner.label.creating_file_wait');
			} else {
				
					// The file is now available for download, so print the link to the file
				$content.= $this->doc->startPage($LANG->getLL('tx_partner.label.report_created'));
				$content.= $this->doc->header($LANG->getLL('tx_partner.label.report_created'));
				$content.= $LANG->getLL('tx_partner.label.file_ready_to_download');
				$content.= $this->doc->spacer(5);
				$fileSize = t3lib_div::formatSize(filesize($this->downloadPath));
				$content.= $fileImg.' <a href="'.$GLOBALS['BACK_PATH'].'../typo3temp/tx_partner/'.$fileInfo['basename'].'" target="_blank">'.$LANG->getLL('tx_partner.label.download').'</a> ('.$fileSize.')';
			}
		}

		$content.= $this->doc->endPage();
		
		return $content;
	}

}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_download_report.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/partner/inc/class.tx_partner_download_report.php']);
}




	// Make instance
$SOBE = t3lib_div::makeInstance('tx_partner_download_report');
$SOBE->init();

	// Clean up old cached files
$SOBE->cleanUpFiles();

	// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

	// Generate in background or online?
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['partner']);
if ($confArr['generateReportsInBackground'])		{
	$SOBE->createReportInBackground();
} else {
	$SOBE->createReportOnline();
}

?>