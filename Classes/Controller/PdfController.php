<?php

namespace Netzmacher\Pdfcontroller\Controller;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

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

/**
 * Class for overloading TCPDF methods
 *
 * @package TYPO3
 * @subpackage pdfcontroller
 * @author Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @version 4.0.0
 * @since 3.1.0
 */
// Require TCPDF and FPDI
$t3_tcpdf = ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'class.tx_t3_tcpdf.php';
$fpdi = ExtensionManagementUtility::extPath( 'pdfcontroller' ) . 'Resources/Private/FPDI-1.5.4/fpdi.php';
require_once( $t3_tcpdf );
require_once( $fpdi );

class PdfControllerFpdi extends \FPDI
{

  /**
   * @var cObject
   */
  private $_cObj;

  /**
   * @var TypoScript configuration
   */
  private $_conf;
  private $_tplIdx = null;    // Current page number of the PDF template
  private $_tplIdLast = null; // Last page number of the PDF template
//  private $objFlexform;       // Flexform object: PDF Controller Interface
  private $settings;          // TypoScript configuration of the current page

  /**
   * Footer( )  : Overloading the tcpdf Footer method
   *
   * @return	void
   * @access public
   * @version 3.1.0
   * @since   3.1.0
   */

  public function Footer()
  {
    $content = $this->_FooterContent();
    $this->_FooterWrite( $content );
  }

  /**
   * _FooterContent( )  : Returns HTML footer code
   *
   * @return	string  $content  : HTML footer code
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _FooterContent()
  {
    $confName = $this->_conf[ 'pdf.' ][ 'content.' ][ 'footer' ];
    $confObj = $this->_conf[ 'pdf.' ][ 'content.' ][ 'footer.' ];
    $content = $this->_zzCObjGetSingle( $confName, $confObj );

    if ( empty( $this->pagegroups ) )
    {
      $content = str_replace( '%page%', $this->getAliasNumPage(), $content );
      $content = str_replace( '%pages%', $this->getAliasNbPages(), $content );
    }
    else
    {
      $content = str_replace( '%page%', $this->getPageNumGroupAlias(), $content );
      $content = str_replace( '%pages%', $this->getPageGroupAlias(), $content );
    }
//    var_dump(__METHOD__, __LINE__, $content );
//    exit();
    return $content;
  }

  /**
   * _FooterWrite( )  : Returns HTML footer code
   *
   * @param string  $content  : HTML footer code
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _FooterWrite( $content )
  {
//    $cur_y = $this->y;
//    $this->SetY( $cur_y );

    $align = 'C';
    $autopadding = true;
    $border = 0;
    $fill = 0;
    $h = 0;
    $ln = 1;
    $reseth = true;
    $w = 0;
    $x = '';
    $y = '';

    $this->writeHTMLCell( $w, $h, $x, $y, $content, $border, $ln, $fill, $reseth, $align, $autopadding );
  }

  /**
   * Header( )  : Overloading the tcpdf Header method
   *              * If content needs another page, this method imports the needed template:
   *                The corresponding page from the PDF template file
   *              * It is a workaround
   *
   * @return	void
   * @access public
   * @version 3.1.0
   * @since   3.1.0
   */
  public function Header()
  {
    $mode = $this->_t3FlexformValue( 'debugging', 'mode' );

    switch ( $mode )
    {
      case('testTCPDF061HTML'):
      case('testTCPDF061PDF'):
        // Follow the workflow
        break;
      case('production'):
      case('testDefaultHTML'):
      default:
        $this->_pdfTemplate();
        break;
    }
    $this->_pdfMargins();
  }

