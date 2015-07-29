<?php

namespace Netzmacher\Pdfcontroller\Userfunc;

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
 * Class for flexform methods for the PDF Controller
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
  private $_maxWidth = '600px';

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

  /**
   * _sheetFontsFieldLockHashKeyWords() :
   *
   * @return	string $prompt
   * @access private
   * @version 4.0.0
   * @since   4.0.0
   */
  private function _sheetFontsFieldLockHashKeyWords()
  {
    $message = NULL;
    $lockHashKeyWords = $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'FE' ][ 'lockHashKeyWords' ];

    switch ( TRUE )
    {
      case(!empty( $lockHashKeyWords )):
        $prompt = $GLOBALS[ 'LANG' ]->sL( 'LLL:EXT:pdfcontroller/Resources/Private/Language/locallang_db.xlf:sheet_check.lockHashKeyWords.message.error' );
        $prompt = str_replace('%lockHashKeyWords%', $lockHashKeyWords, $prompt);
        $message = $message . Flexform::_zzTypo3Message($prompt, 'error');
        break;
      case(empty( $lockHashKeyWords )):
      default:
        $prompt = $GLOBALS[ 'LANG' ]->sL( 'LLL:EXT:pdfcontroller/Resources/Private/Language/locallang_db.xlf:sheet_check.lockHashKeyWords.message.ok' );
        $message = $message . Flexform::_zzTypo3Message($prompt, 'ok');
        break;
    }

    return $message;
  }

  /**
   * _zzTypo3Message() :
   *
   * @param string $prompt
   * @param string $severity
   * @return	string $prompt
   * @access private
   * @version 4.0.0
   * @since   4.0.0
   */
  private function _zzTypo3Message( $prompt, $severity )
  {
    switch ( $severity )
    {
      case 'error':
      case 'information':
      case 'notice':
      case 'ok':
      case 'warning':
        // follow the workflow
        break;
      default:
        var_dump( __METHOD__, __LINE__, 'FATAL ERROR: severity isn\'t defined: ' . $severity );
        die();
    }

    $t3message = '
      <div class="typo3-message message-' . $severity . '" style="max-width:' . $this->_maxWidth . ';">
        <div class="message-body">
          ' . $prompt . '
        </div>
      </div>
      ';

    return $t3message;
  }

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
   * sheetFontsFieldLockHashKeyWords() :
   *
   * @param array $pluginConf: current plugin configuration
   * @return	array $pluginConf: plugin configuration with updated font-family field
   * @access public
   * @version 4.0.0
   * @since   4.0.0
   */
  public function sheetFontsFieldLockHashKeyWords( $pluginConf )
  {
    unset( $pluginConf );
    $prompt = Flexform::_sheetFontsFieldLockHashKeyWords();
    return $prompt;
  }

}
