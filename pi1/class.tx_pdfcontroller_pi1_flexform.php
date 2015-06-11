<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2015 - Dirk Wildt http://wildt.at.die-netzmacher.de
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   59: class tx_pdfcontroller_pi1_flexform
 *  106:     public function __construct($parentObj)
 *  142:     public function main()
 *
 *              SECTION: Tabs
 *  247:     private function sheet_tabs()
 *  277:     private function sheet_javascript()
 *
 *              SECTION: Helper
 *  553:     public function get_sheet_field($sheet, $field, $default)
 *  592:     private function set_default_if_empty($sheet, $field, $default)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * The class tx_pdfcontroller_pi1_flexform bundles all methods for the flexform but any wizard.
 * See Wizards in the wizard class.
 *
 * @author    Dirk Wildt http://wildt.at.die-netzmacher.de
 * @package    TYPO3
 * @subpackage    tx_pdfcontroller_pi1
 * @version 0.0.1
 * @since   0.0.1
 */
class tx_pdfcontroller_pi1_flexform
{

  // Vars set by methods in the current class
  // [array] Flexform array
  private $pi_flexform = null;
  // [array] array with corresponding flexform checkbox values and html2ps parameters (see init())
  var $arr_values_forFlexformCheckboxes = array();
  // [array] array with parameters for html2ps
  var $arr_request = array();
  // default values
  // [sDEF]
  var $sheet_sDEF_field_formula_debug = 0;

  // [sDEF]
  // default values












  /*   * *********************************************
   *
   * Init
   *
   * ******************************************** */

  /**
   * Constructor. The method initiate the parent object
   *
   * @param object    The parent object
   * @return  void
   */
  public function __construct( $parentObj )
  {
    // Set the Parent Object
    $this->pObj = $parentObj;
    // Init some global vars
    $this->init();
  }

