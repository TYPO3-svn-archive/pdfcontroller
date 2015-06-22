<?php
if (!defined ('TYPO3_MODE'))  die ('Access denied.');



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

  $confArr  = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pdfcontroller']);
  $llStatic = $confArr['LLstatic'];
  switch($llStatic)
  {
    case($llStatic == 'German'):
      $llStatic = 'de';
      break;
    default:
      $llStatic = 'default';
  }
    // Language for labels of static templates and page tsConfig



    ///////////////////////////////////////////////////////////
    //
    // Plugin general configuration

  t3lib_div::loadTCA('tt_content');
    // Plugin general configuration



    ///////////////////////////////////////////////////////////
    //
    // Plugin 1 configuration

  $TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
    // Remove the default tt_content fields layout, select_key, pages and recursive.
  $TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
    // Display the field pi_flexform
  \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/pi1/flexform.xml');
    // Register our file with the flexform structure
  \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:pdfcontroller/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1', 'EXT:pdfcontroller/ext_icon.gif'),'list_type');
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
  \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:pdfcontroller/locallang_db.xml:tt_content.list_type_pi2', $_EXTKEY.'_pi2', 'EXT:pdfcontroller/ext_icon.gif'),'list_type');
    // Add the Flexform to the Plugin List
    // Plugin 2 configuration



    ///////////////////////////////////////////////////////////
    //
    // Wizard Icons

  if (TYPO3_MODE=='BE')
  {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_pdfcontroller_pi1_be_wizicon'] =
      \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'pi1/class.tx_pdfcontroller_pi1_be_wizicon.php';
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_pdfcontroller_pi2_be_wizicon'] =
      \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'pi2/class.tx_pdfcontroller_pi2_be_wizicon.php';
  }
    // Wizard Icons



    ////////////////////////////////////////////////////////////////////////////
    //
    // Add pagetree icons

  switch(true)
  {
    case($llStatic == 'de'):
      $TCA['pages']['columns']['module']['config']['items'][] =
         array('PDF-Controller: User Interface', 'pdfctrlg', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'ext_icon.gif');
      $TCA['pages']['columns']['module']['config']['items'][] =
         array('PDF-Controller: Button', 'pdfctrlb', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'ext_icon.gif');
      break;
    default:
      $TCA['pages']['columns']['module']['config']['items'][] =
         array('PDF Controller: User Interface', 'pdfctrlg', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'ext_icon.gif');
      $TCA['pages']['columns']['module']['config']['items'][] =
         array('PDF Controller: Button', 'pdfctrlb', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'ext_icon.gif');
  }
  // #34858, 120315, Rene Staecker-
  //$ICON_TYPES['pdfctrlg'] = array('icon' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'ext_icon.gif');
  //$ICON_TYPES['pdfctrlb'] = array('icon' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'ext_icon.gif');
  // #34858, 120315, Rene Staecker-
  // #34858, 120315, Rene Staecker+
  t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-pdfctrlg', '../typo3conf/ext/pdfcontroller/ext_icon.gif');
  t3lib_SpriteManager::addTcaTypeIcon('pages', 'contains-pdfctrlb', '../typo3conf/ext/pdfcontroller/ext_icon.gif');
  // #34858, 120315, Rene Staecker+

    // Add pagetree icons



    ////////////////////////////////////////////////////////////////////////////
    //
    // Enables the Include Static Templates

    // Case $llStatic
  switch(true)
  {
    case($llStatic == 'de'):
        // German
//      \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY,'static/pi1/', 'PDF-Controller: User-Interface');
      \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY,'Configuration/TypoScript/', 'PDF Controller');
      break;
    default:
        // English
//      \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY,'static/pi1/', 'PDF Controller: User Interface');
      \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY,'Configuration/TypoScript/', 'PDF Controller');
  }
    // Enables the Include Static Templates



?>