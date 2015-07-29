<?php

namespace Netzmacher\Pdfcontroller\Controller;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2015 - Dirk Wildt <http://wildt.at.die-netzmacher.de>
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



/**
 * Plugin 'Calculator for grants' for the 'pdfcontroller' extension.
 *
 * @author    Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @package    TYPO3
 * @subpackage    pdfcontroller
 *
 * @version 3.1.0
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

/**
 * Class for rendering a the PDF Controller Button
 *
 * @package TYPO3
 * @subpackage pdfcontroller
 * @author Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @version 4.0.0
 * @since 3.1.0
 */
class ButtonController extends ActionController
{

  /**
   * @var cObject
   */
  private $_cObj;

  /**
   * @var TypoScript configuration
   */
  private $_conf;
  var $prefixId = 'tx_pdfcontroller_pi2';
  // Same as class name
  var $scriptRelPath = 'pi2/class.tx_pdfcontroller_pi2.php';
  // Path to this script relative to the extension dir.
  var $extKey = 'pdfcontroller';
  // The extension key.
  var $pi_checkCHash = true;
  // [integer] The current typeNum given by the current URL
  var $param_typeNum = 0;
  // [string] current page object: page | print | pdf
  var $str_pageObject = 'page';
  var $str_developer_name = 'Dirk Wildt';
  var $str_developer_mail = 'http://wildt.at.die-netzmacher.de';
  var $str_developer_phone = '+49 361 21655226';
  var $str_developer_company = 'Die Netzmacher';
  var $str_developer_web = 'http://die-netzmacher.de';
  var $str_developer_typo3ext = '';
  var $str_developer_lang = 'german, english';
  // [Boolean] Set by init_drs()
  var $developer_contact = false;
  var $arr_extConf = array();
  // Array out of the extConf file
  // Booleans for DRS - Development Reporting System
  var $b_drs_all = false;
  var $b_drs_error = false;
  var $b_drs_warn = false;
  var $b_drs_info = false;
  var $b_drs_flexform = false;
  var $b_drs_marker = false;
  var $b_drs_perform = false;
  var $b_drs_security = false;
  var $b_drs_typolink = false;
  var $b_drs_typoscript = false;

  // Booleans for DRS - Development Reporting System

  /**
   * _init( ) :
   *
   * @return void
   * @access private
   * @version 4.0.0
   * @since 4.0.0
   */
  private function _init()
  {
    $this->_initConf();
    $this->_initCobj();
  }

  /**
   * _initCobj( ) :
   *
   * @return void
   * @access private
   * @version 4.0.0
   * @since 4.0.0
   */
  private function _initCobj()
  {
    $this->_cObj = $this->configurationManager->getContentObject();
  }

  /**
   * _initConf( ) :
   *
   * @return void
   * @access private
   * @version 4.0.0
   * @since 4.0.0
   */
  private function _initConf()
  {
    $typoScriptService = $this->objectManager->get( 'TYPO3\CMS\Extbase\Service\TypoScriptService' );
    $this->_conf = $typoScriptService->convertPlainArrayToTypoScriptArray( $this->settings );
  }

  /**
   * _zzCObjGetSingle( )  : Renders a typoscript property with cObjGetSingle, if it is an array.
   *                        Otherwise returns the property unchanged.
   *
   * @param	string		$cObj_name  : value or the name of the property like TEXT, COA, IMAGE, ...
   * @param	array		$cObj_conf  : null or the configuration of the property
   * @return	string		$value      : unchanged value or rendered typoscript property
   * @access private
   * @version    4.0.0
   * @since      3.1.0
   */
  private function _zzCObjGetSingle( $cObj_name, $cObj_conf )
  {
    switch ( true )
    {
      case( is_array( $cObj_conf ) ):
        $value = $GLOBALS[ 'TSFE' ]->cObj->cObjGetSingle( $cObj_name, $cObj_conf );
        break;
      case(!( is_array( $cObj_conf ) ) ):
      default:
        $value = $cObj_name;
        break;
    }

    return $value;
  }

  /**
   * mainAction() :
   *
   * @return  string    The content that should be displayed on the website
   * @access  public
   * @version 3.1.0
   * @since 0.0.1
   */
  public function buttonAction()
  {
    $this->_init();

    // Wrap the Pdf Controller button
    $confName = $this->_conf[ 'button' ];
    $confObj = $this->_conf[ 'button.' ];
    $content = ButtonController::_zzCObjGetSingle( $confName, $confObj );

    return $content;
  }

}

if ( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/pdfcontroller/pi2/class.tx_pdfcontroller_pi2.php' ] )
{
  include_once($TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/pdfcontroller/pi2/class.tx_pdfcontroller_pi2.php' ]);
}