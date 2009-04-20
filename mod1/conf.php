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
 * Conf-include for the Web>Partner module
 *
 * @author David Bruehlmeier <typo3@bruehlmeier.com>
 */

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/partner/mod1/');
$BACK_PATH='../../../../typo3/';
$MCONF['name']='web_txpartnerM1';


$MCONF['access']='user,group';
$MCONF['script']='index.php';

$MLANG['default']['tabs_images']['tab'] = '../icons/icon_web_txpartnerM1.gif';
$MLANG['default']['ll_ref']='LLL:EXT:partner/locallang_db.xml';
?>