  /**
   * _pdfMargins() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfMargins()
  {
    switch ( true )
    {
      case( ( $this->getPage() < 2 ) && ( $this->_tplIdLast >= 2 ) ):
        // Current page is the first page and the PDF template contains more than one opage
        $this->_pdfMarginsByFlexform( 'templatefirstpage' );
        break;
      case( ( $this->getPage() >= 2 ) && ( $this->_tplIdLast >= 2 ) ):
        // Current page isn't the first page and the PDF template contains more than one opage
        $this->_pdfMarginsByFlexform( 'template' );
        break;
      case( $this->getPage() < 2 ):
      case( $this->_tplIdLast < 2 ):
      default:
        // Current page is the first page or PDF template contains only one page
        $this->_pdfMarginsByFlexform( 'template' );
        break;
    }
  }

  /**
   * _pdfMarginsByFlexform() :
   *
   * @param string $sheet :
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfMarginsByFlexform( $sheet )
  {
    $margintop = ( int ) $this->_t3FlexformValue( $sheet, 'margintop' );
    $this->SetTopMargin( $margintop );
//    150619, dwildt, 3-: right and left margin must defined before generating the first page
//    $marginright = ( int ) $this->_t3FlexformValue( $sheet, 'marginright' );
//    $marginleft = ( int ) $this->_t3FlexformValue( $sheet, 'marginleft' );
//    $this->SetMargins( $marginleft, $margintop, $marginright, true );

    $marginbottom = ( int ) $this->_t3FlexformValue( $sheet, 'marginbottom' );
    $this->SetAutoPageBreak( TRUE, $marginbottom );

    $marginheader = ( int ) $this->_t3FlexformValue( $sheet, 'marginheader' );
    $this->SetHeaderMargin( $marginheader );
    $marginfooter = ( int ) $this->_t3FlexformValue( $sheet, 'marginfooter' );
    $this->SetFooterMargin( $marginfooter );
  }

  /**
   * _pdfTemplate( ) :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfTemplate()
  {
    $this->_pdfTemplateInit();
    $tplIdForImport = $this->getPage();
    if ( $tplIdForImport > $this->_tplIdLast )
    {
      $tplIdForImport = $this->_tplIdLast;
    }
    $this->_tplIdx = $this->importPage( $tplIdForImport );
    $this->useTemplate( $this->_tplIdx );
  }

  /**
   * _pdfTemplateInit( ) :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfTemplateInit()
  {
    if ( !is_null( $this->_tplIdx ) )
    {
      return;
    }

    $ffValue = $this->_t3FlexformValue( 'template', 'filepath' );
    $this->_tplIdLast = $this->setSourceFile( $ffValue );
  }

  /**
   * _t3FlexformValue( ) : Get value from the flexform
   *
   * @param   string	$sheet:
   * @param   string	$field:
   * @return	string  $value:
   * @access private
   * @version 4.0.0
   * @since   3.1.0
   */
  private function _t3FlexformValue( $sheet, $field )
  {
    $value = $this->_conf[ 'flexform.' ][ $sheet . '.' ][ $field ];

    if ( $value !== NULL )
    {
      return $value;
    }

    if ( isset( $this->_conf[ 'flexform.' ][ $sheet . '.' ][ $field ] ) )
    {
      return $value;
    }

    var_dump( __METHOD__, __LINE__, 'FATAL ERROR: "' . $sheet . '.' . $field . '" isn\'t set!' );
    die();
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
   * setCObj( ) : set the current t3 cObject
   *
   * @param   object $cObj:  current t3 cObject
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  public function setCObj( $cObj )
  {
    $this->_cObj = $cObj;
  }

  /**
   * setConf( ) : set the global private var $conf
   *
   * @param   array $conf:  TypoScript configuration of the current page
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  public function setConf( $conf )
  {
    $this->_conf = $conf;
  }

//  /**
//   * setObjFlexform( ) : set the global private var $objFlexform
//   *
//   * @param   object $objFlexform:  Flexform object: PDF Controller Interface
//   * @return	void
//   * @access private
//   * @version 3.1.0
//   * @since   3.1.0
//   */
//  public function setObjFlexform( $objFlexform )
//  {
//    $this->objFlexform = $objFlexform;
//  }
//
//  /**
//   * setSettings( ) : set the global private var $conf
//   *
//   * @param   array $conf:  TypoScript configuration of the current page
//   * @return	void
//   * @access private
//   * @version 4.0.0
//   * @since   4.0.0
//   */
//  public function setSettings( $settings )
//  {
//    $this->settings = $settings;
//  }
}

/**
 * Class for rendering a HTML page by TCPDF methods
 *
 * @package TYPO3
 * @subpackage pdfcontroller
 * @author Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @version 4.0.0
 * @since 3.1.0
 */
class PdfController extends ActionController
{

  private $_listOfFonts = null; // [array] list of current fonts
//  private $_pObj = null;      // [object] The parent object
  private $_tcpdf = null;     // [object] The t3 tcpdf object
  private $_tplIdLast = 0;    // [int] number of pages of the pdf template file

  /**
   * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
   * @inject
   */
  protected $configurationManager;

  /**
   * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
   * @inject
   */
  protected $objectManager;

  /**
   * @var boolean   DRS should display all prompts
   */
  private $_bDrsAll = FALSE;

  /**
   * @var boolean   DRS should display error prompts
   */
  private $_bDrsError = FALSE;

  /**
   * @var boolean   DRS should display info prompts
   */
  private $_bDrsInfo = FALSE;

  /**
   * @var boolean   DRS should display session prompts
   */
  private $_bDrsSession = FALSE;

  /**
   * @var boolean   DRS should display warning prompts
   */
  private $_bDrsWarn = FALSE;

  /**
   * @var cObject
   */
  private $_cObj;

  /**
   * @var TypoScript configuration
   */
  private $_conf;

  /**
   * @var TypoScript configuration
   */
  private $_drsKey = 'Netzmacher\\Pdfcontroller\\Controller\\PdfController';

  /**
   * rendererAction( ) : Create PDF from HTML (using TCPDF through t3_tcpdf extension)
   *          * Provides a PDF file for download
   *          * It prints the HTML content in debug mode
   *
   * @return void
   * @access public
   * @version 4.0.0
   * @since 3.1.0
   */
  public function rendererAction()
  {
    $this->_init();
//var_dump( __METHOD__, __LINE__, $this->_conf);
//die();
    $mode = PdfController::_ffValue( 'debugging', 'mode' );
    if ( $mode == 'tsDisplay' )
    {
      PdfController::_tsDisplayDie();
    }
//var_dump( __METHOD__, __LINE__, $this->_conf);
//die();

    PdfController::_pdfTcpdfInstance();

    // Reset PDF defaults
    PdfController::_pdfReset();

    // Properties
    PdfController::_pdfPageProperties();
    PdfController::_pdfFont();
    PdfController::_pdfImages();
    PdfController::_pdfLanguage();

    // Debugging
    // CSS
    switch ( $mode )
    {
      case('tsDisplay'):
        var_dump( __METHOD__, __LINE__, 'FATAL ERROR!' );
        die();
      case('testTCPDF061HTML'):
      case('testTCPDF061PDF'):
        // Follow the workflow
        break;
      case('production'):
      case('testDefaultHTML'):
      default:
        // CSS
        PdfController::_pdfCSS();
        break;
    }

    // Header, Footer
    PdfController::_pdfHeader();
    PdfController::_pdfFooter();

    // Use PDF template file
    PdfController::_pdfTemplate();
    // Add page
    $this->_tcpdf->AddPage();
    //PdfController::_pdfAddPage();
    // Add content
    $content = PdfController::_pdfBody();

    // reset pointer to the last page
    $this->_tcpdf->lastPage();

    // Debugging
    switch ( $mode )
    {
      case('testDefaultHTML'):
      case('testTCPDF061HTML'):
        echo $content;
        break;
      case('production'):
      case('testTCPDF061PDF'):
      default:
        // Provide a PDF file
        PdfController::_pdfOutputAndExit();
        break;
    }
    exit();
  }

