<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2012 - Dirk Wildt <http://wildt.at.die-netzmacher.de>
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



//////////////////////////////////////////////////////////////////////
//
// TYPO3 Downwards Compatibility

if (!defined('PATH_typo3'))
{
  //var_dump(get_defined_constants());
  //echo 'Not defined: PATH_typo3.<br />tx_pdfcontroller_pi2 defines it now.<br />';
  if (!defined('PATH_site'))
  {
    echo '<div style="border:1em solid red;padding:1em;color:red;font-weight:bold;font-size:2em;background:white;line-height:2.4em;text-align:center;">Error<br />
      The constant PATH_typo3 isn\'t defined.<br />
      tx_pdfcontroller_pi2 tries to get now PATH_site, but it isn\'t defined neither!<br />
      <br />
      Please check your TYPO3 installation.</div>';
  }
  if (!defined('TYPO3_mainDir'))
  {
    echo '<div style="border:1em solid red;padding:1em;color:red;font-weight:bold;font-size:2em;background:white;line-height:2.4em;text-align:center;">Error<br />
      The constant PATH_typo3 isn\'t defined.<br />
      tx_pdfcontroller_pi2 tries to get now TYPO3_mainDir, but it isn\'t defined neither!<br />
      <br />
      Please check your TYPO3 installation.</div>';
  }
  define('Path_typo3', PATH_site.TYPO3_mainDir);
}
// TYPO3 Downwards Compatibility



