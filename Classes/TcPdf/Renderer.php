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
 * @version 3.0.0
 * @since 3.0.0
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
   * @version 3.0.0
   * @since   3.0.0
   */

  public function Footer()
  {
    $content = $this->FooterContent();
    $this->FooterWrite( $content );
  }

  /**
   * FooterContent( )  : Returns HTML footer code
   *
   * @return	string  $content  : HTML footer code
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function FooterContent()
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

    return $content;
  }

  /**
   * FooterWrite( )  : Returns HTML footer code
   *
   * @param string  $content  : HTML footer code
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function FooterWrite( $content )
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
   * @version 3.0.0
   * @since   3.0.0
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
   * @version 3.0.0
   * @since   3.0.0
   */
  private function _pdfMargins()
  {
    switch ( true )
    {
      case( $this->_tplIdx >= 2 ):
        $this->_pdfMarginsByFlexform( 'template' );
        break;
      case( $this->_tplIdx == 1 ):
      default:
        $this->_pdfMarginsByFlexform( 'templatefirstpage' );
        break;
    }
  }

  /**
   * _pdfMarginsByFlexform() :
   *
   * @param string $sheet :
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
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
   * @version 3.0.0
   * @since   3.0.0
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
   * @version 3.0.0
   * @since   3.0.0
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
   * @version 3.0.0
   * @since   3.0.0
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
   * @version    3.0.0
   * @since      3.0.0
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
   * @version 3.0.0
   * @since   3.0.0
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
   * @version 3.0.0
   * @since   3.0.0
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
   * @version 3.0.0
   * @since   3.0.0
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
 * @version 3.0.0
 * @since 3.0.0
 */
class Renderer
{

  private $pdfTemplateLastPage = 0;   // [int] number of pages of the pdf template file
  private $pObj = null;               // [object] The parent object
  private $tcpdf = null;              // [object] The t3 tcpdf object

  /**
   * ffValue( ) : Get value from the flexform
   *
   * @param   string	$sheet:
   * @param   string	$field:
   * @return	string  $value:
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */

  private function ffValue( $sheet, $field )
  {
    $value = $this->pObj->objFlexform->get_sheet_field( $sheet, $field );
    return $value;
  }

  /**
   * pdf( ) : Create PDF from HTML (using TCPDF through t3_tcpdf extension)
   *          * Provides a PDF file for download
   *          * It prints the HTML content in debug mode
   *
   * @param array $conf
   * @param object $pObj  : The parent object
   * @return void
   * @access public
   * @version 3.0.0
   * @since 3.0.0
   */
  public function pdf( $pObj )
  {
    $this->pObj = $pObj;

    Renderer::pdfTcpdfInstance();

    // Reset PDF defaults
    Renderer::pdfReset();

    // Properties
    Renderer::pdfProperties();
    Renderer::pdfFont();
    Renderer::pdfImages();
    Renderer::pdfLanguage();

    // Header, Footer
    Renderer::pdfHeader();
    Renderer::pdfFooter();

    // Add page
    Renderer::pdfTemplate();
    Renderer::pdfAddPage();

    // Add content
    $content = Renderer::pdfBody();

//    // Debugging
//    var_dump( __METHOD__, __LINE__, $this->tcpdf->getPageDimensions() );
//    exit();

    // Provide a PDF file
    Renderer::pdfOutput();

    // Debug mode
    echo $content;
    exit();
  }

  /**
   * pdfOutput( ) : exit!
   *
   * @return
   * @access private
   * @version 3.0.0
   * @since 3.0.0
   */
  private function pdfOutput()
  {
    $mode = Renderer::ffValue( 'debugging', 'mode' );
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
    $this->tcpdf->Output( Renderer::pdfFilename(), $outMode );
    exit();
  }

  /**
   * pdfAddPage( ) :
   *
   * @return	void
   * @version    3.0.0
   * @since      3.0.0
   */
  private function pdfAddPage()
  {

    Renderer::pdfAddPageWiTemplate();
    Renderer::pdfAddPageWoTemplate();
  }