  /**
   * _ffValue( ) : Get value from the flexform
   *
   * @param   string	$sheet:
   * @param   string	$field:
   * @return	string  $value:
   * @access private
   * @version 4.0.6
   * @since   3.1.0
   */
  private function _ffValue( $sheet, $field )
  {
    $value = $this->_conf[ 'flexform.' ][ $sheet . '.' ][ $field ];

    if ( $value !== NULL )
    {
      return $value;
    }

    if ( isset( $this->_conf[ 'flexform.' ][ $sheet . '.' ][ $field ] ) )
    {
      return $value;
    }

    $prompt = 'FATAL ERROR: "' . $sheet . '.' . $field . '" isn\'t set!<br />'
            . 'Save the Plugin "PDF Controller User Interface" and reload this page.';
    var_dump( __METHOD__, __LINE__, $prompt );
    die();
  }

  /**
   * _fontCopyToTcpdf() :
   *
   * @param string $srce  : Path to the source with filename but extension
   * @param string $dest  : Path to the destination with filename but extension
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _fontCopyToTcpdf( $srce, $dest )
  {
    $extensions = array(
      'ctg.z',
      'php',
      'z'
    );

    foreach ( $extensions as $extension )
    {
      if ( !copy( $srce . '.' . $extension, $dest . '.' . $extension ) )
      {
        echo 'ERROR: copy from ' . $srce . '.' . $extension . ' to ', $dest . '.' . $extension . ' wasn\'t possible.<br />';
        echo __METHOD__ . ' (#' . __LINE__ . ')<br />';
        echo '<br />';
        echo 'Sorry for the trouble. PDF Controller.<br />';
      }
    }
  }

//  /**
//   * _pdfAddPage( ) :
//   *
//   * @return	void
//   * @version    3.1.0
//   * @since      3.1.0
//   */
//  private function _pdfAddPage()
//  {
//    PdfController::_pdfAddPageWiTemplate();
//    PdfController::_pdfAddPageWoTemplate();
//  }
//
//  /**
//   * _pdfAddPageWiTemplate( ) :
//   *
//   * @param	object $_tcpdf: TCPDF object
//   * @return	void
//   * @version    3.1.0
//   * @since      3.1.0
//   */
//  private function _pdfAddPageWiTemplate()
//  {
//
//    if ( $this->_tplIdLast < 1 )
//    {
//      return;
//    }
//
//
//    $nextTemplatePage = $this->_tcpdf->getPage() + 1;
//    if ( $nextTemplatePage > $this->_tplIdLast )
//    {
//      $nextTemplatePage = $this->_tplIdLast;
//    }
//
//    // get id of the next template page
//
//    $templateId = $this->_tcpdf->importPage( $nextTemplatePage );
////var_dump( __METHOD__, __LINE__, $templateId);
//    // get the size of the template
//
//    $size = $this->_tcpdf->getTemplateSize( $templateId );
//
//    // create a page (landscape or portrait depending on the imported template size)
//    if ( $size[ 'w' ] > $size[ 'h' ] )
//    {
//      $this->_tcpdf->AddPage( 'L', array( $size[ 'w' ], $size[ 'h' ] ) );
//    }
//    else
//    {
//      $this->_tcpdf->AddPage( 'P', array( $size[ 'w' ], $size[ 'h' ] ) );
//    }
//
//
//    // use the template
//    // Abscissa of the upper-left corner.
//    $x = 0;
//    // Ordinate of the upper-left corner.
//    $y = 0;
//    // Width of the template in the page. If not specified or equal to zero, it is automatically calculated.
//    $w = 0;
//    // Height of the template in the page. If not specified or equal to zero, it is automatically calculated.
//    $h = 0;
//    // If this parameter is set to true the page size will be adjusted to the size of the imported page.
//    $adjustPageSize = true;
//
//    $this->_tcpdf->useTemplate( $templateId, $x, $y, $w, $h, $adjustPageSize );
//  }
//
//  /**
//   * _pdfAddPageWoTemplate( ) :
//   *
//   * @param	object $_tcpdf: TCPDF object
//   * @return	void
//   * @version    3.1.0
//   * @since      3.1.0
//   */
//  private function _pdfAddPageWoTemplate()
//  {
//    if ( $this->_tplIdLast > 0 )
//    {
//      return;
//    }
//
//    $this->_tcpdf->AddPage();
//  }

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
    $this->_initDrs();
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
   * _initDrs( ) :
   *
   * @return void
   * @access private
   * @internal #i0014
   * @version 4.0.4
   * @since 4.0.4
   */
  private function _initDrs()
  {
    $drs = PdfController::_ffValue( 'debugging', 'drs' );
    switch ( $drs )
    {
      case('all'):
        $this->_initDrsAll();
        return;
      case('session'):
        $this->_initDrsSession();
        return;
      case('disabled'):
      default:
        return;
    }
  }

