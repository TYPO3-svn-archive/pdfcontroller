<?php

namespace Netzmacher\Pdfcontroller\TcPdf;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
/**
 * Class for overloading TCPDF methods
 *
 * @package TYPO3
 * @subpackage pdfcontroller
 * @author Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @version 3.1.0
 * @since 3.1.0
 */
// Require TCPDF and FPDI
$t3_tcpdf = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'class.tx_t3_tcpdf.php';
$fpdi = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( 'pdfcontroller' ) . 'Resources/Private/FPDI-1.5.4/fpdi.php';
require_once( $t3_tcpdf );
require_once( $fpdi );

class PdfcontrollerFpdi extends \FPDI
{

  private $_tplIdx = null;    // Current page number of the PDF template
  private $_tplIdLast = null; // Last page number of the PDF template
  private $cObj;              // Current t3 cObject
  private $conf;              // TypoScript configuration of the current page
  private $objFlexform;       // Flexform object: PDF Controller Interface

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
    $confName = $this->conf[ 'pdf.' ][ 'footer' ];
    $confObj = $this->conf[ 'pdf.' ][ 'footer.' ];
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
    $cur_y = $this->y;
    $this->SetY( $cur_y );

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
    $this->_pdfTemplate();
    $this->_pdfMargins();

//    exit();
//    $this->SetFont( 'freesans', 'B', 9 );
//    $this->SetTextColor( 255 );
//    $this->SetXY( 60.5, 24.8 );
//    $this->Cell( 0, 8.6, "TCPDF and FPDI" );
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
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _t3FlexformValue( $sheet, $field )
  {
    $value = $this->objFlexform->get_sheet_field( $sheet, $field );
    return $value;
  }

