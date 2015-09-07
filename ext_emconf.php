<?php

########################################################################
# Extension Manager/Repository config file for ext "pdfcontroller".
#
# Auto generated 21-02-2012 13:31
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[ $_EXTKEY ] = array(
  'title' => 'PDF Controller 3',
  'description' => 'Responsive TYPO3 to PDF: Offer your HTML page for PDF download. '
  . 'Individual layout with background PDF file. '
  . 'Extra first page is possible. '
  . 'PDF Controller supports TYPO3 sessions and can used on restricted pages. '
  . 'The PDF engine is TCPDF. '
  . 'The PDF Controller 3 supports the TYPO3 responsive Start Kit (Start). '
  . 'Start is based on Zurb Foundation. '
  . 'With Start the PDF Contoller is able to handle responsive TYPO3 including grid layouts. '
  ,
  'category' => 'plugin',
  'shy' => 0,
  'version' => '4.1.1',
  'dependencies' => '',
  'conflicts' => '',
  'priority' => '',
  'loadOrder' => '',
  'module' => '',
  'state' => 'beta',
  'uploadfolder' => 1,
  'createDirs' => '',
  'modify_tables' => '',
  'clearcacheonload' => 0,
  'lockType' => '',
  'author' => 'Dirk Wildt (Die Netzmacher)',
  'author_email' => 'http://wildt.at.die-netzmacher.de',
  'author_company' => '',
  'CGLcompliance' => '',
  'CGLcompliance_note' => '',
  'constraints' => array(
    'depends' => array(
      'typo3' => '6.0.0-6.2.99',
      't3_tcpdf' => '2.4.0-',
    ),
    'conflicts' => array(
    ),
    'suggests' => array(
      'pdfcontroller_fonts' => '',
    ),
  ),
  '_md5_values_when_last_written' => '',
);
?>
