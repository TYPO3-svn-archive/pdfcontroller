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
 * @version 3.1.0
 * @since   3.1.0
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
   * init():
   *
   * @return  void
   * @version 3.1.0
   * @since 0.0.1
   */
  public function init()
  {
    $arr_return = array();

    // Load flexform values
    $arr_return = $this->initPiInitPiFlexForm();
    // Return: there is an error
    if ( !empty( $arr_return ) )
    {
      return $arr_return;
    }

    $this->sheet_debugging();
    $this->sheet_sDEF();
    $this->sheet_template();
    $this->sheet_templatefirstpage();
  }

  /**
   * initPiInitPiFlexForm():
   *
   * @return  void
   * @version 3.1.0
   * @since 2.0.0
   */
  private function initPiInitPiFlexForm()
  {
    // Init methods for pi_flexform
    $this->pObj->pi_initPIflexForm();

    // Load flexform values
    $this->pi_flexform = $this->pObj->cObj->data[ 'pi_flexform' ];

    if ( is_array( $this->pi_flexform ) )
    {
      return;
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
  private function defaultValues()
  {

    // Display values from pi_flexform as an tree
    if ( 1 == 0 )
    {
      $treeDat = $this->pObj->cObj->data[ 'pi_flexform' ];
      var_dump( __METHOD__ . ': ' . __LINE__, $treeDat, t3lib_div::view_array( $treeDat ) );
      $treeDat = t3lib_div::resolveAllSheetsInDS( $treeDat );
      var_dump( __METHOD__ . ': ' . __LINE__, $treeDat, t3lib_div::view_array( $treeDat ) );
    }
    // DRS - Development Reporting System
    if ( $this->pObj->b_drs_flexform )
    {
      $str_header = $this->pObj->cObj->data[ 'header' ];
      $int_uid = $this->pObj->cObj->data[ 'uid' ];
      $int_pid = $this->pObj->cObj->data[ 'pid' ];
      t3lib_div::devlog( '[INFO/FLEXFORM] \'' . $str_header . '\' (pid: ' . $int_pid . ', uid: ' . $int_uid . ')', $this->pObj->extKey, 0 );
    }
    // Init Language
    if ( !$this->pObj->lang )
    {
      $this->pObj->objZz->initLang();
    }
    // Process the Sheets

    $this->sheet_debugging();
    $this->sheet_sDEF();
    $this->sheet_template();
    $this->sheet_templatefirstpage();

    return $this->arr_request;
  }

  /*   * *********************************************
   *
   * Sheets
   *
   * ******************************************** */

  /**
   * sheet_debugging(): Handle configuration from the sheet debugging
   *
   * @return  void
   * @version 3.1.0
   * @since 0.0.1
   */
  private function sheet_debugging()
  {
    $sheet = 'debugging';

    $field = 'mode';
    $default = 'production';
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

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

    $field = 'proxy';
    $default = '';
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    return;
  }

  /**
   * sheet_template(): Handle configuration from the sheet template
   *
   * @return  void
   * @version 3.1.0
   * @since 3.1.0
   */
  private function sheet_template()
  {
    $sheet = 'template';

    $field = 'filepath';
    $default = 'typo3conf/ext/pdfcontroller/Resources/Public/Pdf/typo3-pdfcontroller.pdf';
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'marginbottom';
    $default = 20;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'marginheader';
    $default = 0;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'marginfooter';
    $default = 30;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'marginleft';
    $default = 30;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'marginright';
    $default = 20;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'margintop';
    $default = 20;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    return;
  }

  /**
   * sheet_templatefirstpage(): Handle configuration from the sheet templatefirstpage
   *
   * @return  void
   * @version 3.1.0
   * @since 3.1.0
   */
  private function sheet_templatefirstpage()
  {
    $sheet = 'templatefirstpage';

    $field = 'filepath';
    $default = 'typo3conf/ext/pdfcontroller/Resources/Public/Pdf/typo3-caddy_draft.pdf';
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'marginbottom';
    $default = 20;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'marginheader';
    $default = 0;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'marginfooter';
    $default = 30;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

    $field = 'margintop';
    $default = 20;
    $value = $this->set_default_if_empty( $sheet, $field, $default );
    $this->prompt_drs( $sheet, $field, $value );
    $this->set_arr_request( $sheet, $field, $value );

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
//    var_dump( __METHOD__, __LINE__, $this->pi_flexform[ 'data' ] );
//    exit();
    // Flexform isn't never saved
    if ( !is_array( $this->pi_flexform ) )
    {
      $prompt = '
        <div style="background:white; color:red; font-weight:bold;border:.4em solid red;">
          <h1>
            ERROR</h1>
          <p>
            Flexform isn\'t never saved.<br />
            Please save the flexform of the PDF Controller once.
          </p>
          <p>
            ' . $this->pObj->extKey . ': ' . __METHOD__ . ' (line ' . __LINE__ . ')
          </p>
        </div>';
      die( $prompt );
    }
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
            Maybe flexform isn\'t never saved.<br />
            Please save the flexform of the PDF Controller once.
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
   * prompt_drs(): Prompt to the drs the value if the given sheet.field
   *
   * @param string    $sheet:     label of the sheet
   * @param string    $field:     label of the field
   * @param string    $value:     value of the field
   * @return  void
   * @access: private
   * @version 0.0.1
   * @since 0.0.1
   */
  private function prompt_drs( $sheet, $field, $value )
  {
    if ( $this->pObj->b_drs_flexform )
    {
      t3lib_div::devlog( '[INFO/FLEXFORM] ' .
              $sheet . '.' . $field . ': \'' . $value . '\'', $this->pObj->extKey, 0 );
    }
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
  public function set_arr_request($sheet, $field, $value)
  {
    if(isset($this->pObj->piVars[$field]))
    {
        // Do nothing
      if ($this->pObj->b_drs_flexform )
      {
        t3lib_div::devlog('[INFO/FLEXFORM] '.
          '$_REQUEST[' . $field . '] is set by GET params and won\'t be overriden.', $this->pObj->extKey, 0);
      }
      return;
    }
    $sheet = 'sheet_' . $sheet;
    $value_from_array = null;

    if(isset($this->arr_values_forFlexformCheckboxes[$sheet]))
    {
      if(isset($this->arr_values_forFlexformCheckboxes[$sheet][$field]))
      {
        if(isset($this->arr_values_forFlexformCheckboxes[$sheet][$field][$value]))
        {
          $value_from_array = $this->arr_values_forFlexformCheckboxes[$sheet][$field][$value];
        }
      }
    }

    if($value_from_array != null)
    {
      $this->arr_request[$field] = $this->arr_values_forFlexformCheckboxes[$sheet][$field][$value];
    }
    if($value_from_array == null)
    {
      if($value === 0 || $value)
      {
        $this->arr_request[$field] = $value;
      }
    }

    if ($this->pObj->b_drs_flexform )
    {
      if(isset($this->arr_request[$field]))
      {
        t3lib_div::devlog('[INFO/FLEXFORM] '.
          '$_REQUEST[' . $field . '] is set to : \'' . $this->arr_request[$field] . '\'', $this->pObj->extKey, 0);
      }
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
   * @version 3.1.0
   * @since 0.0.2
   */
  private function set_default_if_empty( $sheet, $field, $default )
  {
    // Flexform isn't saved any time
    if ( !is_array( $this->pi_flexform ) )
    {
      $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ] = $default;
      return $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ];
    }
    // Given field is null
    if ( is_null( $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ] ) )
    {
      $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ] = $default;
      return $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ];
    }
    // Return value
    return $this->pi_flexform[ 'data' ][ $sheet ][ 'lDEF' ][ $field ][ 'vDEF' ];
  }

}

if ( defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1_flexform.php' ] )
{
  include_once($TYPO3_CONF_VARS[ TYPO3_MODE ][ 'XCLASS' ][ 'ext/pdfcontroller/pi1/class.tx_pdfcontroller_pi1_flexform.php' ]);
}
?>