<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2015 -  Dirk Wildt <http://wildt.at.die-netzmacher.de>
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
 * ************************************************************* */



// #62278, 141016, dwildt, 1-
//require_once(PATH_tslib . 'class.tslib_pibase.php');
// #62278, 141016, dwildt, +
list( $main, $sub, $bugfix ) = explode( '.', TYPO3_version );
$version = ( ( int ) $main ) * 1000000;
$version = $version + ( ( int ) $sub ) * 1000;
$version = $version + ( ( int ) $bugfix ) * 1;
// Set TYPO3 version as integer (sample: 4.7.7 -> 4007007)

if ( $version < 6002000 )
{
  require_once(PATH_tslib . 'class.tslib_pibase.php');
}
// #62278, 141016, dwildt, +

/**
 * Main plugin for the 'pdfcontroller' extension.
 *
 * @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @package    TYPO3
 * @subpackage    pdfcontroller
 *
 * @version 3.1.0
 */
class tx_pdfcontroller_pi1 extends tslib_pibase
{

  public $prefixId = 'tx_pdfcontroller_pi1';    // Same as class name
  public $scriptRelPath = 'pi1/class.tx_pdfcontroller_pi1.php'; // Path to this script relative to the extension dir.
  public $extKey = 'pdfcontroller'; // The extension key.
  public $b_drs_all = false;
  public $b_drs_error = false;
  public $b_drs_warn = false;
  public $b_drs_info = false;
  public $b_drs_flexform = false;
  public $b_drs_perform = false;
  public $b_drs_security = false;

  // Booleans for DRS - Development Reporting System


  /*   * *********************************************
   *
   * Main Process
   *
   * ******************************************** */

  /**
   * Main method
   *
   * @param string    $content: The content of the PlugIn
   * @param array   $conf: The PlugIn Configuration
   * @return  string    The content that should be displayed on the website
   * @version 2.0.0
   * @since 0.0.1
   */
  public function main( $content, $conf )
  {
    $this->conf = $conf;

    $this->pi_setPiVarDefaults();
    $this->pi_loadLL();

    $this->startTimetracking();

    // Init DRS - Development Reporting System
    $this->init_drs();
    if ( $this->b_drs_perform )
    {
      t3lib_div::devlog( '[INFO/PERFORMANCE] START ' . ($this->startTime) . ' ms', $this->extKey, 0 );
    }

    // Require and init helper classes
    $this->require_classes();

    $content = Netzmacher\Pdfcontroller\TcPdf\Renderer::pdf( $this );
    if ( $this->b_drs_all )
    {
      $endTime = $this->TT->getDifferenceToStarttime();
      t3lib_div::devLog( '[INFO/ALL] end: ' . ($endTime - $this->startTime) . ' ms', $this->extKey, 0 );
    }

    return $content;
  }

  /*   * *********************************************
   *
   * DRS - Development Reporting System
   *
   * ******************************************** */

  /**
   * Set the booleans for Warnings, Errors and DRS - Development Reporting System
   *
   * @return  void
   * @version 0.0.2
   * @since 0.0.2
   */
  private function init_drs()
  {

    if ( $this->arr_extConf[ 'drs_mode' ] == 'All' )
    {
      $this->b_drs_all = true;
      $this->b_drs_error = true;
      $this->b_drs_warn = true;
      $this->b_drs_info = true;
      $this->b_drs_flexform = true;
      $this->b_drs_perform = true;
      $this->b_drs_security = true;
      t3lib_div::devlog( '[INFO/DRS] DRS - Development Reporting System:<br />' . $this->arr_extConf[ 'drs_mode' ], $this->extKey, 0 );
    }
    if ( $this->arr_extConf[ 'drs_mode' ] == 'Flexform' )
    {
      $this->b_drs_error = true;
      $this->b_drs_warn = true;
      $this->b_drs_info = true;
      $this->b_drs_flexform = true;
      t3lib_div::devlog( '[INFO/DRS] DRS - Development Reporting System:<br />' . $this->arr_extConf[ 'drs_mode' ], $this->extKey, 0 );
    }
    if ( $this->arr_extConf[ 'drs_mode' ] == 'Performance' )
    {
      $this->b_drs_perform = true;
      t3lib_div::devlog( '[INFO/DRS] DRS - Development Reporting System:<br />' . $this->arr_extConf[ 'drs_mode' ], $this->extKey, 0 );
    }
    if ( $this->arr_extConf[ 'drs_mode' ] == 'Security' )
    {
      $this->b_drs_error = true;
      $this->b_drs_warn = true;
      $this->b_drs_info = true;
      $this->b_drs_security = true;
      t3lib_div::devlog( '[INFO/DRS] DRS - Development Reporting System:<br />' . $this->arr_extConf[ 'drs_mode' ], $this->extKey, 0 );
    }
    // Set the DRS mode
  }

  /*   * *********************************************
   *
   * Classes
   *
   * ******************************************** */

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

    require_once('class.tx_pdfcontroller_pi1_flexform.php');
    // Class with methods for get flexform values
    $this->objFlexform = new tx_pdfcontroller_pi1_flexform( $this );

  }

  /**
   * startTimetracking()
   *
   * @return  void
   * @version 3.1.0
   * @since 0.0.1
   */
  private function startTimetracking()
  {
    list( $main, $sub, $bugfix ) = explode( '.', TYPO3_version );
    $version = ( ( int ) $main ) * 1000000;
    $version = $version + ( ( int ) $sub ) * 1000;
    $version = $version + ( ( int ) $bugfix ) * 1;

    // Set TYPO3 version as integer (sample: 4.7.7 -> 4007007)
    if ( $version < 6002000 )
    {
      require_once(PATH_t3lib . 'class.t3lib_timetrack.php');
    }

    $this->TT = new t3lib_timeTrack;
    $this->TT->start();
    $this->startTime = $this->TT->getDifferenceToStarttime();
  }

}

if ( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1.php' ] )
{
  include_once($TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1.php' ]);
}