require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Plugin 'Calculator for grants' for the 'pdfcontroller' extension.
 *
 * @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @package    TYPO3
 * @subpackage    pdfcontroller
 *
 * @version 1.1.1
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   89: class tx_pdfcontroller_pi2 extends tslib_pibase
 *
 *              SECTION: Main Process
 *  156:     public function main($content, $conf)
 *
 *              SECTION: DRS - Development Reporting System
 *  301:     private function init_drs()
 *
 *              SECTION: Classes
 *  395:     private function require_classes()
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_pdfcontroller_pi2 extends tslib_pibase {

  var $prefixId = 'tx_pdfcontroller_pi2';
    // Same as class name
  var $scriptRelPath = 'pi2/class.tx_pdfcontroller_pi2.php';
    // Path to this script relative to the extension dir.
  var $extKey = 'pdfcontroller';
    // The extension key.
  var $pi_checkCHash = true;
  
    // [integer] The current typeNum given by the current URL
  var $param_typeNum  = 0;
    // [string] current page object: page | print | pdf
  var $str_pageObject = 'page';
  



  var $str_developer_name     = 'Dirk Wildt';
  var $str_developer_mail     = 'http://wildt.at.die-netzmacher.de';
  var $str_developer_phone    = '+49 361 21655226';
  var $str_developer_company  = 'Die Netzmacher';
  var $str_developer_web      = 'http://die-netzmacher.de';
  var $str_developer_typo3ext = '';
  var $str_developer_lang     = 'german, english';
    // [Boolean] Set by init_drs()
  var $developer_contact      = false;



  var $arr_extConf            = array();
    // Array out of the extConf file



    // Booleans for DRS - Development Reporting System
  var $b_drs_all          = false;
  var $b_drs_error        = false;
  var $b_drs_warn         = false;
  var $b_drs_info         = false;
  var $b_drs_flexform     = false;
  var $b_drs_marker       = false;
  var $b_drs_perform      = false;
  var $b_drs_security     = false;
  var $b_drs_typolink     = false;
  var $b_drs_typoscript   = false;
    // Booleans for DRS - Development Reporting System











  /***********************************************
   *
   * Main Process
   *
   **********************************************/




  /**
 * Main method of your PlugIn
 *
 * @param string    $content: The content of the PlugIn
 * @param array   $conf: The PlugIn Configuration
 * @return  string    The content that should be displayed on the website
 * @version 1.2.2
 * @since 0.0.1
 */
  public function main( $content, $conf )
  {
    $this->conf = $conf;
    $this->pi_loadLL();



      ////////////////////////////////////////////////////////////////////
      //
      // Timetracking

    require_once(PATH_t3lib.'class.t3lib_timetrack.php');
    $this->TT        = new t3lib_timeTrack;
    $this->TT->start();
    $this->startTime = $this->TT->getDifferenceToStarttime();
      // Timetracking



      //////////////////////////////////////////////////////////////////////
      //
      // Get the values from the localconf.php file

    $this->arr_extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
      // Get the values from the localconf.php file



      //////////////////////////////////////////////////////////////////////
      //
      // Init DRS - Development Reporting System

    $this->init_drs();
    if ($this->b_drs_perform)
    {
      t3lib_div::devlog('[INFO/PERFORMANCE] START', $this->extKey, 0);
    }
      // Init DRS - Development Reporting System



      //////////////////////////////////////////////////////////////////////
      //
      // Require and init helper classes

    $this->require_classes();
      // Require and init helper classes



      //////////////////////////////////////////////////////////////////////
      //
      // Check TypoScript configuration

      // RETURN TypoScript template isn't included, button is missing
    if (empty($this->conf['button']))
    {
      if ($this->b_drs_error)
      {
        t3lib_div::devLog('[ERROR] TypoScript template isn\'t included', $this->extKey, 3);
        t3lib_div::devLog('[INFO] ABORTED', $this->extKey, 0);
      }
      $str_header  = '<h1 style="color:red;">'.$this->pi_getLL('error_typoscripttemplate_h1').'</h1>';
      $str_prompt  = '<p style="color:red;font-weight:bold;">'.$this->pi_getLL('error_typoscripttemplate_p').'</p>';
      return $this->pi_wrapInBaseClass($str_header . $str_prompt);
    }
      // RETURN TypoScript template isn't included, button is missing
      // Check TypoScript configuration



      //////////////////////////////////////////////////////////////////////
      //
      // Init flexform

      // Init methods for pi_flexform
    $this->pi_initPIflexForm();
    $this->pi_flexform = $this->cObj->data['pi_flexform'];
    
      // RETURN flexform is empty
    if (!is_array($this->pi_flexform))
    {
      if ($this->b_drs_error)
      {
        t3lib_div::devLog('[ERROR] Flexform is empty.', $this->extKey, 3);
        t3lib_div::devLog('[INFO] ABORTED', $this->extKey, 0);
      }
      $str_header  = '<h1 style="color:red;">'.$this->pi_getLL('error_flexform_h1').'</h1>';
      $str_prompt  = '<p style="color:red;font-weight:bold;">'.$this->pi_getLL('error_flexform_p').'</p>';
      return $this->pi_wrapInBaseClass($str_header . $str_prompt);
    }
      // RETURN flexform is empty
      // Init flexform



      //////////////////////////////////////////////////////////////////////
      //
      // Get flexform values

      // Uid of the page with the PDF Controller
    $pid_converter  = $this->pi_flexform['data']['sDEF']['lDEF']['pid_converter']['vDEF'];

    $handleimage    = $this->pi_flexform['data']['sDEF']['lDEF']['handleimage']['vDEF'];
    switch( $handleimage )
    {
      case( 1 ):
        $imagefile = $this->pi_flexform['data']['sDEF']['lDEF']['imagepath']['vDEF'];
        $imagefile = 'uploads/tx_pdfcontroller/' . $imagefile;
        break;
      case( 0 ):
      default:
          // Take path for imagefile from TypoScript 
        $imagefile = $this->cObj->cObjGetSingle
        (
          $this->conf['flexform.']['sDEF.']['imagefile'],
          $this->conf['flexform.']['sDEF.']['imagefile.']
        );
    }

    $wrap_in_pibase = $this->pi_flexform['data']['sDEF']['lDEF']['wrap_in_pibase']['vDEF'];
      // Get flexform values



      ////////////////////////////////////////////////////////////////////////
      //
      // Generate additional paramater

    $TYPO3_REQUEST_URL  = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
    $paramsEncode       = rawurlencode ( $TYPO3_REQUEST_URL );
    $additionalParams   = '&tx_pdfcontroller_pi1[URL]=' . $paramsEncode;
      // Generate additional paramater



      ////////////////////////////////////////////////////////////////////////
      //
      // DRS

    if ( $this->b_drs_typolink )
    {
      $prompt = 'Be aware of the configuration of additionalParams: If you aren\'t ' .
                'using RealURL please use %26 in place of an &. If you are using RealURL ' .
                'please use something like print.html in place of something like &type=98. ' .
                'If you have any qeustion, please refer to the manual! ';
      t3lib_div::devLog( '[WARN/TYPOLINK] '. $prompt, $this->extKey, 2 );
      $prompt = 'Marker ###ADDITIONALPARAMS### will replaced with ' . $additionalParams;
      t3lib_div::devLog( '[INFO/TYPOLINK] '. $prompt, $this->extKey, 0 );
      $prompt = '###ADDITIONALPARAMS### is decoded: ' . rawurlencode( $additionalParams );
      t3lib_div::devLog( '[INFO/TYPOLINK] '. $prompt, $this->extKey, 0 );
    }
      // DRS

    
    
      ////////////////////////////////////////////////////////////////////////
      //
      // Replace markers in the TypoScript configuration

      // Get TypoScript configuration as one dimensional array
      // 100921, dwildt, Bugfix in t3lib_BEfunc::implodeTSParams
      // See http://bugs.typo3.org/view.php?id=15757 implodeTSParams(): numeric keys will be renumbered
    $conf_oneDim      = t3lib_BEfunc::implodeTSParams( $this->conf );
      // Get TypoScript configuration as one dimensional array

      // Replacement
    $marker       = array('###IMAGEFILE###',  '###PARAMETER###',  '###ADDITIONALPARAMS###');
    $values       = array($imagefile,         $pid_converter,     $additionalParams);
    $conf_oneDim  = str_replace( $marker, $values, $conf_oneDim );
      // Replacement



      ////////////////////////////////////////////////////////////////////////
      //
      // DRS

    if ( $this->b_drs_marker || $this->b_drs_typoscript )
    {
      foreach( $marker as $markerKey => $markerValue )
      {
        $prompt = $markerValue . ' becomes ' . $values[$markerKey];
        t3lib_div::devLog( '[INFO/MARKER+TYPOSCRIPT] '. $prompt, $this->extKey, 0 );
      }
    }
      // DRS
    
    
    
      // Reset the TypoScript
    $this->conf   = $this->objTyposcript->oneDim_to_tree($conf_oneDim);
      // Replace markers in the TypoScript configuration

    


      ////////////////////////////////////////////////////////////////////////
      //
      // Wrap the Pdf Controller button

    $conf_button = $this->cObj->cObjGetSingle( $this->conf['button'], $this->conf['button.'] );
      // Wrap the Pdf Controller button



      ////////////////////////////////////////////////////////////////////////
      //
      // DRS - Performance

    if ($this->b_drs_perform)
    {
      $endTime = $this->TT->getDifferenceToStarttime();
      t3lib_div::devLog('[INFO/PERFORMANCE] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
    }
      // DRS - Performance



      ////////////////////////////////////////////////////////////////////////
      //
      // Return the button

    $this->content = $conf_button;
    switch($wrap_in_pibase)
    {
      case(0):
        return $this->content;
        break;
      case(1):
      default:
        return $this->pi_wrapInBaseClass($this->content);
    }
      // Return the button
  }









  /***********************************************
   *
   * DRS - Development Reporting System
   *
   **********************************************/



  /**
 * Set the booleans for Warnings, Errors and DRS - Development Reporting System
 *
 * @return  void
 * @version 1.2.2
 * @since 0.0.2
 */
  private function init_drs()
  {

      //////////////////////////////////////////////////////////////////////
      //
      // Prepaire the developer contact prompt

    $this->developer_contact =
      'company: ' . $this->str_developer_company . '<br />'.
      'name: '    . $this->str_developer_name    . '<br />'.
      'mail: <a href="mailto:' . $this->str_developer_mail . '" title="Send a mail">' . $this->str_developer_mail . '</a><br />' .
      'web: <a href="' . $this->str_developer_web . '" title="Website" target="_blank">' . $this->str_developer_web.'</a><br />' .
      'phone: '     . $this->str_developer_phone . '<br />' .
      'languages: ' . $this->str_developer_lang  . '<br /><br />' .
      'TYPO3 Repository:<br /><a href="' . $this->str_developer_typo3ext . '" title="' . $this->extKey . ' online" target="_blank">' . $this->str_developer_typo3ext . '</a>';
      // Prepaire the developer contact prompt



      //////////////////////////////////////////////////////////////////////
      //
      // Set the DRS mode

    if ($this->arr_extConf['drs_mode'] == 'All')
    {
      $this->b_drs_all        = true;
      $this->b_drs_error      = true;
      $this->b_drs_warn       = true;
      $this->b_drs_info       = true;
      $this->b_drs_flexform   = true;
      $this->b_drs_marker     = true;
      $this->b_drs_perform    = true;
      $this->b_drs_typolink   = true;
      $this->b_drs_security   = true;
      $this->b_drs_typoscript = true;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$this->arr_extConf['drs_mode'], $this->extKey, 0);
    }
    if ($this->arr_extConf['drs_mode'] == 'Errors and Warnings')
    {
      $this->b_drs_error      = true;
      $this->b_drs_warn       = true;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$this->arr_extConf['drs_mode'], $this->extKey, 0);
    }
    if ($this->arr_extConf['drs_mode'] == 'Flexform')
    {
      $this->b_drs_error      = true;
      $this->b_drs_warn       = true;
      $this->b_drs_info       = true;
      $this->b_drs_flexform   = true;
      $this->b_drs_perform    = true;
      $this->b_drs_typoscript = true;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$this->arr_extConf['drs_mode'], $this->extKey, 0);
    }
    if ($this->arr_extConf['drs_mode'] == 'Marker')
    {
      $this->b_drs_error      = true;
      $this->b_drs_warn       = true;
      $this->b_drs_info       = true;
      $this->b_drs_marker     = true;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$this->arr_extConf['drs_mode'], $this->extKey, 0);
    }
    if ($this->arr_extConf['drs_mode'] == 'Performance')
    {
      $this->b_drs_perform    = true;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$this->arr_extConf['drs_mode'], $this->extKey, 0);
    }
    if ($this->arr_extConf['drs_mode'] == 'TypoLink')
    {
      $this->b_drs_error      = true;
      $this->b_drs_warn       = true;
      $this->b_drs_info       = true;
      $this->b_drs_marker     = true;
      $this->b_drs_typolink   = true;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$this->arr_extConf['drs_mode'], $this->extKey, 0);
    }
    if ($this->arr_extConf['drs_mode'] == 'Security')
    {
      $this->b_drs_error      = true;
      $this->b_drs_warn       = true;
      $this->b_drs_info       = true;
      $this->b_drs_security   = true;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$this->arr_extConf['drs_mode'], $this->extKey, 0);
    }
      // Set the DRS mode

  }









  /***********************************************
   *
   * Classes
   *
   **********************************************/



  /**
 * Init the helper classes
 *
 * @return  void
 * @version 0.0.2
 * @since 0.0.2
 */
  private function require_classes()
  {
      //////////////////////////////////////////////////////////////////////
      //
      // Require and init helper classes

//    require_once(t3lib_extMgm::extPath('pdfcontroller') . 'lib/class.tx_pdfcontroller_dynmarkers.php');
//      // Class with methods for markers
//    $this->objMarkers = new tx_pdfcontroller_dynmarkers($this);
//
//    require_once('class.tx_pdfcontroller_pi2_flexform.php');
//    // Class with methods for get flexform values
//    $this->objFlexform = new tx_pdfcontroller_pi2_flexform($this);
//
//    require_once('class.tx_pdfcontroller_pi2_javascript.php');
//    // Class with methods for ordering rows
//    $this->objJss = new tx_pdfcontroller_pi2_javascript($this);

    require_once('class.tx_pdfcontroller_pi2_typoscript.php');
    // Class with typoscript methods, which return HTML
    $this->objTyposcript = new tx_pdfcontroller_pi2_typoscript($this);
//
//    require_once('class.tx_pdfcontroller_pi2_zz.php');
//    // Class with zz methods
//    $this->objZz = new tx_pdfcontroller_pi2_zz($this);
//      // Require and init helper classes

  }










}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/pi2/class.tx_pdfcontroller_pi2.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/pi2/class.tx_pdfcontroller_pi2.php']);
}

?>