  /**
   * pdfAddPageWiTemplate( ) :
   *
   * @param	object $tcpdf: TCPDF object
   * @return	void
   * @version    3.0.0
   * @since      3.0.0
   */
  private function pdfAddPageWiTemplate()
  {
    if ( $this->pdfTemplateLastPage < 1 )
    {
      return;
    }


    $nextTemplatePage = $this->tcpdf->getPage() + 1;
    if ( $nextTemplatePage > $this->pdfTemplateLastPage )
    {
      $nextTemplatePage = $this->pdfTemplateLastPage;
    }

    // get id of the next template page

    $templateId = $this->tcpdf->importPage( $nextTemplatePage );
//var_dump( __METHOD__, __LINE__, $templateId);
    // get the size of the template

    $size = $this->tcpdf->getTemplateSize( $templateId );


    // create a page (landscape or portrait depending on the imported template size)
    if ( $size[ 'w' ] > $size[ 'h' ] )
    {
      $this->tcpdf->AddPage( 'L', array( $size[ 'w' ], $size[ 'h' ] ) );
    }
    else
    {
      $this->tcpdf->AddPage( 'P', array( $size[ 'w' ], $size[ 'h' ] ) );
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

    $this->tcpdf->useTemplate( $templateId, $x, $y, $w, $h, $adjustPageSize );
  }

  /**
   * pdfAddPageWoTemplate( ) :
   *
   * @param	object $tcpdf: TCPDF object
   * @return	void
   * @version    3.0.0
   * @since      3.0.0
   */
  private function pdfAddPageWoTemplate()
  {
    if ( $this->pdfTemplateLastPage > 0 )
    {
      return;
    }

    $this->tcpdf->AddPage();
  }

  /**
   * pdfBody() :
   *
   * @return	string  $content : HTML code
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfBody()
  {

    $align = 'L'; // L : left align, C : center, R : right align
    $autopadding = true;
    $content = Renderer::pdfBodyTypenumPrint();
    //$content = 'Hallo';
    $border = 0;
    $fill = 0;
    $h = 0;
    $ln = 1;
    $reseth = true;
    $w = 0;
    $x = '';
    $y = '';

    $this->tcpdf->writeHTMLCell( $w, $h, $x, $y, $content, $border, $ln, $fill, $reseth, $align, $autopadding );

    return $content;
  }

  /**
   * pdfBodyTypenumPrint( )  :
   *
   * @return	string  $content  : Content of the page with the typeNum pdfPrint
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfBodyTypenumPrint()
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
   * pdfFilename() :
   *
   * @return	string  $filename :
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfFilename()
  {
    $confName = $this->conf[ 'pdf.' ][ 'filename' ];
    $confObj = $this->conf[ 'pdf.' ][ 'filename.' ];
    $filename = Renderer::zz_cObjGetSingle( $confName, $confObj );
    return $filename;
  }

  /**
   * pdfFont() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfFont()
  {
    $this->tcpdf->setFontSubsetting( true );
    $this->tcpdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED ); // courier

    Renderer::pdfFontBody();
  }

  /**
   * pdfFont() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfFontBody()
  {
    $bodyFont = $this->conf[ 'pdfBodyFont' ] ? $this->conf[ 'pdfBodyFont' ] : 'helvetica';
    $bodyFontStyle = $this->conf[ 'pdfBodyFontStyle' ] ? $this->conf[ 'pdfBodyFontStyle' ] : '';
    $bodyFontSize = $this->conf[ 'pdfBodyFontSize' ] ? $this->conf[ 'pdfBodyFontSize' ] : 11;
    $bodyFontFile = $this->conf[ 'pdfBodyFontFile' ] ? $this->conf[ 'pdfBodyFontFile' ] : '';
    $this->tcpdf->SetFont( $bodyFont, $bodyFontStyle, $bodyFontSize, $bodyFontFile, 'default' );
  }

  /**
   * pdfFooter() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfFooter()
  {
    Renderer::pdfFooterContent();
    Renderer::pdfFooterMargin();
  }

  /**
   * pdfFooterContent() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfFooterContent()
  {

  }

  /**
   * pdfFooterMargin() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfFooterMargin()
  {
//    $this->tcpdf->SetFooterMargin( PDF_MARGIN_FOOTER );
  }

  /**
   * pdfHeader() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfHeader()
  {
    Renderer::pdfHeaderContent();
    Renderer::pdfHeaderMargin();
  }

  /**
   * pdfHeaderContent() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfHeaderContent()
  {

  }

  /**
   * pdfHeaderMargin() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfHeaderMargin()
  {
//    $this->tcpdf->SetHeaderMargin( PDF_MARGIN_HEADER );
  }

  /**
   * pdfLanguage() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfLanguage()
  {
//    $language = array(
//      'a_meta_charset' => 'UTF-8',
//      'a_meta_dir' => 'rtl',
//      'a_meta_language' => 'fa',
//      'w_page' => 'page'
//    );
//    $this->tcpdf->setLanguageArray( $language );
  }

  /**
   * pdfImages() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfImages()
  {
    Renderer::pdfImagesScale();
  }

  /**
   * pdfImagesScale() :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfImagesScale()
  {
    //set image scale factor
    $this->tcpdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
  }

  /**
   * pdfProperties()  :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfProperties()
  {
    Renderer::pdfPropertiesAuthor();
    Renderer::pdfPropertiesKeywords();
    Renderer::pdfPropertiesSubject();
    Renderer::pdfPropertiesTitle();
  }

  /**
   * pdfPropertiesAuthor()  : Sets the author property of the pdf file
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfPropertiesAuthor()
  {
    $confName = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentAuthor' ];
    $confObj = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentAuthor.' ];
    $this->tcpdf->SetAuthor( Renderer::zz_cObjGetSingle( $confName, $confObj ) );
  }

  /**
   * pdfPropertiesKeywords()  : Sets the author property of the pdf file
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfPropertiesKeywords()
  {
    $confName = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentKeywords' ];
    $confObj = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentKeywords.' ];
    $this->tcpdf->SetKeywords( Renderer::zz_cObjGetSingle( $confName, $confObj ) );
  }

  /**
   * pdfPropertiesSubject()  : Sets the author property of the pdf file
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfPropertiesSubject()
  {
    $confName = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentSubject' ];
    $confObj = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentSubject.' ];
    $this->tcpdf->SetSubject( Renderer::zz_cObjGetSingle( $confName, $confObj ) );
  }

  /**
   * pdfPropertiesTitle()  : Sets the author property of the pdf file
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfPropertiesTitle()
  {
    $confName = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentTitle' ];
    $confObj = $this->conf[ 'pdf.' ][ 'properties.' ][ 'documentTitle.' ];
    $this->tcpdf->SetTitle( Renderer::zz_cObjGetSingle( $confName, $confObj ) );
  }

  /**
   * pdfTemplate()  :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfTemplate()
  {
    Renderer::pdfTemplateFile();
  }

  /**
   * pdfTemplateFile()  :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfTemplateFile()
  {
    $ffValue = Renderer::ffValue( 'template', 'filepath' );
    // get the page count
    $this->pdfTemplateLastPage = $this->tcpdf->setSourceFile( $ffValue );
  }

  /**
   * pdfReset( ) :
   *
   * @return void
   * @access private
   * @version 3.0.0
   * @since 3.0.0
   */
  private function pdfReset()
  {
    Renderer::pdfResetMargins();
  }

  /**
   * pdfResetMargins( ) :
   *
   * @return void
   * @access private
   * @version 3.0.0
   * @since 3.0.0
   */
  private function pdfResetMargins()
  {
    $marginright = ( int ) Renderer::ffValue( 'template', 'marginright' );
    $marginleft = ( int ) Renderer::ffValue( 'template', 'marginleft' );

    $this->tcpdf->SetTopMargin( 0 );
    $this->tcpdf->SetRightMargin( $marginright - 1.000125 );  // Definition for all pages
    $this->tcpdf->SetLeftMargin( $marginleft );               // Definition for all pages
    $this->tcpdf->SetAutoPageBreak( true, 0 );
  }

  /**
   * pdfTcpdfInstance()  :
   *
   * @return	void
   * @access private
   * @version 3.0.0
   * @since   3.0.0
   */
  private function pdfTcpdfInstance()
  {

    $this->tcpdf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    'Netzmacher\Pdfcontroller\TcPdf\PdfcontrollerFpdi', 'P', 'mm', 'A4', true, 'UTF-8', false );
    $this->tcpdf->setCObj( $this->pObj->cObj );
    $this->tcpdf->setConf( $this->pObj->conf );
    $this->tcpdf->setObjFlexform( $this->pObj->objFlexform );
  }

  /**
   * zz_cObjGetSingle( )  : Renders a typoscript property with cObjGetSingle, if it is an array.
   *                        Otherwise returns the property unchanged.
   *
   * @param	string		$cObj_name  : value or the name of the property like TEXT, COA, IMAGE, ...
   * @param	array		$cObj_conf  : null or the configuration of the property
   * @return	string		$value      : unchanged value or rendered typoscript property
   * @access private
   * @version    3.0.0
   * @since      3.0.0
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