  /**
   * _initDrsAll( ) :
   *
   * @return void
   * @access private
   * @version 4.0.4
   * @since 4.0.4
   */
  private function _initDrsAll()
  {
    $this->_bDrsAll = true;
    $this->_bDrsError = true;
    $this->_bDrsWarn = true;
    $this->_bDrsInfo = true;

    $bPrompt = FALSE;
    $this->_initDrsSession( $bPrompt );

    $prompt = 'All prompts are enabled.';
    GeneralUtility::devLog( '[INFO/DRS] ' . $prompt, $this->_drsKey, 0 );
  }

  /**
   * _initDrsSession( ) :
   *
   * @return void
   * @access private
   * @version 4.0.4
   * @since 4.0.4
   */
  private function _initDrsSession( $bPrompt = TRUE )
  {
    $this->_bDrsSession = true;

    if ( !$bPrompt )
    {
      return;
    }

    $prompt = 'Session-management is enabled.';
    GeneralUtility::devLog( '[INFO/DRS] ' . $prompt, $this->_drsKey, 0 );
  }

  /**
   * _pdfBody() :
   *
   * @return	string  $content : HTML code
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfBody()
  {
    $mode = PdfController::_ffValue( 'debugging', 'mode' );
    switch ( $mode )
    {
      case('testTCPDF061HTML'):
      case('testTCPDF061PDF'):
        $content = PdfController::_pdfBodyTCPDF061();
        break;
      case('production'):
      case('testDefaultHTML'):
      default:
        $content = PdfController::_pdfBodyTypenumPrint();
        break;
    }

    $align = 'L'; // L : left align, C : center, R : right align
    $autopadding = true;
    $border = 0;
    $fill = 0;
    $h = 0;
    $ln = 1;
    $reseth = true;
    $w = 0;
    $x = '';
    $y = '';

    $this->_tcpdf->writeHTMLCell( $w, $h, $x, $y, $content, $border, $ln, $fill, $reseth, $align, $autopadding );
    return $content;
  }

  /**
   * _pdfBodyTCPDF061() :
   *
   * @return	string  $content  : TCPDF example 061
   *                              See
   *                              * http://www.tcpdf.org/examples/example_061.phps
   *                              * http://www.tcpdf.org/examples/example_061.pdf
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfBodyTCPDF061()
  {
    $content = '<!-- EXAMPLE OF CSS STYLE -->
<style>
    h1 {
        color: navy;
        font-family: times;
        font-size: 24pt;
        text-decoration: underline;
    }
    p.first {
        color: #003300;
        font-family: helvetica;
        font-size: 12pt;
    }
    p.first span {
        color: #006600;
        font-style: italic;
    }
    p#second {
        color: rgb(00,63,127);
        font-family: times;
        font-size: 12pt;
        text-align: justify;
    }
    p#second > span {
        background-color: #FFFFAA;
    }
    table.first {
        color: #003300;
        font-family: helvetica;
        font-size: 8pt;
        border-left: 3px solid red;
        border-right: 3px solid #FF00FF;
        border-top: 3px solid green;
        border-bottom: 3px solid blue;
        background-color: #ccffcc;
    }
    td {
        border: 2px solid blue;
        background-color: #ffffee;
    }
    td.second {
        border: 2px dashed green;
    }
    div.test {
        color: #CC0000;
        background-color: #FFFF66;
        font-family: helvetica;
        font-size: 10pt;
        border-style: solid solid solid solid;
        border-width: 2px 2px 2px 2px;
        border-color: green #FF00FF blue red;
        text-align: center;
    }
    .lowercase {
        text-transform: lowercase;
    }
    .uppercase {
        text-transform: uppercase;
    }
    .capitalize {
        text-transform: capitalize;
    }
</style>

<h1 class="title">Example of <i style="color:#990000">XHTML + CSS</i></h1>

<p class="first">Example of paragraph with class selector. <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sed imperdiet lectus. Phasellus quis velit velit, non condimentum quam. Sed neque urna, ultrices ac volutpat vel, laoreet vitae augue. Sed vel velit erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Cras eget velit nulla, eu sagittis elit. Nunc ac arcu est, in lobortis tellus. Praesent condimentum rhoncus sodales. In hac habitasse platea dictumst. Proin porta eros pharetra enim tincidunt dignissim nec vel dolor. Cras sapien elit, ornare ac dignissim eu, ultricies ac eros. Maecenas augue magna, ultrices a congue in, mollis eu nulla. Nunc venenatis massa at est eleifend faucibus. Vivamus sed risus lectus, nec interdum nunc.</span></p>

<p id="second">Example of paragraph with ID selector. <span>Fusce et felis vitae diam lobortis sollicitudin. Aenean tincidunt accumsan nisi, id vehicula quam laoreet elementum. Phasellus egestas interdum erat, et viverra ipsum ultricies ac. Praesent sagittis augue at augue volutpat eleifend. Cras nec orci neque. Mauris bibendum posuere blandit. Donec feugiat mollis dui sit amet pellentesque. Sed a enim justo. Donec tincidunt, nisl eget elementum aliquam, odio ipsum ultrices quam, eu porttitor ligula urna at lorem. Donec varius, eros et convallis laoreet, ligula tellus consequat felis, ut ornare metus tellus sodales velit. Duis sed diam ante. Ut rutrum malesuada massa, vitae consectetur ipsum rhoncus sed. Suspendisse potenti. Pellentesque a congue massa.</span></p>

<div class="test">example of DIV with border and fill.
<br />Lorem ipsum dolor sit amet, consectetur adipiscing elit.
<br /><span class="lowercase">text-transform <b>LOWERCASE</b> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</span>
<br /><span class="uppercase">text-transform <b>uppercase</b> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</span>
<br /><span class="capitalize">text-transform <b>cAPITALIZE</b> Lorem ipsum dolor sit amet, consectetur adipiscing elit.</span>
</div>

<br />

<table class="first" cellpadding="4" cellspacing="6">
 <tr>
  <td width="30" align="center"><b>No.</b></td>
  <td width="140" align="center" bgcolor="#FFFF00"><b>XXXX</b></td>
  <td width="140" align="center"><b>XXXX</b></td>
  <td width="80" align="center"> <b>XXXX</b></td>
  <td width="80" align="center"><b>XXXX</b></td>
  <td width="45" align="center"><b>XXXX</b></td>
 </tr>
 <tr>
  <td width="30" align="center">1.</td>
  <td width="140" rowspan="6" class="second">XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX</td>
  <td width="140">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td width="80">XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr>
  <td width="30" align="center" rowspan="3">2.</td>
  <td width="140" rowspan="3">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr>
  <td width="80">XXXX<br />XXXX<br />XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr>
  <td width="80" rowspan="2" >XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr>
  <td width="30" align="center">3.</td>
  <td width="140">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
 <tr bgcolor="#FFFF80">
  <td width="30" align="center">4.</td>
  <td width="140" bgcolor="#00CC00" color="#FFFF00">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td width="80">XXXX<br />XXXX</td>
  <td align="center" width="45">XXXX<br />XXXX</td>
 </tr>
</table>';
    return $content;
  }

  /**
   * _pdfBodyTypenumPrint( )  :
   *
   * @return	string  $content  : Content of the page with the typeNum pdfPrint
   * @access private
   * @version 4.0.4
   * @since   3.1.0
   */
  private function _pdfBodyTypenumPrint()
  {
    global $GLOBALS;

    // #i0009, 150729, dwildt, +
    $feSessionKey = NULL;
    if ( $GLOBALS[ 'TSFE' ]->loginUser )
    {
      $feSessionKey = '&FE_SESSION_KEY=' .
              rawurlencode(
                      $GLOBALS[ 'TSFE' ]->fe_user->id .
                      '-' .
                      md5(
                              $GLOBALS[ 'TSFE' ]->fe_user->id .
                              '/' .
                              $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SYS' ][ 'encryptionKey' ]
                      )
      );
    }

    $conf = array(
      'additionalParams' => '&type=' . ( int ) $this->_conf[ 'typeNum.' ][ 'print' ] . $feSessionKey,
      'forceAbsoluteUrl' => 1,
      'parameter' => $GLOBALS[ 'TSFE' ]->id,
      'returnLast' => 'url',
      'useCacheHash' => 0,
    );
    $url = $GLOBALS[ 'TSFE' ]->cObj->typoLink( null, $conf );

    // #i0014, 150817, dwildt, +
    if ( $this->_bDrsSession )
    {
//      $prompt = $feSessionKey;
//      if ( empty( $prompt ) )
//      {
//        $prompt = 'Any FE_SESSION_KEY isn\'t given.';
//      }
//      GeneralUtility::devLog( '[INFO/SESSION] ' . $prompt, $this->_drsKey, 0 );
      GeneralUtility::devLog( '[INFO/SESSION] ' . $url, $this->_drsKey, 0 );
    }

//    // create curl resource
//    $ch = curl_init();
//    // set url
//    curl_setopt( $ch, CURLOPT_URL, $url );
//    //return the transfer as a string
//    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
//    // return without any header
//    //curl_setopt( $ch, CURLOPT_HEADER, 0 );
//    // $output contains the output string
//    $content = curl_exec( $ch );
//    // close curl resource to free up system resources
//    curl_close( $ch );
//    var_dump( __METHOD__, __LINE__, $GLOBALS[ 'TSFE' ]->fe_user, $GLOBALS[ 'TSFE' ]->fe_user->id, $url, $content );
//    exit;

    $content = file_get_contents( $url );
//    var_dump( __METHOD__, __LINE__, $GLOBALS[ 'TSFE' ]->fe_user, $GLOBALS[ 'TSFE' ]->fe_user->id, $url, $content );
//    exit;
    return $content;
  }