  /**
   * _zzCObjGetSingle( )  : Renders a typoscript property with cObjGetSingle, if it is an array.
   *                        Otherwise returns the property unchanged.
   *
   * @param	string		$cObj_name  : value or the name of the property like TEXT, COA, IMAGE, ...
   * @param	array		$cObj_conf  : null or the configuration of the property
   * @return	string		$value      : unchanged value or rendered typoscript property
   * @access private
   * @version    3.1.0
   * @since      3.1.0
   */
  private function _zzCObjGetSingle( $cObj_name, $cObj_conf )
  {
    switch ( true )
    {
      case( is_array( $cObj_conf ) ):
        $value = $this->cObj->cObjGetSingle( $cObj_name, $cObj_conf );
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
    $this->cObj = $cObj;
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
    $this->conf = $conf;
  }

  /**
   * setObjFlexform( ) : set the global private var $objFlexform
   *
   * @param   object $objFlexform:  Flexform object: PDF Controller Interface
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  public function setObjFlexform( $objFlexform )
  {
    $this->objFlexform = $objFlexform;
  }

}

/**
 * Class for rendering a HTML page by TCPDF methods
 *
 * @package TYPO3
 * @subpackage pdfcontroller
 * @author Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @version 3.1.0
 * @since 3.1.0
 */
class Renderer
{

  private $_listOfFonts = null; // [array] list of current fonts
  private $_pObj = null;      // [object] The parent object
  private $_tcpdf = null;     // [object] The t3 tcpdf object
  private $_tplIdLast = 0;    // [int] number of pages of the pdf template file

  /**
   * pdf( ) : Create PDF from HTML (using TCPDF through t3_tcpdf extension)
   *          * Provides a PDF file for download
   *          * It prints the HTML content in debug mode
   *
   * @param array $conf
   * @param object $_pObj  : The parent object
   * @return void
   * @access public
   * @version 3.1.0
   * @since 3.1.0
   */

  public function pdf( $_pObj )
  {
    $this->_pObj = $_pObj;

    Renderer::_pdfTcpdfInstance();

    // Reset PDF defaults
    Renderer::_pdfReset();

    // Properties
    Renderer::_pdfPageProperties();
    Renderer::_pdfFont();
    Renderer::_pdfImages();
    Renderer::_pdfLanguage();

    // Header, Footer
    Renderer::_pdfHeader();
    Renderer::_pdfFooter();

    // Add page
    Renderer::_pdfTemplate();
    Renderer::_pdfAddPage();

    // Add content
    $content = Renderer::_pdfBody();

//    // Debugging
//    var_dump( __METHOD__, __LINE__, $this->_tcpdf->getPageDimensions() );
//    exit();
    // Provide a PDF file
    Renderer::_pdfOutput();

    // Debug mode
    echo $content;
    exit();
  }

  /**
   * _ffValue( ) : Get value from the flexform
   *
   * @param   string	$sheet:
   * @param   string	$field:
   * @return	string  $value:
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _ffValue( $sheet, $field )
  {
    $value = $this->_pObj->objFlexform->get_sheet_field( $sheet, $field );
    return $value;
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

  /**
   * _pdfAddPage( ) :
   *
   * @return	void
   * @version    3.1.0
   * @since      3.1.0
   */
  private function _pdfAddPage()
  {

    Renderer::_pdfAddPageWiTemplate();
    Renderer::_pdfAddPageWoTemplate();
  }

  /**
   * _pdfAddPageWiTemplate( ) :
   *
   * @param	object $_tcpdf: TCPDF object
   * @return	void
   * @version    3.1.0
   * @since      3.1.0
   */
  private function _pdfAddPageWiTemplate()
  {
    if ( $this->_tplIdLast < 1 )
    {
      return;
    }


    $nextTemplatePage = $this->_tcpdf->getPage() + 1;
    if ( $nextTemplatePage > $this->_tplIdLast )
    {
      $nextTemplatePage = $this->_tplIdLast;
    }

    // get id of the next template page

    $templateId = $this->_tcpdf->importPage( $nextTemplatePage );
//var_dump( __METHOD__, __LINE__, $templateId);
    // get the size of the template

    $size = $this->_tcpdf->getTemplateSize( $templateId );

    // create a page (landscape or portrait depending on the imported template size)
    if ( $size[ 'w' ] > $size[ 'h' ] )
    {
      $this->_tcpdf->AddPage( 'L', array( $size[ 'w' ], $size[ 'h' ] ) );
    }
    else
    {
      $this->_tcpdf->AddPage( 'P', array( $size[ 'w' ], $size[ 'h' ] ) );
    }


    // use the template
    // Abscissa of the upper-left corner.
    $x = 0;
    // Ordinate of the upper-left corner.
    $y = 0;
    // Width of the template in the page. If not specified or equal to zero, it is automatically calculated.
    $w = 0;
    // Height of the template in the page. If not specified or equal to zero, it is automatically calculated.
    $h = 0;
    // If this parameter is set to true the page size will be adjusted to the size of the imported page.
    $adjustPageSize = true;

    $this->_tcpdf->useTemplate( $templateId, $x, $y, $w, $h, $adjustPageSize );
  }

  /**
   * _pdfAddPageWoTemplate( ) :
   *
   * @param	object $_tcpdf: TCPDF object
   * @return	void
   * @version    3.1.0
   * @since      3.1.0
   */
  private function _pdfAddPageWoTemplate()
  {
    if ( $this->_tplIdLast > 0 )
    {
      return;
    }

    $this->_tcpdf->AddPage();
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

    $align = 'L'; // L : left align, C : center, R : right align
    $autopadding = true;
    $content = Renderer::_pdfBodyTypenumPrint();
    //$content = 'Hallo';
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
   * _pdfBodyTypenumPrint( )  :
   *
   * @return	string  $content  : Content of the page with the typeNum pdfPrint
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfBodyTypenumPrint()
  {
    $conf = array(
      'additionalParams' => '&type=' . ( int ) $this->conf[ 'typeNum.' ][ 'print' ],
      'forceAbsoluteUrl' => 1,
      'parameter' => $GLOBALS[ 'TSFE' ]->id,
      'returnLast' => 'url',
      'useCacheHash' => 0,
    );
    $url = $GLOBALS[ 'TSFE' ]->cObj->typoLink( null, $conf );
    $content = file_get_contents( $url );
//var_dump( __METHOD__, __CLASS__, $url );
//exit;
    return $content;
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
    $confName = $this->conf[ 'pdf.' ][ 'filename' ];
    $confObj = $this->conf[ 'pdf.' ][ 'filename.' ];
    $filename = Renderer::zz_cObjGetSingle( $confName, $confObj );
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
    Renderer::_pdfFontAddFonts();
    Renderer::_pdfFontBody();
    Renderer::_pdfFontHeader();
    Renderer::_pdfFontFooter();
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
    Renderer::_pdfFontAddFontsRegisterTcpdf();
    Renderer::_pdfFontAddFontsCopyFromPdfControllerFonts();
    Renderer::_pdfFontAddFontsCopyFromDirectory();
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
    $myDirectory = Renderer::_ffValue( 'fonts', 'path' );
    $srcePath = PATH_site . $myDirectory . '/';

    // Source directory doesn't exist or T3 TCPDF isn't loaded
    switch ( true )
    {
      case (empty( $myDirectory ) ):
      case (!file_exists( $srcePath ) ):
      case (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) ):
        return;
      default:
        // follow the workflow
        break;
    }

    $style = '';
    $srcefile = '';
    $subset = '';

    $destPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';
    $srceFilesPhp = \TYPO3\CMS\Core\Utility\GeneralUtility::getFilesInDir( $srcePath, 'php' );

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
      Renderer::_fontCopyToTcpdf( $srce, $dest );
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
      case (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 'pdfcontroller_fonts' ) ):
      case (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) ):
        return;
      default:
        // follow the workflow
        break;
    }

