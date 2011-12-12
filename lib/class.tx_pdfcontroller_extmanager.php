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
* Class provides methods for the extension manager.
*
* @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
* @package    TYPO3
* @subpackage    pdfcontroller
* @version 1.0.0
* @since 1.0.0
*/


  /**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class tx_pdfcontroller_extmanager
 *   67:     function promptCheckUpdate()
 *  102:     function promptCurrIP()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_pdfcontroller_extmanager
{









  /**
 * promptExternalLinks(): Displays the quick start message.
 *
 * @return  string    message wrapped in HTML
 * @since 3.6.1
 * @version 3.6.1
 */
  function promptExternalLinks()
  {
//.message-notice
//.message-information
//.message-ok
//.message-warning
//.message-error

      $str_prompt = null;

      $str_prompt = $str_prompt.'
<div class="message-body">
  ' . $GLOBALS['LANG']->sL('LLL:EXT:pdfcontroller/lib/locallang.xml:promptExternalLinksBody'). '
</div>';

    return $str_prompt;
  }









}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/lib/class.tx_pdfcontroller_extmanager.php'])
{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/lib/class.tx_pdfcontroller_extmanager.php']);
}

?>