  /**
   * _pdfCSS() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfCSS()
  {
    PdfController::_pdfCSSMargin();
    PdfController::_pdfCSSTags();
  }

  /**
   * _pdfCSSTags()  :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfCSSTags()
  {
    PdfController::_pdfCSSTagsA();
  }

  /**
   * _pdfCSSTagsA()  :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfCSSTagsA()
  {
    $ffRgb = PdfController::_ffValue( 'tags', 'aRgb' );
    $ffFontstyle = PdfController::_ffValue( 'tags', 'aFontstyle' );
    list( $r, $g, $b ) = explode( ',', $ffRgb );
    $rgb = array(
      trim( $r ),
      trim( $g ),
      trim( $b )
    );
    $this->_tcpdf->setHtmlLinksStyle( $rgb, $ffFontstyle );
  }

  /**
   * _pdfCSSMargin()  : Set the vertical space above and below tags.
   *                    See details at: _pdfCSSMargintag()
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfCSSMargin()
  {
    $tagvs = array();
//var_dump(__METHOD__, __LINE__, $this->_conf[ 'pdf.' ][ 'css.' ][ 'margin.' ] );
    $tags = $this->_conf[ 'pdf.' ][ 'css.' ][ 'margin.' ];
    foreach ( array_keys( $tags ) as $tag )
    {
      if ( trim( $tag, '.' ) == $tag )
      {
        continue;
      }
      $tag = trim( $tag, '.' );
      $tagvs = $tagvs + ( array ) PdfController::_pdfCSSMarginTag( $tag );
    }
    //var_dump( __METHOD__, __LINE__, $tagvs );
    $this->_tcpdf->setHtmlVSpace( $tagvs );
  }

  /**
   * _pdfCSSMarginTag() : TCPDF doesn't support the CSS property margin, but enables it by setHtmlVSpace()
   *
   *                      See:
   *                      * http://www.tcpdf.org/examples/example_061.pdf
   *                      * http://www.tcpdf.org/examples/example_061.phps
   *                      * http://www.tcpdf.org/doc/code/classTCPDF.html#a71cec8b4c54a78429640e4767648e73e
   *                      Set the vertical spaces for HTML tags.
   *                      The array must have the following structure (example):
   *                      $tagvs = array('h1' => array(0 => array('h' => '', 'n' => 2), 1 => array('h' => 1.3, 'n' => 1)));
   *                      * The first array level contains the tag names,
   *                      * the second level contains 0 for opening tags or 1 for closing tags,
   *                      * the third level contains the vertical space unit (h) and the number spaces to add (n).
   *                      If the h parameter is not specified, default values are used.
   *
   * @param string $tag :
   * @return	array $tagvs  :
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfCSSMarginTag( $tag )
  {
    //var_dump( __METHOD__, __LINE__, $tag, $this->_conf[ 'pdf.'][ 'css.'][ 'margin.'][ $tag . '.' ] );

    $tagvs = array(
      $tag => array(
        0 => array(
          'h' => $this->_conf[ 'pdf.' ][ 'css.' ][ 'margin.' ][ $tag . '.' ][ 'top.' ][ 'value' ],
          'n' => $this->_conf[ 'pdf.' ][ 'css.' ][ 'margin.' ][ $tag . '.' ][ 'top.' ][ 'times' ]
        ),
        1 => array(
          'h' => $this->_conf[ 'pdf.' ][ 'css.' ][ 'margin.' ][ $tag . '.' ][ 'bottom.' ][ 'value' ],
          'n' => $this->_conf[ 'pdf.' ][ 'css.' ][ 'margin.' ][ $tag . '.' ][ 'bottom.' ][ 'times' ]
        )
      )
    );

    return $tagvs;
  }

  /**
   * _pdfFilename() :
   *
   * @return	string  $filename :
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFilename()
  {
    $confName = $this->_conf[ 'pdf.' ][ 'filename' ];
    $confObj = $this->_conf[ 'pdf.' ][ 'filename.' ];
    $filename = PdfController::_zzCObjGetSingle( $confName, $confObj );
    return $filename;
  }

  /**
   * _pdfFont() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFont()
  {
    $this->_tcpdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
    PdfController::_pdfFontAddFonts();
    PdfController::_pdfFontBody();
    PdfController::_pdfFontHeader();
    PdfController::_pdfFontFooter();
  }

  /**
   * _pdfFontAddFonts() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFontAddFonts()
  {
    PdfController::_pdfFontAddFontsRegisterTcpdf();
    PdfController::_pdfFontAddFontsCopyFromPdfControllerFonts();
    PdfController::_pdfFontAddFontsCopyFromDirectory();
  }

  /**
   * _pdfFontAddFontsCopyFromDirectory() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFontAddFontsCopyFromDirectory()
  {
    $myDirectory = PdfController::_ffValue( 'fonts', 'path' );
    $srcePath = PATH_site . $myDirectory . '/';

    // Source directory doesn't exist or T3 TCPDF isn't loaded
    switch ( true )
    {
      case (empty( $myDirectory ) ):
      case (!file_exists( $srcePath ) ):
      case (!ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) ):
        return;
      default:
        // follow the workflow
        break;
    }

    $style = '';
    $srcefile = '';
    $subset = '';

    $destPath = ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';
    $srceFilesPhp = GeneralUtility::getFilesInDir( $srcePath, 'php' );

    foreach ( $srceFilesPhp as $srceFilePhp )
    {
      list($srceFile) = explode( '.', $srceFilePhp );
      switch ( true )
      {
        case( file_exists( $destPath . $srceFilePhp )):
        case ( in_array( $srceFile, $this->_listOfFonts ) ):
          continue 2;
        default:
          // follow the workflow
          break;
      }
      $dest = $destPath . $srceFile;
      $srce = $srcePath . $srceFile;
      PdfController::_fontCopyToTcpdf( $srce, $dest );
      $family = $destPath . $srceFile;
      $this->_tcpdf->AddFont( $family, $style, $srcefile, $subset );
      $this->_listOfFonts[] = $srceFile;
    }
  }

  /**
   * _pdfFontAddFontsCopyFromPdfControllerFonts() : Add fonts from extension pdfcontroller_fonts
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFontAddFontsCopyFromPdfControllerFonts()
  {
    // PDF Controller Fonts or T3 TCPDF aren't loaded
    switch ( true )
    {
      case (!ExtensionManagementUtility::isLoaded( 'pdfcontroller_fonts' ) ):
      case (!ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) ):
        return;
      default:
        // follow the workflow
        break;
    }

    $style = '';
    $srcefile = '';
    $subset = '';

    $destPath = ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';

    $srcePath = ExtensionManagementUtility::extPath( 'pdfcontroller_fonts' ) . 'fonts/';
    $srceFilesPhp = GeneralUtility::getFilesInDir( $srcePath, 'php' );

    foreach ( $srceFilesPhp as $srceFilePhp )
    {
      list($srceFile) = explode( '.', $srceFilePhp );
      switch ( true )
      {
        case( file_exists( $destPath . $srceFilePhp )):
        case ( in_array( $srceFile, $this->_listOfFonts ) ):
          continue 2;
        default:
          // follow the workflow
          break;
      }
      $dest = $destPath . $srceFile;
      $srce = $srcePath . $srceFile;
      PdfController::_fontCopyToTcpdf( $srce, $dest );
      $family = $destPath . $srceFile;
      $this->_tcpdf->AddFont( $family, $style, $srcefile, $subset );
      $this->_listOfFonts[] = $srceFile;
    }
  }

  /**
   * _pdfFontAddFontsRegisterTcpdf() : Add fonts from extension pdfcontroller_fonts
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFontAddFontsRegisterTcpdf()
  {
    // PDF Controller Fonts isn't loaded
    if ( !ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) )
    {
      return;
    }

    $fontPath = ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';
    $fontFilesPhp = GeneralUtility::getFilesInDir( $fontPath, 'php' );

    foreach ( $fontFilesPhp as $fontFilePhp )
    {
      switch ( $fontFilePhp )
      {
        case('cid0cs.php'):
        case('cid0ct.php'):
        case('cid0jp.php'):
        case('cid0kr.php'):
          continue 2;
        default:
          // follow the workflow
          break;
      }
      list($fontFile) = explode( '.', $fontFilePhp );
      $this->_listOfFonts[] = $fontFile;
    }
  }

  /**
   * _pdfFontBody() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFontBody()
  {
    if ( !ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) )
    {
      return;
    }

    $fontPath = ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';

    $family = PdfController::_ffValue( 'fonts', 'familybody' );
    if ( empty( $family ) )
    {
      $family = 'helvetica';
    }
    $family = $fontPath . $family;

    $fontfile = '';
    $out = true;
    $size = null;
    $subset = 'default';
    $style = '';

    $this->_tcpdf->SetFont( $family, $style, $size, $fontfile, $subset, $out );
  }

  /**
   * _pdfFontFooter() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFontFooter()
  {
    if ( !ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) )
    {
      return;
    }

    $fontPath = ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';

    $family = PdfController::_ffValue( 'fonts', 'familyfooter' );
    if ( empty( $family ) )
    {
      $family = 'helvetica';
    }

    $font = array(
      'family' => $fontPath . $family,
      'size' => null,
      'style' => ''
    );
    $this->_tcpdf->SetFooterFont( $font );
  }

  /**
   * _pdfFontHeader() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFontHeader()
  {
    if ( !ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) )
    {
      return;
    }

    $fontPath = ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';

    $family = PdfController::_ffValue( 'fonts', 'familyheader' );
    if ( empty( $family ) )
    {
      $family = 'helvetica';
    }

    $font = array(
      'family' => $fontPath . $family,
      'size' => null,
      'style' => ''
    );
    $this->_tcpdf->SetFooterFont( $font );
  }

  /**
   * _pdfFooter() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFooter()
  {
    PdfController::_pdfFooterContent();
    PdfController::_pdfFooterMargin();
  }

  /**
   * _pdfFooterContent() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFooterContent()
  {

  }

  /**
   * _pdfFooterMargin() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfFooterMargin()
  {
//    $this->_tcpdf->SetFooterMargin( PDF_MARGIN_FOOTER );
  }

  /**
   * _pdfHeader() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfHeader()
  {
    PdfController::_pdfHeaderContent();
    PdfController::_pdfHeaderMargin();
  }

  /**
   * _pdfHeaderContent() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfHeaderContent()
  {

  }

  /**
   * _pdfHeaderMargin() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfHeaderMargin()
  {
//    $this->_tcpdf->SetHeaderMargin( PDF_MARGIN_HEADER );
  }

  /**
   * _pdfLanguage() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfLanguage()
  {
//    $language = array(
//      'a_meta_charset' => 'UTF-8',
//      'a_meta_dir' => 'rtl',
//      'a_meta_language' => 'fa',
//      'w_page' => 'page'
//    );
//    $this->_tcpdf->setLanguageArray( $language );
  }

  /**
   * _pdfImages() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfImages()
  {
    PdfController::_pdfImagesScale();
  }

  /**
   * _pdfImagesScale() : set image scale factor
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfImagesScale()
  {
    $scale = PdfController::_ffValue( 'images', 'scale' );
    $this->_tcpdf->setImageScale( $scale );
  }

  /**
   * _pdfOutputAndExit( ) : exit!
   *
   * @return
   * @access private
   * @version 3.1.0
   * @since 3.1.0
   */
  private function _pdfOutputAndExit()
  {
    $outMode = 'D'; // D: Download
    $this->_tcpdf->Output( PdfController::_pdfFilename(), $outMode );
    exit();
  }

