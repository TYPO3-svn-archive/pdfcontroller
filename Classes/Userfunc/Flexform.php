<?php

namespace Netzmacher\Pdfcontroller\Userfunc;

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
 * Class for flexform methods
 *
 * @package TYPO3
 * @subpackage pdfcontroller
 * @author Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * @version 4.0.0
 * @since 3.1.0
 */
class Flexform
{

  private $_listOfFonts = null; // [array] list of current fonts

  /**
   * sheetFontsFieldFamilybody() :
   *
   * @param array $pluginConf: current plugin configuration
   * @return	array $pluginConf: plugin configuration with updated font-family field
   * @access public
   * @version 3.1.0
   * @since   3.1.0
   */

  public function sheetFontsFieldFamilybody( $pluginConf )
  {
    $pluginConf = Flexform::_sheetFontsFieldFamily( $pluginConf );
    return $pluginConf;
  }

  /**
   * sheetFontsFieldFamilyfooter() :
   *
   * @param array $pluginConf: current plugin configuration
   * @return	array $pluginConf: plugin configuration with updated font-family field
   * @access public
   * @version 3.1.0
   * @since   3.1.0
   */
  public function sheetFontsFieldFamilyfooter( $pluginConf )
  {
    $pluginConf = Flexform::_sheetFontsFieldFamily( $pluginConf );
    return $pluginConf;
  }

  /**
   * sheetFontsFieldFamilyheader() :
   *
   * @param array $pluginConf: current plugin configuration
   * @return	array $pluginConf: plugin configuration with updated font-family field
   * @access public
   * @version 3.1.0
   * @since   3.1.0
   */
  public function sheetFontsFieldFamilyheader( $pluginConf )
  {
    $pluginConf = Flexform::_sheetFontsFieldFamily( $pluginConf );
    return $pluginConf;
  }


  /**
   * _fontsFromTcpdf() : Add fonts from extension pdfcontroller_fonts
   *
   * @return	void
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _fontsFromTcpdf( $pluginConf )
  {

    // T3 TCPDF isn't loaded
    if ( !\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 't3_tcpdf' ) )
    {
      return $pluginConf;
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
      if ( in_array( $fontFile, ( array ) $this->_listOfFonts ) )
      {
        continue;
      }
      //$value = $fontPath . $fontFile;
      $value = $fontFile;
      switch ( $fontFile )
      {
        case('helvetica'):
          $label = $fontFile . ' (default)';
          break;
        default:
          $label = $fontFile;
          break;
      }
      $pluginConf[ 'items' ][] = array( $label, $value );
      $this->_listOfFonts[] = $fontFile;
    }
    return $pluginConf;
  }

  /**
   * _sheetFontsFieldFamily() :
   *
   * @param array $pluginConf: current plugin configuration
   * @return	array $pluginConf: plugin configuration with updated font-family field
   * @access private
   * @version 3.1.0
   * @since   3.1.0
   */
  private function _sheetFontsFieldFamily( $pluginConf )
  {
    $pluginConf = Flexform::_fontsFromTcpdf( $pluginConf );

    return $pluginConf;
  }

}