  /**
   * init(): Init the array $arr_values_forFlexformCheckboxes.
   *         Key is the value of the flexform.
   *         Value is the value in html2ps.
   *
   * @return  void
   * @version 2.0.0
   * @since 0.0.1
   */
  public function init()
  {
    $arr_return = array();

      // Load flexform values
    $arr_return = $this->initPiInitPiFlexForm();
    if( !empty($arr_return))
    {
      return $arr_return;
    }

    // Allocate the arr_values_forFlexformCheckboxes
    $this->arr_values_forFlexformCheckboxes[ 'sheet_addcontent' ][ 'toc' ][ '0' ] = false;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_addcontent' ][ 'toc' ][ '1' ] = true;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_addcontent' ][ 'toc-location' ][ '0' ] = 'before';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_addcontent' ][ 'toc-location' ][ '1' ] = 'after';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_addcontent' ][ 'toc-location' ][ '2' ] = 'placeholder';

    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'debugbox' ][ '0' ] = false;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'debugbox' ][ '1' ] = true;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'debugnoclip' ][ '0' ] = true;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'debugnoclip' ][ '1' ] = false;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'imagequality_workaround' ][ '0' ] = false;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'imagequality_workaround' ][ '1' ] = true;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'pageborder' ][ '0' ] = false;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'pageborder' ][ '1' ] = true;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'smartpagebreak' ][ '0' ] = false;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'smartpagebreak' ][ '1' ] = true;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'transparency_workaround' ][ '0' ] = false;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_debugging' ][ 'transparency_workaround' ][ '1' ] = true;

    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'pixels' ][ '0' ] = '640';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'pixels' ][ '1' ] = '800';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'pixels' ][ '2' ] = '1024';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'pixels' ][ '3' ] = '1280';

    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'cssmedia' ][ '0' ] = 'handheld';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'cssmedia' ][ '1' ] = 'print';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'cssmedia' ][ '2' ] = 'projection';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'cssmedia' ][ '3' ] = 'Screen';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'cssmedia' ][ '4' ] = 'tty';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'cssmedia' ][ '5' ] = 'tv';

    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '0' ] = '';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '1' ] = 'utf-8';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '2' ] = 'iso-8859-1';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '3' ] = 'iso-8859-2';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '4' ] = 'iso-8859-3';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '5' ] = 'iso-8859-4';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '6' ] = 'iso-8859-5';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '7' ] = 'iso-8859-6';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '8' ] = 'iso-8859-7';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '9' ] = 'iso-8859-9';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '10' ] = 'iso-8859-10';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '11' ] = 'iso-8859-11';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '12' ] = 'iso-8859-13';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '13' ] = 'iso-8859-14';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '14' ] = 'iso-8859-15';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '15' ] = 'windows-1250';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '16' ] = 'windows-1251';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '17' ] = 'windows-1252';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'encoding' ][ '18' ] = 'koi8-r';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'scalepoints' ][ '0' ] = false;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_input' ][ 'scalepoints' ][ '1' ] = true;

    $this->arr_values_forFlexformCheckboxes[ 'sheet_properties' ][ 'renderimages' ][ '0' ] = true;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_properties' ][ 'renderfields' ][ '0' ] = true;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_properties' ][ 'renderforms' ][ '0' ] = true;
    $this->arr_values_forFlexformCheckboxes[ 'sheet_properties' ][ 'renderlinks' ][ '0' ] = true;

    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '0' ] = 'Legal';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '1' ] = 'Executive';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '2' ] = 'A0Oversize';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '3' ] = 'A0';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '4' ] = 'A1';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '5' ] = 'A2';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '6' ] = 'A3';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '7' ] = 'A4';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '8' ] = 'A5';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '9' ] = 'B5';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '10' ] = 'Folio';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '11' ] = 'A6';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '12' ] = 'A7';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '13' ] = 'A8';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '14' ] = 'A9';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '15' ] = 'A10';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '16' ] = 'Screenshot640';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '17' ] = 'Screenshot800';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'media' ][ '18' ] = 'Screenshot1024';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'method' ][ '0' ] = 'fastps';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'method' ][ '1' ] = 'pdflib';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'method' ][ '2' ] = 'fpdf';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'method' ][ '3' ] = 'png';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'landscape' ][ '0' ] = 'Portrait';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'landscape' ][ '1' ] = 'Landscape';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'pdfversion' ][ '0' ] = '1.2';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'pdfversion' ][ '1' ] = '1.3';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'pdfversion' ][ '2' ] = '1.4';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'pdfversion' ][ '3' ] = '1.5';

    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'pslevel' ][ '0' ] = '2';
    $this->arr_values_forFlexformCheckboxes[ 'sheet_output' ][ 'pslevel' ][ '1' ] = '3';
    // Allocate the arr_values_forFlexformCheckboxes
  }

  /**
   * initPiInitPiFlexForm():
   *
   * @return  void
   * @version 2.0.0
   * @since 2.0.0
   */
  private function initPiInitPiFlexForm()
  {
//    if ( $this->pi_flexform !== null )
//    {
//      return;
//    }

    // Init methods for pi_flexform
    $this->pObj->pi_initPIflexForm();

    // Load flexform values
    $this->pi_flexform = $this->pObj->cObj->data[ 'pi_flexform' ];
//var_dump( __METHOD__, __LINE__, $this->pi_flexform);
    if ( is_array( $this->pi_flexform ) )
    {
      return;
    }

    if ( $this->pObj->b_drs_error )
    {
      t3lib_div::devLog( '[ERROR] Flexform is empty.', $this->pObj->extKey, 3 );
      t3lib_div::devLog( '[INFO] ABORTED', $this->pObj->extKey, 0 );
    }
    $str_header = '<h1 style="color:red;">' . $this->pObj->pi_getLL( 'error_flexform_h1' ) . '</h1>';
    $str_prompt = '<p style="color:red;font-weight:bold;">' . $this->pObj->pi_getLL( 'error_flexform_p' ) . '</p>';
    $arr_return = array(
      'error' => array(
        'status' => true,
        'header' => $str_header,
        'prompt' => $str_prompt
      )
    );
    return $arr_return;
  }

  /*   * *********************************************
   *
   * Main
   *
   * ******************************************** */

  /**
   * defaultValues():  process the values from the pi_flexform field.
   *          Process each sheet. Allocates values to TypoScript.
   *
   * @return  void
   * @version 0.0.1
   * @since 0.0.1
   */
  public function defaultValues()
  {



    //////////////////////////////////////////////////////////////////////
    //
      // Development
    // Display values from pi_flexform as an tree
    if ( 1 == 0 )
    {
      $treeDat = $this->pObj->cObj->data[ 'pi_flexform' ];
      var_dump( __METHOD__ . ': ' . __LINE__, $treeDat, t3lib_div::view_array( $treeDat ) );
      $treeDat = t3lib_div::resolveAllSheetsInDS( $treeDat );
      var_dump( __METHOD__ . ': ' . __LINE__, $treeDat, t3lib_div::view_array( $treeDat ) );
    }
    //var_dump(__METHOD__ . ': ' . __LINE__ , $this->pi_flexform);
    // Display values from pi_flexform as an tree
    // Development
    //////////////////////////////////////////////////////////////////////
    //
      // DRS - Development Reporting System

    if ( $this->pObj->b_drs_flexform )
    {
      $str_header = $this->pObj->cObj->data[ 'header' ];
      $int_uid = $this->pObj->cObj->data[ 'uid' ];
      $int_pid = $this->pObj->cObj->data[ 'pid' ];
      t3lib_div::devlog( '[INFO/FLEXFORM] \'' . $str_header . '\' (pid: ' . $int_pid . ', uid: ' . $int_uid . ')', $this->pObj->extKey, 0 );
    }
    // DRS - Development Reporting System
    //////////////////////////////////////////////////////////////////////
    //
      // Init Language

    if ( !$this->pObj->lang )
    {
      $this->pObj->objZz->initLang();
    }
    // Init Language
    //////////////////////////////////////////////////////////////////////
    //
      // Process the Sheets

    $this->sheet_addcontent();
    $this->sheet_debugging();
    $this->sheet_input();
    $this->sheet_properties();
    $this->sheet_output();
    $this->sheet_sDEF();
    // Process the Sheets
    //var_dump(__METHOD__ . ': ' . __LINE__ , $this->pi_flexform);
    //var_dump(__METHOD__ . ': ' . __LINE__ , $this->pObj->conf);

    return $this->arr_request;
  }

  /*   * *********************************************
   *
   * Sheets
   *
   * ******************************************** */

  /**
   * sheet_addcontent(): Handle configuration from the sheet addcontent
   *
   * @return  void
   * @version 0.0.1
   * @since 0.0.1
   */
  private function sheet_addcontent()
  {
    $sheet = 'addcontent';

    //////////////////////////////////////////////////////////////////////
    //
      // Field headerhtml

    $field = 'headerhtml';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field headerhtml
    //////////////////////////////////////////////////////////////////////
    //
      // Field footerhtml

    $field = 'footerhtml';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field footerhtml
    //////////////////////////////////////////////////////////////////////
    //
      // Field toc

    $field = 'toc';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field toc
    //////////////////////////////////////////////////////////////////////
    //
      // Field toc-location

    $field = 'toc-location';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field toc-location
    //////////////////////////////////////////////////////////////////////
    //
      // Field watermarkhtml

    $field = 'watermarkhtml';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field watermarkhtml



    return;
  }

  /**
   * sheet_debugging(): Handle configuration from the sheet debugging
   *
   * @return  void
   * @version 2.0.0
   * @since 0.0.1
   */
  private function sheet_debugging()
  {
    $sheet = 'debugging';

//      //////////////////////////////////////////////////////////////////////
//      //
//      // Field debug_tools
//
//    // 141016, dwildt, 4+
//    $field = 'debug_tools';
//    $value = $this->set_default_if_empty($sheet, $field, $default=null);
//
//    $this->prompt_drs($sheet, $field, $value);
//    $this->set_arr_request($sheet, $field, $value);
//      // Field debug_tools
    //////////////////////////////////////////////////////////////////////
    //
      // Field debugbox

    $field = 'debugbox';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field debugbox
    //////////////////////////////////////////////////////////////////////
    //
      // Field debugnoclip

    $field = 'debugnoclip';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field debugnoclip
    //////////////////////////////////////////////////////////////////////
    //
      // Field imagequality_workaround

    $field = 'imagequality_workaround';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field imagequality_workaround
    //////////////////////////////////////////////////////////////////////
    //
      // Field pageborder

    $field = 'pageborder';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field pageborder
    //////////////////////////////////////////////////////////////////////
    //
      // Field smartpagebreak

    $field = 'smartpagebreak';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field smartpagebreak
    //////////////////////////////////////////////////////////////////////
    //
      // Field transparency_workaround

    $field = 'transparency_workaround';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field transparency_workaround



    return;
  }

  /**
   * sheet_input(): Handle configuration from the sheet input
   *
   * @return  void
   * @version 0.0.1
   * @since 0.0.1
   */
  private function sheet_input()
  {
    $sheet = 'input';

    //////////////////////////////////////////////////////////////////////
    //
      // Field pixels

    $field = 'pixels';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field pixels
    //////////////////////////////////////////////////////////////////////
    //
      // Field encoding

    $field = 'encoding';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field encoding
    //////////////////////////////////////////////////////////////////////
    //
      // Field cssmedia

    $field = 'cssmedia';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field cssmedia
    //////////////////////////////////////////////////////////////////////
    //
      // Field scalepoints

    $field = 'scalepoints';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field scalepoints



    return;
  }

  /**
   * sheet_properties(): Handle configuration from the sheet properties
   *
   * @return  void
   * @version 0.0.1
   * @since 0.0.1
   */
  private function sheet_properties()
  {
    $sheet = 'properties';

    //////////////////////////////////////////////////////////////////////
    //
      // Field renderimages

    $field = 'renderimages';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field renderimages
    //////////////////////////////////////////////////////////////////////
    //
      // Field renderfields

    $field = 'renderfields';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field renderfields
    //////////////////////////////////////////////////////////////////////
    //
      // Field renderforms

    $field = 'renderforms';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field renderforms
    //////////////////////////////////////////////////////////////////////
    //
      // Field renderlinks

    $field = 'renderlinks';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field renderlinks



    return;
  }

  /**
   * sheet_output(): Handle configuration from the sheet output
   *
   * @return  void
   * @version 0.0.1
   * @since 0.0.1
   */
  private function sheet_output()
  {
    $sheet = 'output';

    //////////////////////////////////////////////////////////////////////
    //
      // Field bottommargin

    $field = 'bottommargin';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field bottommargin
    //////////////////////////////////////////////////////////////////////
    //
      // Field leftmargin

    $field = 'leftmargin';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field leftmargin
    //////////////////////////////////////////////////////////////////////
    //
      // Field media

    $field = 'media';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field media
    //////////////////////////////////////////////////////////////////////
    //
      // Field method

    $field = 'method';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field method
    //////////////////////////////////////////////////////////////////////
    //
      // Field landscape

    $field = 'landscape';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field landscape
    //////////////////////////////////////////////////////////////////////
    //
      // Field pdfversion

    $field = 'pdfversion';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field pdfversion
    //////////////////////////////////////////////////////////////////////
    //
      // Field pslevel

    $field = 'pslevel';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field pslevel
    //////////////////////////////////////////////////////////////////////
    //
      // Field rightmargin

    $field = 'rightmargin';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field rightmargin
    //////////////////////////////////////////////////////////////////////
    //
      // Field topmargin

    $field = 'topmargin';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field topmargin



    return;
  }

  /**
   * sheet_sDEF(): Handle configuration from the sheet sDEF
   *
   * @return  void
   * @version 0.0.1
   * @since 0.0.1
   */
  private function sheet_sDEF()
  {
    $sheet = 'sDEF';

    //////////////////////////////////////////////////////////////////////
    //
      // Field proxy

    $field = 'proxy';
    $value = $this->set_default_if_empty( $sheet, $field, $default = null );

    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );
    // Field proxy



    return;
  }

  /*   * *********************************************
   *
   * Helper
   *
   * ******************************************** */

  /**
   * get_sheet_field(): Get the value from the given sheet field.
   *                  It isn't the real value from the flexform.
   *                  It is the value from the array $this->pi_flexform
   *
   * @param string    $sheet:     label of the sheet
   * @param string    $field    label of the field
   * @return  string    value from the given sheet field
   * @version 0.0.2
   * @since 0.0.2
   */
  public function get_sheet_field( $sheet, $field )
  {
    if ( !isset( $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ] ) )
    {
      $prompt = '
        <div style="background:white; color:red; font-weight:bold;border:.4em solid red;">
          <h1>
            ERROR</h1>
          <p>
            Flexform field isn\'t set:<br />
            sheet: ' . $sheet . '<br />
            field: ' . $field . '
          </p>
            <p>
              ' . $this->pObj->extKey . ': ' . __METHOD__ . ' (line ' . __LINE__ . ')
            </p>
        </div>';
      die( $prompt );
    }
    return $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ];
  }

  /**
   * set_arr_request(): Prompt to the drs the value if the given sheet.field
   *
   * @param string    $sheet:     label of the sheet
   * @param string    $field:     label of the field
   * @param string    $value:     value of the field
   * @return  void
   * @version 0.0.1
   * @since 0.0.1
   */
  public function set_arr_request( $sheet, $field, $value )
  {
    if ( isset( $this->pObj->piVars[ $field ] ) )
    {
      // Do nothing
      if ( $this->pObj->b_drs_flexform )
      {
        t3lib_div::devlog( '[INFO/FLEXFORM] ' .
                '$_REQUEST[' . $field . '] is set by GET params and won\'t be overriden.', $this->pObj->extKey, 0 );
      }
      return;
    }
    $sheet = 'sheet_' . $sheet;
    $value_from_array = null;

    if ( isset( $this->arr_values_forFlexformCheckboxes[ $sheet ] ) )
    {
      if ( isset( $this->arr_values_forFlexformCheckboxes[ $sheet ][ $field ] ) )
      {
        if ( isset( $this->arr_values_forFlexformCheckboxes[ $sheet ][ $field ][ $value ] ) )
        {
          $value_from_array = $this->arr_values_forFlexformCheckboxes[ $sheet ][ $field ][ $value ];
        }
      }
    }

    if ( $value_from_array != null )
    {
      $this->arr_request[ $field ] = $this->arr_values_forFlexformCheckboxes[ $sheet ][ $field ][ $value ];
    }
    if ( $value_from_array == null )
    {
      if ( $value === 0 || $value )
      {
        $this->arr_request[ $field ] = $value;
      }
    }

    if ( $this->pObj->b_drs_flexform )
    {
      if ( isset( $this->arr_request[ $field ] ) )
      {
        t3lib_div::devlog( '[INFO/FLEXFORM] ' .
                '$_REQUEST[' . $field . '] is set to : \'' . $this->arr_request[ $field ] . '\'', $this->pObj->extKey, 0 );
      }
    }
  }

  /**
   * prompt_drs(): Prompt to the drs the value if the given sheet.field
   *
   * @param string    $sheet:     label of the sheet
   * @param string    $field:     label of the field
   * @param string    $value:     value of the field
   * @return  void
   * @version 0.0.1
   * @since 0.0.1
   */
  public function prompt_drs( $sheet, $field, $value )
  {
    if ( $this->pObj->b_drs_flexform )
    {
      t3lib_div::devlog( '[INFO/FLEXFORM] ' .
              $sheet . '.' . $field . ': \'' . $value . '\'', $this->pObj->extKey, 0 );
    }
  }

  /**
   * set_default_if_empty(): Sets the given default value, if flexform value is empty
   *                         Effected variable: $this->pi_flexform
   *
   * @param string    $sheet:     label of the sheet
   * @param string    $field    label of the field
   * @param string    $default  default value
   * @return  string            value of the flexform field
   * @version 0.0.3
   * @since 0.0.2
   */
  private function set_default_if_empty( $sheet, $field, $default )
  {
    //////////////////////////////////////////////////////////////////////
    //
      // Flexform isn't saved any time

    if ( !is_array( $this->pi_flexform ) )
    {
      $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ] = $default;
    }
    // Flexform isn't saved any time
    //////////////////////////////////////////////////////////////////////
    //
      // Given field is empty

    if ( empty( $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ] ) )
    {
      $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ] = $default;
    }
    // Given field is empty
    //////////////////////////////////////////////////////////////////////
    //
      // Return value

    return $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ];
    // Return value
  }

}

if ( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1_flexform.php' ] )
{
  include_once($TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1_flexform.php' ]);
}
?>