  /**
   * _pdfPageProperties()  :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfPageProperties()
  {
    PdfController::_pdfPagePropertiesAuthor();
    PdfController::_pdfPagePropertiesKeywords();
    PdfController::_pdfPagePropertiesSubject();
    PdfController::_pdfPagePropertiesTitle();
  }

  /**
   * _pdfPagePropertiesAuthor()  : Sets the author property of the pdf file
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfPagePropertiesAuthor()
  {
    $confName = $this->_conf[ 'pdf.' ][ 'pageproperties.' ][ 'documentAuthor' ];
    $confObj = $this->_conf[ 'pdf.' ][ 'pageproperties.' ][ 'documentAuthor.' ];
//    $confName = "CONTENT";
//    $confObj = array(
//      "table" => "pages",
//      "renderObj" => "TEXT",
//      "renderObj." => array(
//        "field" => "author",
//      ),
//      "select." => array(
//        "where" => "1 OR (uid = {page:uid} AND pid >= 0)",
//        "where." => array(
//          "insertData" => "1",
//        ),
//        "max" => "1",
//      ),
//    );
//    var_dump( __METHOD__, __LINE__, $confName, $confObj, $author );
//    $author = PdfController::_zzCObjGetSingle( $confName, $confObj );
//    var_dump( __METHOD__, __LINE__, $confName, $confObj, $author );
//    $confName = "CONTENT";
//    $confObj = array(
//      "table" => "pages",
//      "renderObj" => array(
//        "field" => "author",
//        "_typoScriptNodeValue" => "TEXT",
//      ),
//      "select" => array(
//        "where" => array(
//          "insertData" => "1",
//          "_typoScriptNodeValue" => "1 OR (uid = {page:uid} AND pid >= 0)",
//        ),
//        "max" => "1",
//      ),
//      "_typoScriptNodeValue" => "CONTENT",
//    );
    $author = PdfController::_zzCObjGetSingle( $confName, $confObj );
//    var_dump( __METHOD__, __LINE__, $confName, $confObj, $author );
    $this->_tcpdf->SetAuthor( $author );
  }

  /**
   * _pdfPagePropertiesKeywords()  : Sets the author property of the pdf file
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfPagePropertiesKeywords()
  {
    $confName = $this->_conf[ 'pdf.' ][ 'pageproperties.' ][ 'documentKeywords' ];
    $confObj = $this->_conf[ 'pdf.' ][ 'pageproperties.' ][ 'documentKeywords.' ];
    $this->_tcpdf->SetKeywords( PdfController::_zzCObjGetSingle( $confName, $confObj ) );
  }

  /**
   * _pdfPagePropertiesSubject()  : Sets the author property of the pdf file
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfPagePropertiesSubject()
  {
    $confName = $this->_conf[ 'pdf.' ][ 'pageproperties.' ][ 'documentSubject' ];
    $confObj = $this->_conf[ 'pdf.' ][ 'pageproperties.' ][ 'documentSubject.' ];
    $this->_tcpdf->SetSubject( PdfController::_zzCObjGetSingle( $confName, $confObj ) );
  }

  /**
   * _pdfPagePropertiesTitle()  : Sets the author property of the pdf file
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfPagePropertiesTitle()
  {
    $confName = $this->_conf[ 'pdf.' ][ 'pageproperties.' ][ 'documentTitle' ];
    $confObj = $this->_conf[ 'pdf.' ][ 'pageproperties.' ][ 'documentTitle.' ];
    $this->_tcpdf->SetTitle( PdfController::_zzCObjGetSingle( $confName, $confObj ) );
  }

  /**
   * _pdfReset( ) :
   *
   * @return void
   * @access private
   * @version 3.1.0
   * @since 3.1.0
   */
  private function _pdfReset()
  {
    PdfController::_pdfResetMargins();
  }

