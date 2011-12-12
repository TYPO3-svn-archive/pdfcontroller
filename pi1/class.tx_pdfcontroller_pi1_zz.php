<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 - Dirk Wildt <http://wildt.at.die-netzmacher.de>
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
* The class tx_pdfcontroller_pi1_zz bundles methods for general purposes.
*
* @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
*
* @since    0.0.1
* @version  0.0.1
*
* @package    TYPO3
* @subpackage    pdfcontroller
*/

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   50: class tx_pdfcontroller_pi1_zz
 *   66:     public function __construct($parentObj)
 *   85:     public function initLang()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_pdfcontroller_pi1_zz
{








    /**
 * Constructor. The method initiate the parent object
 *
 * @param object    The parent object
 * @return  void
 */
  public function __construct($parentObj)
  {
    $this->pObj = $parentObj;
  }









  /**
 * Inits the class 'language'
 *
 * @param string    Fieldname in the _LOCAL_LANG array or the locallang.xml
 * @return  void
 */
  public function initLang()
  {
    require_once(PATH_typo3.'sysext/lang/lang.php');
    $this->pObj->lang = t3lib_div::makeInstance('language');
    $this->pObj->lang->init($GLOBALS['TSFE']->lang);
    if($this->pObj->b_drs_all)
    {
      t3lib_div::devlog('[INFO/ALL] Init a language object.', $this->pObj->extKey, 0);
      t3lib_div::devlog('[INFO/ALL] Value of $GLOBALS[TSFE]->lang :'.$GLOBALS['TSFE']->lang, $this->pObj->extKey, 0);
    }
  }









}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1_zz.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1_zz.php']);
}

?>
