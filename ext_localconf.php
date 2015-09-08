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