  /**
   * _pdfResetMargins( ) :
   *
   * @return void
   * @access private
   * @version 3.1.0
   * @since 3.1.0
   */
  private function _pdfResetMargins()
  {
    $marginright = ( int ) PdfController::_ffValue( 'template', 'marginright' );
    $marginleft = ( int ) PdfController::_ffValue( 'template', 'marginleft' );

    $this->_tcpdf->SetTopMargin( 0 );
    $this->_tcpdf->SetRightMargin( $marginright - 1.000125 );  // Definition for all pages
    $this->_tcpdf->SetLeftMargin( $marginleft );               // Definition for all pages
    $this->_tcpdf->SetAutoPageBreak( true, 0 );
  }

  /**
   * _pdfTemplate()  :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfTemplate()
  {
    PdfController::_pdfTemplateFile();
  }

  /**
   * _pdfTemplateFile()  :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfTemplateFile()
  {
    $ffValue = PdfController::_ffValue( 'template', 'filepath' );
    // get the page count
    $this->_tplIdLast = $this->_tcpdf->setSourceFile( $ffValue );
  }

  /**
   * _pdfTcpdfInstance()  :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfTcpdfInstance()
  {

    $this->_tcpdf = GeneralUtility::makeInstance(
                    'Netzmacher\Pdfcontroller\Controller\PdfControllerFpdi', 'P', 'mm', 'A4', true, 'UTF-8', false );
    $this->_tcpdf->setCObj( $this->_cObj );
    $this->_tcpdf->setConf( $this->_conf );
//    $this->_tcpdf->setObjFlexform( $this->_pObj->objFlexform );
//    $this->_tcpdf->setSettings( $this->settings );
  }

  /**
   * _tsDisplayDie( ) :
   *
   * @return void
   * @access private
   * @version 4.0.0
   * @since 4.0.0
   */
  private function _tsDisplayDie()
  {
    echo '<pre>';
    var_dump( __METHOD__, __LINE__, $this->_conf );
    echo '</pre>';
    die();
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

}
