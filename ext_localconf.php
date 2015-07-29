<?php

if ( !defined( 'TYPO3_MODE' ) )
{
  die( 'Access denied.' );
}

Tx_Extbase_Utility_Extension::configurePlugin(
        'Netzmacher.' . $_EXTKEY
        , 'Pi2'
        , array(
  'Button' => 'button'
        ), array(
  'Button' => 'button'
        )
);

Tx_Extbase_Utility_Extension::configurePlugin(
        'Netzmacher.' . $_EXTKEY
        , 'Pi3'
        , array(
  'Pdf' => 'renderer'
        ), array(
  'Pdf' => 'renderer'
        )
);


////////////////////////////////////////////////////
//
  // Extending TypoScript from static template uid=43 to set up userdefined tag

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43( $_EXTKEY, 'pi1/class.tx_pdfcontroller_pi1.php', '_pi1', 'list_type', 1 );
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43( $_EXTKEY, 'pi2/class.tx_pdfcontroller_pi2.php', '_pi2', 'list_type', 1 );
// Extending TypoScript from static template uid=43 to set up userdefined tag