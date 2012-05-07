<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 - Dirk Wildt <http://wildt.at.die-netzmacher.de>
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
* Class provides user functions for the extension pdfcontroller
*
* @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
* @package    TYPO3
* @subpackage    pdfcontroller
* @version  1.2.2
* @since 1.2.2
*/


  /**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class user_tx_pdfcontroller
 *   67:     function promptCheckUpdate()
 *  102:     function promptCurrIP()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class user_tx_pdfcontroller
{









  /**
 * promptExternalLinks(): Displays the quick start message.
 *
 * @return  string    message wrapped in HTML
 * @since 3.6.1
 * @version 3.6.1
 */
  function addQueryStringUrlEncoded( $content, $conf  )
  {
//.message-notice
//.message-information
//.message-ok
//.message-warning
//.message-error

var_dump( __METHOD__, __LINE__, $content, $conf );

  $content['url'] = 'http://die-netzmacher.de/'
//    return 'http://die-netzmacher.de/';
//    return '<a href="http://die-netzmacher.de/">Die Netzmacher</a>';
    return $content;
  }









}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/lib/class.user_tx_pdfcontroller.php'])
{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/lib/class.user_tx_pdfcontroller.php']);
}

?>
