<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 - Dirk Wildt http://wildt.at.die-netzmacher.de
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
* The class tx_pdfcontroller_pi2_typoscript bundles typoscript methods for the extension pdfcontroller
*
* @author    Dirk Wildt http://wildt.at.die-netzmacher.de
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
 *   58: class tx_pdfcontroller_pi2_typoscript
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_pdfcontroller_pi2_typoscript
{

    //////////////////////////////////////////////////////
    //
    // Variables set by the pObj (by class.tx_pdfcontroller_pi2.php)

    // [Array] The current TypoScript configuration array
  var $conf       = false;
    // Variables set by the pObj (by class.tx_pdfcontroller_pi2.php)









/**
 * Constructor. The method initiate the parent object
 *
 * @param object    The parent object
 * @return  void
 */
  function __construct($parentObj)
  {
    // Set the Parent Object
    $this->pObj = $parentObj;

  }









  /***********************************************
  *
  * TypoScript Management
  *
  **********************************************/






  /**
 * oneDim_to_tree():  Build a multidimensional TypoScript configuration array (tree)
 *                    out of a one dimensional array.
 *                    Example:
 *                    - $conf_oneDim['views.single.1.select'] = tt_news.title
 *                      will become
 *                    - $conf['views.']['single.']['1.']['select'] = tt_news.title
 *
 * @param array   $conf_oneDim  : TypoScript configuration array (one dimension)
 * @return  array   $conf         : TypoScript configuration array
 * @since     1.0.0
 * @version   1.0.0
 */
  function oneDim_to_tree($conf_oneDim)
  {
    $conf = array();

    // Values for preg_replace and preg_split
    $str_delimiter    = '|';
    $str_split        = '/' . preg_quote($str_delimiter, '/') . '/';
    $str_dot          = '/\./';
    $str_dot_replace  = '.|';
    // Values for preg_replace and preg_split

    // Loop: Each TypoScript configuration path
    foreach ($conf_oneDim as $key_oneDim => $value_oneDim)
    {
      // Get all items from the current TypoScript path
      // views.single.1.select -> views.|single.|1.|select
      $key_oneDim   = preg_replace($str_dot, $str_dot_replace, $key_oneDim);
      // array( 'views.', 'single.', '1.', 'select')
      $ts_keys      = preg_split($str_split, $key_oneDim, -1, PREG_SPLIT_NO_EMPTY);
      // 'select'
      $last_ts_key  = array_pop($ts_keys);
      // Get all items from the current TypoScript path

      // Build parent structure
      // Might be slow for really deep and large structures
      $parentArr = &$conf;
      // Loop: Each element of the current configuration path
      foreach ($ts_keys as $ts_key)
      {
        if(!isset($parentArr[$ts_key]))
        {
          $parentArr[$ts_key] = array();
        }
        elseif (!is_array($parentArr[$ts_key]))
        {
          $parentArr[$ts_key] = array();
        }
        $parentArr = &$parentArr[$ts_key];
      }
      // Loop: Each element of the current configuration path
      // Build parent structure

      // Add the final part to the structure
      if(empty($parentArr[$last_ts_key]))
      {
        $parentArr[$last_ts_key] = $value_oneDim;
      }
      // Add the final part to the structure

    }
    // Loop: Each TypoScript configuration path

    return $conf;
  }









}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/pi2/class.tx_pdfcontroller_pi2_typoscript.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/pi2/class.tx_pdfcontroller_pi2_typoscript.php']);
}

?>