    $style = '';
    $srcefile = '';
    $subset = '';

    $destPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';

    $srcePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( 'pdfcontroller_fonts' ) . 'fonts/';
    $srceFilesPhp = \TYPO3\CMS\Core\Utility\GeneralUtility::getFilesInDir( $srcePath, 'php' );

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
      Renderer::_fontCopyToTcpdf( $srce, $dest );
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
    if ( !\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) )
    {
      return;
    }

    $fontPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';
    $fontFilesPhp = \TYPO3\CMS\Core\Utility\GeneralUtility::getFilesInDir( $fontPath, 'php' );

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
    if ( !\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) )
    {
      return;
    }

    $fontPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';

    $family = Renderer::_ffValue( 'fonts', 'familybody' );
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
    if ( !\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) )
    {
      return;
    }

    $fontPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';

    $family = Renderer::_ffValue( 'fonts', 'familyfooter' );
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
    if ( !\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) )
    {
      return;
    }

    $fontPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( 't3_tcpdf' ) . 'tcpdf/fonts/';

    $family = Renderer::_ffValue( 'fonts', 'familyheader' );
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
    Renderer::_pdfFooterContent();
    Renderer::_pdfFooterMargin();
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
    Renderer::_pdfHeaderContent();
    Renderer::_pdfHeaderMargin();
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
    Renderer::_pdfImagesScale();
  }

  /**
   * _pdfImagesScale() :
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _pdfImagesScale()
  {
    //set image scale factor
    $this->_tcpdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
  }

  /**
   * _pdfOutput( ) : exit!
   *
   * @return
   * @access private
   * @version 3.1.0
   * @since 3.1.0
   */
  private function _pdfOutput()
  {
    $mode = Renderer::_ffValue( 'debugging', 'mode' );
    switch ( $mode )
    {
      case('test'):
        return;
      case('production'):
      default:
        // follow the workflow
        break;
    }

    $outMode = 'D'; // D: Download
    $this->_tcpdf->Output( Renderer::_pdfFilename(), $outMode );
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
    Renderer::_pdfPagePropertiesAuthor();
    Renderer::_pdfPagePropertiesKeywords();
    Renderer::_pdfPagePropertiesSubject();
    Renderer::_pdfPagePropertiesTitle();
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
    $confName = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentAuthor' ];
    $confObj = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentAuthor.' ];
    $this->_tcpdf->SetAuthor( Renderer::zz_cObjGetSingle( $confName, $confObj ) );
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
    $confName = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentKeywords' ];
    $confObj = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentKeywords.' ];
    $this->_tcpdf->SetKeywords( Renderer::zz_cObjGetSingle( $confName, $confObj ) );
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
    $confName = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentSubject' ];
    $confObj = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentSubject.' ];
    $this->_tcpdf->SetSubject( Renderer::zz_cObjGetSingle( $confName, $confObj ) );
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
    $confName = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentTitle' ];
    $confObj = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentTitle.' ];
    $this->_tcpdf->SetTitle( Renderer::zz_cObjGetSingle( $confName, $confObj ) );
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
    Renderer::_pdfResetMargins();
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
    $marginright = ( int ) Renderer::_ffValue( 'template', 'marginright' );
    $marginleft = ( int ) Renderer::_ffValue( 'template', 'marginleft' );

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
    Renderer::_pdfTemplateFile();
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
    $ffValue = Renderer::_ffValue( 'template', 'filepath' );
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

    $this->_tcpdf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    'Netzmacher\Pdfcontroller\TcPdf\PdfcontrollerFpdi', 'P', 'mm', 'A4', true, 'UTF-8', false );
    $this->_tcpdf->setCObj( $this->_pObj->cObj );
    $this->_tcpdf->setConf( $this->_pObj->conf );
    $this->_tcpdf->setObjFlexform( $this->_pObj->objFlexform );
  }

  /**
   * zz_cObjGetSingle( )  : Renders a typoscript property with cObjGetSingle, if it is an array.
   *                        Otherwise returns the property unchanged.
   *
   * @param	string		$cObj_name  : value or the name of the property like TEXT, COA, IMAGE, ...
   * @param	array		$cObj_conf  : null or the configuration of the property
   * @return	string		$value      : unchanged value or rendered typoscript property
   * @access private
   * @version    3.1.0
   * @since      3.1.0
   */
  private function zz_cObjGetSingle( $cObj_name, $cObj_conf )
  {
    switch ( true )
    {
      case( is_array( $cObj_conf ) ):
        $value = $this->cObj->cObjGetSingle( $cObj_name, $cObj_conf );
        break;
      case(!( is_array( $cObj_conf ) ) ):
      default:
        $value = $cObj_name;
        break;
    }

    return $value;
  }

}
