<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Dirk Wildt <http://wildt.at.die-netzmacher.de>
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
  //echo 'Not defined: PATH_typo3.<br />tx_pdfcontroller_pi1 defines it now.<br />';
  if (!defined('PATH_site'))
  {
    echo '<div style="border:1em solid red;padding:1em;color:red;font-weight:bold;font-size:2em;background:white;line-height:2.4em;text-align:center;">Error<br />
      The constant PATH_typo3 isn\'t defined.<br />
      tx_pdfcontroller_pi1 tries to get now PATH_site, but it isn\'t defined neither!<br />
      <br />
      Please check your TYPO3 installation.</div>';
  }
  if (!defined('TYPO3_mainDir'))
  {
    echo '<div style="border:1em solid red;padding:1em;color:red;font-weight:bold;font-size:2em;background:white;line-height:2.4em;text-align:center;">Error<br />
      The constant PATH_typo3 isn\'t defined.<br />
      tx_pdfcontroller_pi1 tries to get now TYPO3_mainDir, but it isn\'t defined neither!<br />
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
 * @version 0.0.2
 */

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   89: class tx_pdfcontroller_pi1 extends tslib_pibase
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
class tx_pdfcontroller_pi1 extends tslib_pibase {

  var $prefixId = 'tx_pdfcontroller_pi1';
    // Same as class name
  var $scriptRelPath = 'pi1/class.tx_pdfcontroller_pi1.php';
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
  var $b_drs_perform      = false;
  var $b_drs_security     = false;
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
 * @version 0.0.1
 * @since 0.0.1
 */
  public function main($content, $conf)
  {
    $this->conf = $conf;

    $this->pi_setPiVarDefaults();
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
      t3lib_div::devlog('[INFO/PERFORMANCE] START '. ($this->startTime).' ms', $this->extKey, 0);
    }
      // Init DRS - Development Reporting System



      //////////////////////////////////////////////////////////////////////
      //
      // Require and init helper classes

    $this->require_classes();
      // Require and init helper classes



      //////////////////////////////////////////////////////////////////////
      //
      // Debugging tools - part 1 of 2

      // 120202, security, dwildt+
      // DEFINE HTML2PS_DIR
    if( ! defined( 'HTML2PS_DIR' ) )
    {
      //define( 'HTML2PS_DIR', $_SERVER['DOCUMENT_ROOT'] . 'typo3conf/ext/pdfcontroller/res/html2ps_v2043/public_html/' );
      define( 'HTML2PS_DIR', t3lib_extMgm::extPath( 'pdfcontroller' ) . 'res/html2ps_v2043/public_html/' );
    };
      // DEFINE HTML2PS_DIR

      // Define PDFCONTROLLER_ACCESS
    $this->access( );
      // 120202, security, dwildt+

      // Get the selected tool out of the flexform
    $str_tool = $this->objFlexform->get_sheet_field( $sheet = 'debugging', $field = 'debug_tools' );

      // SWITCH debugging tool
    switch( $str_tool )
    {
      case( 1 ):
          // 120202, security, dwildt+

          // EXIT execute the script systemcheck.php
        $pathToSystemcheck = 'demo/systemcheck.php';
        if( defined( 'PDFCONTROLLER_ACCESS' ) )
        {
            // DRS - Development Reporting System
          if ($this->b_drs_all)
          {
            $endTime = $this->TT->getDifferenceToStarttime();
            t3lib_div::devLog('[INFO/ALL] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
          }
            // DRS - Development Reporting System
            // EXIT
          require_once( HTML2PS_DIR . $pathToSystemcheck );
          exit;
            // EXIT
        }
          // EXIT execute the script systemcheck.php

          // RETURN error message
        $content = $content . '<h1 style="color:red;">'.$this->pi_getLL('error_access_h1').'</h1>';
        $content = $content . '<div style="color:red;font-weight:bold;">'.$this->pi_getLL('error_access_p').'</div>';
        if ($this->b_drs_all)
        {
          $endTime = $this->TT->getDifferenceToStarttime();
          t3lib_div::devLog('[INFO/ALL] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
        }
        return $content;
          // RETURN error message
      case(2):
          // 120202, security, dwildt+

          // BREAK or EXIT
        $pathToForm = 'demo/index.php';
        if( defined( 'PDFCONTROLLER_ACCESS' ) )
        {
            // BREAK follow the workflow
          if( t3lib_div::_GP( 'use_pdfcontroller' ) == true )
          {
            break;
          }
            // BREAK follow the workflow

            // DRS - Development Reporting System
          if ($this->b_drs_all)
          {
            $endTime = $this->TT->getDifferenceToStarttime();
            t3lib_div::devLog('[INFO/ALL] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
          }
            // DRS - Development Reporting System

            // EXIT execute the demo form
          require_once( HTML2PS_DIR . $pathToForm );
          exit;
            // EXIT execute the demo form
        }
          // BREAK or EXIT

          // RETURN error message
        $content = $content . '<h1 style="color:red;">'.$this->pi_getLL('error_access_h1').'</h1>';
        $content = $content . '<div style="color:red;font-weight:bold;">'.$this->pi_getLL('error_access_p').'</div>';
        if ($this->b_drs_all)
        {
          $endTime = $this->TT->getDifferenceToStarttime();
          t3lib_div::devLog('[INFO/ALL] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
        }
        return $content;
          // RETURN error message
      case(3):
          // EXIT locate to the online documentation of html2ps
        if( defined( 'PDFCONTROLLER_ACCESS' ) )
        {
            // DRS - Development Reporting System
          if ($this->b_drs_all)
          {
            $endTime = $this->TT->getDifferenceToStarttime();
            t3lib_div::devLog('[INFO/ALL] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
          }
            // DRS - Development Reporting System

            // EXIT
          $str_url = 'http://www.tufat.com/docs/html2ps/index.html';
          header('Location: ' . $str_url);
          exit;
            // EXIT
        }
          // EXIT locate to the online documentation of html2ps

          // BREAK follow the workflow
        break;
      case(0):
      case(4):
      default:
        // Follow the workflow
    }
      // SWITCH debugging tool
      // Debugging tools - part 1 of 2



      //////////////////////////////////////////////////////////////////////
      //
      // RETURN piVars requirements failed

      // IF no debugging tool
    if( ! $str_tool )
    {
        // RETURN piVars isn't set
      if( ! ( isset( $this->piVars ) ) )
      {
        $this->content = 'WARNING: piVars aren\'t set. But there isn\'t any PDF generating possible without piVars.<br />' .
          '<br />' .
          'Please read the manual.<br />' .
          'See: Warning #1';
        if ($this->b_drs_all)
        {
          $endTime = $this->TT->getDifferenceToStarttime();
          t3lib_div::devLog('[INFO/WARN] piVars aren\'t set', $this->extKey, 2);
          t3lib_div::devLog('[INFO/ALL] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
        }
        return $this->pi_wrapInBaseClass($this->content);
      }
        // RETURN piVars isn't set

        // RETURN piVars['URL'] isn't set
      if( ! ( isset( $this->piVars['URL'] ) ) )
      {
        $this->content = 'WARNING: piVars[URL] isn\'t set. But there isn\'t any PDF generating possible without this piVar.<br />' .
          '<br />' .
          'Please read the manual.<br />' .
          'See: Warning #2';
        if ($this->b_drs_all)
        {
          $endTime = $this->TT->getDifferenceToStarttime();
          t3lib_div::devLog('[INFO/WARN] piVars[URL] isn\'t set', $this->extKey, 2);
          t3lib_div::devLog('[INFO/ALL] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
        }
        return $this->pi_wrapInBaseClass($this->content);
      }
        // RETURN piVars['URL'] isn't set

        // RETURN piVars['URL'] is empty
      if( empty( $this->piVars['URL'] ) )
      {
        $this->content = 'WARNING: piVars[URL] is empty. But it isn\'t possible to generate PDF without this piVar..<br />' .
          '<br />' .
          'Please read the manual.<br />' .
          'See: Warning #3';
        if ($this->b_drs_all)
        {
          $endTime = $this->TT->getDifferenceToStarttime();
          t3lib_div::devLog('[INFO/WARN] piVars[URL] is empty', $this->extKey, 2);
          t3lib_div::devLog('[INFO/ALL] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
        }
        return $this->pi_wrapInBaseClass($this->content);
      }
        // RETURN piVars['URL'] is empty

        // DRS - Development Reporting System
      if ($this->b_drs_all)
      {
        foreach($this->piVars as $key => $value)
        {
          t3lib_div::devLog('[INFO/ALL] piVars[' . $key . ']: ' . $value, $this->extKey, 0);
        }
      }
        // DRS - Development Reporting System
    }
      // IF no debugging tool
      // RETURN piVars requirements failed



      //////////////////////////////////////////////////////////////////////
      //
      // Get default $_REQUEST values from the Plugin (Flexform)

      // 120202, security, dwildt-
//    $_REQUEST                 = $this->objFlexform->defaultValues();
      // 120202, security, dwildt+
    if( t3lib_div::_GP( 'use_pdfcontroller' ) != true )
    {
      $_REQUEST = $this->objFlexform->defaultValues();
    }
      // 120202, security, dwildt+
      // Get default $_REQUEST values from the Plugin (Flexform)



      //////////////////////////////////////////////////////////////////////
      //
      // Add to $_REQUEST piVars

    $this->piVars['convert']        = 'Convert File';
    $this->piVars['process_mode']   = 'single';
    
    
    $this->piVars['encoding']       = '';
    $this->piVars['headerhtml']     = '';
    $this->piVars['footerhtml']     = '';
    $this->piVars['toc-location']   = 'before';
    $this->piVars['pslevel']        = '3';
    $this->piVars['output']         = '0';
    foreach($this->piVars as $key => $value)
    {
        // $key is set: don't override the value
      if( isset( $_REQUEST[$key] ) )
      {
        if ($this->b_drs_all)
        {
          t3lib_div::devLog('[INFO/ALL] $_REQUEST[' . $key . '] is: \'' . $_REQUEST[$key] . '\'', $this->extKey, 0);
        }
      }
        // $key is set: don't override the value
        // $key isn't set: override the value
      if( ! isset( $_REQUEST[$key] ) )
      {
        if ($this->b_drs_all)
        {
          t3lib_div::devLog('[INFO/ALL] $_REQUEST[' . $key . '] is set to: \'' . $value . '\'', $this->extKey, 0);
        }
        $_REQUEST[$key] = $value;
      }
        // $key isn't set: override the value
    }
      // Add to $_REQUEST piVars



      //////////////////////////////////////////////////////////////////////
      //
      // Build URL query

    $params = null;
    foreach( $_REQUEST as $key => $value )
    {
        // #31190, 111213: Borries Jensen-
      //$params = $params . '&' . $key . '=' . $value;
        // #31190, 111213: Borries Jensen+
      switch( true )
      {
        case( $key === 'URL' ):
          $params = $params . '&' . $key . '=' . rawurlencode($value);
          break;
        default:
          $params = $params . '&' . $key . '=' . $value;
          break;
      }
        // #31190, 111213: Borries Jensen+
    }
    $str_url  = t3lib_div::getIndpEnv( 'TYPO3_SITE_URL' ) .
      'typo3conf/ext/pdfcontroller/res/html2ps_v2043/public_html/demo/html2ps.php?';
    $str_url  = $str_url . $params;
      // Build URL query



      //////////////////////////////////////////////////////////////////////
      //
      // Debugging tools - part 2 of 2

      // SWITCH debugging tool
    switch( $str_tool )
    {
      case( 4 ):
          // RETURN list of params and full qualified URL
        $this->content = $this->content . '<h3>List of parameters for html2ps</h3>';
        $this->content = $this->content . '<pre>' . var_export( $_REQUEST, true ) . '</pre>';
        $this->content = $this->content . '<h3>Full qualified URL</h3>';
        $this->content = $this->content . '<p>Copy and paste to your browser.</p>';
        $this->content = $this->content . '<p>' . $str_url . '</p>';
        if ( $this->b_drs_all )
        {
          $endTime = $this->TT->getDifferenceToStarttime( );
          t3lib_div::devLog( '[INFO/ALL] end: ' . ( $endTime - $this->startTime ) . ' ms', $this->extKey, 0 );
        }
        return $this->pi_wrapInBaseClass( $this->content );
        break;
          // RETURN list of params and full qualified URL
      default:
        // follow the workflow
    }
      // SWITCH debugging tool
      // Debugging tools - part 1 of 2



      //////////////////////////////////////////////////////////////////////
      //
      // Remove all files in the html2ps cache

    //$path_to_html2ps        = t3lib_extMgm::extPath( 'pdfcontroller' ) . 'res/html2ps_v2043/public_html/';
    $path_to_html2ps_cache  = HTML2PS_DIR . 'cache/';
    $path_to_html2ps_out    = HTML2PS_DIR . 'out/';
    $path_to_html2ps_temp   = HTML2PS_DIR . 'temp/';
    $arr_pathToDir          = array( $path_to_html2ps_cache, $path_to_html2ps_out, $path_to_html2ps_temp);
    // find /home/www/htdocs/typo3/typo3conf/ext/pdfcontroller/res/html2ps_v2043/public_html/cache/* | xargs /bin/rm
    // find /home/www/htdocs/typo3/typo3conf/ext/pdfcontroller/res/html2ps_v2043/public_html/cache/* -mtime -0,2

      // Set rmCacheAll
    $rmCacheAll = $this->arr_extConf['rmCacheAll'];
    switch( true )
    {
      case( substr( $rmCacheAll, 0 , 3 ) == '---' ):
      case( empty( $rmCacheAll ) ):
        $rmCacheAll = 'always (recommended)';
        break;
    }
      // Set rmCacheAll

      // DRS - Development Reporting System
    if ($this->b_drs_all)
    {
      t3lib_div::devLog('[INFO/ALL] rmCacheAll: '. $rmCacheAll, $this->extKey, 0);
    }
      // DRS - Development Reporting System

      // SWITCH rmCacheAll
    switch( $rmCacheAll )
    {
      case('always (recommended)'):
          // Remove all files
          // LOOP directories
        foreach( $arr_pathToDir as $pathToDir )
        {
            // #31191, 111213: Borries Jensen-
          //$str_exec = 'find ' . $pathToDir . '* -type f | xargs /bin/rm -f';
            // #31191, 111213: Borries Jensen+
          $str_exec = 'find ' . $pathToDir . ' -type f | xargs /bin/rm -f';
          if ($this->b_drs_all)
          {
            t3lib_div::devLog('[INFO/ALL] exec(' . $str_exec . ')', $this->extKey, 0);
          }
          exec($str_exec);
        }
          // LOOP directories
        break;
          // Remove all files
      case('never'):
          // Don't remove anything
        break;
      default:
          // RETURN error message
        $this->content =  'UNDEFINED ERROR<br />' .
                          'Sorry, this should never happen.<br />' .
                          'rmCacheAll has an undefined value: \'' . $rmCacheAll . '\'<br />' .
                          'Please send a report to the developer. <br />' .
                           __METHOD__ . ' (' . __LINE__ .')';
        return $this->pi_wrapInBaseClass( $this->content );
          // RETURN error message
    }
      // SWITCH rmCacheAll
      // Remove all files in the html2ps cache



      //////////////////////////////////////////////////////////////////////
      //
      // Send data to html2ps

    if ($this->b_drs_perform)
    {
      $endTime = $this->TT->getDifferenceToStarttime();
      t3lib_div::devLog('[INFO/PERFORMANCE] before leaving to html2ps', $this->extKey, 0);
      t3lib_div::devLog('[INFO/PERFORMANCE] end: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
    }
    header( 'Location: ' . $str_url );
    exit;
      // Send data to html2ps

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
 * @version 0.0.2
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
      $this->b_drs_all          = true;
      $this->b_drs_error        = true;
      $this->b_drs_warn         = true;
      $this->b_drs_info         = true;
      $this->b_drs_flexform     = true;
      $this->b_drs_perform      = true;
      $this->b_drs_security     = true;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$this->arr_extConf['drs_mode'], $this->extKey, 0);
    }
    if ($this->arr_extConf['drs_mode'] == 'Flexform')
    {
      $this->b_drs_error      = true;
      $this->b_drs_warn       = true;
      $this->b_drs_info       = true;
      $this->b_drs_flexform   = true;
      t3lib_div::devlog('[INFO/DRS] DRS - Development Reporting System:<br />'.$this->arr_extConf['drs_mode'], $this->extKey, 0);
    }
    if ($this->arr_extConf['drs_mode'] == 'Performance')
    {
      $this->b_drs_perform    = true;
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








  /**
 * access( ): The method checks, if current user is logged on the TYPO3 backend
 *            and if user is an administrator.
 *            If it is, the method defines the constant PDFCONTROLLER_ACCESS
 *
 * @return  void
 * @version 1.0.2
 * @since   1.0.2
 */
  private function access( )
  {

    // RETURN no session because cookie is missing
    if( ! isset( $_COOKIE['be_typo_user'] ) )
    {
      if ($this->b_drs_security)
      {
        $prompt = 'No cookie with element be_typo_user.';
        t3lib_div::devlog('[INFO/SECURITY] ' . $prompt, $this->extKey, 0);
        $prompt = 'User doesn\'t get access.';
        t3lib_div::devlog('[OK/SECURITY] ' . $prompt, $this->extKey, -1);
      }
      return;
    }
      // RETURN no session because cookie is missing

      // RETURN no session because cookie element is empty
    if( empty( $_COOKIE["be_typo_user"] ) )
    {
      if ($this->b_drs_security)
      {
        $prompt = 'Cookie element be_typo_user is empty.';
        t3lib_div::devlog('[INFO/SECURITY] ' . $prompt, $this->extKey, 0);
        $prompt = 'User doesn\'t get access.';
        t3lib_div::devlog('[OK/SECURITY] ' . $prompt, $this->extKey, -1);
      }
      return;
    }
      // RETURN no session because cookie element is empty

      // Get backend user id
    $select_fields  = 'ses_userid';
    $from_table     = 'be_sessions';
    $where_clause   = 'ses_id = \'' . $_COOKIE["be_typo_user"] . '\'';
    $groupBy        = '';
    $orderBy        = '';
    $limit          = '';

    $query  = $GLOBALS['TYPO3_DB']->SELECTquery( $select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit );
    $res    = $GLOBALS['TYPO3_DB']->exec_SELECTquery( $select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit );
    $row    = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res );
      // Get backend user id

      // RETURN user isn't logged on the backend
    if( ! is_array( $row ) )
    {
      if ($this->b_drs_security)
      {
        $prompt = 'Row is empty. User isn\'t logged on the backend';
        t3lib_div::devlog('[WARN/SECURITY] ' . $prompt, $this->extKey, 2);
        $prompt = 'Query: ' . $query;
        t3lib_div::devlog('[INFO/SECURITY] ' . $prompt, $this->extKey, 0);
        $prompt = 'User doesn\'t get access.';
        t3lib_div::devlog('[OK/SECURITY] ' . $prompt, $this->extKey, -1);
      }
      return;
    }
      // RETURN user isn't logged on the backend

      // RETURN ses_userid is empty
    if( empty( $row['ses_userid'] ) )
    {
      if ($this->b_drs_security)
      {
        $prompt = 'Undefined error: field ses_userid of current row is empty.';
        t3lib_div::devlog('[ERROR/SECURITY] ' . $prompt, $this->extKey, 3);
        $prompt = 'Query: ' . $query;
        t3lib_div::devlog('[INFO/SECURITY] ' . $prompt, $this->extKey, 0);
        $prompt = 'User doesn\'t get access.';
        t3lib_div::devlog('[OK/SECURITY] ' . $prompt, $this->extKey, -1);
      }
      return;
    }
      // RETURN ses_userid is empty

      // Get backend user
    $select_fields  = 'username, realName, admin';
    $from_table     = 'be_users';
    $where_clause   = 'uid = ' . ( int ) $row['ses_userid'];
    $groupBy        = '';
    $orderBy        = '';
    $limit          = '';

    $query  = $GLOBALS['TYPO3_DB']->SELECTquery( $select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit );
    $res    = $GLOBALS['TYPO3_DB']->exec_SELECTquery( $select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit );
    $row    = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res );
      // Get backend user id

      // RETURN row is empty
    if( ! is_array( $row ) )
    {
      if ($this->b_drs_security)
      {
        $prompt = 'Undefined error: row is empty.';
        t3lib_div::devlog('[ERROR/SECURITY] ' . $prompt, $this->extKey, 3);
        $prompt = 'Query: ' . $query;
        t3lib_div::devlog('[INFO/SECURITY] ' . $prompt, $this->extKey, 0);
        $prompt = 'User doesn\'t get access.';
        t3lib_div::devlog('[OK/SECURITY] ' . $prompt, $this->extKey, -1);
      }
      return;
    }
      // RETURN row is empty

      // RETURN be_user isn't admin
    if( ! ( $row['admin'] == 1 ) )
    {
      if ($this->b_drs_security)
      {
        $prompt = 'User ' . $row['realname'] . ' (' . $row['username'] . ') hasn\'t administrator access!';
        t3lib_div::devlog('[INFO/SECURITY] ' . $prompt, $this->extKey, 0);
        $prompt = 'User doesn\'t get access.';
        t3lib_div::devlog('[OK/SECURITY] ' . $prompt, $this->extKey, -1);
      }
      return;
    }
      // RETURN be_user isn't admin

      // DRS - Devleopment Reporting System
    if ($this->b_drs_security)
    {
      $prompt = 'User ' . $row['realName'] . ' (' . $row['username'] . ') has administrator access!';
      t3lib_div::devlog('[OK/SECURITY] ' . $prompt, $this->extKey, -1);
    }
      // DRS - Devleopment Reporting System

      // SUCCESS: PDFCONTROLLER_ACCESS will defined
    define( 'PDFCONTROLLER_ACCESS', true );
    return;
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
    require_once('class.tx_pdfcontroller_pi1_flexform.php');
    // Class with methods for get flexform values
    $this->objFlexform = new tx_pdfcontroller_pi1_flexform($this);

//    require_once('class.tx_pdfcontroller_pi1_javascript.php');
//    // Class with methods for ordering rows
//    $this->objJss = new tx_pdfcontroller_pi1_javascript($this);
//
//    require_once('class.tx_pdfcontroller_pi1_template.php');
//    // Class with template methods, which return HTML
//    $this->objTemplate = new tx_pdfcontroller_pi1_template($this);

    require_once('class.tx_pdfcontroller_pi1_zz.php');
    // Class with zz methods
    $this->objZz = new tx_pdfcontroller_pi1_zz($this);
      // Require and init helper classes

  }







}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1.php']);
}

?>
