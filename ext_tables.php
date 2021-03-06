<?php

if ( !defined( 'TYPO3_MODE' ) )
{
  die( 'Access denied.' );
}




///////////////////////////////////////////////////////////
//
// INDEX
// Methods for backend workflows
// Language for labels of static templates and page tsConfig
// Plugin general configuration
// Plugin 1 configuration
// Wizard Icons
// Enables the Include Static Templates
// TCA
///////////////////////////////////////////////////////////
//
// Language for labels of static templates and page tsConfig

$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( $_EXTKEY );
$confArr = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'pdfcontroller' ] );
$llStatic = $confArr[ 'LLstatic' ];
switch ( $llStatic )
{
  case($llStatic == 'German'):
    $llStatic = 'de';
    break;
  default:
    $llStatic = 'default';
}
// Language for labels of static templates and page tsConfig
////////////////////////////////////////////////////////////////////////////
//
// Enables the Include Static Templates
// Case $llStatic
switch ( true )
{
  case($llStatic == 'de'):
    // German
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/',                               'PDF Controller [1]' );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/tt_content/',                    'PDF Controller [1.1] +tt_content Optimierung' );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/Extensions/Slick/',              'PDF Controller [2] Ext: Slick' );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/Extensions/Start/',              'PDF Controller [2] Ext: Start' );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/Extensions/Start/Gridelements/', 'PDF Controller [2.1] Ext: +Start Gridelements' );
    break;
  default:
    // English
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/',                               'PDF Controller [1]' );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/tt_content/',                    'PDF Controller [1.1] +tt_content optimisation' );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/Extensions/Slick/',              'PDF Controller [2] Ext: Slick' );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/Extensions/Start/',              'PDF Controller [2] Ext: Start' );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/Extensions/Start/Gridelements/', 'PDF Controller [2.1] Ext: +Start Gridelements' );
    break;
}
// Enables the Include Static Templates
///////////////////////////////////////////////////////////
//
// Plugin general configuration

t3lib_div::loadTCA( 'tt_content' );
// Plugin general configuration
///////////////////////////////////////////////////////////
//
// Plugin 1 configuration

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        $_EXTKEY
        , 'Pi3'
        , 'LLL:EXT:pdfcontroller/Resources/Private/Language/locallang_mod.xlf:pluginPi3Title'
        , t3lib_extMgm::extRelPath( $_EXTKEY ) . 'ext_icon.gif'
);
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_excludelist' ][ 'pdfcontroller_pi3' ] = 'layout,select_key,pages,recursive';
$TCA[ 'tt_content' ][ 'types' ][ 'list' ][ 'subtypes_addlist' ][ 'pdfcontroller_pi3' ] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
        array(
  'LLL:EXT:pdfcontroller/Resources/Private/Language/locallang_mod.xlf:pluginPi3Title'
  , 'pdfcontroller_pi3'
  , 'EXT:pdfcontroller/ext_icon.gif'
        )
        , 'list_type'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'pdfcontroller_pi3'
        , 'FILE:EXT:' . $_EXTKEY . '/Configuration/Flexforms/Pdf/Flexform.xml'
);
// Register our file with the flexform structure
// Add the Flexform to the Plugin List
// Plugin 1 configuration
///////////////////////////////////////////////////////////
//
// Plugin 2 configuration
//  $TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages,recursive';
//    // Remove the default tt_content fields layout, select_key, pages and recursive.
//  $TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi2']='pi_flexform';
//    // Display the field pi_flexform
//  \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:'.$_EXTKEY.'/pi2/flexform.xml');
// Register our file with the flexform structure
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        $_EXTKEY
        , 'Pi2'
        , 'LLL:EXT:pdfcontroller/Resources/Private/Language/locallang_mod.xlf:pluginPi2Title'
        , t3lib_extMgm::extRelPath( $_EXTKEY ) . 'ext_icon.gif'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
        array(
  'LLL:EXT:pdfcontroller/Resources/Private/Language/locallang_mod.xlf:pluginPi2Title'
  , 'pdfcontroller_pi2'
  , 'EXT:pdfcontroller/ext_icon.gif'
        )
        , 'list_type' );
// Add the Flexform to the Plugin List
// Plugin 2 configuration
///////////////////////////////////////////////////////////
//
// Wizard Icons

if ( TYPO3_MODE == 'BE' )
{
  $TBE_MODULES_EXT[ 'xMOD_db_new_content_el' ][ 'addElClasses' ][ 'Netzmacher\Pdfcontroller\Utility\Hook\ContentElementWizard' ] = $extPath . 'Classes/Utility/Hook/ContentElementWizard.php';
}
// Wizard Icons
////////////////////////////////////////////////////////////////////////////
//
// Add pagetree icons

switch ( true )
{
  case($llStatic == 'de'):
    $TCA[ 'pages' ][ 'columns' ][ 'module' ][ 'config' ][ 'items' ][] = array( 'PDF-Controller: User Interface', 'pdfctrlg', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath( $_EXTKEY ) . 'ext_icon.gif' );
    $TCA[ 'pages' ][ 'columns' ][ 'module' ][ 'config' ][ 'items' ][] = array( 'PDF-Controller: Button', 'pdfctrlb', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath( $_EXTKEY ) . 'ext_icon.gif' );
    break;
  default:
    $TCA[ 'pages' ][ 'columns' ][ 'module' ][ 'config' ][ 'items' ][] = array( 'PDF Controller: User Interface', 'pdfctrlg', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath( $_EXTKEY ) . 'ext_icon.gif' );
    $TCA[ 'pages' ][ 'columns' ][ 'module' ][ 'config' ][ 'items' ][] = array( 'PDF Controller: Button', 'pdfctrlb', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath( $_EXTKEY ) . 'ext_icon.gif' );
}
t3lib_SpriteManager::addTcaTypeIcon( 'pages', 'contains-pdfctrlg', '../typo3conf/ext/pdfcontroller/ext_icon.gif' );
t3lib_SpriteManager::addTcaTypeIcon( 'pages', 'contains-pdfctrlb', '../typo3conf/ext/pdfcontroller/ext_icon.gif' );
// Add pagetree icons
